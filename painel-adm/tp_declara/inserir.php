<?php 
	require_once("../../conexao.php");
	require_once("campos.php");

	
	$id = @$_POST['id'];

	$cp1 = $_POST['codigo'];
  $cp2 = $_POST['descricao'];
	$cp3 = $_POST['data_inicio'];
	$cp4 = $_POST['data_fim'];
	$cp5 = $_POST['interno_versao'];
	
	
	//VALIDAR CAMPO
	$query = $pdo->query("SELECT * from $pagina where id = '$id'");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_reg = @count($res);
	$id_reg = @$res[0]['id'];
	if($total_reg > 0 and $id_reg != $id){
		echo 'Registro já cadastrado!!';
		exit();
	}

	if($id == ""){
		$query = $pdo->prepare("INSERT INTO $pagina
		 set codigo = :codigo, descricao = :descricao, data_inicio = :dataInicio, 
		 data_fim = :dataFim, interno_versao = :internoVersao");
	}else{
		$query = $pdo->prepare("UPDATE $pagina SET codigo = :codigo, descricao = :descricao,
		 data_inicio = :dataInicio, data_fim = :dataFim, interno_versao = :internoVersao WHERE id = '$id'");
	}

	$query->bindValue(":codigo", "$cp1");
	$query->bindValue(":descricao", "$cp2");
	$query->bindValue(":dataInicio", "$cp3");
	$query->bindValue(":dataFim", "$cp4");
	$query->bindValue(":internoVersao", "$cp5");
	
	$query->execute();
	
	echo 'Salvo com Sucesso';

?>