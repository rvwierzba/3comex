<?php
// Exemplo: painel-adm/due/formulario_due.php (ou o nome do seu ficheiro de formulário)
define('NOME_FICHEIRO_PHP_FORM_DUE', basename(__FILE__));
error_log("LOG DEBUG >> [" . NOME_FICHEIRO_PHP_FORM_DUE . "] INÍCIO DA EXECUÇÃO @ " . date("Y-m-d H:i:s"));

// Ativar todos os erros para depuração
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ajuste o caminho para o seu ficheiro de conexão
// Se este ficheiro está em painel-adm/due/, e conexao.php na raiz do projeto:
require_once("../../conexao.php");

$pdo_ok = false;
if (!isset($pdo) || !($pdo instanceof PDO)) {
    error_log("[" . NOME_FICHEIRO_PHP_FORM_DUE . "] ERRO FATAL: Conexão PDO (\$pdo) NÃO definida ou inválida. Verifique conexao.php.");
    die("<p class='alert alert-danger m-3'>Erro Crítico: Falha na conexão com o banco de dados. Contacte o administrador.</p>");
} else {
    error_log("[" . NOME_FICHEIRO_PHP_FORM_DUE . "] Conexão PDO OK.");
    $pdo_ok = true;
}

// --- LÓGICA PARA CARREGAR DADOS DA DU-E (SE ESTIVER A EDITAR) ---
$id_due_edicao = $_GET['id'] ?? null;
$dados_due_existente_json_para_js = "[]"; // Default para nova DU-E
$due_data_db_principal = null; // Para preencher campos do formulário principal na edição

if ($id_due_edicao && $pdo_ok) {
    error_log("[" . NOME_FICHEIRO_PHP_FORM_DUE . "] Modo Edição. Tentando carregar DU-E ID: " . htmlspecialchars($id_due_edicao));
    // **IMPLEMENTE AQUI A SUA LÓGICA REAL PARA BUSCAR OS DADOS DA DU-E E SEUS ITENS DO BANCO**
    // Exemplo (substitua pela sua lógica):
    /*
    try {
        $stmt_due_principal = $pdo->prepare("SELECT * FROM sua_tabela_due_principal WHERE id_due_coluna = :id_due");
        $stmt_due_principal->bindParam(':id_due', $id_due_edicao, PDO::PARAM_INT);
        $stmt_due_principal->execute();
        $due_data_db_principal = $stmt_due_principal->fetch(PDO::FETCH_ASSOC);

        $stmt_items = $pdo->prepare("SELECT * FROM sua_tabela_due_itens WHERE id_due_fk_coluna = :id_due ORDER BY nItem_coluna_xml");
        $stmt_items->bindParam(':id_due', $id_due_edicao, PDO::PARAM_INT);
        $stmt_items->execute();
        $items_db = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

        if ($due_data_db_principal && !empty($items_db)) {
            $nf_data_para_js = ['chaveAcesso' => $due_data_db_principal['due_chave_nf_principal'] ?? 'EDICAO-' . $id_due_edicao, 'emitente' => ['nome' => $due_data_db_principal['due_nome_exportador'] ?? '']];
            $dados_formatados_para_js = [['nf' => $nf_data_para_js, 'items' => $items_db]];
            $dados_due_existente_json_para_js = json_encode($dados_formatados_para_js);
            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log("[" . NOME_FICHEIRO_PHP_FORM_DUE . "] ERRO json_encode para DADOS DE EDIÇÃO: " . json_last_error_msg());
                $dados_due_existente_json_para_js = "[]";
            }
        } else {
            error_log("[" . NOME_FICHEIRO_PHP_FORM_DUE . "] DU-E ID " . htmlspecialchars($id_due_edicao) . " não encontrada ou sem itens.");
        }
    } catch (PDOException $e) {
        error_log("[" . NOME_FICHEIRO_PHP_FORM_DUE . "] EXCEÇÃO PDO ao carregar DU-E para edição (ID: " . htmlspecialchars($id_due_edicao) . "): " . $e->getMessage());
    }
    */
} else if (!$id_due_edicao) {
    error_log("[" . NOME_FICHEIRO_PHP_FORM_DUE . "] Modo Criação (Nova DU-E).");
}

