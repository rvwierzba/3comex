<?php
// painel-adm/fornecedores.php

require_once("../conexao.php");
require_once("verificar.php");
$pagina = 'fornecedores';

require_once($pagina . "/campos.php");

?>

<div class="col-md-12 my-3">
    <a href="#" onclick="inserir()" type="button" class="btn btn-dark btn-sm">Novo Fornecedor</a>
</div>

<small>
    <div class="tabela bg-light" id="listar">
        </div>
</small>

<div class="modal fade" id="modalForm" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tituloModal">Inserir Registro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form" method="post">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nome" class="form-label"><?php echo $campo1 ?></label>
                                <input type="text" class="form-control" name="nome" id="nome" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                             <div class="mb-3">
                                <label for="pessoa" class="form-label"><?php echo $campo2 ?></label>
                                <select class="form-select" name="pessoa" id="pessoa">
                                    <option value="Jurídica">Pessoa Jurídica</option>
                                    <option value="Física">Pessoa Física</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                             <div class="mb-3">
                                <label for="doc" class="form-label"><?php echo $campo3 ?></label>
                                <input type="text" class="form-control" name="doc" id="doc">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="telefone" class="form-label"><?php echo $campo4 ?></label>
                                <input type="text" class="form-control" name="telefone" id="telefone">
                            </div>
                        </div>
                    </div>

                     <div class="row">
                        <div class="col-md-8">
                           <div class="mb-3">
                                <label for="endereco" class="form-label"><?php echo $campo5 ?></label>
                                <input type="text" class="form-control" name="endereco" id="endereco">
                            </div>
                        </div>
                         <div class="col-md-4">
                           <div class="mb-3">
                                <label for="ativo" class="form-label"><?php echo $campo6 ?></label>
                                <select class="form-select" name="ativo" id="ativo">
                                    <option value="Sim">Sim</option>
                                    <option value="Não">Não</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label"><?php echo $campo11 ?></label>
                        <input type="email" class="form-control" name="email" id="email">
                    </div>

                    <div class="row">
                         <div class="col-md-8">
                           <div class="mb-3">
                                <label for="banco" class="form-label"><?php echo $campo9 ?></label>
                                <select class="form-select" name="banco" id="banco">
                                    <?php 
                                        $query = $pdo->query("SELECT * from bancos order by nome asc");
                                        $res = $query->fetchAll(PDO::FETCH_ASSOC);
                                        for($i=0; $i < @count($res); $i++){
                                            echo "<option value='".$res[$i]['nome']."'>".$res[$i]['nome']."</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                         <div class="col-md-4">
                           <div class="mb-3">
                                <label for="agencia" class="form-label"><?php echo $campo10 ?></label>
                               <input type="text" class="form-control" name="agencia" id="agencia">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="obs" class="form-label"><?php echo $campo7 ?></label>
                        <textarea class="form-control" name="obs" id="obs" rows="2"></textarea>
                    </div>

                    <small><div id="mensagem" align="center" class="mt-3"></div></small>
                    <input type="hidden" name="id" id="id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btn-fechar">Fechar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalExcluir" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Excluir Registro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-excluir" method="post">
                <div class="modal-body">
                    Deseja realmente excluir: <span id="nome-excluido" class="text-danger"></span>?
                    <small><div id="mensagem-excluir" align="center"></div></small>
                    <input type="hidden" name="id-excluir" id="id-excluir">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script type="text/javascript">
    var pag = "<?=$pagina?>";

    function listar() { /* ... */ } // Implementado no $(document).ready()

    function inserir() {
        $('#id').val('');
        $('#form').trigger('reset'); // Limpa todos os campos do formulário
        $('#tituloModal').text('Inserir Registro');
        // Aciona o evento change para garantir que a máscara de CNPJ seja a padrão
        $('#pessoa').trigger('change'); 
        var myModal = new bootstrap.Modal(document.getElementById('modalForm'));
        myModal.show();
    }

    function editar(id, nome, pessoa, doc, telefone, endereco, ativo, obs, banco, agencia, email) {
        $('#id').val(id);
        $('#nome').val(nome);
        $('#pessoa').val(pessoa).trigger('change'); // Aciona o change para ajustar a máscara
        $('#doc').val(doc);
        $('#telefone').val(telefone);
        $('#endereco').val(endereco);
        $('#ativo').val(ativo);
        $('#obs').val(obs);
        $('#banco').val(banco);
        $('#agencia').val(agencia);
        $('#email').val(email);

        $('#tituloModal').text('Editar Registro');
        var myModal = new bootstrap.Modal(document.getElementById('modalForm'));
        myModal.show();
    }

    function excluir(id, nome) {
        $('#id-excluir').val(id);
        $('#nome-excluido').text(nome);
        var myModal = new bootstrap.Modal(document.getElementById('modalExcluir'));
        myModal.show();
    }

    $(document).ready(function() {
        // Função para listar os dados
        function listar(){
            $.ajax({
                url: pag + '/listar.php',
                method: 'GET',
                success: function(response) {
                    $('#listar').html(response);
                },
            });
        }
        listar();

        // Mudar máscara do documento entre CPF e CNPJ
        $('#pessoa').change(function() {
            var val = $(this).val();
            if(val === 'Física') {
                $('#doc').mask('000.000.000-00');
            } else {
                $('#doc').mask('00.000.000/0000-00');
            }
        });
        $('#telefone').mask('(00) 00000-0000');

        // Handlers dos formulários de salvar e excluir
        $("#form").submit(function(e) { e.preventDefault(); /* ... */ });
        $("#form-excluir").submit(function(e) { e.preventDefault(); /* ... */ });
        
        // ... (código AJAX de submit idêntico ao de cat_despesas) ...
        $("#form").submit(function (event) { event.preventDefault(); var formData = new FormData(this); $.ajax({ url: pag + "/inserir.php", type: 'POST', data: formData, processData: false, contentType: false, success: function (mensagem) { if (mensagem.trim() == "Salvo com Sucesso") { var modal = bootstrap.Modal.getInstance(document.getElementById('modalForm')); modal.hide(); listar(); } else { $('#mensagem').addClass('text-danger').text(mensagem); } } }); });
        $("#form-excluir").submit(function (event) { event.preventDefault(); var formData = new FormData(this); $.ajax({ url: pag + "/excluir.php", type: 'POST', data: formData, processData: false, contentType: false, success: function (mensagem) { if (mensagem.trim() == "Excluído com Sucesso") { var modal = bootstrap.Modal.getInstance(document.getElementById('modalExcluir')); modal.hide(); listar(); } else { $('#mensagem-excluir').addClass('text-danger').text(mensagem); } } }); });
    });
</script>