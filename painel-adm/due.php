<?php
// --- Arquivo: painel-adm/due.php ---
// --- Função: Listagem de DU-Es ---

// Identificador para logs, caso necessário no futuro
define('NOME_FICHEIRO_ATUAL_LISTAGEM_DUE', basename(__FILE__));
// error_log("LOG DEBUG >> [" . NOME_FICHEIRO_ATUAL_LISTAGEM_DUE . "] INÍCIO DA EXECUÇÃO @ " . date("Y-m-d H:i:s"));

// Ativar erros para depuração (ajuste para produção)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// --- INCLUDES ESSENCIAIS ---
// Ajuste o caminho conforme a sua estrutura. Se due.php está em painel-adm/
require_once("../conexao.php");
require_once("due/campos.php"); // Assume que este ficheiro define $pagina, $campo1, $campo3, $campo17, etc.

// --- VERIFICAÇÕES INICIAIS ---
if (!isset($pdo) || !($pdo instanceof PDO)) {
    $log_msg_pdo = "[" . NOME_FICHEIRO_ATUAL_LISTAGEM_DUE . "] ERRO FATAL: Conexão PDO NÃO definida ou inválida.";
    error_log($log_msg_pdo);
    die("<p class='alert alert-danger m-3'>{$log_msg_pdo} Verifique o ficheiro conexao.php e o caminho.</p>");
} else {
    // error_log("[" . NOME_FICHEIRO_ATUAL_LISTAGEM_DUE . "] Conexão PDO OK.");
}

if (!isset($pagina) || $pagina !== 'due') { // $pagina deve ser 'due' conforme o seu campos.php
    $log_msg_pagina = "[" . NOME_FICHEIRO_ATUAL_LISTAGEM_DUE . "] ERRO Crítico: Variável \$pagina ('{$pagina}') não é 'due' ou não está definida. Verifique due/campos.php.";
    error_log($log_msg_pagina);
    die("<p class='alert alert-danger m-3'>{$log_msg_pagina}</p>");
}
if (!isset($campo1, $campo3, $campo17)) { // Verifique os campos principais usados na query
    $log_msg_campos = "[" . NOME_FICHEIRO_ATUAL_LISTAGEM_DUE . "] ERRO Crítico: Variáveis de campo (\$campo1, \$campo3, \$campo17) não carregadas. Verifique due/campos.php.";
    error_log($log_msg_campos);
    die("<p class='alert alert-danger m-3'>{$log_msg_campos}</p>");
}

// --- VARIÁVEIS DE CONTROLE ---
// Nome da "página" (ou rota/parâmetro) que carrega o formulário de inserção/edição.
// Este deve ser o mesmo valor que o seu index.php usa para carregar o formulário da DU-E.
$nome_pag_formulario_due = 'due/inserir'; // Exemplo: 'due/inserir', 'formulario_due', etc.

// Mapeamento para Cabeçalhos da Tabela (usando as variáveis de campos.php)
$labelsCabecalho = [
    $campo1  => 'ID DU-E',        // Ex: due_id
    $campo3  => 'Nome Exportador', // Ex: due_exportador_nome
    $campo17 => 'Data Criação'   // Ex: due_data_criacao
    // Adicione outros campos/labels que desejar na tabela de listagem
];

?>

<div class="mb-3 d-flex justify-content-between align-items-center">
    <h4>Listagem de Declarações Únicas de Exportação (DU-E)</h4>
    <a href="index.php?pag=<?php echo htmlspecialchars($nome_pag_formulario_due); ?>" type="button" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Nova DU-E
    </a>
</div>