// --- CONSULTAS PHP PARA DADOS GLOBAIS (PAÍSES, ENQUADRAMENTOS, INCOTERMS) ---
$paises_form_php = [];
$enquadramentos_form_php = [];
$incoterms_form_php = [];

if ($pdo_ok) {
    try {
        // 1. Consultar Países
        error_log("[" . NOME_FICHEIRO_PHP_FORM_DUE . "] Consultando PAÍSES...");
        $stmt_paises = $pdo->query("SELECT CODIGO_NUMERICO, NOME FROM paises ORDER BY NOME");
        if ($stmt_paises === false) {
            $errorInfo = $pdo->errorInfo();
            error_log("[" . NOME_FICHEIRO_PHP_FORM_DUE . "] ERRO FATAL SQL PAÍSES. query() retornou false. PDO: SQLSTATE[" . $errorInfo[0] . "] Code[" . $errorInfo[1] . "] Msg[" . $errorInfo[2] . "]");
        } else {
            $paises_form_php = $stmt_paises->fetchAll(PDO::FETCH_ASSOC);
            error_log("[" . NOME_FICHEIRO_PHP_FORM_DUE . "] PAÍSES consultados: " . count($paises_form_php) . " encontrados.");
            if (empty($paises_form_php)) {
                error_log("[" . NOME_FICHEIRO_PHP_FORM_DUE . "] ALERTA: Nenhum país retornado. Verifique tabela 'paises' e permissões.");
            }
        }

        // 2. Consultar Enquadramentos (adapte nomes)
        $stmt_enq = $pdo->query("SELECT CODIGO, DESCRICAO FROM enquadramentos_operacao_exportacao ORDER BY CODIGO");
        if($stmt_enq) $enquadramentos_form_php = $stmt_enq->fetchAll(PDO::FETCH_ASSOC);
        else error_log("[" . NOME_FICHEIRO_PHP_FORM_DUE . "] ERRO SQL ENQUADRAMENTOS: " . implode(", ", $pdo->errorInfo()));
        error_log("[" . NOME_FICHEIRO_PHP_FORM_DUE . "] Enquadramentos: " . count($enquadramentos_form_php));

        // 3. Consultar Incoterms (adapte nomes)
        $stmt_inc = $pdo->query("SELECT Sigla, Descricao FROM incoterms ORDER BY Sigla");
        if($stmt_inc) $incoterms_form_php = $stmt_inc->fetchAll(PDO::FETCH_ASSOC);
        else error_log("[" . NOME_FICHEIRO_PHP_FORM_DUE . "] ERRO SQL INCOTERMS: " . implode(", ", $pdo->errorInfo()));
        error_log("[" . NOME_FICHEIRO_PHP_FORM_DUE . "] Incoterms: " . count($incoterms_form_php));

    } catch (PDOException $e) {
        error_log("[" . NOME_FICHEIRO_PHP_FORM_DUE . "] EXCEÇÃO PDO dados auxiliares: " . $e->getMessage());
        $paises_form_php = []; $enquadramentos_form_php = []; $incoterms_form_php = [];
    }
}

// --- PREPARAR JSON PARA JAVASCRIPT ---
$paises_json_para_js = json_encode($paises_form_php);
if (json_last_error() !== JSON_ERROR_NONE) { error_log("[" . NOME_FICHEIRO_PHP_FORM_DUE . "] ERRO json_encode PAÍSES: " . json_last_error_msg()); $paises_json_para_js = "[]"; }

$enquadramentos_json_para_js = json_encode($enquadramentos_form_php);
if (json_last_error() !== JSON_ERROR_NONE) { error_log("[" . NOME_FICHEIRO_PHP_FORM_DUE . "] ERRO json_encode ENQUADRAMENTOS: " . json_last_error_msg()); $enquadramentos_json_para_js = "[]"; }

$incoterms_json_para_js = json_encode($incoterms_form_php);
if (json_last_error() !== JSON_ERROR_NONE) { error_log("[" . NOME_FICHEIRO_PHP_FORM_DUE . "] ERRO json_encode INCOTERMS: " . json_last_error_msg()); $incoterms_json_para_js = "[]"; }

