<?php

require_once(dirname(__DIR__) . "../../siscomex/handshake.php");
require_once(dirname(__DIR__) . '../../conexao.php');

// Configuração da URL
$endpoint = '/api/ext/tabela';
$nomeTabela = 'FUNDAMENTO_LEGAL_TT';
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
        $codigo = $descricao = $mnemonicoSistemaControle = $codigoBeneficioFiscalSisen = $permitirDuimpTerceiros = $permitirPessoaFisica = $inicioVigencia = $fimVigencia = $internoVersao = '';

        // Iterar sobre os campos de cada item
        foreach ($item['campos'] as $campo) {
            switch ($campo['nome']) {
                case 'CODIGO':
                    $codigo = $campo['valor'];
                    break;
                case 'DESCRICAO':
                    $descricao = $campo['valor'];
                    break;
                case 'MNEMONICO_SISTEMA_CONTROLE':
                    $mnemonicoSistemaControle = $campo['valor'];
                    break;
                case 'CODIGO_BENEFICIO_FISCAL_SISEN':
                    $codigoBeneficioFiscalSisen = $campo['valor'];
                    break;
                case 'IN_PERMITIR_DUIMP_VIN_TERCEIROS':
                    $permitirDuimpTerceiros = $campo['valor'];
                    break;
                case 'IN_PERMITE_REGISTRO_PESSOA_FISICA':
                    $permitirPessoaFisica = $campo['valor'];
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
        $querySelect = $pdo->prepare("SELECT codigo FROM fundamento_legal_tt WHERE codigo = :codigo");
        $querySelect->bindParam(':codigo', $codigo);
        $querySelect->execute();
        $total_reg = $querySelect->rowCount();

        if ($total_reg === 0) {
            // Inserir apenas se o registro não existir
            $query = $pdo->prepare("INSERT INTO fundamento_legal_tt (codigo, descricao, mnemonico_sistema_controle, codigo_beneficio_fiscal_sisen, in_permitir_duimp_vin_terceiros, in_permite_registro_pessoa_fisica, data_inicio, data_fim, interno_versao) 
                                    VALUES (:codigo, :descricao, :mnemonicoSistemaControle, :codigoBeneficioFiscalSisen, :permitirDuimpTerceiros, :permitirPessoaFisica, :dataInicio, :dataFim, :internoVersao)");

            $query->bindValue(":codigo", $codigo);
            $query->bindValue(":descricao", $descricao);
            $query->bindValue(":mnemonicoSistemaControle", $mnemonicoSistemaControle);
            $query->bindValue(":codigoBeneficioFiscalSisen", $codigoBeneficioFiscalSisen);
            $query->bindValue(":permitirDuimpTerceiros", $permitirDuimpTerceiros);
            $query->bindValue(":permitirPessoaFisica", $permitirPessoaFisica);
            $query->bindValue(":dataInicio", $inicioVigencia);
            $query->bindValue(":dataFim", $fimVigencia);
            $query->bindValue(":internoVersao", $internoVersao);
            $query->execute();
        }
    }

    // Remover registros duplicados, mantendo apenas o registro mais antigo com base no ID
    $pdo->query("DELETE FROM fundamento_legal_tt WHERE id NOT IN (SELECT MIN(id) FROM fundamento_legal_tt GROUP BY codigo)");
}

curl_close($ch);

// Aparece mensagem de atualização realizada com sucesso e mostra o botão para retornar a lista de registros!
echo "Atualização da Tabela realizada com sucesso!";
echo "<br>";
echo "<a class='btn btn-primary' href='index.php?pag=fundamento_legal_tt'>Retornar à Lista de Fundamentos Legais</a>";

?>
