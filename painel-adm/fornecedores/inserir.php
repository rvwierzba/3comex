<?php
// painel-adm/fornecedores/inserir.php

require_once("../../conexao.php");

$id = $_POST['id'];
$nome = $_POST['nome'];
$pessoa = $_POST['pessoa'];
$doc = $_POST['doc'];
$telefone = $_POST['telefone'];
$endereco = $_POST['endereco'];
$ativo = $_POST['ativo'];
$obs = $_POST['obs'];
$banco = $_POST['banco'];
$agencia = $_POST['agencia'];
$email = $_POST['email'];

if ($nome == "") {
    echo "Preencha o campo Nome!";
    exit();
}
if ($doc == "") {
    echo "Preencha o campo Documento!";
    exit();
}

// VERIFICAR SE O DOCUMENTO JÁ EXISTE
$query = $pdo->prepare("SELECT * FROM fornecedores WHERE doc = :doc");
$query->bindValue(":doc", $doc);
$query->execute();
$res = $query->fetchAll(PDO::FETCH_ASSOC);
if (@count($res) > 0 and $id != @$res[0]['id']) {
    echo "Documento já cadastrado para outro fornecedor!";
    exit();
}

if ($id == "") {
    $query = $pdo->prepare("INSERT INTO fornecedores SET nome = :nome, pessoa = :pessoa, doc = :doc, telefone = :telefone, endereco = :endereco, ativo = :ativo, obs = :obs, data = curDate(), banco = :banco, agencia = :agencia, email = :email");
} else {
    $query = $pdo->prepare("UPDATE fornecedores SET nome = :nome, pessoa = :pessoa, doc = :doc, telefone = :telefone, endereco = :endereco, ativo = :ativo, obs = :obs, banco = :banco, agencia = :agencia, email = :email WHERE id = '$id'");
}

$query->bindValue(":nome", $nome);
$query->bindValue(":pessoa", $pessoa);
$query->bindValue(":doc", $doc);
$query->bindValue(":telefone", $telefone);
$query->bindValue(":endereco", $endereco);
$query->bindValue(":ativo", $ativo);
$query->bindValue(":obs", $obs);
$query->bindValue(":banco", $banco);
$query->bindValue(":agencia", $agencia);
$query->bindValue(":email", $email);
$query->execute();

echo "Salvo com Sucesso";
?>