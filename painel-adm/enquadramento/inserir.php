<?php
require_once(dirname(__DIR__) . '/conexao.php');
require_once('campos.php');

// Capturando os valores do formulário
$codigo = $_POST['codigo'];
$descricao = $_POST['descricao'];
$codigo_tipo_enquadramento = $_POST['codigo_tipo_enquadramento'];
$codigo_grupo_enquadramento = $_POST['codigo_grupo_enquadramento'];
$data_inicio = $_POST['data_inicio'];
$data_fim = $_POST['data_fim'];
$versao_interno = $_POST['interno_versao'];

// Verifica se a data fim está vazia
if (isset($data_fim)) {
    $data_fim = NULL;
}

// Formata as datas para o formato DATETIME do MySQL
$data_inicio_formatada = date('Y-m-d H:i:s', strtotime($data_inicio));
$data_fim_formatada = $data_fim ? date('Y-m-d H:i:s', strtotime($data_fim)) : NULL;

// Preparando a query SQL
$sql = "INSERT INTO ENQUADRAMENTO (CODIGO, DESCRICAO, CODIGO_TIPO_ENQUADRAMENTO, CODIGO_GRUPO_ENQUADRAMENTO, DATA_INICIO, DATA_FIM, INTERNO_VERSAO) 
        VALUES (:codigo, :descricao, :codigo_tipo_enquadramento, :codigo_grupo_enquadramento, :data_inicio, :data_fim, :versao_interno)";

$stmt = $pdo->prepare($sql);

// Binding dos parâmetros
$stmt->bindParam(':codigo', $codigo);
$stmt->bindParam(':descricao', $descricao);
$stmt->bindParam(':codigo_tipo_enquadramento', $codigo_tipo_enquadramento);
$stmt->bindParam(':codigo_grupo_enquadramento', $codigo_grupo_enquadramento);
$stmt->bindParam(':data_inicio', $data_inicio_formatada);
$stmt->bindParam(':data_fim', $data_fim_formatada);
$stmt->bindParam(':versao_interno', $versao_interno, PDO::PARAM_INT);

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
