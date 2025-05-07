<?php
  require_once("../conexao.php");
  require_once("../painel-adm/unidades_rfb/campos.php");
  
?>


<div class="col-md-12 my-3">
    <a href="#" onclick="inserir()" type="button" class="btn btn-dark btn-sm">Novo Usuário</a>
    </div>

<small>
    <div class="tabela bg-light" id="listar">
        </div>
</small>

<div class="modal fade" id="modalForm" tabindex="-1" aria-labelledby="modalFormLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalFormLabel"><span id="tituloModal">Inserir Usuário</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form" method="post">
                <div class="modal-body">

                    <div class="mb-3">
                        <label for="<?php echo htmlspecialchars($campo1); ?>" class="form-label"><?php echo htmlspecialchars(ucfirst($campo1)); // Nome ?></label>
                        <input type="text" class="form-control" name="<?php echo htmlspecialchars($campo1); ?>" placeholder="Nome Completo do Usuário" id="<?php echo htmlspecialchars($campo1); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="<?php echo htmlspecialchars($campo2); ?>" class="form-label"><?php echo htmlspecialchars(ucfirst($campo2)); // Email ?></label>
                        <input type="email" class="form-control" name="<?php echo htmlspecialchars($campo2); ?>" placeholder="email@example.com" id="<?php echo htmlspecialchars($campo2); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="<?php echo htmlspecialchars($campo3); ?>" class="form-label"><?php echo htmlspecialchars(ucfirst($campo3)); // Senha ?></label>
                        <input type="password" class="form-control" name="<?php echo htmlspecialchars($campo3); ?>" placeholder="Deixe em branco para não alterar a senha" id="<?php echo htmlspecialchars($campo3); ?>">
                        </div>

                    <div class="mb-3">
                        <label for="<?php echo htmlspecialchars($campo4); ?>" class="form-label"><?php echo htmlspecialchars(ucfirst($campo4)); // Nível ?></label>
                        <select class="form-select" name="<?php echo htmlspecialchars($campo4); ?>" id="<?php echo htmlspecialchars($campo4); ?>" required>
                            <option value="" disabled selected>Selecione um Nível</option>
                            <?php
                            if (isset($pdo)) { // $pdo deve vir de conexao.php
                                try {
                                    $query_niveis = $pdo->query("SELECT DISTINCT nivel FROM niveis ORDER BY nivel ASC"); // Usar DISTINCT se 'niveis' for uma tabela de histórico
                                    $niveis_disponiveis = $query_niveis->fetchAll(PDO::FETCH_COLUMN);
                                    if (count($niveis_disponiveis) > 0) {
                                        foreach ($niveis_disponiveis as $nivel_item) {
                                            echo '<option value="' . htmlspecialchars($nivel_item) . '">' . htmlspecialchars($nivel_item) . '</option>';
                                        }
                                    } else {
                                        echo '<option value="" disabled>Nenhum nível cadastrado</option>';
                                    }
                                } catch (PDOException $e) {
                                    error_log("Erro ao buscar níveis: " . $e->getMessage());
                                    echo '<option value="" disabled>Erro ao carregar níveis</option>';
                                }
                            } else {
                                echo '<option value="" disabled>Erro: Conexão PDO não disponível</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <small><div id="mensagem" align="center"></div></small>
                    <input type="hidden" class="form-control" name="id" id="id"> </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btn-fechar">Fechar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalExcluir" tabindex="-1" aria-labelledby="modalExcluirLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalExcluirLabel">Excluir Usuário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-excluir" method="post">
                <div class="modal-body">
                    <p>Deseja realmente excluir o usuário: <strong id="nome-excluido"></strong>?</p>
                    <small><div id="mensagem-excluir" align="center"></div></small>
                    <input type="hidden" class="form-control" name="id-excluir" id="id-excluir">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btn-fechar-excluir">Fechar</button>
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDados" tabindex="-1" aria-labelledby="modalDadosLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDadosLabel">Detalhes do Usuário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <small>
                    <p><strong><?php echo htmlspecialchars(ucfirst($campo1)); // Nome ?>:</strong> <span id="modalDados_nome"></span></p>
                    <hr style="margin:6px 0;">
                    <p><strong><?php echo htmlspecialchars(ucfirst($campo2)); // Email ?>:</strong> <span id="modalDados_email"></span></p>
                    <hr style="margin:6px 0;">
                    <p><strong><?php echo htmlspecialchars(ucfirst($campo4)); // Nível ?>:</strong> <span id="modalDados_nivel"></span></p>
                    </small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    // A variável 'pag' é usada pelo ajax.js para construir URLs (ex: pag + "/inserir.php")
    // $pagina vem de painel-adm/usuarios/campos.php (e deve ser 'usuarios')
    var pag = "<?php echo htmlspecialchars($pagina); ?>";
</script>

<script src="../js/ajax.js"></script>

<script type="text/javascript">
$(document).ready(function() {
    // Garante que o comportamento padrão de submit seja prevenido para o formulário principal
    $("#form").on('submit', function(event) {
        // console.log("Handler de submit adicional (em usuarios.php) para #form."); // Para debug
        if (!(event.isDefaultPrevented && event.isDefaultPrevented())) { // Se não foi prevenido ainda
            // console.log("Prevenindo default para #form em usuarios.php..."); // Para debug
            event.preventDefault();
        }
    });

    // Garante que o comportamento padrão de submit seja prevenido para o formulário de exclusão
    $("#form-excluir").on('submit', function(event) {
        // console.log("Handler de submit adicional (em usuarios.php) para #form-excluir."); // Para debug
        if (!(event.isDefaultPrevented && event.isDefaultPrevented())) { // Se não foi prevenido ainda
            // console.log("Prevenindo default para #form-excluir em usuarios.php..."); // Para debug
            event.preventDefault();
        }
    });

    // Adicionar aqui a lógica para preencher o modalDados se necessário,
    // ou garantir que o ajax.js já tenha uma função genérica para isso
    // Exemplo de como poderia ser chamado (a função popularDadosModal precisaria existir):
    // $(document).on('click', '.btn-dados', function() {
    //     var id = $(this).data('id');
    //     // Chamar uma função que busca os dados via AJAX e preenche o modalDados
    //     popularDadosModal(id);
    // });
});

// Função exemplo para popular o modalDados (você precisará adaptá-la ou usar uma existente)
// function popularDadosModal(id) {
//     $.ajax({
//         url: pag + '/buscar_dados.php', // Você precisaria criar este script backend
//         method: 'POST',
//         data: { id: id },
//         dataType: 'json', // Espera JSON do servidor
//         success: function(data) {
//             if(data) {
//                 $('#modalDados_nome').text(data.nome || 'N/D');
//                 $('#modalDados_email').text(data.email || 'N/D');
//                 $('#modalDados_nivel').text(data.nivel || 'N/D');
//                 // Atualizar o título do modal se desejar, ex:
//                 // $('#modalDadosLabel').text('Detalhes de: ' + (data.nome || 'Usuário'));
//                 var modal = new bootstrap.Modal(document.getElementById('modalDados'));
//                 modal.show();
//             } else {
//                 alert('Dados não encontrados.');
//             }
//         },
//         error: function() {
//             alert('Erro ao buscar dados do usuário.');
//         }
//     });
// }
</script>