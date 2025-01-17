<?php
// painel-adm/due/upload-xml.php

header('Content-Type: application/json');

$response = ['success' => false, 'data' => [], 'message' => ''];

// Verificar se arquivos foram enviados
if(!isset($_FILES['xml_files'])){
    $response['message'] = 'Nenhum arquivo XML enviado.';
    echo json_encode($response);
    exit;
}

$files = $_FILES['xml_files'];

// Diretório para salvar os arquivos XML temporariamente
$uploadDir = 'uploads/xml/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

for($i=0; $i < count($files['name']); $i++){
    $fileName = basename($files['name'][$i]);
    $targetFilePath = $uploadDir . time() . '_' . $fileName;

    // Verificar se o arquivo é um XML
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
    if($fileType != 'xml'){
        $response['message'] = 'Apenas arquivos XML são permitidos.';
        echo json_encode($response);
        exit;
    }

    // Mover o arquivo para o diretório de upload
    if(move_uploaded_file($files['tmp_name'][$i], $targetFilePath)){
        // Processar o arquivo XML
        $xml = simplexml_load_file($targetFilePath);
        if($xml === false){
            $response['message'] = 'Erro ao processar o arquivo XML: ' . $fileName;
            echo json_encode($response);
            exit;
        }

        // Extrair informações do XML conforme o modelo
        // Note: Ajuste os caminhos conforme a estrutura real do seu XML
        try{
            $declaration = $xml->children('urn:wco:datamodel:WCO:GoodsDeclaration:1', true)->declarationDrawbackIsencao;
            $goodsShipment = $declaration->GoodsShipment->GovernmentAgencyGoodsItem;
            $additionalDocument = $goodsShipment->AdditionalDocument;
            
            // Extrair campos necessários
            $nrProcesso = (string)$additionalDocument->id->value;
            $nrAdicao = (string)$additionalDocument->itemID->value;
            $chaveAcesso = (string)$additionalDocument->valueWithExchangeCoverAmount->value;
            $nomeImportador = 'Nome Importador Exemplo'; // Substitua com a extração correta
            $paisImportador = 'BR'; // Substitua com a extração correta
            $nrNotaFiscal = '123456'; // Substitua com a extração correta
            $incoterm = 'FOB'; // Substitua com a extração correta
            $destinoFinal = 'Destino Exemplo'; // Substitua com a extração correta
            $comissaoAgente = '10'; // Substitua com a extração correta

            // Adicionar ao array de dados
            $response['data'][] = [
                'xmlPath' => $targetFilePath, // Caminho para o arquivo XML
                'nrProcesso' => $nrProcesso,
                'nrAdicao' => $nrAdicao,
                'chaveAcesso' => $chaveAcesso,
                'nomeImportador' => $nomeImportador,
                'paisImportador' => $paisImportador,
                'nrNotaFiscal' => $nrNotaFiscal,
                'incoterm' => $incoterm,
                'destinoFinal' => $destinoFinal,
                'comissaoAgente' => $comissaoAgente
            ];
        } catch(Exception $e){
            $response['message'] = 'Erro ao extrair dados do XML: ' . $fileName;
            echo json_encode($response);
            exit;
        }
    } else {
        $response['message'] = 'Erro ao mover o arquivo: ' . $fileName;
        echo json_encode($response);
        exit;
    }
}

// Se tudo estiver certo
$response['success'] = true;
echo json_encode($response);
?>
