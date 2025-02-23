<?php

// Conexão com o banco de dados
require_once("C:\\xampp\htdocs\\3comex\\conexao.php");

// Verificação de requisição AJAX
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
  die("Esta página só pode ser acessada via AJAX.");
}

header('Content-Type: application/json');


if (isset($_GET['term'])) {
  $term = "%" . $_GET['term'] . "%";
  try {
      $stmt = $pdo->prepare("SELECT id, nome FROM locais_despacho WHERE nome LIKE :term;");
      $stmt->bindValue(':term', $term, PDO::PARAM_STR);
      $stmt->execute();
      $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
      echo json_encode($results);
  } catch (PDOException $e) {
      echo json_encode(["error" => $e->getMessage()]);
  }
  exit;
}
?>