<?php
require_once(dirname(__DIR__) . '/conexao.php');
require_once('campos.php');

// Capturando os valores do formulário
$sigla_iso2 = $_POST[$campo1];
$sigla_iso3 = $_POST[$campo2];
$nome = $_POST[$campo3];
$nome_ingles = $_POST[$campo4];
$nome_frances = $_POST[$campo5];
$codigo_numerico = $_POST[$campo6];
$data_inicio = $_POST[$campo7];
$data_fim = $_POST[$campo8];
$versao_interno = $_POST[$campo9];
$interno_hash = $_POST[$campo10];

// Verifica se a data fim está vazia
if (isset($data_fim)) {
    $data_fim = NULL;
}


// Preparando a query SQL
$sql = "INSERT INTO $pagina ($campo1, $campo2, $campo3, $campo4, $campo5, $campo6, $campo7, $campo8, $campo9, $campo10) 
        VALUES (:sigla_iso2, :sigla_iso3, :nome, :nome_ingles, :nome_frances, :codigo_numerico, :data_inicio, :data_fim, :versao_interno, :interno_hash)";

$stmt = $pdo->prepare($sql);

// Binding dos parâmetros
$stmt->bindParam(':sigla_iso2', $sigla_iso2);
$stmt->bindParam(':sigla_iso3', $sigla_iso3);
$stmt->bindParam(':nome', $nome);
$stmt->bindParam(':nome_ingles', $nome_ingles);
$stmt->bindParam(':nome_frances', $nome_frances);
$stmt->bindParam(':codigo_numerico', $codigo_numerico, PDO::PARAM_INT);
$stmt->bindParam(':data_inicio', $data_inicio);
$stmt->bindParam(':data_fim', $data_fim);
$stmt->bindParam(':versao_interno', $versao_interno);
$stmt->bindParam(':interno_hash', $interno_hash);

// Executa o insert
if ($stmt->execute()) {
    echo "Registro inserido com sucesso!";
} else {
    echo "Erro ao inserir registro: " . $stmt->errorInfo()[2];
}

// Fecha a conexão
$stmt = null;
$pdo = null;
?>
