<?php

require_once(dirname(__DIR__) . "../../siscomex/handshake.php");
require_once(dirname(__DIR__) . '../../conexao.php');

// Configuração da URL
$endpoint = '/api/ext/tabela';
$nomeTabela = 'MOEDA';
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
 //echo "TESTE RESPONSE: " . $response;

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
        $codigo = $nome = $simbolo = $codigoSwift = $inicioVigencia = $fimVigencia = $internoVersao = '';

        // Iterar sobre os campos de cada item
        foreach ($item['campos'] as $campo) {
            switch ($campo['nome']) {
                case 'CODIGO':
                    $codigo = $campo['valor'];
                    break;
                case 'NOME':
                    $nome = $campo['valor'];
                    break;
                case 'SIMBOLO':
                    $simbolo = $campo['valor'];
                    break;
                case 'CODIGO_SWIFT':
                    $codigoSwift = $campo['valor'];
                    break;

                case 'SIGLA_ISO2':
                    $sigla_iso2 = $campo['valor'];
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
        
        

        // Verifico se o registro a ser atualizado já existe através do CODIGO
        $querySelect = $pdo->query("SELECT codigo from moeda WHERE codigo = '$codigo'");
        $res = $querySelect->fetchAll(PDO::FETCH_ASSOC);
        $total_reg = @count($res);
        if($total_reg === 0)
        { // Se registro for MENOR ou = 0, ou seja, não existir, então registro deve ser incluido! 
            $query = $pdo->prepare("INSERT INTO moeda set codigo = :codigo, nome = :nome,
            simbolo = :simbolo, codigo_swift = :codSwift, sigla_iso2 = :siglaIso2, data_inicio = :dataInicio,
            data_fim = :dataFim, interno_versao = :internoVersao");

            $query->bindValue(":codigo", "$codigo");
            $query->bindValue(":nome", "$nome");
            $query->bindValue(":simbolo", "$simbolo");
            $query->bindValue(":codSwift", "$codigoSwift");
            $query->bindValue(":siglaIso2", "$sigla_iso2");
            $query->bindValue(":dataInicio", "$inicioVigencia");
            $query->bindValue(":dataFim", "$fimVigencia");
            $query->bindValue(":internoVersao", "$internoVersao");
            $query->execute();

            
        }
                
    }

    
}

curl_close($ch);

// Aparece mensagem de atualização realizada com sucesso e mostra o botão para retornar a lista de registros!
echo "Atualização da Tabela realizada com sucesso!";
echo "<br>";
echo "<a class='btn btn-primary' href='index.php?pag=moeda'>Retornar a Lista de Moedas</a>"


?>
