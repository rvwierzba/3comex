<!-- clientes.php -->
<?php 
    require_once("../conexao.php");
    require_once("verificar.php");
    require_once("clientes/campos.php");
?>

<div class="col-md-12 my-3">
    <a href="#" onclick="inserir()" type="button" class="btn btn-dark btn-sm">Novo Cadastro</a>
</div>

<small>
    <div class="tabela bg-light" id="listar">
        <!-- Conteúdo da tabela será carregado via AJAX -->
    </div>
</small>

<!-- ModalForm -->
<div class="modal fade center" id="modalForm" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel" style="margin-left:40%;"><span id="tituloModal">Inserir Registro</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form" method="post">
                <div class="modal-body">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#dados" type="button" role="tab" aria-controls="home" aria-selected="true">Informações Clientes</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#contas" type="button" role="tab" aria-controls="profile" aria-selected="false">Outros</a>
                        </li>
                    </ul>
                    
                    <hr>

                    <div class="tab-content" id="myTabContent">
                        <!-- Aba Informações Clientes -->
                        <div class="tab-pane fade show active" id="dados" role="tabpanel" aria-labelledby="home-tab">
                            <div class="row">
                                <a href="#" onclick="openModalConsulta('clientes')" type="button" class="btn btn-primary">Consultar informações CNPJ</a>
                            </div>

                            <div class="row" style="margin-top:3.5%;">
                                <!-- Campos da aba Informações Clientes -->
                                <div class="col-md-3 col-sm-12">
                                    <div class="mb-3">
                                        <label for="NomeRes" class="form-label">Nome Resumido</label>
                                        <input type="text" class="form-control" name="NomeRes" id="NomeRes">
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-12">
                                    <div class="mb-3">
                                        <label for="Nome" class="form-label">Nome</label>
                                        <input type="text" class="form-control" name="Nome" id="Nome">
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-12">
                                    <div class="mb-3">
                                        <label for="CNPJ" class="form-label">CNPJ</label>
                                        <input type="text" class="form-control" name="CNPJ" id="CNPJ">
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-12">
                                    <div class="mb-3">
                                        <label for="CPF" class="form-label">CPF</label>
                                        <input type="text" class="form-control" name="CPF" id="CPF">
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-12">
                                    <div class="mb-3">
                                        <label for="Endereco" class="form-label">Endereço</label>
                                        <input type="text" class="form-control" name="Endereco" id="Endereco">
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-12">
                                    <div class="mb-3">
                                        <label for="Complemento" class="form-label">Complemento</label>
                                        <input type="text" class="form-control" name="Complemento" id="Complemento">
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-12">
                                    <div class="mb-3">
                                        <label for="Bairro" class="form-label">Bairro</label>
                                        <input type="text" class="form-control" name="Bairro" id="Bairro">
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-12">
                                    <div class="mb-3">
                                        <label for="Cidade" class="form-label">Cidade</label>
                                        <input type="text" class="form-control" name="Cidade" id="Cidade">
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-12">
                                    <div class="mb-3">
                                        <label for="Estado" class="form-label">Estado</label>
                                        <input type="text" class="form-control" name="Estado" id="Estado">
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-12">
                                    <div class="mb-3">
                                        <label for="Cep" class="form-label">CEP</label>
                                        <input type="text" class="form-control" name="Cep" id="Cep">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Aba Outros -->
                        <div class="tab-pane fade" id="contas" role="tabpanel" aria-labelledby="profile-tab">
                            <div class="row">
                                <!-- Campos da aba Outros -->
                                <div class="col-md-3 col-sm-12">
                                    <div class="mb-3">
                                        <label for="Telefone" class="form-label">Telefone</label>
                                        <input type="text" class="form-control" name="Telefone" id="Telefone">
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-12">
                                    <div class="mb-3">
                                        <label for="Celular" class="form-label">Celular</label>
                                        <input type="text" class="form-control" name="Celular" id="Celular">
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-12">
                                    <div class="mb-3">
                                        <label for="InscMun" class="form-label">Inscrição Municipal</label>
                                        <input type="text" class="form-control" name="InscMun" id="InscMun">
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-12">
                                    <div class="mb-3">
                                        <label for="InscEst" class="form-label">Inscrição Estadual</label>
                                        <input type="text" class="form-control" name="InscEst" id="InscEst">
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-12">
                                    <div class="mb-3">
                                        <label for="Site" class="form-label">Site</label>
                                        <input type="text" class="form-control" name="Site" id="Site">
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-12">
                                    <div class="mb-3">
                                        <label for="Email" class="form-label">Email</label>
                                        <input type="text" class="form-control" name="Email" id="Email">
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-12">
                                    <div class="mb-3">
                                        <label for="Obs" class="form-label">Observações</label>
                                        <textarea class="form-control" name="Obs" id="Obs"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <small><div id="mensagem" align="center"></div></small>

                    <div class="modal-footer" style="margin-right:40%;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btn-fechar">Fechar</button>
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                    
                    <input type="hidden" class="form-control" name="id" id="id">
                </div>
            </form>
          </div>
    </div>
