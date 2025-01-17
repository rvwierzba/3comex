<?php
// painel-adm/due/enviar-due.php

header('Content-Type: application/json');

// Função para verificar se todos os campos necessários foram recebidos
function check_required_fields($fields, $data) {
    foreach ($fields as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            return $field;
        }
    }
    return false;
}

// Definir os campos obrigatórios
$required_fields = [
    'cliente',
    'nomeCliente',
    'declarantId',
    'undRFBDesp',
    'recAdu',
    'categoriaDoc',
    'hsClassification',
    'drawbackRecipientId',
    'drawbackHsClassification',
    'itemID',
    'quantityQuantity',
    'unitCode',
    'valueWithExchangeCoverAmount',
    'currentCode',
    'sequenceNumeric',
    'paisDeclarationOffice',
    'paisExitOffice',
    'itensNF'
];

// Verificar se todos os campos foram recebidos
$missing_field = check_required_fields($required_fields, $_POST);
if ($missing_field) {
    echo json_encode(['success' => false, 'message' => "Campo obrigatório faltando: $missing_field"]);
    exit;
}

// Receber dados do POST
$cliente = $_POST['cliente'];
$nomeCliente = $_POST['nomeCliente'];
$declarantId = $_POST['declarantId'];
$undRFBDesp = $_POST['undRFBDesp'];
$recAdu = $_POST['recAdu'];
$categoriaDoc = $_POST['categoriaDoc'];
$hsClassification = $_POST['hsClassification'];
$drawbackRecipientId = $_POST['drawbackRecipientId'];
$drawbackHsClassification = $_POST['drawbackHsClassification'];
$itemID = $_POST['itemID'];
$quantityQuantity = $_POST['quantityQuantity'];
$unitCode = $_POST['unitCode'];
$valueWithExchangeCoverAmount = $_POST['valueWithExchangeCoverAmount'];
$currentCode = $_POST['currentCode'];
$sequenceNumeric = $_POST['sequenceNumeric'];
$paisDeclarationOffice = $_POST['paisDeclarationOffice'];
$paisExitOffice = $_POST['paisExitOffice'];
$itensNF = json_decode($_POST['itensNF'], true);

// Validação dos itens
if (!is_array($itensNF) || count($itensNF) == 0) {
    echo json_encode(['success' => false, 'message' => 'Nenhuma Nota Fiscal foi adicionada.']);
    exit;
}

// Iniciar a construção do XML
$dom = new DOMDocument('1.0', 'UTF-8');
$dom->formatOutput = true;

// Nó principal <Declaration>
$declaration = $dom->createElementNS('urn:wco:datamodel:WCO:GoodsDeclaration:1', 'Declaration');
$dom->appendChild($declaration);

// <declarationDrawbackIsencao>
$declarationDrawback = $dom->createElement('declarationDrawbackIsencao');
$goodsShipmentDrawback = $dom->createElement('GoodsShipment');
$governmentAgencyDrawback = $dom->createElement('GovernmentAgencyGoodsItem');

// <AdditionalDocument> para o item principal
$additionalDocDrawback = $dom->createElement('AdditionalDocument');
$categoryCodeDrawback = $dom->createElement('CategoryCode', htmlspecialchars($categoriaDoc));
$additionalDocDrawback->appendChild($categoryCodeDrawback);

$drawbackHsClassificationElem = $dom->createElement('drawbackHsClassification');
$valueHsDrawback = $dom->createElement('value', htmlspecialchars($drawbackHsClassification));
$drawbackHsClassificationElem->appendChild($valueHsDrawback);
$additionalDocDrawback->appendChild($drawbackHsClassificationElem);

$drawbackRecipientIdElem = $dom->createElement('drawbackRecipientId');
$valueDrawbackRecipient = $dom->createElement('value', htmlspecialchars($drawbackRecipientId));
$drawbackRecipientIdElem->appendChild($valueDrawbackRecipient);
$additionalDocDrawback->appendChild($drawbackRecipientIdElem);

