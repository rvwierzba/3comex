<?php
require_once("C:\\xampp\htdocs\\3comex\\conexao.php");

// Verificação de requisição AJAX
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    die("Esta página só pode ser acessada via AJAX.");
}

header('Content-Type: application/json');


$term = $_GET['term'];

$query = $pdo->prepare("SELECT ID as id, CODIGO as codigo, DESCRICAO as nome FROM enquadramento WHERE DESCRICAO LIKE :term OR CODIGO LIKE :term;");
$query->execute(['term' => '%' . $term . '%']);

$results = $query->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($results);
?>