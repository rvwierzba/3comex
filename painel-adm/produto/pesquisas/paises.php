<?php

  require_once(dirname(__DIR__) . "../../../conexao.php");
    
  $query = "%" . $_POST['query'] . "%";

  $stmt = $pdo->prepare("SELECT SIGLA_ISO2, nome FROM paises WHERE nome LIKE ? LIMIT 10");
  $stmt->execute([$query]);

  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo '<div onclick="selecionarItem(\'pais_origem\', \'' . $row['SIGLA_ISO2'] . '\')">' . $row['nome'] . '</div>';
  }

?>