$idElem = $dom->createElement('id');
$idScheme = $dom->createAttribute('schemeID');
$idScheme->value = 'schemeID_string'; // Substitua 'schemeID_string' pelo valor real
$idElem->appendChild($idScheme);
$valueId = $dom->createElement('value', htmlspecialchars($cliente)); // Exemplo: usando 'cliente' como valor
$idElem->appendChild($valueId);
$additionalDocDrawback->appendChild($idElem);

$itemIDElem = $dom->createElement('itemID');
$itemIDScheme = $dom->createAttribute('schemeID');
$itemIDScheme->value = 'schemeID_string'; // Substitua 'schemeID_string' pelo valor real
$itemIDElem->appendChild($itemIDScheme);
$valueItemID = $dom->createElement('value', htmlspecialchars($itemID));
$itemIDElem->appendChild($valueItemID);
$additionalDocDrawback->appendChild($itemIDElem);

$quantityQuantityElem = $dom->createElement('quantityQuantity');
$unitCodeAttr = $dom->createAttribute('unitCode');
$unitCodeAttr->value = htmlspecialchars($unitCode);
$quantityQuantityElem->appendChild($unitCodeAttr);
$valueQuantity = $dom->createElement('value', htmlspecialchars($quantityQuantity));
$quantityQuantityElem->appendChild($valueQuantity);
$additionalDocDrawback->appendChild($quantityQuantityElem);

$valueWithExchangeCoverAmountElem = $dom->createElement('valueWithExchangeCoverAmount');
$valueWithExchange = $dom->createElement('value', htmlspecialchars($valueWithExchangeCoverAmount));
$valueWithExchangeCoverAmountElem->appendChild($valueWithExchange);
$additionalDocDrawback->appendChild($valueWithExchangeCoverAmountElem);

// Anexando ao nó <GovernmentAgencyGoodsItem>
$governmentAgencyDrawback->appendChild($additionalDocDrawback);

// <GovernmentProcedure>
$governmentProcedureDrawback = $dom->createElement('GovernmentProcedure');
$currentCodeDrawback = $dom->createElement('currentCode', htmlspecialchars($currentCode));
$schemeIDCurrentCodeDrawback = $dom->createAttribute('schemeID');
$schemeIDCurrentCodeDrawback->value = 'schemeID_string'; // Substitua pelo valor real
$currentCodeDrawback->appendChild($schemeIDCurrentCodeDrawback);
$governmentProcedureDrawback->appendChild($currentCodeDrawback);
$governmentAgencyDrawback->appendChild($governmentProcedureDrawback);

// <SequenceNumeric>
$sequenceNumericElem = $dom->createElement('SequenceNumeric', htmlspecialchars($sequenceNumeric));
$governmentAgencyDrawback->appendChild($sequenceNumericElem);

// Finalizando <GoodsShipment> e <declarationDrawbackIsencao>
$goodsShipmentDrawback->appendChild($governmentAgencyDrawback);
$declarationDrawback->appendChild($goodsShipmentDrawback);
$declaration->appendChild($declarationDrawback);

// <declarationNFe>
$declarationNFe = $dom->createElement('declarationNFe');

// <AdditionalDocument> para NFe
$additionalDocNFe = $dom->createElement('AdditionalDocument');
$categoryCodeNFe = $dom->createElement('CategoryCode', htmlspecialchars($categoriaDoc));
$additionalDocNFe->appendChild($categoryCodeNFe);

$drawbackHsClassificationNFe = $dom->createElement('drawbackHsClassification');
$valueHsNFe = $dom->createElement('value', htmlspecialchars($hsClassification));
$drawbackHsClassificationNFe->appendChild($valueHsNFe);
$additionalDocNFe->appendChild($drawbackHsClassificationNFe);

