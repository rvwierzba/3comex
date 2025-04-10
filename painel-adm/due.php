<?php
// --- Arquivo: painel-adm/due.php ---
// --- Função: Listagem de DU-Es (Localização: painel-adm/) ---

// --- INCLUDES ---
// Caminhos relativos a partir de 'painel-adm/'
require_once("../conexao.php");     // Sobe um nível para 3comex/
require_once("due/campos.php");    // Entra na subpasta 'due'

// --- Verificações Iniciais ---
if (!isset($pdo)) { die("<p class='alert alert-danger m-3'>Erro Crítico: Conexão falhou.</p>"); }
if (!isset($pagina) || $pagina !== 'due') { die("<p class='alert alert-danger m-3'>Erro Crítico: 'campos.php' inválido.</p>"); }
if (!isset($campo1)) { die("<p class='alert alert-danger m-3'>Erro Crítico: Variáveis \$campoX não carregadas.</p>"); }

// --- Variáveis de Controle ---
// ***** USA O NOME DO WRAPPER nos links *****
$nome_pag_formulario = 'due/inserir'; // << Aponta para o wrapper painel-adm/form_due.php

// Mapeamento para Cabeçalhos
$labelsCabecalho = [ $campo1 => 'ID DU-E', $campo3 => 'Nome Exportador', $campo17 => 'Data Criação' ];

?>

<div class="mb-3 d-flex justify-content-between align-items-center">
    <h4>Listagem de DU-Es</h4>
    <a href="index.php?pag=<?php echo $nome_pag_formulario; ?>" type="button" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i> Nova DU-E
    </a>
</div>

<div class="table-responsive">
    <table id="tabelaListarDue" class="table table-striped table-light table-hover w-100">
        <thead>
            <tr>
                <th><?php echo htmlspecialchars($labelsCabecalho[$campo1] ?? $campo1); ?></th>
                <th><?php echo htmlspecialchars($labelsCabecalho[$campo3] ?? $campo3); ?></th>
                <th><?php echo htmlspecialchars($labelsCabecalho[$campo17] ?? $campo17); ?></th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            try {
                $sql = "SELECT {$campo1}, {$campo3}, {$campo17} FROM {$pagina} ORDER BY {$campo17} DESC";
                $query = $pdo->query($sql);
                $res = $query->fetchAll(PDO::FETCH_ASSOC);

                if (@count($res) > 0) {
                    foreach ($res as $item) {
                        $id_due = $item[$campo1];
                        $data_criacao_formatada_tabela = $item[$campo17] ? (new DateTime($item[$campo17]))->format('d/m/Y H:i:s') : 'N/A';
                        $id_due_esc = htmlspecialchars($id_due, ENT_QUOTES, 'UTF-8');
                        $nome_exportador_esc = htmlspecialchars($item[$campo3] ?? 'N/A', ENT_QUOTES, 'UTF-8');
                        $data_criacao_esc = htmlspecialchars($data_criacao_formatada_tabela, ENT_QUOTES, 'UTF-8');

                        // JSON para modal Ver Dados (OPCIONAL, pode ser removido se não usar modal de visualização)
                         $dadosParaModal = [ $campo1 => $id_due, $campo3 => $item[$campo3] ?? '', $campo17 => $data_criacao_formatada_tabela ];
                         $dadosJsonEsc = htmlspecialchars(json_encode($dadosParaModal), ENT_QUOTES, 'UTF-8');
            ?>
                        <tr>
                            <td><?php echo $id_due_esc; ?></td>
                            <td><?php echo $nome_exportador_esc; ?></td>
                            <td><?php echo $data_criacao_esc; ?></td>
                            <td>
                                <a href="index.php?pag=<?php echo $nome_pag_formulario; ?>&id=<?php echo $id_due_esc; ?>" title="Editar DU-E <?php echo $id_due_esc; ?>" class="me-1">
                                    <i class="bi bi-pencil-square text-primary"></i>
                                </a>
                                <a href="#" onclick="prepararExclusao('<?php echo $id_due_esc; ?>', 'DU-E <?php echo $id_due_esc; ?>')" title="Excluir DU-E <?php echo $id_due_esc; ?>" data-bs-toggle="modal" data-bs-target="#modalExcluir" class="me-1">
                                    <i class="bi bi-trash text-danger"></i>
                                </a>
                                <a href="index.php?pag=<?php echo $nome_pag_formulario; ?>&id=<?php echo $id_due_esc; ?>&mode=view" title="Ver Dados DU-E <?php echo $id_due_esc; ?>">
                                    <i class="bi bi-info-square text-info"></i>
                                </a>
                            </td>
                        </tr>
            <?php
                    } // Fim foreach
                } else { echo '<tr><td colspan="4" class="text-center">Nenhuma DU-E encontrada.</td></tr>'; }
            } catch (Exception $e) { error_log("Erro listar DU-Es (due.php): " . $e->getMessage()); echo '<tr><td colspan="4" class="text-center text-danger">Erro ao buscar dados.</td></tr>'; }
            ?>
        </tbody>
    </table>
</div>

<script type="text/javascript">
    // $(document).ready(function() { $('#tabelaListarDue').DataTable({...}); });
    function prepararExclusao(id, nome) { /* ...código mantido... */ }
    function confirmarExclusao() { /* ...código mantido com fetch para due/excluir.php... */ }
    // function mostrarDados() { /* Não é mais necessária aqui */ }
    function htmlspecialchars(str) { /* ...código mantido... */ }
</script>