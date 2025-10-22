<?php 
require_once("../conexao.php");
require_once("verificar.php");
$pagina = 'bancarias';

require_once($pagina."/campos.php");

?>

<div class="col-md-12 my-3">
	<a href="#" onclick="inserir()" type="button" class="btn btn-dark btn-sm">Nova Conta Bancária</a>
</div>

<small>
	<div class="tabela bg-light" id="listar">

	</div>
</small>



<!-- Modal -->
<div class="modal fade" id="modalForm" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel"><span id="tituloModal">Inserir Registro</span></h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form id="form" method="post">
				<div class="modal-body">

					<div class="row">
						<div class="col-md-6 col-sm-12">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label"><?php echo $campo1 ?> </label>
								<select class="form-select" aria-label="Default select example" name="<?php echo $campo1 ?>" id="<?php echo $campo1 ?>">
									<?php 
									$query = $pdo->query("SELECT * FROM bancos order by nome asc");
									$res = $query->fetchAll(PDO::FETCH_ASSOC);
									for($i=0; $i < @count($res); $i++){
										foreach ($res[$i] as $key => $value){	}
											$id_item = $res[$i]['id'];
										$nome_item = $res[$i]['nome'];
										?>
										<option value="<?php echo $nome_item ?>"><?php echo $nome_item ?></option>

									<?php } ?>


								</select>
							</div>
						</div>
						<div class="col-md-6 col-sm-12">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label"><?php echo $campo2 ?></label>
								<input type="text" class="form-control" name="<?php echo $campo2 ?>" placeholder="<?php echo $campo2 ?>" id="<?php echo $campo2 ?>" required>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6 col-sm-12">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label"><?php echo $campo3 ?></label>
								<input type="text" class="form-control" name="<?php echo $campo3 ?>" placeholder="<?php echo $campo3 ?>" id="<?php echo $campo3 ?>" required>
							</div>
						</div>
						<div class="col-md-6 col-sm-12">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label"><?php echo $campo4 ?></label>
								<select class="form-select" aria-label="Default select example" name="<?php echo $campo4 ?>" id="<?php echo $campo4 ?>">
									<option value="Corrente">Corrente</option>
									<option value="Poupança">Poupança</option>
								</select>
							</div>
						</div>
					</div>

					

					<div class="row">
						<div class="col-md-6 col-sm-12">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label"><?php echo $campo5 ?></label>
								<select class="form-select" aria-label="Default select example" name="<?php echo $campo5 ?>" id="<?php echo $campo5 ?>">
									<option value="Física">Física</option>
									<option value="Jurídica">Jurídica</option>

								</select>
							</div>
						</div>
						<div class="col-md-6 col-sm-12">
							<div class="mb-3">
								<label for="exampleFormControlInput1" class="form-label">CPF / CNPJ</label>
								<input type="text" class="form-control" name="<?php echo $campo6 ?>" id="<?php echo $campo6 ?>" required>
							</div>
						</div>
					</div>

					<small><div id="mensagem" align="center"></div></small>

					<input type="hidden" class="form-control" name="id"  id="id">


				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btn-fechar">Fechar</button>
					<button type="submit" class="btn btn-primary">Salvar</button>
				</div>
			</form>
		</div>
	</div>
</div>




<!-- Modal -->
<div class="modal fade" id="modalExcluir" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel"><span id="tituloModal">Excluir Registro</span></h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form id="form-excluir" method="post">
				<div class="modal-body">

					Deseja Realmente excluir este Registro: <span id="nome-excluido"></span>?

					<small><div id="mensagem-excluir" align="center"></div></small>

					<input type="hidden" class="form-control" name="id-excluir"  id="id-excluir">


				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btn-fechar-excluir">Fechar</button>
					<button type="submit" class="btn btn-danger">Excluir</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
    // Garante que o PHP setou a variável 'pag' para 'bancarias'
    var pag = "<?=$pagina?>"; 
</script>
<script src="../js/ajax.js"></script>


