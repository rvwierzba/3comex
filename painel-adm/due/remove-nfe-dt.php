<?php
  session_start();

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['chaveNFe'])) {
      $chaveNFe = $_POST['chaveNFe'];

      if (isset($_SESSION['nfe_list'])) {
          $_SESSION['nfe_list'] = array_filter($_SESSION['nfe_list'], function($nfe) use ($chaveNFe) {
              return $nfe['CodBarra'] !== $chaveNFe;
          });
      }

      echo json_encode(['status' => 'success']);
  } else {
      echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
  }
?>