error_log("[" . NOME_FICHEIRO_PHP_FORM_DUE . "] Preparando HTML. JSON Países (início): " . substr($paises_json_para_js, 0, 100));
echo "\n";
?>

<div id="conteudo-do-formulario-due" class="container-fluid mt-3 mb-5">
    <h4><?php echo $id_due_edicao ? 'Editar DU-E: ' . htmlspecialchars($id_due_edicao) : 'Nova DU-E'; ?> (Renderizado por <?php echo htmlspecialchars(NOME_FICHEIRO_PHP_FORM_DUE); ?>)</h4>
    <hr>
    
    <form id="dueForm">
        <input type="hidden" id="due_id_hidden" name="due_id_hidden" value="<?php echo htmlspecialchars($id_due_edicao ?? ''); ?>">
        
        <div class="card mb-3">
            <div class="card-header">Dados Gerais da DU-E</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="text-cnpj-cpf-select" class="form-label">CNPJ/CPF Exportador:</label>
                        <input type="text" class="form-control form-control-sm" id="text-cnpj-cpf-select" name="cnpj_exportador" value="<?php echo htmlspecialchars($due_data_db_principal['due_exportador_cnpj'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="nomeCliente" class="form-label">Nome Exportador:</label>
                        <input type="text" class="form-control form-control-sm" id="nomeCliente" name="nome_exportador" value="<?php echo htmlspecialchars($due_data_db_principal['due_exportador_nome'] ?? ''); ?>" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="info-compl" class="form-label">Informações Complementares da DU-E:</label>
                    <textarea class="form-control form-control-sm" id="info-compl" name="info_complementar_geral" rows="3"><?php echo htmlspecialchars($due_data_db_principal['info_complementar_geral'] ?? ''); ?></textarea>
                </div>
                </div>
        </div>

        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                Itens da DU-E
                <div>
                    <label for="xml-files" class="btn btn-sm btn-info mb-0 me-2" title="Carregar um ou mais arquivos XML de NF-e">
                        <i class="bi bi-upload me-1"></i> Carregar XML(s)
                    </label>
                    <input type="file" id="xml-files" multiple accept=".xml,text/xml" style="display: none;">
                    <button type="button" id="batchEditButton" class="btn btn-sm btn-warning mb-0" title="Editar campos selecionados para todos os itens de uma vez">
                        <i class="bi bi-pencil-square me-1"></i> Editar em Lote
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div id="uploadStatus" class="mb-2"></div>
                <div id="spinner" class="text-center mb-2" style="display: none;">
                    <div class="spinner-border text-primary" role="status"><span class="visually-hidden">A processar...</span></div>
                </div>
                <div class="table-responsive">
                    <table id="notasFiscaisTable" class="table table-sm table-bordered table-hover align-middle">
                        <thead>
                            <tr>
                                <th style="width:15%;">Chave NF-e</th>
                                <th class="text-center" style="width:5%;">Item NF</th>
                                <th style="width:10%;">NCM</th>
                                <th style="width:30%;">Descrição Mercadoria (NF-e)</th>
                                <th style="width:15%;">Importador (NF-e)</th>
                                <th style="width:15%;">País Destino (Item)</th>
                                <th class="text-center" style="width:5%;">Ações</th>
                                <th class="text-center" style="width:5%;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td colspan="8" class="text-center text-muted fst-italic">(Carregue arquivos XML ou adicione itens manualmente)</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-4 mb-5 text-center">
            <button type="button" id="salvarDUE" class="btn btn-primary btn-lg me-2">
                <i class="bi bi-save me-1"></i> Salvar Rascunho DU-E
            </button>
            <button type="button" id="enviarDUE" class="btn btn-success btn-lg" disabled>
                <i class="bi bi-send me-1"></i> Enviar DU-E (Simulação)
            </button>
            <a href="index.php?pag=due" class="btn btn-secondary ms-2">
                <i class="bi bi-list-ul me-1"></i> Voltar para Listagem
            </a>
        </div>
    </form>
</div>

