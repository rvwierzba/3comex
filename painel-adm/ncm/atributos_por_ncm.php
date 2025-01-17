<?php

  
    $ncmWd = $_GET['ncmwd'];
    $ncm = str_replace('.', '', $ncmWd);
          
  require_once(dirname(__DIR__)  . '../../siscomex/handshake.php');

  // Montando a URL
  $endpointAttN = 'ext/atributo-ncm/' . $ncm;
  $attnURL = $baseURL . '/cadatributos/api/' . $endpointAttN;
  

  // Tokens do HANDSHAKE: $setToken & $csrfToken no handshake.php
  $requestHeaders= array(
    "Authorization: $setToken", 
    "X-Csrf-Token: $csrfToken "
  );


  // Inicializar cURL
  $ch = curl_init();

  // Configurar a requisição
  curl_setopt($ch, CURLOPT_URL, $attnURL);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    
  // Configurar outras opções, se necessário
  // Por exemplo:
   //curl_setopt($ch, CURLOPT_HTTPGET, true);
  // curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

  // Executar solicitação
  $response = curl_exec($ch);
  // Decodifica o JSON para um array associativo
  $responseArray = json_decode($response, true); // true converte para array associativo


  // Verificar erros
  if ($response === false) {
      echo "Erro na requisição cURL: " . curl_error($ch);
  } else {
    // Processar a resposta

    // Processar 'listaNcm'
  if (isset($responseArray['listaNcm']) && is_array($responseArray['listaNcm'])) {
    foreach ($responseArray['listaNcm'] as $ncmItem) {
        echo "<h2>Código NCM: " . $ncmItem['codigoNcm'] . "</h2> <br>";
        echo "<h3>Atributos:</h3><br>";
        foreach ($ncmItem['listaAtributos'] as $atributo) {
            echo "<p style=font-size:20px>" . $atributo['codigo'] . ", Modalidade: " . $atributo['modalidade'] . ", Obrigatório: " . ($atributo['obrigatorio'] ? 'Sim' : 'Não') . "</p> ";
        }
        
    }
  }

  // Processar 'detalhesAtributos'
  if (isset($responseArray['detalhesAtributos']) && is_array($responseArray['detalhesAtributos'])) {
    echo "<h3 style='margin-top:3%'>Detalhes dos Atributos:</h3><br>";
    foreach ($responseArray['detalhesAtributos'] as $detalhe) {
        echo "<p style=font-size:20px>" . $detalhe['codigo'] . ", Nome: " . $detalhe['nome'] . "</p>";
        // Adicione mais campos conforme necessário
    }
  }

    
    
    

  }

  // Fechar conexão cURL
  curl_close($ch);

  

?>