</div>


<!-- ModalConsulta -->
<div class="modal fade center" id="modalConsulta" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel" style="margin-left:40%;"><span>Consultar CNPJ</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php include("comum/form-consulta-cnpj.php"); ?>
                <small><div id="mensagem" align="center"></div></small>
            </div>
        </div>
    </div>
</div>


<!-- Modal Dados Clientes -->
<div class="modal fade" id="modalDados" tabindex="-1" aria-labelledby="modalDadosClienteLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDadosClienteLabel">Dados do Cliente: <span id="clienteNome"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <small>
                    <!-- Primeira linha -->
                    <div class="row">
                        <div class="col-md-6">
                            <span><b>Nome Completo: </b><span id="campo1"></span></span>
                        </div>
                        <div class="col-md-6">
                            <span><b>CNPJ/CPF: </b><span id="campo2"></span></span>
                        </div>
                    </div>
                    <hr style="margin:6px;">
                    
                    <!-- Segunda linha -->
                    <div class="row">
                        <div class="col-md-6">
                            <span><b>Endereço: </b><span id="campo3"></span></span>
                        </div>
                        <div class="col-md-6">
                            <span><b>Bairro: </b><span id="campo4"></span></span>
                        </div>
                    </div>
                    <hr style="margin:6px;">
                    
                    <!-- Terceira linha -->
                    <div class="row">
                        <div class="col-md-6">
                            <span><b>Cidade: </b><span id="campo5"></span></span>
                        </div>
                        <div class="col-md-6">
                            <span><b>CEP: </b><span id="campo6"></span></span>
                        </div>
                    </div>
                    <hr style="margin:6px;">
                    
                    <!-- Quarta linha -->
                    <div class="row">
                        <div class="col-md-6">
                            <span><b>Telefone: </b><span id="campo7"></span></span>
                        </div>
                        <div class="col-md-6">
                            <span><b>Email: </b><span id="campo8"></span></span>
                        </div>
                    </div>
                    <hr style="margin:6px;">

                    <!-- Continuando até o campo 44 -->
                    <div class="row">
                        <div class="col-md-6">
                            <span><b>Campo 44: </b><span id="campo44"></span></span>
                        </div>
                    </div>
                </small>
            </div>
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
    var pag = "<?=$pagina?>";
</script>
<script src="../js/ajax.js"></script>

<script>
       $(document).ready(function() {
        $('#CNPJ').mask('00.000.000/0000-00');
        $('#CPF').mask('000.000.000-00');
        $('#cnpj-form').data('origin', 'clientes');
    });

    function openModalConsulta(origin){
       // Esconde o modal atual (formulário principal)
       $('#modalForm').modal('hide');

        // Limpa os campos do modal de consulta
        limparCamposConsultaCNPJ();

        // Armazena a origem no formulário de consulta de CNPJ
        $('#modalConsulta').data('origin', origin);

        // Exibe o modal de consulta de CNPJ
        var myModal = new bootstrap.Modal(document.getElementById('modalConsulta'), {});
        myModal.show();
    }
</script>
