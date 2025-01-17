<?php 
	@session_start();
	require_once("../../conexao.php");
	require_once("campos.php");

	$classificacao = $_POST['classificacao'];
	$id = @$_POST['id'];
	
	//VALIDAR CAMPO
	$query = $pdo->query("SELECT * from classif where classificacao = '$classificacao'");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_reg = @count($res);
	$id_reg = @$res[0]['id'];
	if($total_reg > 0 and $id_reg != $id){
		echo 'Esta classificação já está cadastrada!!';
		exit();
	}

	if($id == ""){
		$query = $pdo->prepare("INSERT INTO classif set classificacao = :classificacao");
	}else{
		$query = $pdo->prepare("UPDATE classif set classificacao = :classificacao WHERE id = '$id'");
	}

	$query->bindValue(":classificacao", "$classificacao");
	$query->execute();
	
	echo 'Salvo com Sucesso';

?>