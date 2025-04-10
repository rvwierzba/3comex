<?php
// --- Arquivo: painel-adm/due/inserir.php ---
// --- Função: Formulário Add/Edit (VERSÃO COMPLETA FINAL CORRIGIDA) ---

// Configuração de Erros (Logar, não exibir)
error_reporting(E_ALL);
ini_set('display_errors', 0); // 0 Para não quebrar a tela com warnings/notices
ini_set('log_errors', 1);
// Verifique o log: C:\xampp\php\logs\php_error_log

// --- INCLUDES ---
@require_once __DIR__ . "/../../conexao.php"; // 2 níveis acima
@require_once __DIR__ . "/campos.php";       // Mesma pasta 'due'

// --- Verificações Iniciais ---
if (!isset($pdo)) { die("<p class='alert alert-danger m-3'>Erro Crítico: Conexão falhou (inserir.php).</p>"); }
if (!isset($pagina) || $pagina !== 'due') { die("<p class='alert alert-danger m-3'>Erro Crítico: 'campos.php' inválido (inserir.php).</p>"); }
if (!isset($campo1) || !isset($campo16)) { die("<p class='alert alert-danger m-3'>Erro Crítico: Variáveis \$campo1 ou \$campo16 não carregadas (inserir.php).</p>"); }

// --- LÓGICA PARA CARREGAR DADOS ---
$edit_id = null; $is_editing = false; $is_view_mode = false; $due_data = []; $itens_json_data = '[]'; $page_title = 'Nova DU-E';

// Verifica ID e Modo (Prioriza GET)
if (isset($_GET['id'])) {
    $edit_id = $_GET['id']; $is_editing = true;
    if (isset($_GET['mode']) && $_GET['mode'] == 'view') { $is_view_mode = true; }
} elseif (isset($_POST['edit_id'])) { $edit_id = $_POST['edit_id']; $is_editing = true; } // Fallback

// Busca dados se for Edição ou Visualização e ID válido
if ($is_editing && !empty($edit_id)) {
    $edit_id_sanitized = null;
    // Validar formato básico (Ex: DUE12345-12)
    if (preg_match('/^DUE\d{5}-\d{2}$/', $edit_id)) {
        $edit_id_sanitized = $edit_id;
    } else { error_log("[INSERIR.PHP] ERRO: Formato de ID inválido: '$edit_id'"); }

    if ($edit_id_sanitized) {
        try {
            $sql_edit = "SELECT * FROM {$pagina} WHERE {$campo1} = :id";
            $stmt_edit = $pdo->prepare($sql_edit);
            $stmt_edit->bindParam(':id', $edit_id_sanitized, PDO::PARAM_STR);
            $stmt_edit->execute();
            $due_data = $stmt_edit->fetch(PDO::FETCH_ASSOC);

            if ($due_data) {
                $page_title = ($is_view_mode ? 'Visualizar ' : 'Editar ') . 'DU-E: ' . htmlspecialchars($due_data[$campo1]);
                $itens_json_data = $due_data[$campo16] ?? '[]';
                // Log CRUCIAL para verificar se os dados corretos estão sendo carregados
                error_log("[INSERIR.PHP - FINAL] Dados carregados OK para ID $edit_id_sanitized. Conteúdo \$due_data: " . print_r($due_data, true));
            } else {
                error_log("[INSERIR.PHP - FINAL] ALERTA - ID '$edit_id_sanitized' NÃO encontrado no banco.");
                $is_editing = $is_view_mode = false; $edit_id = null; $due_data = []; // Reseta
            }
        } catch (Exception $e) {
            error_log("ERRO DB busca ID $edit_id_sanitized: ".$e->getMessage());
            $edit_id = null; $is_editing = $is_view_mode = false; $due_data = []; // Reseta
            // Evita die() para não dar tela branca total, mas loga o erro.
             echo "<p class='alert alert-danger m-3'>Erro no Banco de Dados ao carregar DU-E. Verifique os logs.</p>";
        }
    } else {
         // ID inválido, reseta para modo de inserção
         $edit_id = null; $is_editing = $is_view_mode = false; $due_data = [];
         echo "<p class='alert alert-warning m-3'>ID inválido fornecido.</p>";
    }
} else {
    // Modo Inserção
    error_log("[INSERIR.PHP - FINAL] Modo Inserção (sem ID válido).");
}

