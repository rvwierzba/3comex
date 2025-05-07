<?php 
	require_once("../../conexao.php");
	require_once("campos.php");

  $id = @$_POST['id'];
  $codigo = $_POST['codigo'];
  $descricao = $_POST['descricao'];
	
	
	//VALIDAR CAMPO
	$query = $pdo->query("SELECT * from $pagina where $campo1 = '$codigo'");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_reg = @count($res);
	$id_reg = @$res[0]['id'];
	if($total_reg > 0 and $id_reg != $id){
		echo 'Este modelo LCPO já está cadastrado!!';
		exit();
	}

	if($id == ""){
		$query = $pdo->prepare("INSERT INTO $pagina SET codigo = :codigo, descricao = :descricao");
	}else{
		$query = $pdo->prepare("UPDATE $pagina SET codigo = :codigo, descricao = :descricao WHERE id = '$id'");
	}

	$query->bindValue(":codigo", "$codigo");
  $query->bindValue(":descricao", "$descricao");
	$query->execute();
	
	echo 'Salvo com Sucesso';

?>