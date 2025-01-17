<?php

  require_once("../../conexao.php");
  require_once("campos.php");

  $id = @$_POST['id-excluir'];

  $sql = "DELETE from $pagina where id = '$id'";
  $stmt = $pdo->prepare($sql);
  if($stmt->execute()) {
      echo 'Excluído com Sucesso';
  } else {
      echo 'Erro ao excluir registro!';
      echo PDO::ERRMODE_EXCEPTION;
  }

?>