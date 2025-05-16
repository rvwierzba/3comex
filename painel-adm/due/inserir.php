<?php
// --- Arquivo: painel-adm/due/inserir.php ---
// --- Função: Formulário Add/Edit (VERSÃO COM CORREÇÕES PARA main.mjs) ---

// Configuração de Erros (Logar, não exibir)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// --- INCLUDES ---
@require_once __DIR__ . "/../../conexao.php";
@require_once __DIR__ . "/campos.php";

// --- Verificações Iniciais ---
if (!isset($pdo)) { die("<p class='alert alert-danger m-3'>Erro Crítico: Conexão falhou (inserir.php).</p>"); }
if (!isset($pagina) || $pagina !== 'due') { die("<p class='alert alert-danger m-3'>Erro Crítico: 'campos.php' inválido (inserir.php).</p>"); }
if (!isset($campo1) || !isset($campo16)) { die("<p class='alert alert-danger m-3'>Erro Crítico: Variáveis \$campo1 ou \$campo16 não carregadas (inserir.php).</p>"); }

// --- LÓGICA PARA CARREGAR DADOS ---
$edit_id = null; $is_editing = false; $is_view_mode = false; $due_data = null; $itens_array_php = []; $page_title = 'Nova DU-E';

if (isset($_GET['id'])) {
    $edit_id = $_GET['id']; $is_editing = true;
    if (isset($_GET['mode']) && $_GET['mode'] == 'view') { $is_view_mode = true; }
} elseif (isset($_POST['edit_id'])) { $edit_id = $_POST['edit_id']; $is_editing = true; }

if ($is_editing && !empty($edit_id)) {
    $edit_id_sanitized = null;
    if (preg_match('/^DUE\d{5}-\d{2}$/', $edit_id) || is_numeric($edit_id)) { // Ajustado para aceitar numérico também, se for o caso
        $edit_id_sanitized = $edit_id;
    } else { error_log("[INSERIR.PHP] ERRO: Formato de ID inválido: '$edit_id'"); }

    if ($edit_id_sanitized) {
        try {
            $sql_edit = "SELECT * FROM {$pagina} WHERE {$campo1} = :id";
            $stmt_edit = $pdo->prepare($sql_edit);
            $stmt_edit->bindParam(':id', $edit_id_sanitized, PDO::PARAM_STR);
            $stmt_edit->execute();
            $due_data_from_db = $stmt_edit->fetch(PDO::FETCH_ASSOC);

            if ($due_data_from_db) {
                $page_title = ($is_view_mode ? 'Visualizar ' : 'Editar ') . 'DU-E: ' . htmlspecialchars($due_data_from_db[$campo1]);
                // $due_data é usado para passar para o JavaScript via window.dueDataPrincipalPHP
                $due_data = $due_data_from_db; 
                
                // Decodifica os itens JSON para um array PHP
                $itens_json_string = $due_data_from_db[$campo16] ?? '[]';
                $itens_array_php = json_decode($itens_json_string, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    error_log("[INSERIR.PHP] Erro ao decodificar JSON dos itens para ID $edit_id_sanitized: " . json_last_error_msg());
                    $itens_array_php = []; // Define como array vazio em caso de erro
                }
                if (!is_array($itens_array_php)) $itens_array_php = []; // Garante que é um array

                error_log("[INSERIR.PHP] Dados carregados OK para ID $edit_id_sanitized.");
            } else {
                error_log("[INSERIR.PHP] ALERTA - ID '$edit_id_sanitized' NÃO encontrado no banco.");
                $is_editing = $is_view_mode = false; $edit_id = null; $due_data = null; $itens_array_php = [];
            }
        } catch (Exception $e) {
            error_log("ERRO DB busca ID $edit_id_sanitized: ".$e->getMessage());
            $edit_id = null; $is_editing = $is_view_mode = false; $due_data = null; $itens_array_php = [];
            echo "<p class='alert alert-danger m-3'>Erro no Banco de Dados ao carregar DU-E. Verifique os logs.</p>";
        }
    } else {
         $edit_id = null; $is_editing = $is_view_mode = false; $due_data = null; $itens_array_php = [];
         echo "<p class='alert alert-warning m-3'>ID inválido fornecido.</p>";
    }
} else {
    error_log("[INSERIR.PHP] Modo Inserção (sem ID válido).");
}

