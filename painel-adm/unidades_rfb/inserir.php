<?php 
	require_once("../../conexao.php");
	require_once("campos.php");

	$id = @$_POST['id'];
  
  $sigla = $_POST['sigla'];
  $codigo = $_POST['codigo'];
  $nome = $_POST['nome'];
  $regiaoFiscal = $_POST['regiao_fiscal'];
  $nomeCurto = $_POST['nome_curto'];
  $dataInicio = $_POST['data_inicio'];
  $dataFim = $_POST['data_fim'];
  $internoVersao = $_POST['interno_versao'];
	
	
	//VALIDAR CAMPO
	$query = $pdo->query("SELECT * from $pagina where $campo1 = '$codigo'");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_reg = @count($res);
	$id_reg = @$res[0]['id'];
	if($total_reg > 0 and $id_reg != $id){
		echo 'Este modelo Unidade  já está cadastrado!!';
		exit();
	}

	if($id == ""){
		$query = $pdo->prepare("INSERT INTO $pagina SET sigla = :sigla, codigo = :codigo, nome = :nome,
     regiao_fiscal = :regiaoFiscal, nome_curto = :nomeCurto, data_inicio = :dataInicio, data_fim = :dataFim,
     interno_versao = :internoVersao");
	}else{
		$query = $pdo->prepare("UPDATE $pagina SET sigla = :sigla, codigo = :codigo, nome = :nome,
    regiao_fiscal = :regiaoFiscal, nome_curto = :nomeCurto, data_inicio = :dataInicio, data_fim = :dataFim,
    interno_versao = :internoVersao WHERE id = '$id'");
	}

  $query->bindValue(":sigla", "$sigla");
	$query->bindValue(":codigo", "$codigo");
  $query->bindValue(":nome", "$nome");
  $query->bindValue(":regiaoFiscal", "$regiaoFiscal");
  $query->bindValue(":nomeCurto", "$nomeCurto");
  $query->bindValue(":dataInicio", "$dataInicio");
  $query->bindValue(":dataFim", "$dataFim");
  $query->bindValue(":internoVersao", "$internoVersao");
    
	$query->execute();
	
	echo 'Salvo com Sucesso';

?>