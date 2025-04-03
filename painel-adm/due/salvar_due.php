<?php
// -----------------------------------------------------------------------------
// Script: salvar_due.php
// Responsável por receber dados (via POST JSON) e salvar/atualizar uma DU-E.
// Retorna sempre uma resposta JSON.
// -----------------------------------------------------------------------------

// ** Configuração de Erros Essencial **
error_reporting(E_ALL);
ini_set('display_errors', 0); // NUNCA exibir erros em API JSON
ini_set('log_errors', 1);
// ini_set('error_log', '/path/to/your/php-error.log'); // Defina se necessário

// --- Função para Enviar Resposta JSON e Sair ---
function enviarRespostaJson(array $data, int $httpStatusCode = 200): void {
    if (headers_sent($file, $line)) {
         error_log("salvar_due.php - Erro: Headers já enviados em $file na linha $line antes de enviar JSON.");
    } else {
         http_response_code($httpStatusCode);
         header('Content-Type: application/json; charset=utf-8');
    }
    $jsonOutput = json_encode($data);
    if ($jsonOutput === false) {
        error_log("salvar_due.php - ERRO FATAL ao codificar JSON: " . json_last_error_msg());
        if (!headers_sent()) {
             http_response_code(500);
             header('Content-Type: application/json; charset=utf-8');
        }
        echo '{"success": false, "message": "Erro interno do servidor ao gerar resposta." }';
    } else {
        echo $jsonOutput;
    }
    exit;
}

// --- Conexão com o Banco de Dados ---
try {
    // Usando o caminho absoluto que funcionou para você.
    @include_once "C:\\xampp\\htdocs\\3comex\\conexao.php"; // <<< CAMINHO ABSOLUTO MANTIDO

    if (!isset($pdo) || !$pdo) {
        error_log("salvar_due.php - Erro Crítico PÓS-INCLUDE: Variável \$pdo não definida ou conexão falhou (verificar conexao.php e seus logs).");
        enviarRespostaJson(['success' => false, 'message' => 'Erro crítico: Falha na conexão com o banco de dados (verificar logs).'], 500);
    }
    // Log removido daqui para evitar poluir em cada requisição normal

} catch (Throwable $e) {
    error_log("salvar_due.php - Exceção/Erro durante include/conexão inicial: " . $e->getMessage());
    enviarRespostaJson(['success' => false, 'message' => 'Erro interno do servidor durante inicialização.'], 500);
}


// --- Receber e Decodificar Dados da Requisição ---
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

if ($input === null && json_last_error() !== JSON_ERROR_NONE) {
    error_log("salvar_due.php - Erro ao decodificar JSON recebido: " . json_last_error_msg() . " - Input: " . $inputJSON);
    enviarRespostaJson(['success' => false, 'message' => 'Erro: Dados inválidos ou mal formatados recebidos.'], 400);
}


// --- Extrair e Validar Dados ---
$formData = $input['formData'] ?? [];
$itemsData = $input['itemsData'] ?? [];

if (empty($formData)) {
     error_log("salvar_due.php - Erro: formData não encontrado no payload.");
     enviarRespostaJson(['success' => false, 'message' => 'Erro: Dados do formulário não recebidos.'], 400);
}

$dueId = $formData['due_id'] ?? null;
$isUpdate = !empty($dueId);
// Log da operação movido para depois, caso haja erro antes


// --- Função para Gerar o Próximo ID da DU-E ---
// (Função mantida como na resposta anterior)
function gerarProximoDueId(PDO $pdo): string {
    $anoAtual = date('y');
    $prefixoBusca = 'DUE_____-' . $anoAtual;
    $formatoId = 'DUE%05d-' . $anoAtual;

    try {
        $sql = "SELECT due_id FROM due WHERE due_id LIKE :prefixo ORDER BY due_id DESC LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':prefixo' => $prefixoBusca]);
        $ultimoId = $stmt->fetchColumn();

        $proximoNumero = 1;
        if ($ultimoId) {
            $ultimoNumero = (int) substr($ultimoId, 3, 5);
            $proximoNumero = $ultimoNumero + 1;
        }
        $novoDueId = sprintf($formatoId, $proximoNumero);
        return $novoDueId;

    } catch (PDOException $e) {
         error_log("salvar_due.php - Erro PDO ao gerar próximo DUE ID: " . $e->getMessage());
         throw new RuntimeException("Falha ao gerar o ID da DU-E.", 0, $e);
    }
}