// --- PRE-FETCH DROPDOWNS (Mantido, pois podem ser úteis para modais ou outras partes) ---
$incoterms = []; try{ $incoterms = $pdo->query('SELECT Sigla, Descricao FROM incoterms ORDER BY Sigla')->fetchAll(PDO::FETCH_ASSOC); } catch(Exception $e){}
$enquadramentos = []; try{ $enquadramentos = $pdo->query('SELECT CODIGO, DESCRICAO FROM enquadramento ORDER BY CODIGO')->fetchAll(PDO::FETCH_ASSOC); } catch(Exception $e){}
$paises = []; try{ $paises = $pdo->query('SELECT Sigla_is03, Nome, CodigoBACEN FROM paises ORDER BY Nome')->fetchAll(PDO::FETCH_ASSOC); } catch(Exception $e){error_log("Erro fetch Paises: ".$e->getMessage());}
$moedas = []; try{ $moedas = $pdo->query('SELECT Codigo, Nome FROM moeda ORDER BY Nome')->fetchAll(PDO::FETCH_ASSOC); } catch(Exception $e){}
// Não precisamos buscar unidades e recintos aqui, pois o main.mjs fará isso via AJAX

// --- Funções Auxiliares PHP ---
function get_value($field_key, $data_array, $default = '') { return htmlspecialchars($data_array[$field_key] ?? $default, ENT_QUOTES, 'UTF-8'); }
function is_checked($field_key, $value_to_check, $data_array) { return isset($data_array[$field_key]) && $data_array[$field_key] == $value_to_check ? 'checked' : ''; }
function is_selected($field_key, $value_to_check, $data_array) { return isset($data_array[$field_key]) && $data_array[$field_key] == $value_to_check ? 'selected' : ''; }
function is_switch_checked($field_key, $data_array) { return !empty($data_array[$field_key]); }
$disable_attr = $is_view_mode ? 'readonly' : '';
$disable_attr_select = $is_view_mode ? 'disabled' : '';

