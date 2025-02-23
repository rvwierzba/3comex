<?php
require_once("C:\\xampp\htdocs\\3comex\\conexao.php");

// Verificação de requisição AJAX
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    die("Esta página só pode ser acessada via AJAX.");
}

header('Content-Type: application/json');


if (isset($_GET['term'])) {
    $term = $_GET['term'];
    $results = [];
    try {
        $stmt = $pdo->prepare("SELECT id, nome FROM agentes WHERE nome LIKE ?");
        $stmt->execute(['%' . $term . '%']);
        $results = array_merge($results, $stmt->fetchAll(PDO::FETCH_ASSOC)); //Mescla agentes

        $stmt2 = $pdo->prepare("SELECT codigo, nome FROM clientes WHERE nome LIKE ?"); // Use 'codigo' como 'id' para clientes
        $stmt2->execute(['%' . $term . '%']);
        $results = array_merge($results, $stmt2->fetchAll(PDO::FETCH_ASSOC)); //Mescla clientes
    } catch (PDOException $e) {
        $results = ['error' => $e->getMessage()]; // Adicione a chave 'error' para tratamento de erros no JS.
    }

    echo json_encode($results);
    exit; //Encerra o script após enviar o JSON.
}
?>