<div class="modal fade" id="itemDetailsModal" tabindex="-1" aria-labelledby="itemDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="itemDetailsModalLabel">Detalhes do Item da DU-E</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div class="text-center p-5"><div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"><span class="visually-hidden">A carregar...</span></div></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" id="saveItemDetails"><i class="bi bi-check-lg me-1"></i>Salvar Alterações do Item</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="batchEditModal" tabindex="-1" aria-labelledby="batchEditModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title" id="batchEditModalLabel">Edição em Lote dos Itens</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        <form id="batchEditForm">
            <div class="alert alert-info small">Preencha apenas os campos que deseja alterar para todos os itens carregados.</div>
            <div class="mb-3 row">
                <label for="batchIncotermSelectModal" class="col-sm-4 col-form-label">Condição de Venda (Incoterm):</label>
                <div class="col-sm-8">
                    <select id="batchIncotermSelectModal" name="batch_condicao_venda" class="form-select form-select-sm">
                        <option value="">Não alterar</option>
                        <?php if(!empty($incoterms_form_php)): ?>
                            <?php foreach($incoterms_form_php as $inc): ?>
                                <option value="<?php echo htmlspecialchars($inc['Sigla']); ?>"><?php echo htmlspecialchars($inc['Sigla'] . ' - ' . $inc['Descricao']); ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
            <div class="mb-3 row">
                <label for="batchPaisDestinoInputModal" class="col-sm-4 col-form-label">País Destino Final (DU-E):</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control form-control-sm" id="batchPaisDestinoInputModal" name="batch_pais_destino_nome" list="paisesDestinoListBatchModal" placeholder="Digite para buscar ou selecione...">
                    <datalist id="paisesDestinoListBatchModal">
                        <?php if(!empty($paises_form_php)): ?>
                            <?php foreach($paises_form_php as $pais): ?>
                                <option value="<?php echo htmlspecialchars($pais['NOME']);?>" data-codigo="<?php echo htmlspecialchars($pais['CODIGO_NUMERICO']);?>"></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </datalist>
                </div>
            </div>
            <?php for ($i = 1; $i <= 4; $i++): ?>
            <div class="mb-3 row">
                <label for="batchEnquadramento<?php echo $i; ?>SelectModal" class="col-sm-4 col-form-label"><?php echo $i; ?>º Enquadramento:</label>
                <div class="col-sm-8">
                    <select id="batchEnquadramento<?php echo $i; ?>SelectModal" name="batch_enquadramento<?php echo $i; ?>" class="form-select form-select-sm">
                        <option value="">Não alterar</option>
                         <?php if(!empty($enquadramentos_form_php)): ?>
                            <?php foreach($enquadramentos_form_php as $enq): ?>
                                <option value="<?php echo htmlspecialchars($enq['CODIGO']); ?>"><?php echo htmlspecialchars($enq['CODIGO'] . ' - ' . $enq['DESCRICAO']); ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <option value="99999">99999 - SEM ENQUADRAMENTO</option>
                    </select>
                </div>
            </div>
            <?php endfor; ?>
            <div class="mb-3 row">
                <label class="col-sm-4 col-form-label">Acordo CCPT/CCROM:</label>
                <div class="col-sm-8 pt-2">
                    <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="batchCcptCcromModal" id="batchCcptCcromAlterarModal" value="" checked><label class="form-check-label" for="batchCcptCcromAlterarModal">Não alterar</label></div>
                    <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="batchCcptCcromModal" id="batchCcptCcromNenhumModal" value="NA"><label class="form-check-label" for="batchCcptCcromNenhumModal">Nenhum</label></div>
                    <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="batchCcptCcromModal" id="batchCcptCcromCCPTModal" value="CCPT"><label class="form-check-label" for="batchCcptCcromCCPTModal">CCPT</label></div>
                    <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="batchCcptCcromModal" id="batchCcptCcromCCROMModal" value="CCROM"><label class="form-check-label" for="batchCcptCcromCCROMModal">CCROM</label></div>
                </div>
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-warning" id="saveBatchEdit"><i class="bi bi-check2-all me-1"></i>Aplicar Alterações em Lote</button>
      </div>
    </div>
  </div>
</div>

<div id="toast-container" class="position-fixed top-0 end-0 p-3" style="z-index: 1090"></div>