$drawbackRecipientIdNFe = $dom->createElement('drawbackRecipientId');
$valueDrawbackRecipientNFe = $dom->createElement('value', htmlspecialchars($drawbackRecipientId));
$drawbackRecipientIdNFe->appendChild($valueDrawbackRecipientNFe);
$additionalDocNFe->appendChild($drawbackRecipientIdNFe);

$idNFe = $dom->createElement('id');
$idNFeScheme = $dom->createAttribute('schemeID');
$idNFeScheme->value = 'schemeID_string'; // Substitua pelo valor real
$idNFe->appendChild($idNFeScheme);
$valueIdNFe = $dom->createElement('value', htmlspecialchars($cliente)); // Exemplo: usando 'cliente' como valor
$idNFe->appendChild($valueIdNFe);
$additionalDocNFe->appendChild($idNFe);

$itemIDNFe = $dom->createElement('itemID');
$itemIDNFeScheme = $dom->createAttribute('schemeID');
$itemIDNFeScheme->value = 'schemeID_string'; // Substitua pelo valor real
$itemIDNFe->appendChild($itemIDNFeScheme);
$valueItemIDNFe = $dom->createElement('value', htmlspecialchars($itemID));
$itemIDNFe->appendChild($valueItemIDNFe);
$additionalDocNFe->appendChild($itemIDNFe);

$quantityQuantityNFe = $dom->createElement('quantityQuantity');
$unitCodeNFeAttr = $dom->createAttribute('unitCode');
$unitCodeNFeAttr->value = htmlspecialchars($unitCode);
$quantityQuantityNFe->appendChild($unitCodeNFeAttr);
$valueQuantityNFe = $dom->createElement('value', htmlspecialchars($quantityQuantity));
$quantityQuantityNFe->appendChild($valueQuantityNFe);
$additionalDocNFe->appendChild($quantityQuantityNFe);

$valueWithExchangeCoverAmountNFe = $dom->createElement('valueWithExchangeCoverAmount');
$valueWithExchangeNFe = $dom->createElement('value', htmlspecialchars($valueWithExchangeCoverAmount));
$valueWithExchangeCoverAmountNFe->appendChild($valueWithExchangeNFe);
$additionalDocNFe->appendChild($valueWithExchangeCoverAmountNFe);

// Anexando ao nó <AdditionalDocument>
$declarationNFe->appendChild($additionalDocNFe);

// <AdditionalInformation>
$additionalInformation = $dom->createElement('AdditionalInformation');
$statementCode = $dom->createElement('StatementCode', 'string'); // Substitua pelo valor real
$additionalInformation->appendChild($statementCode);
$statementTypeCode = $dom->createElement('StatementTypeCode', 'ACT');
$additionalInformation->appendChild($statementTypeCode);
$declarationNFe->appendChild($additionalInformation);

// <currencyExchange>
$currencyExchange = $dom->createElement('currencyExchange');
$currencyTypeCode = $dom->createElement('CurrencyTypeCode', 'AED'); // Substitua pelo valor real se necessário
$currencyExchange->appendChild($currencyTypeCode);
$declarationNFe->appendChild($currencyExchange);

// <declarant>
$declarant = $dom->createElement('declarant');

// <contact>
$contact = $dom->createElement('contact');
$communication = $dom->createElement('Communication');
$idCommunication = $dom->createElement('id');
$valueIdCommunication = $dom->createElement('value', 'string'); // Substitua pelo valor real
$idCommunication->appendChild($valueIdCommunication);
$communication->appendChild($idCommunication);
$typeCodeCommunication = $dom->createElement('TypeCode', 'EM');
$communication->appendChild($typeCodeCommunication);
$contact->appendChild($communication);

$name = $dom->createElement('name');
$valueName = $dom->createElement('value', htmlspecialchars($nomeCliente));
$name->appendChild($valueName);
$contact->appendChild($name);

$declarant->appendChild($contact);

