<!-- agentes.php -->
<?php 
require_once("../conexao.php");
require_once("verificar.php");
require_once("../painel-adm/agentes/campos.php");
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
                            <a class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#dados" type="button" role="tab" aria-controls="home" aria-selected="true">Informações Gerais</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#contas" type="button" role="tab" aria-controls="profile" aria-selected="false">Contatos</a>
                        </li>
                    </ul>
                    
                    <hr>

                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="dados" role="tabpanel" aria-labelledby="home-tab">
                            <div class="row">
                                <a href="#" onclick="openModalConsulta('agentes')" type="button" class="btn btn-primary">Consultar informações CNPJ</a>
                            </div>

                            <div class="row" style="margin-top:3.5%;">
                                <!-- Campos do formulário de agentes -->
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
                                        <label for="Tipo" class="form-label">Tipo</label>
                                        <input type="text" class="form-control" name="Tipo" id="Tipo">
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
                                <div class="col-md-3 col-sm-12">
                                    <div class="mb-3">
                                        <label for="Telefone" class="form-label">Telefone</label>
                                        <input type="text" class="form-control" name="Telefone" id="Telefone">
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-12">
                                    <div class="mb-3">
                                        <label for="Whatsapp" class="form-label">Whatsapp</label>
                                        <input type="text" class="form-control" name="Whatsapp" id="Whatsapp">
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
                            </div>
                        </div>

                        <div class="tab-pane fade" id="contas" role="tabpanel" aria-labelledby="profile-tab">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="Contato1" class="form-label">Contato 1</label>
                                        <input type="text" class="form-control" name="Contato1" id="Contato1">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="email1" class="form-label">Email 1</label>
                                        <input type="text" class="form-control" name="email1" id="email1">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="Telefone1" class="form-label">Telefone 1</label>
                                        <input type="text" class="form-control" name="Telefone1" id="Telefone1">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="Cargo1" class="form-label">Cargo 1</label>
                                        <input type="text" class="form-control" name="Cargo1" id="Cargo1">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="Obs1" class="form-label">Observação 1</label>
                                        <input type="text" class="form-control" name="Obs1" id="Obs1">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="Contato2" class="form-label">Contato 2</label>
                                        <input type="text" class="form-control" name="Contato2" id="Contato2">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="Email2" class="form-label">Email 2</label>
                                        <input type="text" class="form-control" name="Email2" id="Email2">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="Telefone2" class="form-label">Telefone 2</label>
                                        <input type="text" class="form-control" name="Telefone2" id="Telefone2">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="Cargo2" class="form-label">Cargo 2</label>
                                        <input type="text" class="form-control" name="Cargo2" id="Cargo2">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="Obs2" class="form-label">Observação 2</label>
                                        <input type="text" class="form-control" name="Obs2" id="Obs2">
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

<!-- Modal Dados Agentes -->
<div class="modal fade" id="modalDados" tabindex="-1" aria-labelledby="modalDadosAgenteLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDadosAgenteLabel">Dados do Agente: <span id="agenteNome"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <small>
                    <!-- Primeira linha -->
                    <div class="row">
                        <div class="col-md-6">
                            <span><b>Nome Resumido: </b><span id="campo1"></span></span>
                        </div>
                        <div class="col-md-6">
                            <span><b>CNPJ: </b><span id="campo2"></span></span>
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
                    
                    <!-- Quinta linha -->
                    <div class="row">
                        <div class="col-md-6">
                            <span><b>Inscrição Estadual: </b><span id="campo9"></span></span>
                        </div>
                        <div class="col-md-6">
                            <span><b>Inscrição Municipal: </b><span id="campo10"></span></span>
                        </div>
                    </div>
                    <hr style="margin:6px;">
                    
                    <!-- Continuando até o campo 25 -->
                    <div class="row">
                        <div class="col-md-6">
                            <span><b>Campo 11: </b><span id="campo11"></span></span>
                        </div>
                        <div class="col-md-6">
                            <span><b>Campo 12: </b><span id="campo12"></span></span>
                        </div>
                    </div>
                    <hr style="margin:6px;">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <span><b>Campo 13: </b><span id="campo13"></span></span>
                        </div>
                        <div class="col-md-6">
                            <span><b>Campo 14: </b><span id="campo14"></span></span>
                        </div>
                    </div>
                    <hr style="margin:6px;">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <span><b>Campo 15: </b><span id="campo15"></span></span>
                        </div>
                        <div class="col-md-6">
                            <span><b>Campo 16: </b><span id="campo16"></span></span>
                        </div>
                    </div>
                    <hr style="margin:6px;">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <span><b>Campo 17: </b><span id="campo17"></span></span>
                        </div>
                        <div class="col-md-6">
                            <span><b>Campo 18: </b><span id="campo18"></span></span>
                        </div>
                    </div>
                    <hr style="margin:6px;">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <span><b>Campo 19: </b><span id="campo19"></span></span>
                        </div>
                        <div class="col-md-6">
                            <span><b>Campo 20: </b><span id="campo20"></span></span>
                        </div>
                    </div>
                    <hr style="margin:6px;">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <span><b>Campo 21: </b><span id="campo21"></span></span>
                        </div>
                        <div class="col-md-6">
                            <span><b>Campo 22: </b><span id="campo22"></span></span>
                        </div>
                    </div>
                    <hr style="margin:6px;">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <span><b>Campo 23: </b><span id="campo23"></span></span>
                        </div>
                        <div class="col-md-6">
                            <span><b>Campo 24: </b><span id="campo24"></span></span>
                        </div>
                    </div>
                    <hr style="margin:6px;">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <span><b>Responsável: </b><span id="campo25"></span></span>
                        </div>
                    </div>
                </small>
            </div>
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
        $('#cnpj-form').data('origin', 'agentes');


  
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
