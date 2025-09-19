<?php
// painel-adm/despesas/inserir.php

require_once("../../conexao.php");

$id = $_POST['id'];
$nome = $_POST['nome'];
$cat_despesa = $_POST['cat_despesa'];

if ($nome == "") {
    echo "Preencha o campo Nome!";
    exit();
}
if ($cat_despesa == "") {
    echo "Selecione uma Categoria!";
    exit();
}

// VERIFICAR SE O REGISTRO JÁ EXISTE
$query = $pdo->prepare("SELECT * FROM despesas WHERE nome = :nome");
$query->bindValue(":nome", "$nome");
$query->execute();
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$id_reg = @$res[0]['id'];
if (@count($res) > 0 and $id_reg != $id) {
    echo "Despesa já cadastrada!";
    exit();
}

if ($id == "") {
    $query = $pdo->prepare("INSERT INTO despesas SET nome = :nome, cat_despesa = :cat_despesa");
} else {
    $query = $pdo->prepare("UPDATE despesas SET nome = :nome, cat_despesa = :cat_despesa WHERE id = '$id'");
}

$query->bindValue(":nome", $nome);
$query->bindValue(":cat_despesa", $cat_despesa);
$query->execute();

echo "Salvo com Sucesso";
?>