// --- PRE-FETCH DROPDOWNS ---
$incoterms = []; try{ $incoterms = $pdo->query('SELECT Sigla, Descricao FROM incoterms ORDER BY Sigla')->fetchAll(PDO::FETCH_ASSOC); } catch(Exception $e){}
$enquadramentos = []; try{ $enquadramentos = $pdo->query('SELECT CODIGO, DESCRICAO FROM enquadramento ORDER BY CODIGO')->fetchAll(PDO::FETCH_ASSOC); } catch(Exception $e){}
// CORRIGIDO query paises (sem CodigoBACEN no SELECT principal)
// ATENÇÃO: O modal Batch Edit ainda tenta usar CodigoBACEN, ajuste-o se necessário ou garanta que a coluna exista.
$paises = []; try{ $paises = $pdo->query('SELECT Sigla_is03, Nome, CodigoBACEN FROM paises ORDER BY Nome')->fetchAll(PDO::FETCH_ASSOC); } catch(Exception $e){error_log("Erro fetch Paises: ".$e->getMessage());}
$moedas = []; try{ $moedas = $pdo->query('SELECT Codigo, Nome FROM moeda ORDER BY Nome')->fetchAll(PDO::FETCH_ASSOC); } catch(Exception $e){}
$unidades_rfb = []; try{ $unidades_rfb = $pdo->query('SELECT Codigo, Nome FROM unidades_rfb ORDER BY Nome')->fetchAll(PDO::FETCH_ASSOC); } catch(Exception $e){}
$recintos = []; try{ $recintos = $pdo->query('SELECT codigo, Nome FROM recinto_aduaneiro ORDER BY Nome')->fetchAll(PDO::FETCH_ASSOC); } catch(Exception $e){}


// --- Funções Auxiliares PHP ---
function get_value($field_key, $data_array, $default = '') { return htmlspecialchars($data_array[$field_key] ?? $default, ENT_QUOTES, 'UTF-8'); }
function is_checked($field_key, $value_to_check, $data_array) { return isset($data_array[$field_key]) && $data_array[$field_key] == $value_to_check ? 'checked' : ''; }
function is_selected($field_key, $value_to_check, $data_array) { return isset($data_array[$field_key]) && $data_array[$field_key] == $value_to_check ? 'selected' : ''; }
function is_switch_checked($field_key, $data_array) { return !empty($data_array[$field_key]); }
$disable_attr = $is_view_mode ? 'readonly' : '';
$disable_attr_select = $is_view_mode ? 'disabled' : '';