// --- Preparar Dados para o Banco ---
// Verifica se 'nomeCliente' está chegando do JS
$nomeExportadorRecebido = filter_var($formData['nomeCliente'] ?? null, FILTER_SANITIZE_STRING);
if ($nomeExportadorRecebido === null) {
     error_log("salvar_due.php - ALERTA: Campo 'nomeCliente' CHEGOU COMO NULL do frontend. Verifique o JS e o atributo 'name' no input HTML '#nomeCliente'.");
     // Mesmo chegando null, vamos tentar salvar null no banco. Se a coluna não permitir null, dará erro SQL.
}

$params = [
    ':cnpj_cpf' => filter_var($formData['cnpj-cpf'] ?? null, FILTER_SANITIZE_STRING),
    // ---> Usando a variável verificada <---
    ':nome_exportador' => $nomeExportadorRecebido,
    ':forma_exportacao' => filter_var($formData['forma-export'] ?? null, FILTER_SANITIZE_STRING),
    ':tipo_doc_fiscal' => filter_var($formData['tp-doc-amp-merc-export'] ?? null, FILTER_SANITIZE_STRING),
    ':moeda' => filter_var($formData['moeda'] ?? null, FILTER_SANITIZE_STRING),
    ':ruc' => filter_var($formData['ruc'] ?? null, FILTER_SANITIZE_STRING) ?: null,
    ':situacao_especial' => filter_var($formData['situacao-espec-despacho'] ?? null, FILTER_SANITIZE_STRING) ?: null,
    ':export_cons' => isset($formData['export-cons']) && $formData['export-cons'] ? 1 : 0,
    ':unidade_rfb_d' => filter_var($formData['unidade_rfb_despacho'] ?? null, FILTER_SANITIZE_STRING) ?: null,
    ':recinto_d' => filter_var($formData['recinto_aduaneiro_despacho'] ?? null, FILTER_SANITIZE_STRING) ?: null,
    ':unidade_rfb_e' => filter_var($formData['unidade_rfb_embarque'] ?? null, FILTER_SANITIZE_STRING) ?: null,
    ':recinto_e' => filter_var($formData['recinto_aduaneiro_embarque'] ?? null, FILTER_SANITIZE_STRING) ?: null,
    ':via_especial' => filter_var($formData['via-especial-transport'] ?? null, FILTER_SANITIZE_STRING) ?: null,
    ':info_compl' => filter_var($formData['info-compl'] ?? null, FILTER_SANITIZE_STRING) ?: null,
];

$itemsJson = json_encode($itemsData);
if ($itemsJson === false) {
    error_log("salvar_due.php - Erro ao codificar itemsData para JSON: " . json_last_error_msg());
    enviarRespostaJson(['success' => false, 'message' => 'Erro interno ao processar dados dos itens.'], 500);
}
$params[':items_json'] = $itemsJson;

// Log CRÍTICO para depuração: Verificar o valor EXATO que será enviado ao banco
error_log("salvar_due.php: Preparando DML. Valor para :nome_exportador = '" . ($params[':nome_exportador'] ?? 'NULL') . "'");


