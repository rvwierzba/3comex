<?php

require_once("../../conexao.php");
require_once("campos.php");

$codigo = @$_POST['codNum-excluir'];

$sql = "DELETE from $pagina where CODIGO_NUMERICO = :codigo";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':codigo', $codigo);

if ($stmt->execute()) {
    echo 'Excluído com Sucesso';
} else {
    echo 'Erro ao excluir registro!';
    echo PDO::ERRMODE_EXCEPTION;
}

?>