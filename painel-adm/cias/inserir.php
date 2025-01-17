<?php 
require_once("../../conexao.php");
require_once("campos.php");

@session_start();
require_once("../../conexao.php");
require_once("../verificar.php");
$id_usuario = $_SESSION['id_usuario'];
//RECUPERAR DADOS DO USUÁRIO
$query = $pdo->query("SELECT * from usuarios where id = '$id_usuario' ");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$nome_usuario = $res[0]['nome'];
$email_usuario = $res[0]['email'];
$senha_usuario = $res[0]['senha'];
$nivel_usuario = $res[0]['nivel'];

$cp1 = $_POST[$campo1];
$cp2 = $_POST[$campo2];
$cp3 = $_POST[$campo3];
$cp4 = $_POST[$campo4];
$cp5 = $_POST[$campo5];
$cp6 = $_POST[$campo6];
$cp7 = $_POST[$campo7];
$cp8 = $_POST[$campo8];
$cp9 = $_POST[$campo9];
$cp10 = $_POST[$campo10];
$cp11 = $_POST[$campo11];
$cp13 = $_POST[$campo13];
$cp14 = $_POST[$campo14];
$cp15 = $_POST[$campo15];
$cp16 = $_POST[$campo16];
$cp17 = $_POST[$campo17];
$cp18 = $_POST[$campo18];
$cp19 = date('d/m/Y');
$cp20 = $nome_usuario;


$id = @$_POST['id'];


// buscar codigo da bandeira
$query = $pdo->query("SELECT * from paises where NOME = '$cp12' ");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$cp21 = $res[0]['CODIGO_NUMERICO'];



//VALIDAR CAMPO
$query = $pdo->query("SELECT * from $pagina where codigo = '$cp1'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
$id_reg = @$res[0]['id'];
if($total_reg > 0 and $id_reg != $id){
	echo 'Este registro já está cadastrado!!';
	exit();
}

if($id == ""){
	$query = $pdo->prepare("INSERT INTO $pagina set codigo = :campo1, nomecia = :campo2, nome = :campo3, codvoo = :campo4, endereco = :campo5, cidade = :campo6, estado = :campo7, cep = :campo8, telefone = :campo9, whatsapp = :campo10, email = :campo11, nif = :campo13, motivo = :campo14, cnpj = :campo15, account = :campo16, rps = :campo17, codcontabil = :campo18, datareg = :campo19, usuario = :campo20, codbandeira = :campo21");

	$pais=$_POST['pais'];
	$query2 = $pdo->prepare("INSERT INTO paises SET NOME = :nomePais");
	$query2->bindValue(':nomePais', $pais);
}else{
	$cp12 = $_POST[$campo12];
	$query = $pdo->prepare("UPDATE $pagina set codigo = :campo1, nomecia = :campo2, nome = :campo3, codvoo = :campo4, endereco = :campo5, cidade = :campo6, estado = :campo7, cep = :campo8, telefone = :campo9, whatsapp = :campo10, email = :campo11, bandeira = :campo12, nif = :campo13, motivo = :campo14, cnpj = :campo15, account = :campo16, rps = :campo17, codcontabil = :campo18, datareg = :campo19, usuario = :campo20, codbandeira = :campo21 WHERE id = '$id'");
	$query->bindValue(":campo12", "$cp12");
}

$query->bindValue(":campo1", "$cp1");
$query->bindValue(":campo2", "$cp2");
$query->bindValue(":campo3", "$cp3");
$query->bindValue(":campo4", "$cp4");
$query->bindValue(":campo5", "$cp5");
$query->bindValue(":campo6", "$cp6");
$query->bindValue(":campo7", "$cp7");
$query->bindValue(":campo8", "$cp8");
$query->bindValue(":campo9", "$cp9");
$query->bindValue(":campo10", "$cp10");
$query->bindValue(":campo11", "$cp11");
$query->bindValue(":campo13", "$cp13");
$query->bindValue(":campo14", "$cp14");
$query->bindValue(":campo15", "$cp15");
$query->bindValue(":campo16", "$cp16");
$query->bindValue(":campo17", "$cp17");
$query->bindValue(":campo18", "$cp18");
$query->bindValue(":campo19", "$cp19");
$query->bindValue(":campo20", "$cp20");
$query->bindValue(":campo21", "$cp21");

$query->execute();

if($id == ""){
	$query2->execute();
}

echo 'Salvo com Sucesso';

 ?>