$idDeclarant = $dom->createElement('id');
$idDeclarantScheme = $dom->createAttribute('schemeID');
$idDeclarantScheme->value = 'schemeID_string'; // Substitua pelo valor real
$idDeclarant->appendChild($idDeclarantScheme);
$valueIdDeclarant = $dom->createElement('value', htmlspecialchars($declarantId));
$idDeclarant->appendChild($valueIdDeclarant);
$declarant->appendChild($idDeclarant);

$declarationNFe->appendChild($declarant);

// <declarationOffice>
$declarationOffice = $dom->createElement('declarationOffice');

// <id>
$idDeclarationOffice = $dom->createElement('id');
$idDeclarationOfficeListID = $dom->createAttribute('listID');
$idDeclarationOfficeListID->value = 'listID_string'; // Substitua pelo valor real
$idDeclarationOffice->appendChild($idDeclarationOfficeListID);
$valueIdDeclarationOffice = $dom->createElement('value', 'string'); // Substitua pelo valor real
$idDeclarationOffice->appendChild($valueIdDeclarationOffice);
$declarationOffice->appendChild($idDeclarationOffice);

// <warehouse>
$warehouseDeclarationOffice = $dom->createElement('warehouse');

// <address>
$addressDeclarationOffice = $dom->createElement('address');
$countryCodeDeclarationOffice = $dom->createElement('CountryCode', htmlspecialchars($paisDeclarationOffice));
$addressDeclarationOffice->appendChild($countryCodeDeclarationOffice);
$lineDeclarationOffice = $dom->createElement('line');
$languageIDAttrDeclarationOffice = $dom->createAttribute('languageID');
$languageIDAttrDeclarationOffice->value = 'string'; // Substitua pelo valor real
$lineDeclarationOffice->appendChild($languageIDAttrDeclarationOffice);
$valueLineDeclarationOffice = $dom->createElement('value', 'string'); // Substitua pelo valor real
$lineDeclarationOffice->appendChild($valueLineDeclarationOffice);
$addressDeclarationOffice->appendChild($lineDeclarationOffice);
$warehouseDeclarationOffice->appendChild($addressDeclarationOffice);

// <id>
$idWarehouseDeclarationOffice = $dom->createElement('id');
$idWarehouseDeclarationOfficeScheme = $dom->createAttribute('schemeID');
$idWarehouseDeclarationOfficeScheme->value = 'schemeID_string'; // Substitua pelo valor real
$idWarehouseDeclarationOffice->appendChild($idWarehouseDeclarationOfficeScheme);
$valueIdWarehouseDeclarationOffice = $dom->createElement('value', 'string'); // Substitua pelo valor real
$idWarehouseDeclarationOffice->appendChild($valueIdWarehouseDeclarationOffice);
$warehouseDeclarationOffice->appendChild($idWarehouseDeclarationOffice);

// <TypeCode>
$typeCodeWarehouseDeclarationOffice = $dom->createElement('TypeCode', 'string'); // Substitua pelo valor real
$warehouseDeclarationOffice->appendChild($typeCodeWarehouseDeclarationOffice);

$declarationOffice->appendChild($warehouseDeclarationOffice);
$declarationNFe->appendChild($declarationOffice);

// <DutyTaxFee>
$dutyTaxFee = $dom->createElement('DutyTaxFee');

// <payment>
$payment = $dom->createElement('payment');

// <dueDateTime>
$dueDateTime = $dom->createElement('dueDateTime');
$languageIDDueDateTime = $dom->createAttribute('languageID');
$languageIDDueDateTime->value = 'string'; // Substitua pelo valor real
$dueDateTime->appendChild($languageIDDueDateTime);
$valueDueDateTime = $dom->createElement('value', 'string'); // Substitua pelo valor real
$dueDateTime->appendChild($valueDueDateTime);
$payment->appendChild($dueDateTime);

// <interest>
$interest = $dom->createElement('interest');
$valueInterest = $dom->createElement('value', '0'); // Substitua pelo valor real se necessário
$interest->appendChild($valueInterest);
$payment->appendChild($interest);