// ----- FIM DO BLOCO PHP INICIAL -----
?>
<div class="container mt-4">
    <h2 class="mb-4 text-center"><?php echo $page_title; ?></h2>
    <div class="mb-3"> <a href="index.php?pag=due" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i> Voltar</a> </div>

    <ul class="nav nav-tabs" id="dueTabs">
        <li class="nav-item"><a class="nav-link active" id="tab-link-1" data-bs-toggle="tab" data-bs-target="#aba1" href="#aba1">Dados gerais</a></li>
        <li class="nav-item"><a class="nav-link" id="tab-link-upload" data-bs-toggle="tab" data-bs-target="#abaUpload" href="#abaUpload">Upload XML</a></li>
        <li class="nav-item"><a class="nav-link" id="tab-link-3" data-bs-toggle="tab" data-bs-target="#aba3" href="#aba3">Itens da DU-E</a></li>
    </ul>

    <div class="tab-content" id="dueTabsContent">
        <div class="tab-pane fade show active" id="aba1" role="tabpanel" aria-labelledby="tab-link-1">
            <form id="dueForm">
                 <input type="hidden" id="due_id_hidden" name="due_id" value="<?php echo htmlspecialchars($edit_id ?? '', ENT_QUOTES, 'UTF-8'); ?>">

                 <div class="card mb-4">
                    <div class="card-header">Informações Gerais</div>
                    <div class="card-body">
                        <div class="row g-3">
                             <div class="col-md-6 form-group">
                                <label for="text-cnpj-cpf-select" class="form-label">CNPJ/CPF Exp.:</label>
                                <input class="form-control" type="text" id="text-cnpj-cpf-select" name="cnpj-cpf" value="<?php echo get_value($campo2, $due_data); ?>" readonly>
                             </div>
                             <div class="col-md-6 form-group">
                                 <label for="nomeCliente" class="form-label">Nome Exp.:</label>
                                 <input type="text" id="nomeCliente" name="nomeCliente" class="form-control" value="<?php echo get_value($campo3, $due_data); ?>" readonly>
                             </div>
                        </div>
                        <div class="row g-3 mt-3">
                              <div class="col-md-6 form-group">
                                 <h6>Forma Exportação</h6>
                                 <?php $current_val = $due_data[$campo4] ?? 'Por conta própria'; ?>
                                 <div class="form-check"> <input class="form-check-input" type="radio" id="por-conta-propria" name="forma-export" value="Por conta própria" <?php echo ($current_val == 'Por conta própria')?'checked':''; ?> <?php echo $disable_attr_select;?>> <label class="form-check-label" for="por-conta-propria">Própria</label> </div>
                                 <div class="form-check"> <input class="form-check-input" type="radio" id="p-conta-ordem-terceiros" name="forma-export" value="Por conta ou ordem de terceiros" <?php echo ($current_val == 'Por conta ou ordem de terceiros')?'checked':''; ?> <?php echo $disable_attr_select;?>> <label class="form-check-label" for="p-conta-ordem-terceiros">Conta/Ordem 3º</label> </div>
                                 <div class="form-check"> <input class="form-check-input" type="radio" id="p-op-rm-post-ou-remss" name="forma-export" value="Por operador de remessa postal ou expressa" <?php echo ($current_val == 'Por operador de remessa postal ou expressa')?'checked':''; ?> <?php echo $disable_attr_select;?>> <label class="form-check-label" for="p-op-rm-post-ou-remss">Op. Remessa</label> </div>
                              </div>
                              <div class="col-md-6 form-group">
                                  <h6>Tipo Doc Fiscal</h6>
                                  <?php $current_val = $due_data[$campo5] ?? 'Nota fiscal eletronica'; ?>
                                  <div class="form-check"> <input class="form-check-input" type="radio" id="nfe" name="tp-doc-amp-merc-export" value="Nota fiscal eletronica" <?php echo ($current_val == 'Nota fiscal eletronica')?'checked':''; ?> <?php echo $disable_attr_select;?>> <label class="form-check-label" for="nfe">NF-e</label> </div>
                                  <div class="form-check"> <input class="form-check-input" type="radio" id="nf-form" name="tp-doc-amp-merc-export" value="Nota fiscal formulario" <?php echo ($current_val == 'Nota fiscal formulario')?'checked':''; ?> <?php echo $disable_attr_select;?>> <label class="form-check-label" for="nf-form">NF Formulário</label> </div>
                                  <div class="form-check"> <input class="form-check-input" type="radio" id="s-nf" name="tp-doc-amp-merc-export" value="Sem nota fiscal" <?php echo ($current_val == 'Sem nota fiscal')?'checked':''; ?> <?php echo $disable_attr_select;?>> <label class="form-check-label" for="s-nf">Sem NF</label> </div>
                               </div>
                         </div>
                         <div class="row g-3 mt-3">
                             <div class="col-md-6 form-group">
                                 <label for="text-moeda" class="form-label">Moeda Negociação:</label>
                                 <select id="text-moeda" name="moeda" class="form-select" <?php echo $disable_attr_select; ?>>
                                     <option value="">Selecione...</option>
                                     <?php if (!empty($moedas)): foreach($moedas as $row): ?>
                                        <option value="<?php echo htmlspecialchars($row['Codigo']); ?>" <?php echo is_selected($campo6, $row['Codigo'], $due_data); ?>>
                                             <?php echo htmlspecialchars($row['Codigo']); ?> - <?php echo htmlspecialchars($row['Nome']); ?>
                                         </option>
                                     <?php endforeach; endif; ?>
                                 </select>
                             </div>
                             <div class="col-md-6 form-group">
                                 <label for="ruc" class="form-label">RUC:</label>
                                 <input type="text" class="form-control" id="ruc" name="ruc" placeholder="Opcional" maxlength="35" value="<?php echo get_value($campo7, $due_data); ?>" <?php echo $disable_attr; ?>>
                             </div>
                         </div>
                        <div class="row g-3 mt-3">
                             <div class="col-md-6 form-group">
                                 <label for="situacao-espec-despacho" class="form-label">Situação Especial Despacho:</label>
                                 <select name="situacao-espec-despacho" class="form-select" id="situacao-espec-despacho" <?php echo $disable_attr_select; ?>>
                                     <option value="" <?php echo is_selected($campo8, '', $due_data); ?>>Nenhuma</option>
                                     <option value="DU-E a posteriori" <?php echo is_selected($campo8, 'DU-E a posteriori', $due_data); ?>>DU-E a posteriori</option>
                                     <option value="Embarque antecipado" <?php echo is_selected($campo8, 'Embarque antecipado', $due_data); ?>>Embarque antecipado</option>
                                     <option value="Exportação sem saída da mercadoria do país" <?php echo is_selected($campo8, 'Exportação sem saída da mercadoria do país', $due_data); ?>>Exportação sem saída...</option>
                                 </select>
                             </div>
                             <div class="col-md-6 form-group d-flex align-items-center pt-3">
                                 <div class="form-check form-switch">
                                     <input class="form-check-input" type="checkbox" role="switch" id="export-cons" name="export-cons" <?php echo is_switch_checked($campo9, $due_data)?'checked':''; ?> <?php echo $disable_attr_select; ?>>
                                     <label class="form-check-label" for="export-cons">Exportação Consorciada</label>
                                 </div>
                             </div>
                         </div>
                         <hr class="my-4">
                         <div> <h5 id="lbl-local-despacho">Local Despacho</h5> <div class="row g-3"> <div class="col-md-6 form-group"> <label for="text-campo-de-pesquisa-unidades-rfb-d" class="form-label">Unidade RFB:</label> <input id="text-campo-de-pesquisa-unidades-rfb-d" type="text" class="form-control" list="unidades-rfb-d-list" name="unidade_rfb_despacho" value="<?php echo get_value($campo10, $due_data); ?>" <?php echo $disable_attr;?>> <datalist id="unidades-rfb-d-list"><?php if(!empty($unidades_rfb)){foreach($unidades_rfb as $row){ echo '<option value="'.htmlspecialchars($row['Codigo']).'">'.htmlspecialchars($row['Nome']).'</option>';}}?></datalist> </div> <div class="col-md-6 form-group"> <label for="text-campo-de-pesquisa-recinto-alfandegado-d" class="form-label">Recinto:</label> <input id="text-campo-de-pesquisa-recinto-alfandegado-d" type="text" class="form-control" list="recinto-aduaneiro-d-list" name="recinto_aduaneiro_despacho" value="<?php echo get_value($campo11, $due_data); ?>" <?php echo $disable_attr;?>> <datalist id="recinto-aduaneiro-d-list"><?php if(!empty($recintos)){foreach($recintos as $row){echo '<option value="'.htmlspecialchars($row['codigo']).'">'.htmlspecialchars($row['Nome']).'</option>';}}?></datalist> </div> </div> </div>
                         <hr class="my-4">
                         <div> <h5 id="lbl-local-embarque">Local Embarque</h5> <div class="row g-3"> <div class="col-md-6 form-group"> <label for="text-campo-de-pesquisa-unidades-rfb-e" class="form-label">Unidade RFB:</label> <input id="text-campo-de-pesquisa-unidades-rfb-e" type="text" class="form-control" list="unidades-rfb-e-list" name="unidade_rfb_embarque" value="<?php echo get_value($campo12, $due_data); ?>" <?php echo $disable_attr;?>> <datalist id="unidades-rfb-e-list"><?php if(!empty($unidades_rfb)){foreach($unidades_rfb as $row){echo '<option value="'.htmlspecialchars($row['Codigo']).'">'.htmlspecialchars($row['Nome']).'</option>';}}?></datalist> </div> <div class="col-md-6 form-group"> <label for="text-campo-de-pesquisa-recinto-alfandegado-e" class="form-label">Recinto:</label> <input id="text-campo-de-pesquisa-recinto-alfandegado-e" type="text" class="form-control" list="recinto-aduaneiro-e-list" name="recinto_aduaneiro_embarque" value="<?php echo get_value($campo13, $due_data); ?>" <?php echo $disable_attr;?>> <datalist id="recinto-aduaneiro-e-list"><?php if(!empty($recintos)){foreach($recintos as $row){echo '<option value="'.htmlspecialchars($row['codigo']).'">'.htmlspecialchars($row['Nome']).'</option>';}}?></datalist> </div> </div> </div>
                         <hr class="my-4">
                         <div id="complementos"> <h5 id="lbl-complementos">Complementos</h5> <div class="row g-3"> <div class="col-md-6 form-group"> <label for="via-especial-transport" class="form-label">Via Esp. Transporte:</label> <select class="form-select" id="via-especial-transport" name="via-especial-transport" <?php echo $disable_attr_select;?>><option value="" <?php echo is_selected($campo14,'',$due_data);?>>Nenhuma</option><option value="MEIOS PRÓPRIOS" <?php echo is_selected($campo14,'MEIOS PRÓPRIOS',$due_data);?>>MEIOS PRÓPRIOS</option><option value="DUTOS" <?php echo is_selected($campo14,'DUTOS',$due_data);?>>DUTOS</option><option value="LINHAS DE TRANSMISSÃO" <?php echo is_selected($campo14,'LINHAS DE TRANSMISSÃO',$due_data);?>>LINHAS DE TRANSMISSÃO</option><option value="EM MÃO" <?php echo is_selected($campo14,'EM MÃO',$due_data);?>>EM MÃOS</option><option value="POR REBOQUE" <?php echo is_selected($campo14,'POR REBOQUE',$due_data);?>>POR REBOQUE</option><option value="TRANSPORTE VICINAL FRONTEIRIÇO" <?php echo is_selected($campo14,'TRANSPORTE VICINAL FRONTEIRIÇO',$due_data);?>>TRANSPORTE VICINAL FRONTEIRIÇO</option></select> </div> <div class="col-md-6 form-group"> <label for="info-compl" class="form-label">Info Complementares:</label> <textarea class="form-control" id="info-compl" name="info-compl" rows="3" placeholder="Info adicionais" <?php echo $disable_attr;?>><?php echo get_value($campo15, $due_data);?></textarea> </div> </div> </div>
                     </div> </div> </form> </div><div class="tab-pane fade" id="abaUpload" role="tabpanel" aria-labelledby="tab-link-upload">
            <div class="card mb-4"> <div class="card-header">Upload NF-e (XML)</div> <div class="card-body"> <div class="alert alert-info small"><i class="bi bi-info-circle me-1"></i>Carregar XMLs <?php echo $is_editing ? 'substituirá' : 'adicionará'; ?> itens após salvar.</div> <div class="form-group mb-0"> <label for="xml-files" class="form-label">Selecionar Arquivos XML</label> <input type="file" id="xml-files" class="form-control" accept=".xml,text/xml,application/xml" multiple <?php echo $disable_attr_select;?>> </div> <div id="uploadStatus" class="mt-2 text-muted small"></div> </div> </div>
        </div>

        <div class="tab-pane fade" id="aba3" role="tabpanel" aria-labelledby="tab-link-3">
            <div class="card mb-4"> <div class="card-header d-flex justify-content-between align-items-center"><span>Itens da DU-E</span><button type="button" class="btn btn-secondary btn-sm" id="batchEditButton" data-bs-toggle="modal" data-bs-target="#batchEditModal" <?php echo $disable_attr_select;?>><i class="bi bi-pencil-square me-1"></i> Lote</button></div> <div class="card-body"><div id="tabelaContainer" class="table-responsive"><table class="table table-bordered table-hover table-sm" id="notasFiscaisTable"><thead class="thead-light"><tr><th>Chave NF-e</th><th>Item</th><th>NCM</th><th>Descrição</th><th>Importador</th><th>País</th><th>Ações</th><th>Status</th></tr></thead><tbody></tbody></table></div></div> </div>
        </div>

    </div> <div class="form-group mt-4 text-center">
        <?php if (!$is_view_mode): ?>
            <button type="button" id="salvarDUE" class="btn btn-success me-2"><i class="bi bi-save me-1"></i> <?php echo $is_editing ? 'Atualizar DU-E' : 'Salvar Nova DU-E'; ?></button>
            <button type="button" id="enviarDUE" class="btn btn-primary" <?php echo $is_editing ? '' : 'disabled'; ?>><i class="bi bi-send me-1"></i> Enviar DU-E</button>
        <?php else: ?>
            <a href="index.php?pag=due" class="btn btn-secondary"><i class="bi bi-arrow-left me-1"></i> Voltar</a>
        <?php endif; ?>
     </div>
     <div id="spinner" class="spinner-border text-primary" role="status" style="display: none;"><span class="visually-hidden">Carregando...</span></div>

