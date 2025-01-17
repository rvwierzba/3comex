<?php 
	require_once("../../conexao.php");
	require_once("campos.php");
	
	$id = @$_POST['id'];

	  $cp1 = $_POST['codigo'];
		$cp2 = $_POST['codigo_interno'];
		$cp3 = $_POST['descricao'];
		$cp4 = $_POST['denominacao'];
		$cp5 = $_POST['ncm'];
		$cp6 = $_POST['periodo_registro_inicio'];
		$cp7 = $_POST['periodo_registro_fim'];
    $cp8 = $_POST['situacao'];
    $cp9 = $_POST['ultima_alteracao_inicio'];
    $cp10 = $_POST['ultima_alteracao_fim'];
    $cp11 = $_POST['pais_origem'];
    $cp12 = $_POST['cpf_cnpj_fabricante'];
    $cp13 = $_POST['cpf_cnpj_raiz'];
    $cp14 = $_POST['modalidade'];
    $cp15 = $_POST['data_referencia'];
    $cp16 = $_POST['operador_estrangeiro_codigo'];
	
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
		$query = $pdo->prepare("INSERT INTO $pagina set codigo = :codigo, codigo_interno = :codigoInterno, descricao = :descricao, denominacao = :denominacao, ncm = :ncm,
    periodo_registro_inicio = :periodoRegistroInicio, periodo_registro_fim = :periodoRegistroFim, situacao = :situacao, ultima_alteracao_inicio = :ultimaAlteracaoInicio,
    ultima_alteracao_fim = :ultimaAlteracaoFim, pais_origem = :paisOrigem, cpf_cnpj_fabricante = :cpfCnpjFabricante, cpf_cnpj_raiz = :cpfCnpjRaiz, modalidade = :modalidade, 
    data_referencia = :dataReferencia, operador_estrangeiro_codigo = :operadorEstrangeiroCodigo");
	}else{
		$query = $pdo->prepare("UPDATE $pagina set codigo = :codigo, codigo_interno = :codigoInterno, descricao = :descricao,
    denominacao = :denominacao, ncm = :ncm, periodo_registro_inicio = :periodoRegistroInicio, periodo_registro_fim = :periodoRegistroFim,
    situacao = :situacao, ultima_alteracao_inicio = :ultimaAlteracaoInicio, ultima_alteracao_fim = :ultimaAlteracaoFim, pais_origem = :paisOrigem,
    cpf_cnpj_fabricante = :cpfCnpjFabricante, cpf_cnpj_raiz = :cpfCnpjRaiz, modalidade = :modalidade, data_referencia = :dataReferencia, 
    operador_estrangeiro_codigo = :operadorEstrangeiroCodigo WHERE id = '$id'");
	}

	$query->bindValue(":codigo", "$cp1");
	$query->bindValue(":codigoInterno", "$cp2");
	$query->bindValue(":descricao", "$cp3");
	$query->bindValue(":denominacao", "$cp4");
	$query->bindValue(":ncm", "$cp5");
	$query->bindValue(":periodoRegistroInicio", "$cp6");
	$query->bindValue(":periodoRegistroFim", "$cp7");
  $query->bindValue(":situacao", "$cp8");
  $query->bindValue(":ultimaAlteracaoInicio", "$cp9");
  $query->bindValue(":ultimaAlteracaoFim", "$cp10");
  $query->bindValue(":paisOrigem", "$cp11");
  $query->bindValue(":cpfCnpjFabricante", "$cp12");
  $query->bindValue(":cpfCnpjRaiz", "$cp13");
  $query->bindValue(":modalidade", "$cp14");
  $query->bindValue(":dataReferencia", "$cp15");
  $query->bindValue(":operadorEstrangeiroCodigo", "$cp16");

	$query->execute();
	
	echo 'Salvo com Sucesso';

?>