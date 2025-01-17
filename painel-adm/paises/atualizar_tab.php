<?php

require_once(dirname(__DIR__) . "../../siscomex/handshake.php");
require_once(dirname(__DIR__) . '../../conexao.php');

// Configuração da URL
$endpoint = '/api/ext/tabela';
$nomeTabela = 'PAIS';
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
        $sigla_iso2 = $sigla_iso3 = $nome = $nome_ingles = $nome_frances = $codigo_numerico = $inicioVigencia = $fimVigencia = $internoVersao = $internoHash = '';

        // Iterar sobre os campos de cada item
        foreach ($item['campos'] as $campo) {
            switch ($campo['nome']) {
                case 'SIGLA_ISO2':
                    $sigla_iso2 = $campo['valor'];
                    break;
                case 'SIGLA_IS03':
                    $sigla_iso3 = $campo['valor'];
                    break;
                case 'NOME':
                    $nome = $campo['valor'];
                    break;
                case 'NOME_INGLES':
                    $nome_ingles = $campo['valor'];
                    break;
                case 'NOME_FRANCES':
                    $nome_frances = $campo['valor'];
                    break;
                case 'CODIGO_NUMERICO':
                    $codigo_numerico = $campo['valor'];
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
        
        // Verifico se o registro já existe através do CODIGO_NUMERICO
        $querySelect = $pdo->prepare("SELECT codigo_numerico FROM paises WHERE codigo_numerico = :codigo_numerico");
        $querySelect->bindParam(':codigo_numerico', $codigo_numerico);
        $querySelect->execute();
        $total_reg = $querySelect->rowCount();

        if ($total_reg === 0) {
            // Inserir apenas se o registro não existir
            $query = $pdo->prepare("INSERT INTO paises (SIGLA_ISO2, SIGLA_IS03, NOME, NOME_INGLES, NOME_FRANCES, CODIGO_NUMERICO, DATA_INICIO, DATA_FIM, INTERNO_VERSAO, INTERNO_HASH) 
                                    VALUES (:sigla_iso2, :sigla_iso3, :nome, :nome_ingles, :nome_frances, :codigo_numerico, :dataInicio, :dataFim, :internoVersao, :internoHash)");

            $query->bindValue(":sigla_iso2", $sigla_iso2);
            $query->bindValue(":sigla_iso3", $sigla_iso3);
            $query->bindValue(":nome", $nome);
            $query->bindValue(":nome_ingles", $nome_ingles);
            $query->bindValue(":nome_frances", $nome_frances);
            $query->bindValue(":codigo_numerico", $codigo_numerico);
            $query->bindValue(":dataInicio", $inicioVigencia);
            $query->bindValue(":dataFim", $fimVigencia);
            $query->bindValue(":internoVersao", $internoVersao);
            $query->bindValue(":internoHash", $internoHash);
            $query->execute();
        }
    }

    // Remover registros duplicados, mantendo apenas o mais recente (considerando `interno_versao`)
    $pdo->query("DELETE p1 FROM paises p1
                 INNER JOIN paises p2 
                 WHERE 
                    p1.codigo_numerico = p2.codigo_numerico AND 
                    p1.interno_versao < p2.interno_versao");
}

curl_close($ch);

// Aparece mensagem de atualização realizada com sucesso e mostra o botão para retornar a lista de registros!
echo "Atualização da Tabela realizada com sucesso!";
echo "<br>";
echo "<a class='btn btn-primary' href='index.php?pag=paises'>Retornar à Lista de Países</a>";

?>
