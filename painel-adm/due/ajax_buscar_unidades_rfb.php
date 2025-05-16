<?php
// painel-adm/due/ajax_buscar_unidades_rfb.php
error_reporting(0);
header('Content-Type: application/json; charset=utf-8');
require_once("../../conexao.php"); // Ajuste o caminho

$resposta_ajax = ['sucesso' => false, 'mensagem' => 'Erro desconhecido.', 'unidades' => []];

if (isset($pdo) && ($pdo instanceof PDO)) {
    try {
        // AJUSTE ESTA CONSULTA: Use os nomes corretos da sua tabela e colunas para unidades da RFB
        $stmt = $pdo->query("SELECT codigo as CODIGO, nome as NOME FROM unidades_rfb ORDER BY NOME");
        if ($stmt) {
            $unidades = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $resposta_ajax['sucesso'] = true;
            $resposta_ajax['mensagem'] = count($unidades) . ' unidades encontradas.';
            $resposta_ajax['unidades'] = $unidades;
        } else {
            $resposta_ajax['mensagem'] = 'Erro ao executar a consulta de unidades.';
        }
    } catch (PDOException $e) {
        $resposta_ajax['mensagem'] = 'Exceção PDO: ' . $e->getMessage();
    }
} else {
    $resposta_ajax['mensagem'] = 'Erro crítico: Falha na conexão com o banco de dados.';
}
echo json_encode($resposta_ajax);
exit;
?>