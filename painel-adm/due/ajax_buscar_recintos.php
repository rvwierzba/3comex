<?php
// painel-adm/due/ajax_buscar_recintos.php

// error_reporting(0); // Comente para depuração, descomente para produção
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json; charset=utf-8');

// Ajuste o caminho para o seu ficheiro de conexão
// Se este ficheiro está em painel-adm/due/, e conexao.php na raiz do projeto:
require_once("../../conexao.php");

$resposta_ajax = ['sucesso' => false, 'mensagem' => 'Erro desconhecido ao buscar recintos.', 'recintos' => []];

if (isset($pdo) && ($pdo instanceof PDO)) {
    try {
        // Usar os nomes corretos da tabela e colunas
        $nome_tabela_recintos = 'recinto_aduaneiro'; // Nome correto da tabela
        $coluna_codigo_recinto = 'codigo';           // Nome correto da coluna de código
        $coluna_nome_recinto = 'nome';             // Nome correto da coluna de nome

        // A consulta SQL corrigida
        $sql = "SELECT {$coluna_codigo_recinto} as CODIGO, {$coluna_nome_recinto} as NOME FROM {$nome_tabela_recintos} ORDER BY NOME";
        
        // Log da consulta para depuração
        error_log("[ajax_buscar_recintos.php] SQL Executada: " . $sql);
        
        $stmt = $pdo->query($sql);
        
        if ($stmt) {
            $recintos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $resposta_ajax['sucesso'] = true;
            $resposta_ajax['mensagem'] = count($recintos) . ' recintos encontrados.';
            $resposta_ajax['recintos'] = $recintos;
            // Log do número de recintos encontrados
            error_log("[ajax_buscar_recintos.php] Recintos consultados com sucesso: " . count($recintos));
            if (count($recintos) > 0) {
                // error_log("[ajax_buscar_recintos.php] Exemplo primeiro recinto: " . print_r($recintos[0], true));
            }
        } else {
            $errorInfo = $pdo->errorInfo();
            $resposta_ajax['mensagem'] = 'Erro ao executar a consulta de recintos. PDO Error: ' . ($errorInfo[2] ?? 'Detalhe não disponível');
            error_log("[ajax_buscar_recintos.php] ERRO SQL: " . $resposta_ajax['mensagem']);
        }
    } catch (PDOException $e) {
        $resposta_ajax['mensagem'] = 'Exceção PDO ao consultar recintos: ' . $e->getMessage();
        error_log("[ajax_buscar_recintos.php] EXCEÇÃO PDO: " . $e->getMessage());
    }
} else {
    $resposta_ajax['mensagem'] = 'Erro crítico: Falha na conexão com o banco de dados (PDO não definido).';
    error_log("[ajax_buscar_recintos.php] ERRO FATAL: Conexão PDO NÃO definida.");
}

// Garante que o output é sempre JSON válido
$json_output = json_encode($resposta_ajax);
if (json_last_error() !== JSON_ERROR_NONE) {
    error_log("[ajax_buscar_recintos.php] ERRO json_encode FINAL: " . json_last_error_msg());
    $resposta_ajax_erro_json = ['sucesso' => false, 'mensagem' => 'Erro crítico ao gerar JSON de resposta (recintos): ' . json_last_error_msg(), 'recintos' => []];
    echo json_encode($resposta_ajax_erro_json);
} else {
    echo $json_output;
}
exit;
?>
