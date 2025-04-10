<?php
require_once __DIR__ . "/../../..conexao.php";
require_once __DIR__ . "/campos.php";

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID não fornecido']);
    exit;
}

try {
    $id = filter_var($_GET['id'], FILTER_SANITIZE_STRING);
    $sql = "DELETE FROM {$pagina} WHERE {$campo1} = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_STR);
    $stmt->execute();

    echo json_encode(['success' => true, 'message' => 'Registro excluído']);
} catch (PDOException $e) {
    error_log("Erro ao excluir: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro no servidor']);
}
?>