// <paymentAmount>
$paymentAmount = $dom->createElement('paymentAmount');
$valuePaymentAmount = $dom->createElement('value', '0'); // Substitua pelo valor real se necessário
$paymentAmount->appendChild($valuePaymentAmount);
$payment->appendChild($paymentAmount);

// <penalty>
$penalty = $dom->createElement('penalty');
$valuePenalty = $dom->createElement('value', '0'); // Substitua pelo valor real se necessário
$penalty->appendChild($valuePenalty);
$payment->appendChild($penalty);

$dutyTaxFee->appendChild($payment);
$declarationNFe->appendChild($dutyTaxFee);

// <exitOffice>
$exitOffice = $dom->createElement('exitOffice');

// <id>
$idExitOffice = $dom->createElement('id');
$idExitOfficeScheme = $dom->createAttribute('schemeID');
$idExitOfficeScheme->value = 'schemeID_string'; // Substitua pelo valor real
$idExitOffice->appendChild($idExitOfficeScheme);
$valueIdExitOffice = $dom->createElement('value', 'string'); // Substitua pelo valor real
$idExitOffice->appendChild($valueIdExitOffice);
$exitOffice->appendChild($idExitOffice);

// <warehouse>
$warehouseExitOffice = $dom->createElement('warehouse');

// <address>
$addressExitOffice = $dom->createElement('address');
$countryCodeExitOffice = $dom->createElement('CountryCode', htmlspecialchars($paisExitOffice));
$addressExitOffice->appendChild($countryCodeExitOffice);
$lineExitOffice = $dom->createElement('line');
$languageIDAttrExitOffice = $dom->createAttribute('languageID');
$languageIDAttrExitOffice->value = 'string'; // Substitua pelo valor real
$lineExitOffice->appendChild($languageIDAttrExitOffice);
$valueLineExitOffice = $dom->createElement('value', 'string'); // Substitua pelo valor real
$lineExitOffice->appendChild($valueLineExitOffice);
$addressExitOffice->appendChild($lineExitOffice);
$warehouseExitOffice->appendChild($addressExitOffice);

// <id>
$idWarehouseExitOffice = $dom->createElement('id');
$idWarehouseExitOfficeScheme = $dom->createAttribute('schemeID');
$idWarehouseExitOfficeScheme->value = 'schemeID_string'; // Substitua pelo valor real
$idWarehouseExitOffice->appendChild($idWarehouseExitOfficeScheme);
$valueIdWarehouseExitOffice = $dom->createElement('value', 'string'); // Substitua pelo valor real
$idWarehouseExitOffice->appendChild($valueIdWarehouseExitOffice);
$warehouseExitOffice->appendChild($idWarehouseExitOffice);

// <TypeCode>
$typeCodeWarehouseExitOffice = $dom->createElement('TypeCode', 'string'); // Substitua pelo valor real
$warehouseExitOffice->appendChild($typeCodeWarehouseExitOffice);

$exitOffice->appendChild($warehouseExitOffice);
$declarationNFe->appendChild($exitOffice);

// <GoodsShipment> para declarationNFe
$goodsShipmentNFe = $dom->createElement('GoodsShipment');
$governmentAgencyNFe = $dom->createElement('GovernmentAgencyGoodsItem');

