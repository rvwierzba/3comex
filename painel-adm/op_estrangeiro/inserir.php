<?php 
	require_once("../../conexao.php");
	require_once("campos.php");

	$id = @$_POST['id'];

		$cp1 = $_POST['cpf_cnpj_raiz'];
		$cp2 = $_POST['tin'];
		$cp3 = $_POST['codigo'];
		$cp4 = $_POST['codigo_interno'];
		$cp5 = $_POST['nome'];
		$cp6 = $_POST['pais_origem'];
	
	
	//VALIDAR CAMPO
	$query = $pdo->query("SELECT * from $pagina where id = '$id'");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_reg = @count($res);
	$id_reg = @$res[0]['id'];
	if($total_reg > 0 and $id_reg != $id){
		echo 'Operador já está cadastrado!!';
		exit();
	}

	if($id == ""){
		$query = $pdo->prepare("INSERT INTO $pagina set cpf_cnpj_raiz = :cpfCnpjRaiz, tin = :tin, codigo = :codigo,
		 codigo_interno = :codigoInterno, nome = :nome, pais_origem = :paisOrigem");
	}else{
		$query = $pdo->prepare("UPDATE $pagina set cpf_cnpj_raiz = :cpfCnpjRaiz, tin = :tin, codigo = :codigo,
		codigo_interno = :codigoInterno, nome = :nome, pais_origem = :paisOrigem WHERE id = '$id'");
	}

	$query->bindValue(":cpfCnpjRaiz", "$cp1");
	$query->bindValue(":tin", "$cp2");
	$query->bindValue(":codigo", "$cp3");
	$query->bindValue(":codigoInterno", "$cp4");
	$query->bindValue(":nome", "$cp5");
	$query->bindValue(":paisOrigem", "$cp6");
	$query->execute();
	
	echo 'Salvo com Sucesso';

?>