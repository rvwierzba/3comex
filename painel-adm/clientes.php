<?php 
    require_once("../conexao.php");
    require_once("verificar.php");
    require_once("clientes/campos.php");

    $clientes_url_base = $pagina; 
    $bancaria_url_base = "bancarias";


    $base_path = 'painel-adm';    
    $base_path_bancarias = $base_path ."/". $bancaria_url_base; 
    $base_path_clientes = $base_path ."/". $clientes_url_base;

    
    $base_url_projeto = "/3comex"; 

   
    $js_vars = [
        'pag' => $pagina,
        'pag_clientes' => $clientes_url_base,
        'path_bancarias' => $base_path_bancarias,
        'path_clientes' => $base_path_clientes,
        'base_http_raiz' => 'http://localhost' . $base_url_projeto
    ];
    
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
                <input type="hidden" class="form-control" name="tipo_acao" id="tipo_acao" value="cliente"> 
                <input type="hidden" class="form-control" name="doc_cliente_pai" id="doc_cliente_pai">
                <div class="modal-body">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation"><a class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#dados" type="button" role="tab" aria-controls="home" aria-selected="true">Informações Clientes</a></li>
                        <li class="nav-item" role="presentation"><a class="nav-link" id="profile-tab-outros" data-bs-toggle="tab" data-bs-target="#contas" type="button" role="tab" aria-controls="profile" aria-selected="false">Outros</a></li>
                        <li class="nav-item" role="presentation"><a class="nav-link" id="dadosBanc-tab" data-bs-toggle="tab" data-bs-target="#dadosBanc" type="button" role="tab" aria-controls="profile" aria-selected="false">Dados bancários</a></li>
                        <li class="nav-item" role="presentation"><a class="nav-link" id="vencimentos-tab" data-bs-toggle="tab" data-bs-target="#vencimentos" type="button" role="tab" aria-controls="profile" aria-selected="false">Vencimentos</a></li>
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

                        <div class="tab-pane fade" id="contas" role="tabpanel" aria-labelledby="profile-tab-outros">
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

                        <div class="tab-pane fade" id="dadosBanc" role="tabpanel" aria-labelledby="dadosBanc-tab">
                           <button type="button" onclick="novaContaBancaria()" class="btn btn-dark btn-sm">Nova Conta Bancária</button>
                            <div class="table-responsive mt-3">
                                <table id="tabelaContasCliente" class="table table-striped table-hover my-4">
                                    <thead>
                                        <tr>
                                            <th>Banco</th>
                                            <th>Agência</th>
                                            <th>Conta</th> 
                                            <th>Tipo Conta</th> 
                                            <th>Pessoa</th> 
                                            <th>CPF / CNPJ</th> 
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody id="corpoTabelaBancaria">
                                        <!-- AQUI VIRÃO AS CONTAS BANCÁRIAS DO CLIENTE, QUANDO TIVEREM CONTAS BANC. CADASTRADAS EM SEU DOC. (CPF OR CNPJ) -->
                                    </tbody>                                
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="vencimentos" role="tabpanel" aria-labelledby="vencimentos-tab">
                            <div class="row g-3"> 
                                <div class="col-md-3 col-sm-12"><div class="mb-3"><label for="DiasVenc" class="form-label">Dias Vencimento</label><input type="number" class="form-control" name="DiasVenc" id="DiasVenc"></div></div>
                                <div class="col-md-3 col-sm-12"><div class="mb-3"><label for="VencRadar" class="form-label">Vencimento Radar</label><input type="date" class="form-control" name="VencRadar" id="VencRadar"></div></div>
                                <div class="col-md-3 col-sm-12"><div class="mb-3"><label for="VencProcuracao" class="form-label">Vencimento Procuração</label><input type="date" class="form-control" name="VencProcuracao" id="VencProcuracao"></div></div>
                                <div class="col-md-3 col-sm-12"><div class="mb-3"><label for="VencMercante" class="form-label">Vencimento Mercante</label><input type="date" class="form-control" name="VencMercante" id="VencMercante"></div></div>
                                <div class="col-md-3 col-sm-12"><div class="mb-3"><label for="VencAnvisa" class="form-label">Vencimento Anvisa</label><input type="date" class="form-control" name="VencAnvisa" id="VencAnvisa"></div></div>
                            </div>
                        </div>
                    </div>
                </div>
                <small><div id="mensagem" align="center"></div></small>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btn-fechar">Fechar</button><button type="button" class="btn btn-primary">Salvar</button></div>
                <input type="hidden" class="form-control" name="id" id="id">
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



