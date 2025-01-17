<?php 
	require_once("../../conexao.php");
	require_once("campos.php");

	$documento = $_POST['documento'];
	$id = @$_POST['id'];
	
	//VALIDAR CAMPO
	$query = $pdo->query("SELECT * from $pagina where documento = '$documento'");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_reg = @count($res);
	$id_reg = @$res[0]['id'];
	if($total_reg > 0 and $id_reg != $id){
		echo 'Esta classificação já está cadastrada!!';
		exit();
	}

	if($id == ""){
		$query = $pdo->prepare("INSERT INTO $pagina set documento = :documento");
	}else{
		$query = $pdo->prepare("UPDATE $pagina set documento = :documento WHERE id = '$id'");
	}

	$query->bindValue(":documento", "$documento");
	$query->execute();
	
	echo 'Salvo com Sucesso';

?>