?>
<div class="container mt-4">
    <h2 class="mb-4 text-center"><?php echo htmlspecialchars($page_title); ?></h2>
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
                                <input class="form-control" type="text" id="text-cnpj-cpf-select" name="cnpj-cpf" value="<?php echo get_value($campo2, $due_data ?? []); ?>" readonly>
                             </div>
                             <div class="col-md-6 form-group">
                                 <label for="nomeCliente" class="form-label">Nome Exp.:</label>
                                 <input type="text" id="nomeCliente" name="nomeCliente" class="form-control" value="<?php echo get_value($campo3, $due_data ?? []); ?>" readonly>
                             </div>
                        </div>
                        <div class="row g-3 mt-3">
                              <div class="col-md-6 form-group">
                                 <h6>Forma Exportação</h6>
                                 <?php $current_val_forma_export = $due_data[$campo4] ?? 'Por conta própria'; ?>
                                 <div class="form-check"> <input class="form-check-input" type="radio" id="por-conta-propria" name="forma-export" value="Por conta própria" <?php echo ($current_val_forma_export == 'Por conta própria')?'checked':''; ?> <?php echo $disable_attr_select;?>> <label class="form-check-label" for="por-conta-propria">Própria</label> </div>
                                 <div class="form-check"> <input class="form-check-input" type="radio" id="p-conta-ordem-terceiros" name="forma-export" value="Por conta ou ordem de terceiros" <?php echo ($current_val_forma_export == 'Por conta ou ordem de terceiros')?'checked':''; ?> <?php echo $disable_attr_select;?>> <label class="form-check-label" for="p-conta-ordem-terceiros">Conta/Ordem 3º</label> </div>
                                 <div class="form-check"> <input class="form-check-input" type="radio" id="p-op-rm-post-ou-remss" name="forma-export" value="Por operador de remessa postal ou expressa" <?php echo ($current_val_forma_export == 'Por operador de remessa postal ou expressa')?'checked':''; ?> <?php echo $disable_attr_select;?>> <label class="form-check-label" for="p-op-rm-post-ou-remss">Op. Remessa</label> </div>
                              </div>
                              <div class="col-md-6 form-group">
                                  <h6>Tipo Doc Fiscal</h6>
                                  <?php $current_val_tipo_doc = $due_data[$campo5] ?? 'Nota fiscal eletronica'; ?>
                                  <div class="form-check"> <input class="form-check-input" type="radio" id="nfe" name="tp-doc-amp-merc-export" value="Nota fiscal eletronica" <?php echo ($current_val_tipo_doc == 'Nota fiscal eletronica')?'checked':''; ?> <?php echo $disable_attr_select;?>> <label class="form-check-label" for="nfe">NF-e</label> </div>
                                  <div class="form-check"> <input class="form-check-input" type="radio" id="nf-form" name="tp-doc-amp-merc-export" value="Nota fiscal formulario" <?php echo ($current_val_tipo_doc == 'Nota fiscal formulario')?'checked':''; ?> <?php echo $disable_attr_select;?>> <label class="form-check-label" for="nf-form">NF Formulário</label> </div>
                                  <div class="form-check"> <input class="form-check-input" type="radio" id="s-nf" name="tp-doc-amp-merc-export" value="Sem nota fiscal" <?php echo ($current_val_tipo_doc == 'Sem nota fiscal')?'checked':''; ?> <?php echo $disable_attr_select;?>> <label class="form-check-label" for="s-nf">Sem NF</label> </div>
                               </div>
                         </div>
                         <div class="row g-3 mt-3">
                             <div class="col-md-6 form-group">
                                 <label for="text-moeda" class="form-label">Moeda Negociação:</label>
                                 <select id="text-moeda" name="moeda" class="form-select" <?php echo $disable_attr_select; ?>>
                                     <option value="">Selecione...</option>
                                     <?php if (!empty($moedas)): foreach($moedas as $row): ?>
                                        <option value="<?php echo htmlspecialchars($row['Codigo']); ?>" <?php echo is_selected($campo6, $row['Codigo'], $due_data ?? []); ?>>
                                             <?php echo htmlspecialchars($row['Codigo']); ?> - <?php echo htmlspecialchars($row['Nome']); ?>
                                         </option>
                                     <?php endforeach; endif; ?>
                                 </select>
                             </div>
                             <div class="col-md-6 form-group">
                                 <label for="ruc" class="form-label">RUC:</label>
                                 <input type="text" class="form-control" id="ruc" name="ruc" placeholder="Opcional" maxlength="35" value="<?php echo get_value($campo7, $due_data ?? []); ?>" <?php echo $disable_attr; ?>>
                             </div>
                         </div>
                        <div class="row g-3 mt-3">
                             <div class="col-md-6 form-group">
                                 <label for="situacao-espec-despacho" class="form-label">Situação Especial Despacho:</label>
                                 <select name="situacao-espec-despacho" class="form-select" id="situacao-espec-despacho" <?php echo $disable_attr_select; ?>>
                                     <option value="" <?php echo is_selected($campo8, '', $due_data ?? []); ?>>Nenhuma</option>
                                     <option value="DU-E a posteriori" <?php echo is_selected($campo8, 'DU-E a posteriori', $due_data ?? []); ?>>DU-E a posteriori</option>
                                     <option value="Embarque antecipado" <?php echo is_selected($campo8, 'Embarque antecipado', $due_data ?? []); ?>>Embarque antecipado</option>
                                     <option value="Exportação sem saída da mercadoria do país" <?php echo is_selected($campo8, 'Exportação sem saída da mercadoria do país', $due_data ?? []); ?>>Exportação sem saída...</option>
                                 </select>
                             </div>
                             <div class="col-md-6 form-group d-flex align-items-center pt-3">
                                 <div class="form-check form-switch">
                                     <input class="form-check-input" type="checkbox" role="switch" id="export-cons" name="export-cons" <?php echo is_switch_checked($campo9, isset($due_data) ? $due_data : array()) ? 'checked':''; ?> <?php echo $disable_attr_select; ?>>
                                     <label class="form-check-label" for="export-cons">Exportação Consorciada</label>
                                 </div>
                             </div>
                         </div>
                         <hr class="my-4">
                         <div>
                             <h5 id="lbl-local-despacho">Local Despacho</h5>
                             <div class="row g-3">
                                 <div class="col-md-6 form-group">
                                     <label for="text-campo-de-pesquisa-unidades-rfb-d" class="form-label">Unidade RFB:</label>
                                     <!-- REMOVIDO: list="unidades-rfb-d-list" e <datalist> -->
                                     <input id="text-campo-de-pesquisa-unidades-rfb-d" type="text" class="form-control" name="unidade_rfb_despacho" value="<?php echo get_value($campo10, $due_data ?? []); ?>" <?php echo $disable_attr;?>>
                                 </div>
                                 <div class="col-md-6 form-group">
                                     <label for="text-campo-de-pesquisa-recinto-alfandegado-d" class="form-label">Recinto:</label>
                                     <!-- REMOVIDO: list="recinto-aduaneiro-d-list" e <datalist> -->
                                     <input id="text-campo-de-pesquisa-recinto-alfandegado-d" type="text" class="form-control" name="recinto_aduaneiro_despacho" value="<?php echo get_value($campo11, $due_data ?? []); ?>" <?php echo $disable_attr;?>>
                                 </div>
                             </div>
                         </div>
                         <hr class="my-4">
                         <div>
                             <h5 id="lbl-local-embarque">Local Embarque</h5>
                             <div class="row g-3">
                                 <div class="col-md-6 form-group">
                                     <label for="text-campo-de-pesquisa-unidades-rfb-e" class="form-label">Unidade RFB:</label>
                                     <!-- REMOVIDO: list="unidades-rfb-e-list" e <datalist> -->
                                     <input id="text-campo-de-pesquisa-unidades-rfb-e" type="text" class="form-control" name="unidade_rfb_embarque" value="<?php echo get_value($campo12, $due_data ?? []); ?>" <?php echo $disable_attr;?>>
                                 </div>
                                 <div class="col-md-6 form-group">
                                     <label for="text-campo-de-pesquisa-recinto-alfandegado-e" class="form-label">Recinto:</label>
                                     <!-- REMOVIDO: list="recinto-aduaneiro-e-list" e <datalist> -->
                                     <input id="text-campo-de-pesquisa-recinto-alfandegado-e" type="text" class="form-control" name="recinto_aduaneiro_embarque" value="<?php echo get_value($campo13, $due_data ?? []); ?>" <?php echo $disable_attr;?>>
                                 </div>
                             </div>
                         </div>
                         <hr class="my-4">
                         <div id="complementos">
                             <h5 id="lbl-complementos">Complementos</h5>
                             <div class="row g-3">
                                 <div class="col-md-6 form-group">
                                     <label for="via-especial-transport" class="form-label">Via Esp. Transporte:</label>
                                     <select class="form-select" id="via-especial-transport" name="via-especial-transport" <?php echo $disable_attr_select;?>>
                                         <option value="" <?php echo is_selected($campo14,'',$due_data ?? []);?>>Nenhuma</option>
                                         <option value="MEIOS PRÓPRIOS" <?php echo is_selected($campo14,'MEIOS PRÓPRIOS',$due_data ?? []);?>>MEIOS PRÓPRIOS</option>
                                         <option value="DUTOS" <?php echo is_selected($campo14,'DUTOS',$due_data ?? []);?>>DUTOS</option>
                                         <option value="LINHAS DE TRANSMISSÃO" <?php echo is_selected($campo14,'LINHAS DE TRANSMISSÃO',$due_data ?? []);?>>LINHAS DE TRANSMISSÃO</option>
                                         <option value="EM MÃO" <?php echo is_selected($campo14,'EM MÃO',$due_data ?? []);?>>EM MÃOS</option>
                                         <option value="POR REBOQUE" <?php echo is_selected($campo14,'POR REBOQUE',$due_data ?? []);?>>POR REBOQUE</option>
                                         <option value="TRANSPORTE VICINAL FRONTEIRIÇO" <?php echo is_selected($campo14,'TRANSPORTE VICINAL FRONTEIRIÇO',$due_data ?? []);?>>TRANSPORTE VICINAL FRONTEIRIÇO</option>
                                     </select>
                                 </div>
                                 <div class="col-md-6 form-group">
                                     <label for="info-compl" class="form-label">Info Complementares:</label>
                                     <textarea class="form-control" id="info-compl" name="info-compl" rows="3" placeholder="Info adicionais" <?php echo $disable_attr;?>><?php echo get_value($campo15, $due_data ?? []);?></textarea>
                                 </div>
                             </div>
                         </div>
                    </div> <!-- card-body -->
                </div> <!-- card -->
            </form>
        </div> <!-- tab-pane aba1 -->

        <div class="tab-pane fade" id="abaUpload" role="tabpanel" aria-labelledby="tab-link-upload">
            <div class="card mb-4">
                 <div class="card-header">Upload NF-e (XML)</div>
                 <div class="card-body">
                     <div class="alert alert-info small"><i class="bi bi-info-circle me-1"></i>Carregar XMLs <?php echo $is_editing ? 'substituirá os itens existentes da primeira NF-e ou adicionará novas NF-es. Salve a DU-E para persistir as alterações.' : 'adicionará os itens das NF-es carregadas.'; ?></div>
                     <div class="form-group mb-0">
                         <label for="xml-files" class="form-label">Selecionar Arquivos XML</label>
                         <input type="file" id="xml-files" class="form-control" accept=".xml,text/xml,application/xml" multiple <?php echo $is_view_mode ? 'disabled' : '';?>>
                     </div>
                     <div id="uploadStatus" class="mt-2 text-muted small"></div>
                 </div>
            </div>
        </div> <!-- tab-pane abaUpload -->

        <div class="tab-pane fade" id="aba3" role="tabpanel" aria-labelledby="tab-link-3">
            <div class="card mb-4">
                 <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Itens da DU-E</span>
                    <?php if (!$is_view_mode): ?>
                    <button type="button" class="btn btn-secondary btn-sm" id="batchEditButton" data-bs-toggle="modal" data-bs-target="#batchEditModal" <?php echo $disable_attr_select;?>>
                        <i class="bi bi-pencil-square me-1"></i> Editar em Lote
                    </button>
                    <?php endif; ?>
                </div>
                 <div class="card-body">
                    <div id="tabelaContainer" class="table-responsive">
                        <table class="table table-bordered table-hover table-sm" id="notasFiscaisTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>Chave NF-e</th>
                                    <th>Item NF</th>
                                    <th>NCM</th>
                                    <th>Descrição (NF-e)</th>
                                    <th>Importador (NF-e)</th>
                                    <th>País Destino (DU-E)</th>
                                    <th class="text-center">Ações</th>
                                    <!-- A coluna Status DUE será adicionada pelo JavaScript -->
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Será populado pelo JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> <!-- tab-pane aba3 -->

    </div> <!-- tab-content -->

     <div class="form-group mt-4 text-center">
        <?php if (!$is_view_mode): ?>
            <button type="button" id="salvarDUE" class="btn btn-success me-2"><i class="bi bi-save me-1"></i> <?php echo $is_editing ? 'Atualizar DU-E' : 'Salvar Nova DU-E'; ?></button>
            <button type="button" id="enviarDUE" class="btn btn-primary" <?php echo $is_editing && $edit_id ? '' : 'disabled'; ?>><i class="bi bi-send me-1"></i> Enviar DU-E</button>
        <?php else: ?>
            <a href="index.php?pag=due" class="btn btn-secondary"><i class="bi bi-arrow-left me-1"></i> Voltar para Lista</a>
        <?php endif; ?>
     </div>
     <div id="spinner" class="spinner-border text-primary" role="status" style="display: none;"><span class="visually-hidden">Carregando...</span></div>