<script>
    // Função para abrir o modal em modo de inserção (chamada pelo onclick do botão)
    function inserir() {
        $('#form').trigger("reset");
        $('#id').val('');
        $('#tituloModal').text('Inserir Novo Registro');
        var myModal = new bootstrap.Modal(document.getElementById('modalForm'));
        myModal.show();
        $('#mensagem').text('');
        limparCampos(); // Garante que os campos estejam limpos
    }

    // Função que chama o listar.php e preenche a div #listar
    function listarRegistros() {
        $.ajax({
            // Chama o script de listagem dentro da pasta bancarias/
            url: pag + '/listar.php', 
            method: 'GET',
            success: function(responseHtml) {
                $('#listar').html(responseHtml);
            },
            error: function() {
                $('#listar').html("<p class='text-danger'>Erro ao carregar a lista de registros.</p>");
            }
        });
    }

    // FUNÇÃO COMPLETA: Abre o modal para EDIÇÃO
    function editar(id, cp1, cp2, cp3, cp4, cp5, cp6){
        $('#mensagem').text('');
        $('#id').val(id);
        
        // 1. Preenche os campos de texto
        $('#<?=$campo2?>').val(cp2); // Agência
        $('#<?=$campo3?>').val(cp3); // Conta
        $('#<?=$campo6?>').val(cp6); // CPF / CNPJ

        // 2. Preenche os campos SELECT (e dispara o trigger para máscara/select2)
        $('#<?=$campo1?>').val(cp1).trigger('change'); // Banco
        $('#<?=$campo4?>').val(cp4).trigger('change'); // Tipo (Corrente/Poupança)
        $('#<?=$campo5?>').val(cp5).trigger('change'); // Pessoa
        
        // Garante que o valor do documento seja setado após a máscara mudar
        setTimeout(function(){
            $('#<?=$campo6?>').val(cp6);
        }, 50);
        
        $('#tituloModal').text('Editar Registro');
        var myModal = new bootstrap.Modal(document.getElementById('modalForm'));
        myModal.show();
    }

    // FUNÇÃO COMPLETA: Prepara e chama o modal de EXCLUSÃO
    function excluir(id, nome){
        $('#id-excluir').val(id);
        $('#nome-excluido').text(nome); // Assumindo que 'nome' é o banco ou conta para descrição
        $('#mensagem-excluir').text('');
        
        var myModal = new bootstrap.Modal(document.getElementById('modalExcluir'));
        myModal.show();
    }

    // Função para limpar os campos do modal após salvar/inserir
    function limparCampos(){
        $('#form').trigger("reset"); // Limpa todos os inputs e selects
        $('#id').val('');
        
        // Ajuste específico para campos que podem precisar de re-inicialização após reset
        $('#<?=$campo5?>').val('Física').trigger('change'); // Pessoa (Redefinido para padrão)
        $('#<?=$campo6?>').val(''); // Limpa o CPF/CNPJ

        $('#mensagem').text('');
    }
    

    $(document).ready(function() {
        // 1. Inicializa a lista ao carregar a página
        listarRegistros(); 
        
        // 2. Aplica máscaras e handlers de mudança no CPF/CNPJ
        $('#<?=$campo6?>').mask('000.000.000-00');
        $('#<?=$campo6?>').attr('placeholder','CPF');

        $('#<?=$campo5?>').change(function(){
            if($(this).val() == 'Física'){
                $('#<?=$campo6?>').mask('000.000.000-00');
                $('#<?=$campo6?>').attr('placeholder','CPF');
            }else{
                $('#<?=$campo6?>').mask('00.000.000/0000-00');
                $('#<?=$campo6?>').attr('placeholder','CNPJ');
            }
            $('#<?=$campo6?>').val(''); // Limpa o valor do campo de documento ao mudar o tipo de pessoa
        });


        // 3. Handler principal para o formulário de SALVAR/EDITAR (INSERIR/ATUALIZAR)
        $("#form").submit(function (event) {
            event.preventDefault();
            var formData = new FormData(this);
            var urlSalvar = pag + "/inserir.php"; // Chama bancarias/inserir.php

            $.ajax({
                url: urlSalvar,
                type: 'POST',
                data: formData,
                dataType: 'text',
                processData: false,
                contentType: false,
                success: function (mensagem) {
                    if (mensagem.trim() == "Salvo com Sucesso") {
                        var modal = bootstrap.Modal.getInstance(document.getElementById('modalForm'));
                        modal.hide();
                        listarRegistros(); // Atualiza a lista após salvar
                    } else {
                        $('#mensagem').addClass('text-danger').text(mensagem);
                    }
                },
                error: function(xhr, status, error) {
                    $('#mensagem').addClass('text-danger').text("Erro de comunicação com o servidor (Status: " + status + "). Verifique o console (F12).");
                    console.error("Erro AJAX: ", status, error, xhr.responseText);
                }
            });
        });


        // 4. Handler para o formulário de EXCLUIR (AGORA COMPLETO)
        $("#form-excluir").submit(function (event) {
            event.preventDefault();
            var formData = new FormData(this);
            var urlExcluir = pag + "/excluir.php"; // Chama bancarias/excluir.php

            $.ajax({
                url: urlExcluir,
                type: 'POST',
                data: formData,
                dataType: 'text',
                processData: false,
                contentType: false,
                success: function (mensagem) {
                    if (mensagem.trim() == "Excluído com Sucesso") {
                        var modal = bootstrap.Modal.getInstance(document.getElementById('modalExcluir'));
                        modal.hide();
                        listarRegistros(); // Atualiza a lista após excluir
                    } else {
                        $('#mensagem-excluir').addClass('text-danger').text(mensagem);
                    }
                },
                error: function() {
                    $('#mensagem-excluir').addClass('text-danger').text("Erro na comunicação com o servidor.");
                }
            });
        });

    });
</script>
