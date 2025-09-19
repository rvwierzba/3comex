<?php
// painel-adm/despesas/excluir.php

require_once("../../conexao.php");

$id = $_POST['id-excluir'];

$pdo->query("DELETE from despesas WHERE id = '$id'");

echo "Excluído com Sucesso";
?>