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
        </div>
</small>

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
                        <li class="nav-item" role="presentation"><a class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#dados" type="button" role="tab" aria-controls="home" aria-selected="true">Informações Clientes</a></li>
                        <li class="nav-item" role="presentation"><a class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#contas" type="button" role="tab" aria-controls="profile" aria-selected="false">Outros</a></li>
                    </ul>
                    <hr>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="dados" role="tabpanel" aria-labelledby="home-tab">
                            <div class="row">
                                <a href="#" onclick="openModalConsulta('clientes')" type="button" class="btn btn-primary">Consultar informações CNPJ</a>
                            </div>
                            <div class="row" style="margin-top:3.5%;">
                                <div class="col-md-3 col-sm-12"><div class="mb-3"><label for="NomeRes" class="form-label">Nome Resumido</label><input type="text" class="form-control" name="NomeRes" id="NomeRes"></div></div>
                                <div class="col-md-3 col-sm-12"><div class="mb-3"><label for="Nome" class="form-label">Nome</label><input type="text" class="form-control" name="Nome" id="Nome"></div></div>
                                <div class="col-md-3 col-sm-12"><div class="mb-3"><label for="CNPJ" class="form-label">CNPJ</label><input type="text" class="form-control" name="CNPJ" id="CNPJ"></div></div>
                                <div class="col-md-3 col-sm-12"><div class="mb-3"><label for="CPF" class="form-label">CPF</label><input type="text" class="form-control" name="CPF" id="CPF"></div></div>
                                <div class="col-md-3 col-sm-12"><div class="mb-3"><label for="Endereco" class="form-label">Endereço</label><input type="text" class="form-control" name="Endereco" id="Endereco"></div></div>
                                <div class="col-md-3 col-sm-12"><div class="mb-3"><label for="Complemento" class="form-label">Complemento</label><input type="text" class="form-control" name="Complemento" id="Complemento"></div></div>
                                <div class="col-md-3 col-sm-12"><div class="mb-3"><label for="Bairro" class="form-label">Bairro</label><input type="text" class="form-control" name="Bairro" id="Bairro"></div></div>
                                <div class="col-md-3 col-sm-12"><div class="mb-3"><label for="Cidade" class="form-label">Cidade</label><input type="text" class="form-control" name="Cidade" id="Cidade"></div></div>
                                <div class="col-md-3 col-sm-12"><div class="mb-3"><label for="Estado" class="form-label">Estado</label><input type="text" class="form-control" name="Estado" id="Estado"></div></div>
                                <div class="col-md-3 col-sm-12"><div class="mb-3"><label for="Cep" class="form-label">CEP</label><input type="text" class="form-control" name="Cep" id="Cep"></div></div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="contas" role="tabpanel" aria-labelledby="profile-tab">
                            <div class="row">
                                <div class="col-md-3 col-sm-12"><div class="mb-3"><label for="Telefone" class="form-label">Telefone</label><input type="text" class="form-control" name="Telefone" id="Telefone"></div></div>
                                <div class="col-md-3 col-sm-12"><div class="mb-3"><label for="Celular" class="form-label">Celular</label><input type="text" class="form-control" name="Celular" id="Celular"></div></div>
                                <div class="col-md-3 col-sm-12"><div class="mb-3"><label for="InscMun" class="form-label">Inscrição Municipal</label><input type="text" class="form-control" name="InscMun" id="InscMun"></div></div>
                                <div class="col-md-3 col-sm-12"><div class="mb-3"><label for="InscEst" class="form-label">Inscrição Estadual</label><input type="text" class="form-control" name="InscEst" id="InscEst"></div></div>
                                <div class="col-md-3 col-sm-12"><div class="mb-3"><label for="Site" class="form-label">Site</label><input type="text" class="form-control" name="Site" id="Site"></div></div>
                                <div class="col-md-3 col-sm-12"><div class="mb-3"><label for="Email" class="form-label">Email</label><input type="text" class="form-control" name="Email" id="Email"></div></div>
                                <div class="col-md-3 col-sm-12"><div class="mb-3"><label for="Obs" class="form-label">Observações</label><textarea class="form-control" name="Obs" id="Obs"></textarea></div></div>
                            </div>
                        </div>
                    </div>
                    <small><div id="mensagem" align="center"></div></small>
                    <div class="modal-footer" style="margin-right:40%;"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btn-fechar">Fechar</button><button type="submit" class="btn btn-primary">Salvar</button></div>
                    <input type="hidden" class="form-control" name="id" id="id">
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade center" id="modalConsulta" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel" style="margin-left:40%;"><span>Consultar CNPJ</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php include("comum/form-consulta-cnpj.php"); ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalExcluir" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Excluir Registro</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><form id="form-excluir" method="post"><div class="modal-body">Deseja Realmente excluir este Registro: <span id="nome-excluido"></span>?<small><div id="mensagem-excluir" align="center"></div></small><input type="hidden" name="id-excluir"  id="id-excluir"></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button><button type="submit" class="btn btn-danger">Excluir</button></div></form></div></div>
</div>