<!--   MODAIS CONTAS BANCÁRIAS  =============================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================-->

<!-- ModalExcluirConta -->
<div class="modal fade" id="modalExcluirConta" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><span id="tituloModalExcluirConta">Excluir Conta Bancária</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-excluir-conta" method="post">
                <div class="modal-body">

                    Deseja Realmente excluir este Registro: <span id="nome-excluido-conta"></span>?

                    <small><div id="mensagem-excluir-conta" align="center"></div></small>

                    <input type="hidden" class="form-control" name="id-excluir"  id="id-excluir-conta">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btn-fechar-excluir-conta">Fechar</button>
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ModalConta-->
<div class="modal fade" id="modalConta" tabindex="-1" aria-labelledby="exampleModalLabelConta" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabelConta"><span id="tituloModalConta">Inserir Registro</span></h5> 
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-conta" method="post"> 
                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="mb-3">
                                <label for="Banco" class="form-label">Banco</label>
                                <select class="form-select" aria-label="Default select example" name="Banco" id="Banco">
                                    <?php 
                                    $query = $pdo->query("SELECT * FROM bancos order by nome asc");
                                    $res = $query->fetchAll(PDO::FETCH_ASSOC);
                                    for($i=0; $i < @count($res); $i++){
                                        foreach ($res[$i] as $key => $value){  }
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
                                <label for="Agencia" class="form-label">Agência</label>
                                <input type="text" class="form-control" name="Agencia" placeholder="Agência" id="Agencia" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="mb-3">
                                <label for="Conta" class="form-label">Conta</label>
                                <input type="text" class="form-control" name="Conta" placeholder="Conta" id="Conta" required>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="mb-3">
                                <label for="Tipo" class="form-label">Tipo Conta</label>
                                <select class="form-select" aria-label="Default select example" name="Tipo" id="Tipo">
                                    <option value="Corrente">Corrente</option>
                                    <option value="Poupança">Poupança</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="mb-3">
                                <label for="Pessoa" class="form-label">Pessoa</label>
                                <select class="form-select" aria-label="Default select example" name="Pessoa" id="Pessoa">
                                 <option value="">Selecionar Pessoa</option>
                                    <option value="F">Física</option> 
                                    <option value="J">Jurídica</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="mb-3">
                                <label for="Doc" class="form-label">CPF / CNPJ da Conta</label>
                                <input type="text" class="form-control" name="Doc" id="Doc" required>
                            </div>
                        </div>
                    </div>

                    <small><div id="mensagem-conta" align="center"></div></small>

                    <input type="hidden" class="form-control" name="id" id="id-conta">
                    <input type="hidden" class="form-control" name="doc_cliente" id="doc-cliente-conta"> 

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btn-fechar-conta">Fechar</button>
                    <button type="submit" onclick="submeterContaBancaria()" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>



<!-- ======================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================== -->

<script type="text/javascript">
    // Garante que o PHP setou a variável 'pag'
    var pag = "<?=$pagina?>"; 
</script>


<script type="text/javascript">


  // CÓDIGOS RELACIONADOS A CONSULTA DE CNPJ PARA INCLUSÃO COMO CLIENTE=========================================================================                                      


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
        $('#CNPJ').val(dados.cnpj_raiz || '');
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




//=================================================================================================================================================

    // INJEÇÃO SEGURO DAS VARIÁVEIS PHP NO JAVASCRIPT
    const config = <?php echo json_encode($js_vars); ?>;
    
    let id_cliente_atual = 0; // Armazena o CPF/CNPJ do cliente que será consultado

    
    // =====================================================================================
    // FUNÇÕES GLOBAIS (DEFINIDAS FORA DO ready PARA SEREM CHAMADAS PELO HTML/OUTRAS FUNÇÕES)
    // =====================================================================================

    // --- FUNÇÃO DE RECARREGAMENTO DE CONTAS BANCÁRIAS (AGORA GLOBAL) ---
   function carregarDadosBancarios() {
        // Verifique o ID da tabela no seu HTML!
        var tabelaContas = $('#tabelaBancaria'); // Ex: <table id="tabelaBancaria">
        var corpoTabela = $('#corpoTabelaBancaria'); // Ex: <tbody id="corpoTabelaBancaria">

        if (id_cliente_atual === 0 || id_cliente_atual === "") {
            corpoTabela.html('<tr><td colspan="7" class="text-center text-danger">Erro: Documento do cliente não definido.</td></tr>');
            return;
        }

        // 1. DESTRUIÇÃO SEGURA DO DataTables (RESOLVE O ERRO 'parentNode is null')
        if ($.fn.dataTable.isDataTable(tabelaContas)) { 
            try { 
                tabelaContas.DataTable().destroy(); 
            } catch (e) { 
                console.error("Erro ao tentar destruir DataTables:", e);
            }
        }
        
        // 2. CHAMA O AJAX USANDO MÉTODO POST (Conforme o PHP exige)
        var urlListarContas = config.base_http_raiz + "/" + config.path_clientes + "/listar-contas.php"; 
        
        // Dados a serem enviados via POST
        var dadosParaEnviar = { doc: id_cliente_atual }; 
        
        // Limpa a tabela antes de carregar
        corpoTabela.html('<tr><td colspan="7" class="text-center text-secondary">Carregando dados...</td></tr>');


        $.ajax({
            url: urlListarContas,
            method: 'POST', // CORREÇÃO CRÍTICA: USAR POST
            data: dadosParaEnviar, // Envia o documento do cliente
            success: function(responseHtml) {
                
                // 3. Insere o novo HTML
                corpoTabela.html(responseHtml); 
                
                // 4. Reinicializa DataTables
                if ($('#corpoTabelaBancaria tr').length > 0) {
                    tabelaContas.DataTable({ 
                        "ordering": false,
                        "language": {"url": "../js/pt-BR.json"}
                    });
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                corpoTabela.html('<tr><td colspan="7" class="text-center text-danger">Erro de comunicação/rede (' + textStatus + ').</td></tr>');
            }
        });
    }


    // --- FUNÇÃO PARA ABRIR MODAL DE CONTAS (Chamada pelo listar.php) ---
    function abrirModalContas(documento) {
        id_cliente_atual = documento; 
        
        // Sobrescreve imediatamente a mensagem fixa do HTML para 'Carregando dados...'
        $('#corpoTabelaBancaria').html('<tr><td colspan="7" class="text-center text-secondary">Carregando dados...</td></tr>');

        // Chama a função global carregarDadosBancarios
        carregarDadosBancarios();
    }


    // Função auxiliar para limpar e contar dígitos
    function contarDigitos(documento) {
        if (!documento) return 0;
        const documentoLimpo = documento.toString().replace(/\D/g, ''); 
        return documentoLimpo.length;
    }

    
    // --- FUNÇÃO PARA ABRIR MODAL DE NOVA CONTA BANCÁRIA (Chamada pelo botão Nova Conta) ---
   function novaContaBancaria() {
        if (id_cliente_atual === 0 || id_cliente_atual === "") {
            alert("Erro: Salve ou edite o cliente primeiro para poder adicionar contas bancárias.");
            return;
        }
        
        // Fecha o modal do cliente
        var clienteModal = bootstrap.Modal.getInstance(document.getElementById('modalForm'));
        if (clienteModal) { clienteModal.hide(); }
        
        // Limpa e configura o modal da conta
        $('#tituloModalConta').text('Inserir Nova Conta Bancária');
        $('#form-conta')[0].reset(); 
        $('#mensagem-conta').text('');
        $('#id-conta').val(''); 
        
        $('#doc-cliente-conta').val(id_cliente_atual); 

        // CRÍTICO: Preenche o campo #Doc (que o PHP espera) e #Pessoa
        $('#Doc').val(id_cliente_atual); 

        const numDigitos = contarDigitos(id_cliente_atual);
        
        if (numDigitos === 11) {
            $('#Pessoa').val('F').trigger('change'); 
        } else if (numDigitos === 14) {
            $('#Pessoa').val('J').trigger('change'); 
        } else {
            $('#Pessoa').val('').trigger('change'); 
        }

        // Abre o Modal da Conta
        var contaModal = new bootstrap.Modal(document.getElementById('modalConta'), {});
        contaModal.show();
    }

    // --- FUNÇÃO DEDICADA PARA SUBMETER O CADASTRO/EDIÇÃO DA CONTA BANCÁRIA ---
    // Esta função é chamada quando o botão 'Salvar' do modal de contas é acionado.
   function submeterContaBancaria() {
        const formContaElement = document.getElementById('form-conta'); 
        
        if (!formContaElement) {
            $('#mensagem-conta').addClass('text-danger').text('ERRO FATAL JS: Formulário #form-conta não encontrado.');
            return;
        }

        var formData = new FormData(formContaElement); 
        var urlAcao = config.base_http_raiz + "/" + config.path_bancarias + "/inserir.php"; 

        // Limpa a mensagem antes de enviar
        $('#mensagem-conta').text('');
        $('#mensagem-conta').removeClass(); 
        $('#mensagem-conta').text('Aguardando resposta do servidor...'); // Feedback de envio

        $.ajax({
            url: urlAcao,
            type: 'POST',
            data: formData,

            success: function (mensagem) {
                
                var mensagemLimpa = mensagem.trim();

                if (mensagemLimpa == "Salvo com Sucesso") {
                    
                    // SE HOUVE SUCESSO, A MENSAGEM PERMANECERÁ NA TELA
                    $('#mensagem-conta').addClass('text-success').text("SUCESSO: O registro foi salvo no banco de dados.");
                    
                    // NOTA: NÃO TENTA FECHAR O MODAL NEM CHAMAR carregarDadosBancarios().
                    
                } else {
                    // SE HOUVE ERRO (SQL, Validação, etc.), EXIBE EM VERMELHO
                    $('#mensagem-conta').addClass('text-danger').text(mensagemLimpa);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                // ERRO DE REDE OU SERVIDOR
                $('#mensagem-conta').addClass('text-danger').text('ERRO DE REDE/COMUNICAÇÃO: O PHP não respondeu. Verifique o console. Status: ' + textStatus);
            },

            cache: false,
            contentType: false,
            processData: false,
        });
    }


    // --- FUNÇÃO DE EDIÇÃO DE CONTA BANCÁRIA (Chamada pelo listar-contas.php) ---
  
    function editarConta(e, id, banco, agencia, conta, tipo, pessoa, doc_conta) {
        if (e) { e.stopPropagation(); } // <<<< FIX CRÍTICO: IMPEDE QUE O CLIQUE FECHE O MODAL PAI

        // FECHA O MODAL DO CLIENTE (#modalForm)
        var clienteModal = bootstrap.Modal.getInstance(document.getElementById('modalForm'));
        if (clienteModal) { clienteModal.hide(); }

        // PREENCHE E ABRE O MODAL DA CONTA (#modalConta)
        $('#id-conta').val(id);
        $('#Banco').val(banco);
        $('#Agencia').val(agencia);
        $('#ContaNumero').val(conta);
        $('#Tipo').val(tipo);
        $('#Pessoa').val(pessoa);
        $('#DocConta').val(doc_conta);
        $('#doc-cliente-conta').val(id_cliente_atual); 

        $('#tituloModalConta').text('Editar Conta Bancária');
        
        var contaModal = new bootstrap.Modal(document.getElementById('modalConta'), {});
        contaModal.show();
        $('#mensagem-conta').text('');
    }

    // --- FUNÇÃO DE EXCLUSÃO DE CONTA BANCÁRIA (Chamada pelo listar-contas.php) ---
   function excluirConta(e, conta_id, nome_banco) {
        if (e) { e.stopPropagation(); } // <<<< FIX CRÍTICO: IMPEDE QUE O CLIQUE FECHE O MODAL PAI
        
        // FECHA O MODAL DO CLIENTE (#modalForm)
        var clienteModal = bootstrap.Modal.getInstance(document.getElementById('modalForm'));
        if (clienteModal) { clienteModal.hide(); }

        // CONFIGURA E ABRE O MODAL DE EXCLUSÃO (#modalExcluirConta)
        $('#nome-excluido-conta').text(nome_banco); 
        $('#id-excluir-conta').val(conta_id); 
        
        var excluirModal = new bootstrap.Modal(document.getElementById('modalExcluirConta'), {});
        excluirModal.show();
        $('#mensagem-excluir-conta').text('');
    }
    
    // =====================================================================================
    // FUNÇÕES INTERNAS (AJAX DE SUBMISSÃO E LISTAGEM DE CLIENTES)
    // =====================================================================================

    $(document).ready(function() {
        
         // --- AJAX PARA INSERÇÃO/EDIÇÃO DE CONTA BANCÁRIA (id="form-conta") ---
        function novoCadastroCliente(){
             // 1. ZERA O ESTADO CRÍTICO (variável global usada para contas)
            id_cliente_atual = 0; 

            // 2. Limpa visualmente a tabela de contas no modal (garante que não apareça lixo)
            $('#corpoTabelaBancaria').html('<tr><td colspan="7" class="text-center text-secondary">Nenhuma conta cadastrada. Salve o cliente para adicionar contas.</td></tr>');
            
            // 3. Limpar o formulário principal e abrir o modal (usando a função genérica que você já tem)
            // Assume-se que 'inserir()' apenas limpa o formulário principal e abre o #modalForm
            inserir();
        }

        function inserir() {
            $('#form').trigger("reset");
            $('#id').val('');
            $('#tituloModal').text('Inserir Novo Registro');
            var myModal = new bootstrap.Modal(document.getElementById('modalForm'));
            myModal.show();
            $('#mensagem').text('');
            limparCampos(); // Garante que os campos estejam limpos
        }


        // --- AJAX PARA EXCLUSÃO DE CONTA BANCÁRIA (id="form-excluir-conta") ---
        $('#form-excluir-conta').submit(function(event) {
            event.preventDefault();
            
            var formData = $(this).serialize();
            var urlAcao = config.base_http_raiz + "/" + config.path_bancarias + "/excluir.php"; 
            
            $.ajax({
                url: urlAcao,
                method: 'POST',
                data: formData,
                dataType: 'text',
                success: function(mensagem) {
                    
                    if (mensagem.trim().includes("Excluído com Sucesso")) {
                        
                        $('#btn-fechar-excluir-conta').click(); 
                        carregarDadosBancarios(); // Chama a função GLOBAL
                        
                    } else {
                        $('#mensagem-excluir-conta').text(mensagem.trim()); 
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                     $('#mensagem-excluir-conta').text('Erro de comunicação/rede (' + textStatus + ').');
                }
            });
        });

        // --- FUNÇÃO DE RECARREGAMENTO DE CLIENTES ---
        function listarClientes() { 
            var tabelaClientes = $('#tabela-clientes'); 
            var containerClientes = $('#listar');
            
            // 1. BLINDAGEM AGRESSIVA CONTRA 'nTableWrapper is null'
            if ($.fn.dataTable.isDataTable(tabelaClientes)) {
                try {
                    tabelaClientes.DataTable().destroy();
                } catch (e) {
                    var wrapper = tabelaClientes.closest('.dataTables_wrapper');
                    if (wrapper.length) {
                        wrapper.remove();
                    }
                }
            } else if (tabelaClientes.closest('.dataTables_wrapper').length) {
                 tabelaClientes.closest('.dataTables_wrapper').remove();
            } else if (tabelaClientes.length) {
                 tabelaClientes.remove();
            }
            
            // 2. Recria a tabela vazia (para o AJAX carregar)
            containerClientes.html('<table id="tabela-clientes" class="table table-hover"></table>');
            tabelaClientes = $('#tabela-clientes');


            var urlListarClientes = pag + "/listar.php"; 
            
            $.ajax({
                url: urlListarClientes, 
                method: 'GET',
                success: function(responseHtml) {
                    
                    tabelaClientes.html(responseHtml); 
                    
                    // 3. Inicialização DataTables (Só se houver linhas)
                    if ($('#tabela-clientes tr').length > 0) {
                        tabelaClientes.DataTable({
                            "ordering": false,
                            "language": {"url": "../js/pt-BR.json"}
                        });
                    }
                },
                error: function() {
                    containerClientes.html("<p class='text-danger'>Erro ao carregar lista de clientes.</p>");
                }
            });
        }
        
        // Chamada inicial para carregar clientes
        listarClientes();
    });
</script>

<script src="../js/ajax.js"></script>