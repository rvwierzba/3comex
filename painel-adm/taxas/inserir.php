<?php 
	require_once("../../conexao.php");
	require_once("campos.php");

	$id = @$_POST['id'];
	$cp1 = $_POST[$campo1];
	$cp2 = $_POST[$campo2];

	//VALIDAR CAMPO
	$query = $pdo->query("SELECT * from $pagina where id = '$id'");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_reg = @count($res);
	$id_reg = @$res[0]['id'];
	if($total_reg > 0 and $id_reg != $id){
		echo 'Registro já cadastrado!!';
		exit();
	}

	try {
		if($id == ""){
			$query = $pdo->prepare("INSERT INTO $pagina (descricao, tributavel) VALUES (:descricao, :tributavel)");
		}else{
			$query = $pdo->prepare("UPDATE $pagina SET descricao = :descricao, tributavel = :tributavel WHERE id = :id");
			$query->bindValue(":id", $id);
		}

		$query->bindValue(":descricao", $cp1);
		$query->bindValue(":tributavel", $cp2);

		$query->execute();
		
		echo 'Salvo com Sucesso';
	} catch (PDOException $e) {
		echo 'Erro: ' . $e->getMessage();
	}
?>