</div> <!-- container -->

<!-- Modais (Detalhes do Item e Edição em Lote) -->
<div class="modal fade" id="itemDetailsModal" tabindex="-1" aria-labelledby="itemDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl"> <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="itemDetailsModalLabel">Detalhes do Item</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body"> <!-- Conteúdo será preenchido pelo JS --> </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            <?php if (!$is_view_mode): ?>
            <button type="button" class="btn btn-primary" id="saveItemDetails">Salvar Alterações do Item</button>
            <?php endif; ?>
        </div>
    </div> </div>
</div>

<div class="modal fade" id="batchEditModal" tabindex="-1" aria-labelledby="batchEditModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg"> <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="batchEditModalLabel">Preenchimento em Lote</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <p class="text-muted small">Marque os campos que deseja alterar e preencha os novos valores. Eles serão aplicados a TODOS os itens da DU-E.</p>
            <form id="batchEditForm">
                <div class="row g-3">
                    <!-- Exemplo de campo para edição em lote: Incoterm -->
                    <div class="col-md-1 d-flex align-items-end pb-1">
                        <input class="form-check-input" type="checkbox" value="condicaoVenda" id="cb_batchCondicaoVenda" data-field-name="condicaoVenda">
                    </div>
                    <div class="col-md-5 mb-3">
                        <label for="batchCondicaoVenda" class="form-label">Condição de Venda (Incoterm):</label>
                        <select id="batchCondicaoVenda" name="condicaoVenda" class="form-select form-select-sm">
                            <option value="" selected>Não alterar...</option>
                            <?php if(!empty($incoterms)): foreach ($incoterms as $incoterm): ?>
                                <option value="<?php echo htmlspecialchars($incoterm['Sigla']); ?>"><?php echo htmlspecialchars($incoterm['Sigla'].' - '.$incoterm['Descricao']); ?></option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>

                    <!-- Exemplo: País de Destino -->
                     <div class="col-md-1 d-flex align-items-end pb-1">
                        <input class="form-check-input" type="checkbox" value="paisDestino" id="cb_batchPaisDestino" data-field-name="paisDestino">
                    </div>
                    <div class="col-md-5 mb-3">
                        <label for="batchPaisDestino" class="form-label">País Destino Final:</label>
                        <input type="text" id="batchPaisDestino" name="paisDestino" class="form-control form-control-sm" placeholder="Digite para buscar...">
                        <!-- O autocomplete para este campo será configurado via JS se necessário -->
                    </div>
                    
                    <!-- Exemplo: Enquadramentos -->
                    <?php for ($i=1; $i<=4; $i++): ?>
                    <div class="col-md-1 d-flex align-items-end pb-1">
                        <input class="form-check-input" type="checkbox" value="enquadramento<?php echo $i; ?>" id="cb_batchEnquadramento<?php echo $i; ?>" data-field-name="enquadramento<?php echo $i; ?>">
                    </div>
                    <div class="col-md-5 mb-3">
                        <label for="batchEnquadramento<?php echo $i; ?>" class="form-label"><?php echo $i; ?>º Enquadramento:</label>
                        <select id="batchEnquadramento<?php echo $i; ?>" name="enquadramento<?php echo $i; ?>" class="form-select form-select-sm">
                            <option value="" selected>Não alterar...</option>
                            <?php if(!empty($enquadramentos)): foreach($enquadramentos as $enq): ?>
                                <option value="<?php echo htmlspecialchars($enq['CODIGO']); ?>"><?php echo htmlspecialchars($enq['CODIGO'].' - '.$enq['DESCRICAO']); ?></option>
                            <?php endforeach; endif; ?>
                            <option value="99999">99999 - SEM ENQUADRAMENTO</option>
                        </select>
                    </div>
                    <?php endfor; ?>
                    
                    <!-- Exemplo: CCPT/CCROM -->
                    <div class="col-md-1 d-flex align-items-start pt-4">
                        <input class="form-check-input" type="checkbox" value="ccptCcrom" id="cb_batchCcptCcrom" data-field-name="ccptCcrom">
                    </div>
                    <div class="col-md-11 mb-3">
                        <label class="form-label d-block">Acordo Comercial (Exportador Original):</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="batchCcptCcromModal" id="batchCcptCcromModal_NoChange" value="" checked>
                            <label class="form-check-label" for="batchCcptCcromModal_NoChange">Não Alterar</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="batchCcptCcromModal" id="batchCcptCcromModal_None" value=""> <!-- Value vazio para "Nenhum" -->
                            <label class="form-check-label" for="batchCcptCcromModal_None">Nenhum</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="batchCcptCcromModal" id="batchCcptModal" value="CCPT">
                            <label class="form-check-label" for="batchCcptModal">CCPT</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="batchCcptCcromModal" id="batchCcromModal" value="CCROM">
                            <label class="form-check-label" for="batchCcromModal">CCROM</label>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-success" id="saveBatchEdit">Aplicar Alterações em Lote</button>
        </div>
    </div> </div>
