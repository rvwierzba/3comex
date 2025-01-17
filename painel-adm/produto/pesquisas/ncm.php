<?php
  require_once(dirname(__DIR__) . "../../../conexao.php");


  $query = "%" . $_POST['query'] . "%";

  $stmt = $pdo->prepare("SELECT codigo, descricao FROM ncm WHERE codigo LIKE ? OR descricao LIKE ? LIMIT 10");
  $stmt->execute([$query, $query]);

  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $codigo = str_replace('.', '', $row['codigo']);
    echo '<div onclick="selecionarItem(\'ncm\', \'' . $codigo . '\')">' . $row['codigo'] . ' - ' . $row['descricao'] . '</div>';
  }
  
?>