<div class="table-responsive">
    <table id="tabelaListarDue" class="table table-striped table-hover w-100">
        <thead>
            <tr>
                <th><?php echo htmlspecialchars($labelsCabecalho[$campo1] ?? $campo1); ?></th>
                <th><?php echo htmlspecialchars($labelsCabecalho[$campo3] ?? $campo3); ?></th>
                <th><?php echo htmlspecialchars($labelsCabecalho[$campo17] ?? $campo17); ?></th>
                <th class="text-center">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $lista_dues = [];
            try {
                // Monta a lista de campos a serem selecionados
                $campos_select = implode(", ", array_keys($labelsCabecalho));
                if (empty($campos_select)) { // Fallback se $labelsCabecalho estiver mal configurado
                    $campos_select = "{$campo1}, {$campo3}, {$campo17}";
                }

                // A variável $pagina é definida em due/campos.php e deve corresponder ao nome da sua tabela de DU-Es
                $sql_listar_dues = "SELECT {$campos_select} FROM {$pagina} ORDER BY {$campo17} DESC"; // Ordena pela data de criação, mais recentes primeiro
                error_log("[" . NOME_FICHEIRO_ATUAL_LISTAGEM_DUE . "] SQL Listar DU-Es: " . $sql_listar_dues);
                
                $query_dues = $pdo->query($sql_listar_dues);

                if ($query_dues) {
                    $lista_dues = $query_dues->fetchAll(PDO::FETCH_ASSOC);
                    error_log("[" . NOME_FICHEIRO_ATUAL_LISTAGEM_DUE . "] DU-Es listadas: " . count($lista_dues) . " encontradas.");
                } else {
                    $errorInfo = $pdo->errorInfo();
                    error_log("[" . NOME_FICHEIRO_ATUAL_LISTAGEM_DUE . "] ERRO na consulta SQL de listagem de DU-Es. Detalhes PDO: SQLSTATE[" . $errorInfo[0] . "] Code[" . $errorInfo[1] . "] Mensagem[" . $errorInfo[2] . "]");
                }

            } catch (PDOException $e) {
                error_log("[" . NOME_FICHEIRO_ATUAL_LISTAGEM_DUE . "] EXCEÇÃO PDO ao listar DU-Es: " . $e->getMessage());
                // Não interrompa a página, apenas mostre uma mensagem de erro na tabela.
                $lista_dues = false; // Indica que houve um erro
            }

            if ($lista_dues === false) {
                 echo '<tr><td colspan="4" class="text-center text-danger">Erro ao buscar dados das DU-Es. Consulte o administrador.</td></tr>';
            } elseif (empty($lista_dues)) {
                echo '<tr><td colspan="4" class="text-center text-muted">Nenhuma DU-E encontrada.</td></tr>';
            } else {
                foreach ($lista_dues as $item_due) {
                    $id_due_atual = $item_due[$campo1] ?? 'N/A';
                    $id_due_esc = htmlspecialchars($id_due_atual, ENT_QUOTES, 'UTF-8');
                    
                    $nome_exportador_esc = htmlspecialchars($item_due[$campo3] ?? 'N/A', ENT_QUOTES, 'UTF-8');
                    
                    $data_criacao_formatada = 'N/A';
                    if (!empty($item_due[$campo17])) {
                        try {
                            // Formata a data para o padrão brasileiro
                            $data_obj = new DateTime($item_due[$campo17]);
                            $data_criacao_formatada = $data_obj->format('d/m/Y H:i:s');
                        } catch (Exception $ex_data) {
                            // Data inválida, mantém N/A ou loga o erro
                            error_log("[" . NOME_FICHEIRO_ATUAL_LISTAGEM_DUE . "] Erro ao formatar data '{$item_due[$campo17]}': " . $ex_data->getMessage());
                        }
                    }
                    $data_criacao_esc = htmlspecialchars($data_criacao_formatada, ENT_QUOTES, 'UTF-8');
            ?>
                    <tr>
                        <td><?php echo $id_due_esc; ?></td>
                        <td><?php echo $nome_exportador_esc; ?></td>
                        <td><?php echo $data_criacao_esc; ?></td>
                        <td class="text-center">
                            <a href="index.php?pag=<?php echo htmlspecialchars($nome_pag_formulario_due); ?>&id=<?php echo $id_due_esc; ?>" 
                               title="Editar DU-E <?php echo $id_due_esc; ?>" class="btn btn-outline-primary btn-sm me-1">
                                <i class="bi bi-pencil-square"></i> Editar
                            </a>
                            <a href="#" 
                               onclick="prepararExclusao('<?php echo $id_due_esc; ?>', 'DU-E <?php echo $id_due_esc; ?>')" 
                               title="Excluir DU-E <?php echo $id_due_esc; ?>" 
                               class="btn btn-outline-danger btn-sm me-1" 
                               data-bs-toggle="modal" data-bs-target="#modalExcluirDue">
                                <i class="bi bi-trash"></i> Excluir
                            </a>
                            <a href="index.php?pag=<?php echo htmlspecialchars($nome_pag_formulario_due); ?>&id=<?php echo $id_due_esc; ?>&mode=view" 
                               title="Ver Dados DU-E <?php echo $id_due_esc; ?>" class="btn btn-outline-info btn-sm">
                                <i class="bi bi-info-square"></i> Ver
                            </a>
                        </td>
                    </tr>
            <?php
                } // Fim foreach
            } // Fim else (se $lista_dues não estiver vazia)
            ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="modalExcluirDue" tabindex="-1" aria-labelledby="modalExcluirDueLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalExcluirDueLabel">Confirmar Exclusão</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        Tem certeza que deseja excluir a <strong id="nomeItemParaExcluir">DU-E</strong>? Esta ação não poderá ser desfeita.
        <input type="hidden" id="idItemParaExcluir" value="">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-danger" onclick="confirmarExclusaoDue()">Confirmar Exclusão</button>
      </div>
    </div>
  </div>
