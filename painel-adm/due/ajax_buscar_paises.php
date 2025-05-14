<?php
// painel-adm/due/ajax_buscar_paises.php

// Ativar todos os erros para depuração (remova ou ajuste em produção)
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json; charset=utf-8'); // Define o tipo de conteúdo da resposta

// Ajuste o caminho para o seu ficheiro de conexão
// Se este ficheiro está em painel-adm/due/, e conexao.php na raiz do projeto:
require_once("../../conexao.php"); // Exemplo de caminho

$resposta_ajax = ['sucesso' => false, 'mensagem' => 'Erro desconhecido.', 'paises' => []];

if (!isset($pdo) || !($pdo instanceof PDO)) {
    $resposta_ajax['mensagem'] = 'Erro crítico: Falha na conexão com o banco de dados (PDO não definido).';
    error_log("[ajax_buscar_paises.php] ERRO FATAL: Conexão PDO NÃO definida.");
    echo json_encode($resposta_ajax);
    exit;
}

try {
    $stmt_paises = $pdo->query("SELECT CODIGO_NUMERICO, NOME FROM paises ORDER BY NOME");

    if ($stmt_paises === false) {
        $errorInfo = $pdo->errorInfo();
        $resposta_ajax['mensagem'] = 'Erro ao executar a consulta de países. Detalhes do PDO: SQLSTATE[' . $errorInfo[0] . '] Code[' . $errorInfo[1] . '] Mensagem[' . $errorInfo[2] . ']';
        error_log("[ajax_buscar_paises.php] ERRO SQL PAÍSES: " . $resposta_ajax['mensagem']);
    } else {
        $paises_array = $stmt_paises->fetchAll(PDO::FETCH_ASSOC);
        if ($paises_array === false) { // fetchAll pode retornar false em erro
            $resposta_ajax['mensagem'] = 'Erro ao buscar os dados dos países após a consulta.';
            error_log("[ajax_buscar_paises.php] ERRO: fetchAll retornou false para países.");
        } else {
            $resposta_ajax['sucesso'] = true;
            $resposta_ajax['mensagem'] = count($paises_array) . ' países encontrados.';
            $resposta_ajax['paises'] = $paises_array;
            // error_log("[ajax_buscar_paises.php] Países consultados com sucesso: " . count($paises_array));
        }
    }
} catch (PDOException $e) {
    $resposta_ajax['mensagem'] = 'Exceção PDO ao consultar países: ' . $e->getMessage();
    error_log("[ajax_buscar_paises.php] EXCEÇÃO PDO: " . $e->getMessage());
}

// Garante que o output é sempre JSON válido, mesmo em caso de erro de encoding (raro com fetchAll)
$json_output = json_encode($resposta_ajax);
if (json_last_error() !== JSON_ERROR_NONE) {
    error_log("[ajax_buscar_paises.php] ERRO json_encode FINAL: " . json_last_error_msg());
    // Prepara uma resposta de erro JSON válida se o encoding falhar
    $resposta_ajax_erro_json = ['sucesso' => false, 'mensagem' => 'Erro crítico ao gerar JSON de resposta: ' . json_last_error_msg(), 'paises' => []];
    echo json_encode($resposta_ajax_erro_json);
} else {
    echo $json_output;
}

exit;
?>
