<?php

require_once("../../conexao.php");
require_once("campos.php");

$id = $_POST['id'];

$cp1 = $_POST['codigo'];
$cp2 = $_POST['nome']; 
$cp3 = $_POST['data_inicio'];
$cp4 = $_POST['data_fim'];
$cp7 = $_POST['sigla_regiao_fiscal'];

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
	$query = $pdo->prepare("INSERT INTO $pagina set codigo = :campo1, nome = :campo2,
  data_inicio = :campo3, data_fim = :campo4, sigla_regiao_fiscal = :campo7");
}else{
	$query = $pdo->prepare("UPDATE $pagina set codigo = :campo1, nome = :campo2,
  data_inicio = :campo3,   data_fim = :campo4, sigla_regiao_fiscal = :campo7 WHERE id = '$id'");
}

$query->bindValue(":campo1", "$cp1");
$query->bindValue(":campo2", "$cp2");
$query->bindValue(":campo3", "$cp3");
$query->bindValue(":campo4", "$cp4");
$query->bindValue(":campo4", "$cp7");

$query->execute();

echo 'Salvo com Sucesso';

?>