<?php
if(isset($_POST['cnpj'])) {
    $cnpj = $_POST['cnpj'];
    $url = "https://publica.cnpj.ws/cnpj/$cnpj";

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    // for debug only!
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $resp = curl_exec($curl);
    curl_close($curl);

    // Retornar a resposta diretamente como JSON
    header('Content-Type: application/json');
    echo $resp;
} else {
    header('Content-Type: application/json');
    echo json_encode(['message' => 'CNPJ não fornecido.']);
}
?>