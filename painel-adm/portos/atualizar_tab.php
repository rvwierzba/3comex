<?php

require_once(dirname(__DIR__) . "../../siscomex/handshake.php");
require_once(dirname(__DIR__) . '../../conexao.php');

// Configuração da URL
$endpoint = '/api/ext/tabela';
$nomeTabela = 'PORTO';
$fullURL =  $baseURL . '/' . 'tabx' . '/' . $endpoint . '/' . $nomeTabela;

// Tokens do HANDSHAKE: $setToken & $csrfToken no handshake.php
$requestHeaders = array(
    "Authorization: $setToken",
    "X-Csrf-Token: $csrfToken"
);

// Inicializar cURL
$ch = curl_init();

// Configurar a requisição
curl_setopt($ch, CURLOPT_URL, $fullURL);
curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");

// Executar a solicitação
$response = curl_exec($ch);

// Verifica o status HTTP da resposta
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Decodifica o JSON para um array associativo
$responseCleaned = trim($response);
$responseArray = json_decode($responseCleaned, true);

// Verificar erros
if ($response === false) {
    echo "Erro na requisição cURL: " . curl_error($ch);
} elseif ($httpCode !== 200) {
    echo "Conexão estabelecida com sucesso. HTTP Status Code: $httpCode";
    echo "Erro na resposta: HTTP Status Code não é 200.";
} elseif (!is_array($responseArray) || !isset($responseArray['dados'])) {
    echo "Erro na resposta ou a resposta não é um array válido.";
} else {
    // Sucesso na obtenção da resposta
    // Iterar sobre os itens na resposta
    foreach ($responseArray['dados'] as $item) {
        // Preparando as variáveis para evitar erros de variáveis indefinidas
        $codigo = $descricao = $inicioVigencia = $fimVigencia = $internoVersao = $internoHash = '';

        // Iterar sobre os campos de cada item
        foreach ($item['campos'] as $campo) {
            switch ($campo['nome']) {
                case 'CODIGO':
                    $codigo = $campo['valor'];
                    break;
                case 'DESCRICAO':
                    $descricao = $campo['valor'];
                    break;
                case 'DATA_INICIO':
                    $inicioVigencia = $campo['valor'];
                    break;
                case 'DATA_FIM':
                    $fimVigencia = $campo['valor'];
                    break;
                case 'INTERNO_VERSAO':
                    $internoVersao = $campo['valor'];
                    break;
                case 'INTERNO_HASH':
                    $internoHash = $campo['valor'];
                    break;
            }
        }
        
        // Verifico se o registro já existe através do CODIGO
        $querySelect = $pdo->prepare("SELECT codigo FROM portos WHERE codigo = :codigo");
        $querySelect->bindParam(':codigo', $codigo);
        $querySelect->execute();
        $total_reg = $querySelect->rowCount();

        if ($total_reg === 0) {
            // Inserir apenas se o registro não existir
            $query = $pdo->prepare("INSERT INTO portos (CODIGO, DESCRICAO, DATA_INICIO, DATA_FIM, INTERNO_VERSAO, INTERNO_HASH) 
                                    VALUES (:codigo, :descricao, :dataInicio, :dataFim, :internoVersao, :internoHash)");

            $query->bindValue(":codigo", $codigo);
            $query->bindValue(":descricao", $descricao);
            $query->bindValue(":dataInicio", $inicioVigencia);
            $query->bindValue(":dataFim", $fimVigencia);
            $query->bindValue(":internoVersao", $internoVersao);
            $query->bindValue(":internoHash", $internoHash);
            $query->execute();
        }
    }

    // Remover registros duplicados, mantendo apenas o mais recente com base em `interno_versao`
    $pdo->query("DELETE p1 FROM portos p1
                 INNER JOIN portos p2 
                 WHERE 
                    p1.codigo = p2.codigo AND 
                    p1.interno_versao < p2.interno_versao");
}

curl_close($ch);

// Aparece mensagem de atualização realizada com sucesso e mostra o botão para retornar a lista de registros!
echo "Atualização da Tabela realizada com sucesso!";
echo "<br>";
echo "<a class='btn btn-primary' href='index.php?pag=portos'>Retornar à Lista de Portos</a>";

?>
