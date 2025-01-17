<?php
require_once(dirname(__DIR__).'../../conexao.php'); // Inclua seu arquivo de configuração do banco de dados

if (isset($_POST['query'])) {
  $query = $_POST['query'];
  
  // Verificar se a consulta é um número
  if (is_numeric($query)) {
      $stmt = $pdo->prepare("SELECT nome, codigo FROM moeda WHERE codigo LIKE ? ORDER BY nome");
      $stmt->execute(['%' . $query . '%']);
  } else {
      $stmt = $pdo->prepare("SELECT nome, codigo FROM moeda WHERE nome LIKE ? ORDER BY nome");
      $stmt->execute(['%' . $query . '%']);
  }
  
  $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
  if (count($results) > 0) {
      foreach ($results as $row) {
          echo '<a href="#" class="list-group-item list-group-item-action">' . $row['codigo'] . ' - ' . $row['nome'] . '</a>';
      }
  } else {
      echo '<p class="list-group-item">Nenhum resultado encontrado</p>';
  }
}
?>