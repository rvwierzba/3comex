<?php

require_once(dirname(__DIR__) . "../../siscomex/handshake.php");
require_once(dirname(__DIR__) . '../../conexao.php');

// Configuração da URL
$endpoint = '/api/ext/tabela';
$nomeTabela = 'TIPO_CONHECIMENTO';
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

// Verificar erros
if ($response === false) {
    echo "Erro na requisição cURL: " . curl_error($ch);
} elseif ($httpCode !== 200) {
    echo "Conexão estabelecida com sucesso. HTTP Status Code: $httpCode";
    echo "Erro na resposta: HTTP Status Code não é 200.";
    echo "Resposta do servidor: " . $response;  // Exibe a resposta para análise
} elseif (!is_array(json_decode($response, true)) || !isset(json_decode($response, true)['dados'])) {
    echo "Erro na resposta ou a resposta não é um array válido.";
} else {
    // Sucesso na obtenção da resposta
    $responseArray = json_decode(trim($response), true);

    // Iterar sobre os itens na resposta
    foreach ($responseArray['dados'] as $item) {
        // Preparando as variáveis para evitar erros de variáveis indefinidas
        $codigo = $descricao = $indicadorTpBasico = $internoVersao = $inicioVigencia = $fimVigencia = '';

        // Iterar sobre os campos de cada item
        foreach ($item['campos'] as $campo) {
            switch ($campo['nome']) {
                case 'CODIGO':
                    $codigo = $campo['valor'];
                    break;
                case 'DESCRICAO':
                    $descricao = $campo['valor'];
                    break;
                case 'INDICADOR_TIPO_BASICO':
                    $indicadorTpBasico = $campo['valor'];
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
            }
        }

        // Verifico se o registro já existe através do CODIGO
        $querySelect = $pdo->prepare("SELECT codigo FROM tp_conhec WHERE codigo = :codigo");
        $querySelect->bindParam(':codigo', $codigo);
        $querySelect->execute();
        $total_reg = $querySelect->rowCount();

        if ($total_reg === 0) {
            // Inserir apenas se o registro não existir
            $query = $pdo->prepare("INSERT INTO tp_conhec (codigo, descricao, data_inicio, data_fim, interno_versao, indicador_tipo_basico) 
                                    VALUES (:codigo, :descricao, :dataInicio, :dataFim, :internoVersao, :indicTpBasico)");

            $query->bindValue(":codigo", $codigo);
            $query->bindValue(":descricao", $descricao);
            $query->bindValue(":dataInicio", $inicioVigencia);
            $query->bindValue(":dataFim", $fimVigencia);
            $query->bindValue(":internoVersao", $internoVersao);
            $query->bindValue(":indicTpBasico", $indicadorTpBasico);
            $query->execute();
        }
    }

    // Remover registros duplicados, mantendo apenas o mais recente com base em `interno_versao`
    $pdo->query("DELETE t1 FROM tp_conhec t1
                 INNER JOIN tp_conhec t2 
                 WHERE 
                    t1.codigo = t2.codigo AND 
                    t1.interno_versao < t2.interno_versao");
}

curl_close($ch);

// Aparece mensagem de atualização realizada com sucesso e mostra o botão para retornar a lista de registros!
echo "Atualização da Tabela realizada com sucesso!";
echo "<br>";
echo "<a class='btn btn-primary' href='index.php?pag=tp_conhec'>Retornar à Lista Tipo Conhecimentos</a>";

?>
