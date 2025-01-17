<?php
  require_once(dirname(__DIR__) . "../../../conexao.php");
  
  $query = "%" . $_POST['query'] . "%";

  $stmt = $pdo->prepare("SELECT codigo, nome FROM op_estrangeiro WHERE codigo LIKE ? OR nome LIKE ? LIMIT 10");
  $stmt->execute([$query, $query]);

  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo '<div onclick="selecionarItem(\'operador_estrangeiro_codigo\', \'' . $row['codigo'] . '\')">' . $row['codigo'] . ' - ' . $row['nome'] . '</div>';
  }
  
?>
