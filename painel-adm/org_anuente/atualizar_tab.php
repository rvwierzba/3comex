<?php

require_once(dirname(__DIR__) . "../../siscomex/handshake.php");
require_once(dirname(__DIR__) . '../../conexao.php');

// Configuração da URL
$endpoint = '/api/ext/tabela';
$nomeTabela = 'ORGAO_ANUENTE';
$fullURL =  $baseURL . '/' . 'tabx' . '/' . $endpoint . '/' . $nomeTabela;

// Tokens do HANDSHAKE: $setToken & $csrfToken no handshake.php
$requestHeaders= array(
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

// Decodifica o JSON para um array associativo
$responseCleaned = trim($response);
$responseArray = json_decode($responseCleaned, true);

// Verificar erros
if ($response === false) {
    echo "Erro na requisição cURL: " . curl_error($ch);
} elseif (!is_array($responseArray) || !isset($responseArray['dados'])) {
    echo "Erro na resposta ou a resposta não é um array válido.";
} else {
    // Sucesso na obtenção da resposta
    
    // Iterar sobre os itens na resposta
    foreach ($responseArray['dados'] as $item) {
        // Preparando as variáveis para evitar erros de variáveis indefinidas
        $codigo = $sigla= $descricao = $cnpj = $inicioVigencia = $fimVigencia = $internoVersao = '';

        // Iterar sobre os campos de cada item
        foreach ($item['campos'] as $campo) {
            switch ($campo['nome']) {
                case 'CODIGO':
                    $codigo = $campo['valor'];
                    break;
                case 'SIGLA':
                    $sigla = $campo['valor'];
                    break;
                case 'DESCRICAO':
                    $descricao = $campo['valor'];
                    break;
                case 'CNPJ':
                    $cnpj = $campo['valor'];
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
        $querySelect = $pdo->prepare("SELECT codigo FROM org_anuente WHERE codigo = :codigo");
        $querySelect->bindParam(':codigo', $codigo);
        $querySelect->execute();
        $total_reg = $querySelect->rowCount();

        if ($total_reg === 0) {
            // Inserir apenas se o registro não existir
            $query = $pdo->prepare("INSERT INTO org_anuente (codigo, sigla, descricao, cnpj, data_inicio, data_fim, interno_versao) 
                                    VALUES (:codigo, :sigla, :descricao, :cnpj, :dataInicio, :dataFim, :internoVersao)");

            $query->bindValue(":codigo", $codigo);
            $query->bindValue(":sigla", $sigla);
            $query->bindValue(":descricao", $descricao);
            $query->bindValue(":cnpj", $cnpj);
            $query->bindValue(":dataInicio", $inicioVigencia);
            $query->bindValue(":dataFim", $fimVigencia);
            $query->bindValue(":internoVersao", $internoVersao);
            $query->execute();
        }
    }

    // Remover registros duplicados, mantendo apenas o registro mais antigo com base no ID
    $pdo->query("DELETE FROM org_anuente WHERE id NOT IN (SELECT MIN(id) FROM org_anuente GROUP BY codigo)");
}

curl_close($ch);

// Aparece mensagem de atualização realizada com sucesso e mostra o botão para retornar a lista de registros!
echo "Atualização da Tabela realizada com sucesso!";
echo "<br>";
echo "<a class='btn btn-primary' href='index.php?pag=org_anuente'>Retornar a Lista de Orgões Anuentes</a>";

?>
