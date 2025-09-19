<?php
// painel-adm/cat_despesas/salvar.php

require_once("../../conexao.php");

$id = $_POST['id'];
$nome = $_POST['nome'];

if ($nome == "") {
    echo "Preencha o campo Nome!";
    exit();
}

// VERIFICAR SE O REGISTRO JÁ EXISTE
$query = $pdo->prepare("SELECT * FROM cat_despesas WHERE nome = :nome");
$query->bindValue(":nome", "$nome");
$query->execute();
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$id_reg = @$res[0]['id'];
if (@count($res) > 0 and $id_reg != $id) {
    echo "Categoria já cadastrada!";
    exit();
}

if ($id == "") {
    $query = $pdo->prepare("INSERT INTO cat_despesas SET nome = :nome");
} else {
    $query = $pdo->prepare("UPDATE cat_despesas SET nome = :nome WHERE id = '$id'");
}

$query->bindValue(":nome", "$nome");
$query->execute();

echo "Salvo com Sucesso";
?>