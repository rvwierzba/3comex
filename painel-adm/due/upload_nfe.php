<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/tmp/php-error.log');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['xml_file'])) {
    $error_message = 'Requisição inválida.';
    error_log($error_message);
    echo json_encode(['success' => false, 'message' => $error_message]);
    exit;
}

$xmlFile = $_FILES['xml_file'];

if ($xmlFile['error'] !== UPLOAD_ERR_OK) {
    $error_message = 'Erro no upload do arquivo: ' . $xmlFile['error'];
    error_log($error_message);
    echo json_encode(['success' => false, 'message' => $error_message]);
    exit;
}

$xmlContent = @file_get_contents($xmlFile['tmp_name']);

if ($xmlContent === FALSE) {
    $error_message = 'Erro ao ler o arquivo XML.';
    error_log($error_message);
    echo json_encode(['success' => false, 'message' => $error_message]);
    exit;
}

try {
    libxml_use_internal_errors(true);
    $xml = new SimpleXMLElement($xmlContent);

    if ($xml === FALSE) {
      $errors = libxml_get_errors();
      $error_message = "Erro ao processar o XML:\n";
      foreach ($errors as $error) {
          $error_message .= "\t" . $error->message;
      }
      libxml_clear_errors();
      error_log($error_message);
      echo json_encode(['success' => false, 'message' => $error_message]);
      exit;
    }

    // Extrair dados da NFE (adapte para a estrutura da sua NFE)
    $cnpj_emitente = (string)$xml->NFe->infNFe->emit->CNPJ;
    error_log("CNPJ Emitente: " . $cnpj_emitente);

    $nome_emitente = (string)$xml->NFe->infNFe->emit->xNome;
    error_log("Nome Emitente: " . $nome_emitente);

    $nome_destinatario = (string)$xml->NFe->infNFe->dest->xNome;
    error_log("Nome Destinatario: " . $nome_destinatario);
    
    $endereco_destinatario = (string)$xml->NFe->infNFe->dest->enderDest->xLgr . ', ' . (string)$xml->NFe->infNFe->dest->enderDest->nro;
    error_log("Endereço Destinatario: " . $endereco_destinatario);

    $pais_destinatario = (string)$xml->NFe->infNFe->dest->enderDest->xPais;
    error_log("País Destinatario: " . $pais_destinatario);

    //Dados dentro do nó Detalhes
    $und_estatistica = 'KG'; // Substitua com o valor real se existir no XML (NÃO TEM NO XML)
    error_log("Unidade Estatística: " . $und_estatistica);

    $qtd_estatistica = (float)$xml->NFe->infNFe->det->prod->qCom;
    error_log("Quantidade Estatística: " . $qtd_estatistica);

    $und_comercializacao = (string)$xml->NFe->infNFe->det->prod->uCom;
    error_log("Unidade Comercialização: " . $und_comercializacao);

    $qtd_comercializada = (float)$xml->NFe->infNFe->det->prod->qCom;
    error_log("Quantidade Comercializada: " . $qtd_comercializada);

    $valor_total = (float)$xml->NFe->infNFe->det->prod->vProd;
    error_log("Valor Total: " . $valor_total);

    $peso_liquido = (float)$xml->NFe->infNFe->transp->vol->pesoL;
    error_log("Peso Líquido: " . $peso_liquido);

    $numero_nf = (string)$xml->NFe->infNFe->ide->nNF;
    error_log("Número NF: " . $numero_nf);



    $nfeData = [
        'chave_acesso' => (string)$xml->NFe->infNFe->attributes()->Id,
        'emitente' => [
            'cnpj' => $cnpj_emitente,
            'nome' => $nome_emitente
        ],
        'destinatario' => [
            'nome' => $nome_destinatario,
            'endereco' =>  $endereco_destinatario,
            'pais' => $pais_destinatario
        ],
        'detalhes' =>[
            'und_estatistica' => $und_estatistica, //Não tem no XML
            'qtd_estatistica' => $qtd_estatistica,
            'und_comercializacao' => $und_comercializacao,
            'qtd_comercializada' => $qtd_comercializada,
            'valor_total' => $valor_total
        ],
        'total' => [
            'peso_liquido' => $peso_liquido
        ],
        'numero_nf' => $numero_nf,
        // Adicione outros campos que você precisa extrair
    ];

    $response = ['success' => true, 'nfe' => $nfeData];
    error_log(json_encode($response));
    echo json_encode($response);

} catch (Exception $e) {
    $error_message = 'Erro ao processar o XML: ' . $e->getMessage();
    error_log($error_message);
    echo json_encode(['success' => false, 'message' => $error_message]);
} finally {
  libxml_use_internal_errors(false);
}
?>