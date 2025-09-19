<?php

require_once("../conexao.php");
require_once("verificar.php");
$pagina = 'cat_despesas';

require_once($pagina . "/campos.php");

?>

<div class="col-md-12 my-3">
    <a href="#" onclick="inserir()" type="button" class="btn btn-dark btn-sm">Nova Categoria</a>
</div>

<small>
    <div class="tabela bg-light" id="listar">
        </div>
</small>

<div class="modal fade" id="modalForm" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tituloModal">Inserir Registro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nome" class="form-label"><?php echo $campo1 ?></label>
                        <input type="text" class="form-control" name="nome" placeholder="Nome da Categoria" id="nome-input" required>
                    </div>

                    <small><div id="mensagem" align="center" class="mt-3"></div></small>
                    <input type="hidden" class="form-control" name="id" id="id-input">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btn-fechar">Fechar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalExcluir" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Excluir Registro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-excluir" method="post">
                <div class="modal-body">
                    Deseja realmente excluir este registro: <span id="nome-excluido" class="text-danger"></span>?
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

    function listar() {
        $.ajax({
            url: pag + '/listar.php',
            method: 'GET',
            success: function(response) {
                $('#listar').html(response);
            },
        });
    }

    function inserir() {
        $('#id-input').val('');
        $('#nome-input').val('');
        $('#mensagem').text('');
        $('#tituloModal').text('Inserir Registro');
        var myModal = new bootstrap.Modal(document.getElementById('modalForm'));
        myModal.show();
    }

    function editar(id, nome) {
        $('#id-input').val(id);
        $('#nome-input').val(nome);
        $('#mensagem').text('');
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
        listar();

        $("#form").submit(function (event) {
            event.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                // =====> MUDANÇA AQUI <=====
                url: pag + "/inserir.php",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (mensagem) {
                    if (mensagem.trim() == "Salvo com Sucesso") {
                        var modal = bootstrap.Modal.getInstance(document.getElementById('modalForm'));
                        modal.hide();
                        listar();
                    } else {
                        $('#mensagem').addClass('text-danger').text(mensagem);
                    }
                }
            });
        });

        $("#form-excluir").submit(function (event) {
            event.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                url: pag + "/excluir.php",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (mensagem) {
                    if (mensagem.trim() == "Excluído com Sucesso") {
                        var modal = bootstrap.Modal.getInstance(document.getElementById('modalExcluir'));
                        modal.hide();
                        listar();
                    } else {
                        $('#mensagem-excluir').addClass('text-danger').text(mensagem);
                    }
                }
            });
        });
    });
</script>