<?php

//Definições de URL
$baseURL = "https://portalunico.siscomex.gov.br"; // Ambiente de produção
$hsEndpoint = "portal/api/autenticar"; 
$hsURL = $baseURL . '/' . $hsEndpoint;

// Caminho do certificado e senha
$certPath = dirname(__DIR__) . "/siscomex/certificado/eliton2024.pem";
$certPass = 'eliton2024';

// Inicializa cURL
$ch = curl_init();

// Configurações de cURL para SSL
curl_setopt($ch, CURLOPT_URL, $hsURL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_SSLCERT, $certPath);
curl_setopt($ch, CURLOPT_SSLCERTPASSWD, $certPass);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Role-Type: IMPEXP" // Definição Role-Type 
]);

// Habilita a saída de depuração do cURL (opcional)
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_STDERR, fopen('php://stderr', 'w'));

// Executa a requisição
$response = curl_exec($ch);
$info = curl_getinfo($ch);

if (curl_errno($ch)) {
    // Caso ocorra algum erro com cURL
    echo 'Erro cURL: ' . curl_error($ch);
} else {
    // Verifica se a requisição foi bem sucedida
    if ($info['http_code'] == 200) {
        echo "Conexão estabelecida com sucesso. HTTP Status Code: 200\n";
        
        // Extrai os headers necessários da resposta
        $headers = [];
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header_lines = explode("\n", substr($response, 0, $header_size));
        foreach ($header_lines as $line) {
            if (strpos($line, ':') !== false) {
                list($key, $value) = explode(':', $line, 2);
                $headers[strtolower(trim($key))] = trim($value);
            }
        }

        // Tokens extraídos
        $setToken = $headers['set-token'] ?? null;
        $csrfToken = $headers['x-csrf-token'] ?? null;
        $csrfExpiration = $headers['x-csrf-expiration'] ?? null;

        // Verificação dos tokens
        if ($setToken && $csrfToken && $csrfExpiration) {
           /* echo "Tokens recebidos com sucesso:\n";
            echo "Set-Token: $setToken\n";
            echo "X-CSRF-Token: $csrfToken\n";
            echo "X-CSRF-Expiration: $csrfExpiration\n";*/
        } else {
            echo "Falha ao extrair os tokens da resposta.\n";
        }
    } else {
        // Em caso de falha na autenticação
        echo "Falha na autenticação, HTTP Status: " . $info['http_code'] . "\n";
        echo "Resposta completa: \n$response";
    }
}

curl_close($ch); 






     
 





        
       



?>

  

     





