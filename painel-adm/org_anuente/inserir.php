<?php 
	require_once("../../conexao.php");
	require_once("campos.php");
	
	$id = @$_POST['id'];

	$cp1 = $_POST['codigo'];
  $cp2 = $_POST['sigla'];
	$cp3 = $_POST['descricao'];
	$cp4 = $_POST['cnpj'];
	$cp5 = $_POST['data_inicio'];
	$cp6 = $_POST['data_fim'];
	$cp7 = $_POST['interno_versao'];
	
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
		 set codigo = :codigo, sigla = :sigla, descricao = :descricao, cnpj = :cnpj, 
		 data_inicio = :dataInicio, data_fim = :dataFim, interno_versao = :internoVersao");
	}else{
		$query = $pdo->prepare("UPDATE $pagina set codigo = :codigo, sigla = :sigla, descricao = :descricao, cnpj = :cnpj, 
		data_inicio = :dataInicio, data_fim = :dataFim, interno_versao = :internoVersao WHERE id = '$id'");
	}

	$query->bindValue(":codigo", "$cp1");
	$query->bindValue(":sigla", "$cp2");
	$query->bindValue(":descricao", "$cp3");
	$query->bindValue(":cnpj", "$cp4");
	$query->bindValue(":dataInicio", "$cp5");
	$query->bindValue(":dataFim", "$cp6");
	$query->bindValue(":internoVersao", "$cp7");

	$query->execute();
	
	echo 'Salvo com Sucesso';

?>