</div>


<script>
    // Passa dados PHP -> JS
    // É importante que estes sejam arrays JavaScript válidos
    window.incotermsData = <?php echo json_encode($incoterms ?: [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
    window.enquadramentosData = <?php echo json_encode($enquadramentos ?: [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
    window.paisesDataGlobal = <?php echo json_encode($paises ?: [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>; // Renomeado para evitar conflito com dataSources.paises
    window.isViewMode = <?php echo $is_view_mode ? 'true' : 'false'; ?>;

    // Definição de window.processedNFData e window.dueDataPrincipalPHP
    <?php if ($is_editing && !empty($due_data)): ?>
        window.dueDataPrincipalPHP = <?php echo json_encode($due_data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
        console.log("PHP->JS: window.dueDataPrincipalPHP (modo edição) DEFINIDO:", window.dueDataPrincipalPHP);
        try {
            // $itens_array_php já é um array PHP ou []
            window.processedNFData = [{
                nf: window.dueDataPrincipalPHP, // Usa os dados principais da DU-E como se fosse a "NF" para consistência
                items: <?php echo json_encode($itens_array_php, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>
            }];
            console.log("PHP->JS: window.processedNFData (modo edição) DEFINIDO:", window.processedNFData);
        } catch (e) {
            console.error("PHP->JS: Erro ao definir window.processedNFData (modo edição):", e);
            window.processedNFData = [];
        }
    <?php else: ?>
        window.dueDataPrincipalPHP = null;
        window.processedNFData = []; 
        console.log("PHP->JS: window.dueDataPrincipalPHP e window.processedNFData (modo inserção) DEFINIDOS como nulo/vazio.");
    <?php endif; ?>
    console.log('PHP->JS: Verificação final de window.processedNFData:', window.processedNFData, '(Tipo:', typeof window.processedNFData, ', É Array?:', Array.isArray(window.processedNFData), ')');
</script>

<!-- main.mjs DEVE ser carregado DEPOIS da definição das variáveis globais acima -->
<script src="due/js/main.mjs" type="module"></script>

<?php
// Não é mais necessário o bloco de script problemático aqui.
error_log("[inserir.php] FIM DA EXECUÇÃO @ " . date("Y-m-d H:i:s"));
?>