// <AdditionalDocument> para cada nota fiscal
foreach ($itensNF as $item) {
    $additionalDocNFeItem = $dom->createElement('AdditionalDocument');
    $categoryCodeNFeItem = $dom->createElement('CategoryCode', htmlspecialchars($categoriaDoc));
    $additionalDocNFeItem->appendChild($categoryCodeNFeItem);

    $drawbackHsClassificationNFeItem = $dom->createElement('drawbackHsClassification');
    $valueHsNFeItem = $dom->createElement('value', htmlspecialchars($hsClassification));
    $drawbackHsClassificationNFeItem->appendChild($valueHsNFeItem);
    $additionalDocNFeItem->appendChild($drawbackHsClassificationNFeItem);

    $drawbackRecipientIdNFeItem = $dom->createElement('drawbackRecipientId');
    $valueDrawbackRecipientNFeItem = $dom->createElement('value', htmlspecialchars($drawbackRecipientId));
    $drawbackRecipientIdNFeItem->appendChild($valueDrawbackRecipientNFeItem);
    $additionalDocNFeItem->appendChild($drawbackRecipientIdNFeItem);

    $idNFeItem = $dom->createElement('id');
    $idNFeItemScheme = $dom->createAttribute('schemeID');
    $idNFeItemScheme->value = 'schemeID_string'; // Substitua pelo valor real
    $idNFeItem->appendChild($idNFeItemScheme);
    $valueIdNFeItem = $dom->createElement('value', htmlspecialchars($cliente)); // Exemplo: usando 'cliente' como valor
    $idNFeItem->appendChild($valueIdNFeItem);
    $additionalDocNFeItem->appendChild($idNFeItem);

    $itemIDNFeItem = $dom->createElement('itemID');
    $itemIDNFeItemScheme = $dom->createAttribute('schemeID');
    $itemIDNFeItemScheme->value = 'schemeID_string'; // Substitua pelo valor real
    $itemIDNFeItem->appendChild($itemIDNFeItemScheme);
    $valueItemIDNFeItem = $dom->createElement('value', htmlspecialchars($item['nrAdicao'])); // Ajuste conforme necessário
    $itemIDNFeItem->appendChild($valueItemIDNFeItem);
    $additionalDocNFeItem->appendChild($itemIDNFeItem);

    $quantityQuantityNFeItem = $dom->createElement('quantityQuantity');
    $unitCodeNFeItemAttr = $dom->createAttribute('unitCode');
    $unitCodeNFeItemAttr->value = htmlspecialchars($unitCode);
    $quantityQuantityNFeItem->appendChild($unitCodeNFeItemAttr);
    $valueQuantityNFeItem = $dom->createElement('value', htmlspecialchars($item['quantityQuantity']));
    $quantityQuantityNFeItem->appendChild($valueQuantityNFeItem);
    $additionalDocNFeItem->appendChild($quantityQuantityNFeItem);

    $valueWithExchangeCoverAmountNFeItem = $dom->createElement('valueWithExchangeCoverAmount');
    $valueWithExchangeNFeItem = $dom->createElement('value', htmlspecialchars($valueWithExchangeCoverAmount));
    $valueWithExchangeCoverAmountNFeItem->appendChild($valueWithExchangeNFeItem);
    $additionalDocNFeItem->appendChild($valueWithExchangeCoverAmountNFeItem);

    $governmentAgencyNFe->appendChild($additionalDocNFeItem);
}

$goodsShipmentNFe->appendChild($governmentAgencyNFe);
$declarationNFe->appendChild($goodsShipmentNFe);

// Adicionando o nó completo ao <Declaration>
$declaration->appendChild($declarationNFe);

// Gerar XML
$xmlGerado = $dom->saveXML();

// Agora enviar o XML gerado para a API
include_once(dirname(__DIR__) . '/siscomex/handshake.php'); // Incluir handshake da API Siscomex

// Fazer a requisição cURL com o XML gerado
$ch = curl_init($dueURL); // $dueURL definido no handshake.php
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlGerado);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/xml",
    "Authorization: Bearer $setToken", // $setToken definido no handshake.php
    "X-CSRF-Token: $csrfToken" // $csrfToken definido no handshake.php
]);

$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Verificação da resposta
if ($httpcode == 200) {
    echo json_encode(['success' => true, 'message' => 'DU-E enviada com sucesso']);
} else {
    // Você pode optar por retornar a resposta completa para depuração
    echo json_encode(['success' => false, 'message' => 'Erro ao enviar DU-E: ' . $response]);
}
?>