</div>


<script>
    // Função para preparar o modal de exclusão
    function prepararExclusao(id, nomeExibicao) {
        const idInput = document.getElementById('idItemParaExcluir');
        const nomeSpan = document.getElementById('nomeItemParaExcluir');
        if (idInput) idInput.value = id;
        if (nomeSpan) nomeSpan.textContent = nomeExibicao || `DU-E ${id}`;
        // console.log(`Preparando para excluir ID: ${id}, Nome: ${nomeExibicao}`);
    }

    // Função para confirmar a exclusão (exemplo com fetch)
    async function confirmarExclusaoDue() {
        const id = document.getElementById('idItemParaExcluir').value;
        if (!id) {
            alert('ID para exclusão não encontrado.');
            return;
        }
        console.log(`Confirmando exclusão da DU-E ID: ${id}`);

        // ADAPTE O CAMINHO PARA O SEU SCRIPT DE EXCLUSÃO
        const urlExclusao = `due/excluir_due.php?id=${id}`; // Exemplo, pode ser POST

        try {
            // Adicione um spinner ou desabilite o botão durante a requisição
            const response = await fetch(urlExclusao, {
                method: 'POST', // Ou 'GET', ou 'DELETE', conforme a sua API
                // headers: { 'Content-Type': 'application/json' }, // Se enviar corpo JSON
                // body: JSON.stringify({ id: id }) // Se enviar corpo JSON
            });

            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`Erro HTTP ${response.status}: ${errorText || response.statusText}`);
            }

            const result = await response.json(); // Espera uma resposta JSON

            if (result.sucesso) { // Adapte 'sucesso' para a chave usada na sua resposta JSON
                alert(result.mensagem || 'DU-E excluída com sucesso!');
                location.reload(); // Recarrega a página para atualizar a lista
            } else {
                alert('Erro ao excluir DU-E: ' + (result.mensagem || 'Erro desconhecido retornado pelo servidor.'));
            }
        } catch (error) {
            console.error('Erro na requisição de exclusão:', error);
            alert('Erro de comunicação ao tentar excluir a DU-E. Verifique o console para detalhes.');
        } finally {
            // Fechar o modal, reabilitar botões, etc.
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalExcluirDue'));
            if (modal) modal.hide();
        }
    }

    // Se você estiver usando DataTables para a tabela de listagem:
    // $(document).ready(function() {
    //     $('#tabelaListarDue').DataTable({
    //         // Opções do DataTables (tradução, ordenação padrão, etc.)
    //         // "language": { "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Portuguese-Brasil.json" }
    //     });
    // });
</script>

<?php
// Não é necessário definir window.paisesData, window.enquadramentosData, etc., aqui,
// pois esta é a página de LISTAGEM. Esses dados são carregados via AJAX pelo main.mjs
// quando o formulário/modal de item é aberto (na página de inserção/edição).

// Se o seu index.php inclui o main.mjs para todas as páginas, não há problema,
// mas o main.mjs só fará a busca AJAX de países quando a função createItemDetailsFields for chamada.

error_log("[" . NOME_FICHEIRO_ATUAL_LISTAGEM_DUE . "] FIM DA EXECUÇÃO @ " . date("Y-m-d H:i:s"));
?>