// --- Lógica de Banco de Dados (INSERT ou UPDATE) ---
try {
    $pdo->beginTransaction();
    $logOperation = $isUpdate ? "UPDATE" : "INSERT";
    error_log("salvar_due.php: Iniciando $logOperation para DUE ID: " . ($dueId ?? '(novo)'));

    if ($isUpdate) {
        // --- UPDATE ---
        $params[':due_id'] = $dueId;

        // ****** CONFIRMAR QUE nome_exportador = :nome_exportador ESTÁ AQUI ******
        $sql = "UPDATE due SET
                    cnpj_cpf_exportador = :cnpj_cpf, nome_exportador = :nome_exportador,
                    forma_exportacao = :forma_exportacao, tipo_doc_fiscal = :tipo_doc_fiscal,
                    moeda_negociacao_codigo = :moeda, ruc = :ruc,
                    situacao_especial_despacho = :situacao_especial, exportacao_consorciada = :export_cons,
                    unidade_rfb_despacho_codigo = :unidade_rfb_d, recinto_aduaneiro_despacho_codigo = :recinto_d,
                    unidade_rfb_embarque_codigo = :unidade_rfb_e, recinto_aduaneiro_embarque_codigo = :recinto_e,
                    via_especial_transporte = :via_especial, informacoes_complementares = :info_compl,
                    itens_json = :items_json
                WHERE due_id = :due_id";

        $stmt = $pdo->prepare($sql);
        $success = $stmt->execute($params);
        $finalDueId = $dueId;
        $message = 'DU-E atualizada com sucesso!';

    } else {
        // --- INSERT ---
        $novoDueId = gerarProximoDueId($pdo);
        $params[':due_id'] = $novoDueId;

        // ****** CONFIRMAR QUE nome_exportador e :nome_exportador ESTÃO AQUI ******
        $sql = "INSERT INTO due (
                    due_id, cnpj_cpf_exportador, nome_exportador, forma_exportacao, tipo_doc_fiscal,
                    moeda_negociacao_codigo, ruc, situacao_especial_despacho, exportacao_consorciada,
                    unidade_rfb_despacho_codigo, recinto_aduaneiro_despacho_codigo,
                    unidade_rfb_embarque_codigo, recinto_aduaneiro_embarque_codigo,
                    via_especial_transporte, informacoes_complementares, itens_json
                ) VALUES (
                    :due_id, :cnpj_cpf, :nome_exportador, :forma_exportacao, :tipo_doc_fiscal,
                    :moeda, :ruc, :situacao_especial, :export_cons,
                    :unidade_rfb_d, :recinto_d,
                    :unidade_rfb_e, :recinto_e,
                    :via_especial, :info_compl, :items_json
                )";

        $stmt = $pdo->prepare($sql);
        $success = $stmt->execute($params);
        $finalDueId = $novoDueId;
        $message = 'DU-E salva com sucesso!';
    }

    if ($success) {
        $pdo->commit();
        error_log("salvar_due.php: Sucesso $logOperation para DUE ID: $finalDueId");
        enviarRespostaJson(['success' => true, 'message' => $message, 'due_id' => $finalDueId]);
    } else {
        $pdo->rollBack();
        $errorInfo = $stmt->errorInfo();
        error_log("salvar_due.php - Erro SQL ($logOperation) para DUE ID: " . ($finalDueId ?? 'Novo') . " - ErrorInfo: " . implode(" | ", $errorInfo));
        // Mensagem de erro mais genérica para o usuário, mas específica no log
        enviarRespostaJson(['success' => false, 'message' => 'Erro ao executar a operação no banco de dados.'], 500);
    }

} catch (PDOException $e) {
    if ($pdo->inTransaction()) { $pdo->rollBack(); }
    error_log("salvar_due.php - Exceção PDO na transação: " . $e->getMessage()); // Log mais conciso
    enviarRespostaJson(['success' => false, 'message' => 'Erro de banco de dados durante a operação.'], 500); // Mensagem genérica

} catch (Throwable $e) { // Captura outros erros (ex: RuntimeException do gerarId)
    if ($pdo->inTransaction()) { $pdo->rollBack(); }
    error_log("salvar_due.php - Erro geral/Exceção não PDO: " . $e->getMessage());
    enviarRespostaJson(['success' => false, 'message' => 'Erro interno no servidor durante a operação.'], 500); // Mensagem genérica
}