<script id="script-dados-globais-gerados-nesta-pagina">
    console.log('[<?php echo NOME_FICHEIRO_PHP_FORM_DUE; ?> - inline script] INÍCIO: Tentando definir variáveis JavaScript globais...');

    window.paisesData = <?php echo $paises_json_para_js; ?>;
    if (typeof window.paisesData !== "undefined" && window.paisesData && Array.isArray(window.paisesData)) {
        console.log('[<?php echo NOME_FICHEIRO_PHP_FORM_DUE; ?> - inline script] SUCESSO: window.paisesData definido com ' + window.paisesData.length + ' países. Primeiro país (exemplo):', window.paisesData.length > 0 ? JSON.stringify(window.paisesData[0]) : 'Array vazio');
    } else {
        console.error('[<?php echo NOME_FICHEIRO_PHP_FORM_DUE; ?> - inline script] FALHA CRÍTICA AO DEFINIR window.paisesData. Verifique os logs do PHP para erros na consulta ou no json_encode. Valor que o PHP tentou enviar para paisesData:', <?php echo json_encode($paises_json_para_js); ?>);
    }

    window.enquadramentosData = <?php echo $enquadramentos_json_para_js; ?>;
    console.log('[<?php echo NOME_FICHEIRO_PHP_FORM_DUE; ?> - inline script] window.enquadramentosData definido com ' + (typeof window.enquadramentosData !== "undefined" && window.enquadramentosData ? window.enquadramentosData.length : 0) + ' registos.');

    window.incotermsData = <?php echo $incoterms_json_para_js; ?>;
    console.log('[<?php echo NOME_FICHEIRO_PHP_FORM_DUE; ?> - inline script] window.incotermsData definido com ' + (typeof window.incotermsData !== "undefined" && window.incotermsData ? window.incotermsData.length : 0) + ' registos.');
    
    // Seus logs anteriores indicam que window.processedNFData é definido pelo index.php.
    // Se este ficheiro (ex: formulario_due.php) for o único responsável por definir TODOS os dados para o main.mjs
    // quando está a ser incluído pelo index.php, então defina-o aqui.
    // Se o index.php já o define, esta linha pode ser redundante ou causar conflito se a lógica de carregamento de edição for diferente.
    // Por segurança, se o index.php já o faz, é melhor não redefinir aqui, a menos que tenha a certeza.
    if (typeof window.processedNFData === 'undefined') { // Só define se o index.php não o fez
        window.processedNFData = <?php echo $dados_due_existente_json_para_js; ?>;
        console.log('[<?php echo NOME_FICHEIRO_PHP_FORM_DUE; ?> - inline script] window.processedNFData (definido por este ficheiro):', window.processedNFData);
    } else {
        console.log('[<?php echo NOME_FICHEIRO_PHP_FORM_DUE; ?> - inline script] window.processedNFData já estava definido (provavelmente pelo index.php). Não foi sobrescrito.');
    }


    // Para o caso de edição, se os dados principais do formulário (não os itens) vierem do PHP
    // e não estiverem dentro da estrutura de processedNFData.
    <?php if ($id_due_edicao && $due_data_db_principal): ?>
    window.dueDataPrincipalPHP = <?php echo json_encode($due_data_db_principal); ?>;
    console.log('[<?php echo NOME_FICHEIRO_PHP_FORM_DUE; ?> - inline script] window.dueDataPrincipalPHP (para edição do form principal):', window.dueDataPrincipalPHP);
    <?php else: ?>
    window.dueDataPrincipalPHP = null;
    <?php endif; ?>


    console.log('[<?php echo NOME_FICHEIRO_PHP_FORM_DUE; ?> - inline script] FIM: Variáveis globais deste ficheiro foram definidas (ou tentadas).');
</script>

<?php
// O seu index.php (router principal) deve incluir o main.mjs DEPOIS de incluir este ficheiro
// (ex: formulario_due.php) para que as variáveis acima estejam disponíveis para o main.mjs.
error_log("[" . NOME_FICHEIRO_PHP_FORM_DUE . "] FIM DA EXECUÇÃO DESTE FICHEIRO (PHP) @ " . date("Y-m-d H:i:s"));
?>
