<?php
header('Content-Type: application/json');
require_once("C:\\xampp\htdocs\\3comex\\conexao.php");

// Verificação de requisição AJAX
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    die("Esta página só pode ser acessada via AJAX.");
}

header('Content-Type: application/json');


$term = isset($_GET['term']) ? $_GET['term'] : '';
error_log("Term recebido (buscar_unidades_rfb.php): " . $term);

try {
    $stmt = $pdo->prepare("SELECT id, nome FROM unidades_rfb WHERE nome LIKE :term;");
    $stmt->execute(["%" . $term . "%"]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    error_log("Resultados da consulta (buscar_unidades_rfb.php): " . print_r($results, true));

    echo json_encode($results);
} catch (PDOException $e) {
    error_log("Erro no banco de dados (buscar_unidades_rfb.php): " . $e->getMessage());
    echo json_encode([]);
}
?>