<?php
// ajax_buscar_recintos.php - VERSÃO SIMPLES E FINAL

header('Content-Type: application/json');
include_once '../../conexao.php';

if (!isset($pdo)) {
    echo json_encode(['sucesso' => false, 'dados' => [], 'erro' => 'Falha na conexão PDO.']);
    exit;
}

$unidade_rfb_codigo = isset($_GET['unidade_rfb_codigo']) ? $_GET['unidade_rfb_codigo'] : '';

$recintos = [];

try {
    if (!empty($unidade_rfb_codigo)) {
        // LÓGICA DE FILTRO (quando um código de unidade é passado)
        $regiao_fiscal_num = substr($unidade_rfb_codigo, 0, 2);
        $sigla_para_buscar = 'RF' . $regiao_fiscal_num;
        
        $stmt = $pdo->prepare("SELECT codigo, nome, sigla_regiao_fiscal FROM recinto_aduaneiro WHERE sigla_regiao_fiscal = ? ORDER BY nome ASC");
        $stmt->execute([$sigla_para_buscar]);
        $recintos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // LÓGICA NOVA: Se nenhum código for passado, RETORNA TUDO.
        $stmt = $pdo->prepare("SELECT codigo, nome, sigla_regiao_fiscal FROM recinto_aduaneiro ORDER BY nome ASC");
        $stmt->execute();
        $recintos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    echo json_encode(['sucesso' => false, 'dados' => [], 'erro' => $e->getMessage()]);
    $pdo = null;
    exit;
}

$pdo = null;
echo json_encode(['sucesso' => true, 'dados' => $recintos]);

?>