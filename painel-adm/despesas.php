<?php
// painel-adm/despesas.php (AJUSTADO PARA O PADRÃO CORRETO)

require_once("../conexao.php");
require_once("verificar.php");
$pagina = 'despesas';

require_once($pagina . "/campos.php");

?>

<div class="col-md-12 my-3">
    <a href="#" onclick="inserir()" type="button" class="btn btn-dark btn-sm">Nova Despesa</a>
</div>

<small>
    <div class="tabela bg-light" id="listar"></div>
</small>

<div class="modal fade" id="modalForm" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tituloModal">Inserir Registro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="<?php echo $campo1 ?>" class="form-label"><?php echo ucfirst(str_replace('_', ' ', $campo1)) ?></label>
                        <input type="text" class="form-control" name="<?php echo $campo1 ?>" id="<?php echo $campo1 ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="<?php echo $campo2 ?>" class="form-label"><?php echo ucfirst(str_replace('_', ' ', $campo2)) ?></label>
                        <select class="form-select" name="<?php echo $campo2 ?>" id="<?php echo $campo2 ?>">
                            <?php 
                                $query = $pdo->query("SELECT * from cat_despesas order by nome asc");
                                $res = $query->fetchAll(PDO::FETCH_ASSOC);
                                for($i=0; $i < @count($res); $i++){
                                    echo "<option value='".$res[$i]['id']."'>".$res[$i]['nome']."</option>";
                                }
                            ?>
                        </select>
                    </div>

                    <small><div id="mensagem" align="center" class="mt-3"></div></small>
                    <input type="hidden" class="form-control" name="id" id="id-input">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
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
    var campo1 = "<?=$campo1?>";
    var campo2 = "<?=$campo2?>";

    function listar() { /* ... */ } // Implementado abaixo

    function inserir() {
        $('#id-input').val('');
        $('#form').trigger("reset");
        $('#tituloModal').text('Inserir Registro');
        var myModal = new bootstrap.Modal(document.getElementById('modalForm'));
        myModal.show();
    }

    function editar(id, valorCampo1, valorCampo2) {
        $('#id-input').val(id);
        $('#' + campo1).val(valorCampo1);
        $('#' + campo2).val(valorCampo2);
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
    
    // O código de listar e dos formulários submit continua o mesmo da versão anterior,
    // pois ele já está preparado para funcionar com esta nova lógica.
    $(document).ready(function() {
        function listar(){ $.ajax({ url: pag + '/listar.php', method: 'GET', success: function(r) { $('#listar').html(r); } }); }
        listar();
        $("#form").submit(function(e){ e.preventDefault(); var fd=new FormData(this); $.ajax({ url: pag+"/inserir.php", type:'POST', data:fd, processData:false, contentType:false, success: function(m){ if(m.trim()=="Salvo com Sucesso"){ bootstrap.Modal.getInstance(document.getElementById('modalForm')).hide(); listar(); } else { $('#mensagem').addClass('text-danger').text(m); } } }); });
        $("#form-excluir").submit(function(e){ e.preventDefault(); var fd=new FormData(this); $.ajax({ url: pag+"/excluir.php", type:'POST', data:fd, processData:false, contentType:false, success: function(m){ if(m.trim()=="Excluído com Sucesso"){ bootstrap.Modal.getInstance(document.getElementById('modalExcluir')).hide(); listar(); } else { $('#mensagem-excluir').addClass('text-danger').text(m); } } }); });
    });
</script>