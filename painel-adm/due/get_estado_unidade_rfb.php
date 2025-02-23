<?php
header('Content-Type: application/json');
require_once("C:\\xampp\htdocs\\3comex\\conexao.php");

$id = isset($_GET['id']) ? $_GET['id'] : 0;
error_log("ID recebido (get_estado_unidade_rfb.php): " . $id);

if ($id > 0) {
    try {
        $stmt = $conn->prepare("SELECT nome FROM unidades_rfb WHERE id = :id;");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            // Extrai o estado do INÍCIO do nome da unidade
            $nome_unidade = $result['nome'];
            $estado = substr($nome_unidade, 0, 2); // Pega os dois primeiros caracteres

            error_log("Estado extraído do nome da unidade (get_estado_unidade_rfb.php): " . $estado);

            echo json_encode(['estado' => $estado]);
        } else {
            error_log("Unidade RFB não encontrada com ID: " . $id);
            echo json_encode(['estado' => null]);
        }
    } catch (PDOException $e) {
        error_log("Erro ao buscar estado da RFB (get_estado_unidade_rfb.php): " . $e->getMessage());
        echo json_encode(['estado' => null]);
    }
} else {
    echo json_encode(['estado' => null]);
}
?>