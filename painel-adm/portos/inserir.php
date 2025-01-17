<?php
require_once(dirname(__DIR__) . '/conexao.php');
require_once('campos.php');

// Capturando os valores do formulário
$codigo = $_POST[$campo1];
$descricao = $_POST[$campo2];
$data_inicio = $_POST[$campo3];
$data_fim = $_POST[$campo4];
$versao_interno = $_POST[$campo5];
$interno_hash = $_POST[$campo6];

// Verifica se a data fim está vazia
if (isset($data_fim)) {
    $data_fim = NULL;
}


// Preparando a query SQL
$sql = "INSERT INTO $pagina ($campo1, $campo2, $campo3, $campo4, $campo5, $campo6) 
        VALUES (:codigo, :descricao, :data_inicio, :data_fim, :versao_interno, :interno_hash)";

$stmt = $pdo->prepare($sql);

// Binding dos parâmetros
$stmt->bindParam(':codigo', $codigo);
$stmt->bindParam(':descricao', $descricao);
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
