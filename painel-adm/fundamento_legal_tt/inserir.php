<?php
require_once(dirname(__DIR__) . '/conexao.php');
require_once('campos.php');

// Capturando os valores do formulário
$codigo = $_POST[$campo1];
$descricao = $_POST[$campo2];
$mnemonico_sistema_controle = $_POST[$campo3];
$codigo_beneficio_fiscal_sisen = $_POST[$campo4];
$permitir_duimp_vin_terceiros = $_POST[$campo5];
$permitir_registro_pessoa_fisica = $_POST[$campo6];
$data_inicio = $_POST[$campo7];
$data_fim = $_POST[$campo8];
$versao_interno = $_POST[$campo9];

// Verifica se a data fim está vazia
if (isset($data_fim)) {
    $data_fim = NULL;
}

// Formata as datas para o formato DATETIME do MySQL
$data_inicio_formatada = date('Y-m-d H:i:s', strtotime($data_inicio));
$data_fim_formatada = $data_fim ? date('Y-m-d H:i:s', strtotime($data_fim)) : NULL;

// Preparando a query SQL
$sql = "INSERT INTO FUNDAMENTO_LEGAL_TT ($campo1, $campo2, $campo3, $campo4, $campo5, $campo6, $campo7, $campo8, $campo9) 
        VALUES (:codigo, :descricao, :mnemonico_sistema_controle, :codigo_beneficio_fiscal_sisen, :permitir_duimp_vin_terceiros, :permitir_registro_pessoa_fisica, :data_inicio, :data_fim, :versao_interno)";

$stmt = $pdo->prepare($sql);

// Binding dos parâmetros
$stmt->bindParam(':codigo', $codigo);
$stmt->bindParam(':descricao', $descricao);
$stmt->bindParam(':mnemonico_sistema_controle', $mnemonico_sistema_controle);
$stmt->bindParam(':codigo_beneficio_fiscal_sisen', $codigo_beneficio_fiscal_sisen);
$stmt->bindParam(':permitir_duimp_vin_terceiros', $permitir_duimp_vin_terceiros);
$stmt->bindParam(':permitir_registro_pessoa_fisica', $permitir_registro_pessoa_fisica);
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
