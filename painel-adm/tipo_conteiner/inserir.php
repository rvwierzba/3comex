<?php

require_once("../../conexao.php");
require_once("campos.php");

$id = $_POST['id'];

$cp1 = $_POST['codigo'];
$cp2 = $_POST['descricao']; 
$cp3 = $_POST['comprimento']; 
$cp4 = $_POST[ 'dimensoes']; 
$cp5 = $_POST['codigo_grupo_tipo_conteiner']; 
$cp6 = $_POST['data_inicio'];
$cp7 = $_POST['data_fim'];
$cp8 = $_POST['interno_versao']; 

//VALIDAR CAMPO
$query = $pdo->query("SELECT * from $pagina where id = '$id'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
$id_reg = @$res[0]['id'];
if($total_reg > 0 and $id_reg != $id){
	echo 'Este registro já está cadastrado!!';
	exit();
}

if($id == ""){
	$query = $pdo->prepare("INSERT INTO $pagina set codigo = :campo1, descricao = :campo2,
   comprimento = :campo3, dimensoes = :campo4, codigo_grupo_tipo_conteiner = :campo5, data_inicio = :campo6,
   data_fim = :campo7, interno_versao = :campo8");
}else{
	$query = $pdo->prepare("UPDATE $pagina set codigo = :campo1, descricao = :campo2,
  comprimento = :campo3, dimensoes = :campo4, codigo_grupo_tipo_conteiner = :campo5, data_inicio = :campo6,
  data_fim = :campo7, interno_versao = :campo8 WHERE id = '$id'");
}

$query->bindValue(":campo1", "$cp1");
$query->bindValue(":campo2", "$cp2");
$query->bindValue(":campo3", "$cp3");
$query->bindValue(":campo4", "$cp4");
$query->bindValue(":campo5", "$cp5");
$query->bindValue(":campo6", "$cp6");
$query->bindValue(":campo7", "$cp7");
$query->bindValue(":campo8", "$cp8");
$query->execute();

echo 'Salvo com Sucesso';

?>