</div> <div class="modal fade" id="itemDetailsModal" tabindex="-1" aria-labelledby="itemDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl"> <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="itemDetailsModalLabel">Detalhes do Item</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body"> </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            <button type="button" class="btn btn-primary" id="saveItemDetails">Salvar Alterações do Item</button> </div>
    </div> </div>
</div>

<div class="modal fade" id="batchEditModal" tabindex="-1" aria-labelledby="batchEditModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg"> <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="batchEditModalLabel">Preenchimento em Lote</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <p class="text-muted small">Aplicar a todos os itens...</p>
            <form id="batchEditForm">
                <div class="row g-3">
                    <div class="col-md-6 mb-3"><label for="batchIncotermSelect" class="form-label">Incoterm:</label><select id="batchIncotermSelect" class="form-select"><option value="" selected>Não alterar...</option><?php if(!empty($incoterms)){foreach ($incoterms as $incoterm){echo '<option value="'.htmlspecialchars($incoterm['Sigla']).'">'.htmlspecialchars($incoterm['Sigla'].' - '.$incoterm['Descricao']).'</option>';}}?></select></div>
                     <div class="col-md-6 mb-3"><label for="batchPaisDestinoInput" class="form-label">País Destino:</label><input type="text" id="batchPaisDestinoInput" class="form-control" list="paisesDestinoListBatch" placeholder="Digite nome..."><datalist id="paisesDestinoListBatch"><option value="">Não alterar...</option><?php if(!empty($paises)){foreach ($paises as $pais){echo '<option value="'.htmlspecialchars($pais['Nome']).'">'.htmlspecialchars($pais['Nome']).'</option>';}}?></datalist></div>
                     <?php for ($i=1;$i<=4;$i++):?><div class="col-md-6 mb-3"><label for="batchEnquadramento<?=$i?>Select" class="form-label"><?=$i?>º Enq.:</label><select id="batchEnquadramento<?=$i?>Select" class="form-select"><option value="" selected>Não alterar...</option><?php if(!empty($enquadramentos)){foreach($enquadramentos as $enq){echo '<option value="'.htmlspecialchars($enq['CODIGO']).'">'.htmlspecialchars($enq['CODIGO'].' - '.$enq['DESCRICAO']).'</option>';}}?><option value="99999">99999 - SEM ENQ.</option></select></div><?php endfor;?>
                     <div class="col-12 mb-3"><label class="form-label d-block">Acordo Mercosul:</label><div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="batchCcptCcrom" id="batchCcptCcromAlterar" value="" checked><label class="form-check-label" for="batchCcptCcromAlterar">Não alterar</label></div><div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="batchCcptCcrom" id="batchCcptCcromNone" value="NA"><label class="form-check-label" for="batchCcptCcromNone">N/A</label></div><div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="batchCcptCcrom" id="batchCcpt" value="CCPT"><label class="form-check-label" for="batchCcpt">CCPT</label></div><div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="batchCcptCcrom" id="batchCcrom" value="CCROM"><label class="form-check-label" for="batchCcrom">CCROM</label></div></div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-success" id="saveBatchEdit">Aplicar a Todos</button> </div>
    </div> </div>
</div>

<script>
    // Passa dados PHP -> JS
    window.incotermsData = <?php echo json_encode($incoterms ?: [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
    window.enquadramentosData = <?php echo json_encode($enquadramentos ?: [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
    window.paisesData = <?php echo json_encode($paises ?: [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
    window.isViewMode = <?php echo $is_view_mode ? 'true' : 'false'; ?>;

    // Passa ITENS (se editando)
    console.log("PHP->JS: Definindo window.processedNFData inicial...");
    try {
        const initialItemsJsonStr = <?php echo json_encode($itens_json_data ?: '[]', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
        window.processedNFData = JSON.parse(initialItemsJsonStr);
        if (!Array.isArray(window.processedNFData)) { throw new Error("Initial data not array."); }
        console.log("PHP->JS: window.processedNFData OK:", window.processedNFData.length);
    } catch (e) { console.error("PHP->JS: Erro parse JSON itens:", e); window.processedNFData = []; }
</script>
<script src="due/js/main.mjs" type="module"></script>

<?php // ----- FIM DO CONTEÚDO ----- ?>