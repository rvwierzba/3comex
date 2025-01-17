<?php 
require_once("../../conexao.php");
require_once("campos.php");
$cod = @$_POST['id-excluir'];

$pdo->query("DELETE from $pagina where codigo = '$cod'");
echo 'Excluído com Sucesso';

 ?>