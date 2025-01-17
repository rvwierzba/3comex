<?php
require_once(dirname(__DIR__) . "/conexao.php");
require_once(dirname(__DIR__) . "/siscomex/handshake.php");

// Função para enviar dados para a API do Siscomex
function enviarDadosParaSiscomex($endpoint, $data, $setToken, $csrfToken) {
    $baseURL = "https://portalunico.siscomex.gov.br";
    $url = $baseURL . '/' . $endpoint;
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Set-Token: $setToken",
        "X-CSRF-Token: $csrfToken"
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);

    curl_close($ch);

    return [
        'httpCode' => $httpCode,
        'response' => json_decode($response, true),
        'error' => $error
    ];
}

// Verifique se os tokens estão definidos
if (!isset($setToken) || !isset($csrfToken)) {
    echo json_encode([
        'error' => 'Tokens de autenticação não definidos.'
    ]);
    exit;
}

// Obtém dados do POST
$data = $_POST;

// Extrai os dados necessários do POST
$identificacao = json_decode($data['identificacao'], true);
$carga = json_decode($data['carga'], true);
$documentos = json_decode($data['documentos'], true);
$processos = json_decode($data['processos'], true);
$pagamentos = json_decode($data['pagamentos'], true);
$resumo = json_decode($data['resumo'], true);

// Inicializa respostas
$respostas = [];

// Enviar dados para a API do Siscomex
$respostas['identificacao'] = enviarDadosParaSiscomex('duimp/identificacao', $identificacao, $setToken, $csrfToken);
$respostas['carga'] = enviarDadosParaSiscomex('duimp/carga', $carga, $setToken, $csrfToken);
$respostas['documentos'] = enviarDadosParaSiscomex('duimp/documentos', $documentos, $setToken, $csrfToken);
$respostas['processos'] = enviarDadosParaSiscomex('duimp/processos', $processos, $setToken, $csrfToken);
$respostas['pagamentos'] = enviarDadosParaSiscomex('duimp/pagamentos', $pagamentos, $setToken, $csrfToken);
$respostas['resumo'] = enviarDadosParaSiscomex('duimp/resumo', $resumo, $setToken, $csrfToken);

// Verifica se houve algum erro e prepara a resposta final
$erro = false;
foreach ($respostas as $chave => $resposta) {
    if ($resposta['httpCode'] != 200) {
        $erro = true;
        break;
    }
}

// Retorna a resposta final para o cliente
if ($erro) {
    echo json_encode([
        'error' => 'Erro ao enviar dados para a API do Siscomex.',
        'details' => $respostas
    ]);
} else {
    echo json_encode([
        'success' => 'Dados enviados com sucesso.',
        'details' => $respostas
    ]);
}
?>
