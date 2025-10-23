<?php 
require_once("../../conexao.php");

$tblBancarias = 'bancarias';

$cp1 = $_POST['Banco'];
$cp2 = $_POST['Agencia'];
$cp3 = $_POST['Conta'];
$cp4 = $_POST['Tipo'];
$cp5 = $_POST['Pessoa'];
$cp6 = $_POST['Doc'];

$id = @$_POST['id'];

//VALIDAR CAMPO
$query = $pdo->query("SELECT * from $tblBancarias where conta = '$cp3'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
$id_reg = @$res[0]['id'];
if($total_reg > 0 and $id_reg != $id){
	echo 'Este registro já está cadastrado!!';
	exit();
}

if($id == ""){
	$sql = "INSERT INTO $tblBancarias set banco = :campo1, agencia = :campo2, conta = :campo3, tipo = :campo4, pessoa = :campo5, doc = :campo6";
}else{
	$sql = "UPDATE $tblBancarias set banco = :campo1, agencia = :campo2, conta = :campo3, tipo = :campo4, pessoa = :campo5, doc = :campo6 WHERE id = '$id'";
}

$query = $pdo->prepare($sql);

$query->bindValue(":campo1", "$cp1");
$query->bindValue(":campo2", "$cp2");
$query->bindValue(":campo3", "$cp3");
$query->bindValue(":campo4", "$cp4");
$query->bindValue(":campo5", "$cp5");
$query->bindValue(":campo6", "$cp6");

try {
    $query->execute();
    echo 'Salvo com Sucesso';
} catch (PDOException $e) {
    // *** RETORNO CRÍTICO DE ERRO SQL PARA O JAVASCRIPT ***
    // Se a tabela ou a coluna estiver errada, esta mensagem aparecerá.
    echo 'ERRO_SQL: ' . $e->getMessage();
    exit();
}
// O SCRIPT TERMINA AQUI APÓS A EXECUÇÃO
?>