<script type="text/javascript">
    var pag = "<?php echo isset($pagina) ? htmlspecialchars($pagina) : 'ERRO_PAG_NAO_DEFINIDA'; ?>";

    /**
     * Função para preencher o formulário principal com os dados da consulta de CNPJ.
     */
    window.preencherFormularioComDadosCNPJ = function(dados) {
        if (!dados || !dados.estabelecimento) {
            alert("A consulta retornou dados incompletos.");
            return;
        }
        const est = dados.estabelecimento;
        $('#Nome').val(dados.razao_social || '');
        $('#NomeRes').val(est.nome_fantasia || '');
        $('#CNPJ').val(dados.cnpj || '');
        $('#Endereco').val(`${est.logradouro || ''}, ${est.numero || ''}`);
        $('#Complemento').val(est.complemento || '');
        $('#Bairro').val(est.bairro || '');
        $('#Cidade').val(dados.cidade?.nome || '');
        $('#Estado').val(dados.estado?.sigla || '');
        $('#Cep').val(est.cep ? String(est.cep).replace(/\D/g,'') : '');
        $('#Telefone').val(`${est.ddd1 || ''}${est.telefone1 || ''}`);
        $('#Email').val(est.email || '');
        
        var modalConsulta = bootstrap.Modal.getInstance(document.getElementById('modalConsulta'));
        if (modalConsulta) {
            modalConsulta.hide();
        }
    };

    // Funções de controle dos modais
    function inserir() {
        $('#form').trigger("reset");
        $('#id').val('');
        $('#tituloModal').text('Inserir Novo Registro');
        var myModal = new bootstrap.Modal(document.getElementById('modalForm'));
        myModal.show();
        $('#mensagem').text('');
    }

    function openModalConsulta() {
        var myModal = new bootstrap.Modal(document.getElementById('modalConsulta'));
        myModal.show();
    }

    /**
     * Função que carrega a lista de clientes via AJAX.
     */
    function listarClientes() {
        if (!pag || pag === 'ERRO_PAG_NAO_DEFINIDA') {
            $('#listar').html("<p class='text-danger'>Erro de configuração da página. Não foi possível carregar dados.</p>");
            return;
        }

        $.ajax({
            url: pag + '/listar.php',
            method: 'GET',
            success: function(responseHtml) {
                $('#listar').html(responseHtml);
            },
            error: function() {
                $('#listar').html("<p class='text-danger'>Erro ao carregar lista de clientes. Verifique o console (F12).</p>");
            }
        });
    }


    // Lógica principal executada quando a página carrega
    $(document).ready(function() {
        // **CHAMADA DA FUNÇÃO PARA CARREGAR A LISTA**
        listarClientes();

        // Aplicando máscaras aos campos
        $('#CNPJ').mask('00.000.000/0000-00');
        $('#CPF').mask('000.000.000-00');
        $('#Telefone').mask('(00) 0000-00009');
        $('#Cep').mask('00000-000');

        // Handler para o formulário de SALVAR/EDITAR
        $("#form").submit(function (event) {
            event.preventDefault();
            var formData = new FormData(this);
            var urlSalvar = pag + "/inserir.php"; 

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
                        listarClientes(); // Atualiza a lista após salvar
                    } else {
                        $('#mensagem').addClass('text-danger').text(mensagem);
                    }
                },
                error: function() {
                     $('#mensagem').addClass('text-danger').text("Erro na comunicação com o servidor.");
                }
            });
        });

        // Handler para o formulário de EXCLUIR
        $("#form-excluir").submit(function (event) {
            event.preventDefault();
            var formData = new FormData(this);
            var urlExcluir = pag + "/excluir.php";

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
                        listarClientes(); // Atualiza a lista após excluir
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