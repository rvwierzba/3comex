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
   
    // DEFINIÇÃO SEGURA DA VARIÁVEL 'pag' (Isso deve ficar aqui!)
    var pag = "<?php echo isset($pagina) && !empty($pagina) ? htmlspecialchars($pagina, ENT_QUOTES | ENT_HTML5) : 'ERRO_PAG_NAO_DEFINIDA_NO_PHP'; ?>";
    console.log("clientes.php JS: Variável global 'pag' definida como:", pag);

    if (pag === 'ERRO_PAG_NAO_DEFINIDA_NO_PHP') {
        console.error("clientes.php JS ERRO CRÍTICO: A variável PHP '$pagina' (de campos.php) não foi definida ou está vazia. Funcionalidades AJAX podem falhar.");
        if ($('#listar').length) $('#listar').html("<p class='text-danger'>Erro de configuração da página. Não foi possível carregar dados.</p>");
    }

    // Definições das funções que SÃO CHAMADAS DIRETAMENTE DO HTML (onclick)
    // Tornando-as globais explicitamente no 'window' é uma boa prática
    window.inserir = function() {
        console.log("JS global inserir: Chamada.");
        limparCampos();
        $('#tituloModal').text('Inserir Novo Registro');
        var modalFormEl = document.getElementById('modalForm');
        if (modalFormEl) {
            var myModal = new bootstrap.Modal(modalFormEl);
            myModal.show();
        } else { console.error("JS global inserir: Modal #modalForm não encontrado."); }
        // NOTA: O ID 'mensagem' no modalConsulta pode dar problema.
        // Você tem <small><div id="mensagem" align="center"></div></small> em ambos os modais.
        // Renomeie o ID de um deles, por exemplo, para #mensagemConsulta.
        // Temporariamente, vamos limpar #mensagem no modalForm:
        if ($('#modalForm #mensagem').length) $('#modalForm #mensagem').text(''); else console.warn("JS global inserir: Campo #mensagem no modalForm não encontrado.");

         // Lógica para exibir/ocultar campos específicos, se necessário na inserção
        if($('#paisLbl').length || $('#pais').length){
             $('#paisLbl').show();
             $('#pais').show();
             $('#bandeira').hide();
             $('#bandeiraLbl').hide();
         }
    };

    // Função para limpar os campos do modalForm (Chamada por inserir() e editar())
    // Pode ser global se chamada por outras funções globais, ou movida para ajax.js se apenas o código lá a chama
    // Vamos mantê-la aqui para simplificar já que inserir/editar estão aqui.
    window.limparCampos = function() {
        console.log("JS global limparCampos: Chamada.");
        if ($('#id').length) $('#id').val(''); else console.warn("JS global limparCampos: Campo #id não encontrado.");
        if ($('#modalForm #mensagem').length) $('#modalForm #mensagem').text(''); else console.warn("JS global limparCampos: Campo #mensagem no modalForm não encontrado."); // Limpar mensagem específica do modalForm

        // Limpeza dos campos do formulário baseada nos IDs definidos em campos.php
        <?php
        for ($i = 1; $i <= 44; $i++) {
            $nome_da_variavel_php_com_id_do_input = 'campo' . $i;
            if (isset($$nome_da_variavel_php_com_id_do_input) && !empty($$nome_da_variavel_php_com_id_do_input)) {
                $id_do_input_html = $$nome_da_variavel_php_com_id_do_input;
        ?>
        if ($('#<?php echo $id_do_input_html; ?>').length) {
            $('#<?php echo $id_do_input_html; ?>').val('');
        } else {
             // console.warn('JS global limparCampos: Input ID "#<?php echo $id_do_input_html; ?>" (de $<?php echo $nome_da_variavel_php_com_id_do_input; ?>) não encontrado.');
        }
        <?php
            }
        }
        ?>
    };

    // Função chamada pelo botão "Editar" na tabela
    // Deve ser global para ser chamada pelo onclick
    window.editar = function(id, cp1, cp2, cp3, cp4, cp5, cp6, cp7, cp8, cp9, cp10, cp11, cp12, cp13, cp14, cp15, cp16, cp17, cp18, cp19, cp20, cp21, cp22, cp23, cp24, cp25, cp26, cp27, cp28, cp29, cp30, cp31, cp32, cp33, cp34, cp35, cp36, cp37, cp38, cp39, cp40, cp41, cp42, cp43, cp44) {
        console.log("JS global editar: Chamada com ID:", id);
        try {
            limparCampos();
            if ($('#id').length) $('#id').val(id || ''); else console.error("JS global editar: Campo #id não encontrado para preencher.");
            if (!id) { console.warn("JS global editar: chamada sem ID."); }

            // Preenchimento dos campos do formulário com os dados recebidos
            // Adapte ($campoX ?? "fallback_id") para o nome do seu $campoX de campos.php
            // Certifique-se de que $campoX está definido em campos.php E o input no HTML tem o ID correto
            // Exemplo: if($campo1 for NomeRes, e o input é <input id="NomeRes">) -> use $('#NomeRes')
            // Se $campo1 == 'NomeRes', então use $('#<?php echo $campo1; ?>') ou $('#NomeRes') diretamente
            <?php
             // Liste todos os cpX e os IDs correspondentes dos seus inputs
             $campos_para_preencher = [
                 'cp1' => 'NomeRes', 'cp2' => 'Nome', 'cp3' => 'CNPJ', 'cp4' => 'CPF',
                 'cp5' => 'Endereco', 'cp6' => 'Complemento', 'cp7' => 'Bairro',
                 'cp8' => 'Cidade', 'cp9' => 'Estado', 'cp10' => 'Cep', 'cp11' => 'Telefone',
                 'cp12' => 'Celular', 'cp13' => 'InscMun', 'cp14' => 'InscEst',
                 'cp15' => 'Site', 'cp16' => 'Email', 'cp17' => 'Vendedor', 'cp18' => 'ComVend',
                 'cp19' => 'Ptax', 'cp20' => 'Obs', 'cp21' => 'CustService', 'cp22' => 'EmailNfe',
                 'cp23' => 'LocalRps', 'cp24' => 'Grupo', 'cp25' => 'DiasVenc', 'cp26' => 'VencRadar',
                 'cp27' => 'VencProcuracao', 'cp28' => 'VencMercante', 'cp29' => 'VencAnvisa',
                 'cp30' => 'IrDia', 'cp31' => 'IN381', 'cp32' => 'Simples', 'cp33' => 'IOF',
                 'cp34' => 'ImpEsc', 'cp35' => 'NumPad', 'cp36' => 'SubsTrib', 'cp37' => 'ISS',
                 'cp38' => 'Suframa', 'cp39' => 'CodInt', 'cp40' => 'CodContabil', 'cp41' => 'FDA',
                 'cp42' => 'CtaDesp', 'cp43' => 'DataCad', 'cp44' => 'UsuResp'
             ];

             // Agora use esses IDs nos seletores jQuery
             foreach ($campos_para_preencher as $cp_var => $html_id) {
                 // Verifica se a variável PHP correspondente a este campo (e que define o ID) existe
                 // Ex: para 'NomeRes', verifica se $NomeRes existe. Para 'CNPJ', verifica se $CNPJ existe.
                 // Se campos.php define $campo1='NomeRes', $campo3='CNPJ', use $$cp_var == $html_id
                 // É mais seguro verificar a existência do INPUT pelo ID que você SABE que ele tem no HTML
                 // Vamos assumir que os IDs dos inputs no HTML são NomeRes, Nome, CNPJ, etc.
                 // Baseado nos IDs usados nos seus inputs no HTML, vamos preencher diretamente
                 $js_var_name = $cp_var; // 'cp1', 'cp2', etc.
                 $js_id_selector = "#" . $html_id; // '#NomeRes', '#Nome', '#CNPJ', etc.
             ?>
             if ($('<?php echo $js_id_selector; ?>').length) {
                 // O nome da variável JS é $js_var_name (ex: cp1, cp2)
                 // O valor da variável JS é a variável correspondente na lista de argumentos da função editar
                 // O acesso é feito dinamicamente assim: arguments[indice]
                 // O índice de cp1 é 1 (id é o 0), cp2 é 2, etc.
                 var paramValue = arguments[<?php echo substr($js_var_name, 2); ?>] || ''; // cp1 -> indice 1
                 $('<?php echo $js_id_selector; ?>').val(paramValue);
                 // console.log("JS global editar: Preenchido '<?php echo $js_id_selector; ?>' com", paramValue);
             } else {
                  // console.warn('JS global editar: Input ID "<?php echo $js_id_selector; ?>" (corresp. a <?php echo $js_var_name; ?>) não encontrado.');
             }
             <?php
             } // Fim do foreach
             ?>


            $('#tituloModal').text('Editar Registro');
            var modalFormEl = document.getElementById('modalForm');
            if (modalFormEl) {
                var myModal = new bootstrap.Modal(modalFormEl, {});
                myModal.show();
            } else { console.error("JS global editar: Modal #modalForm não encontrado."); }

             // Oculte/exiba campos específicos na edição, se necessário
             if($('#paisLbl').length || $('#pais').length){
                 $('#paisLbl').hide();
                 $('#pais').hide();
                 $('#bandeira').show(); // Talvez mostrar a bandeira ou outro indicador
                 $('#bandeiraLbl').show();
             }


        } catch (error) {
            console.error("Erro grave na função editar():", error);
            if($('#modalForm #mensagem').length) $('#modalForm #mensagem').text("ERRO JS CRÍTICO ao preencher. Verifique F12.");
        }
    };

    // Função mostrarDados (Chamada pelo onclick na tabela)
    // Adapte para preencher o modalDados corretamente
    window.mostrarDados = function(id, cp1, cp2, cp3, cp4, cp5, cp6, cp7, cp8, cp9, cp10, cp11, cp12, cp13, cp14, cp15, cp16, cp17, cp18, cp19, cp20, cp21, cp22, cp23, cp24, cp25, cp26, cp27, cp28, cp29, cp30, cp31, cp32, cp33, cp34, cp35, cp36, cp37, cp38, cp39, cp40, cp41, cp42, cp43, cp44) {
        console.log("JS global mostrarDados: Chamada com ID:", id);

        // Preencher os spans no modalDados
        // Adapte os IDs dos spans (#modalDados #campo1, #modalDados #campo2, etc.)
        // para corresponder aos cpX e o que eles representam
        // Ex: Span para Nome Completo tem id="campo1", cp2 é o Nome Completo
        if($('#modalDados #clienteNome').length) $('#modalDados #clienteNome').text(cp1 || cp2 || 'Cliente'); // Usa NomeRes ou Nome Completo
        if($('#modalDados #campo1').length) $('#modalDados #campo1').text(cp2 || ''); // Nome Completo
        if($('#modalDados #campo2').length) $('#modalDados #campo2').text((cp3 || '') + '/' + (cp4 || '')); // CNPJ/CPF
        if($('#modalDados #campo3').length) $('#modalDados #campo3').text(cp5 || ''); // Endereço
        if($('#modalDados #campo4').length) $('#modalDados #campo4').text(cp7 || ''); // Bairro
        // ... continue para os outros campos até o 44, ajustando os IDs dos spans
        // Exemplo: $('#modalDados #campo5').text(cp8 || ''); // Cidade
        // Exemplo: $('#modalDados #campo6').text(cp10 || ''); // CEP
        // Exemplo: $('#modalDados #campo7').text(cp11 || ''); // Telefone
        // Exemplo: $('#modalDados #campo8').text(cp16 || ''); // Email
        // ...
        if($('#modalDados #campo44').length) $('#modalDados #campo44').text(cp44 || ''); // UsuResp ou o campo que for o 44

        var modalDadosEl = document.getElementById('modalDados');
        if (modalDadosEl) {
            var myModal = new bootstrap.Modal(modalDadosEl, {});
            myModal.show();
        } else { console.error("JS global mostrarDados: Modal #modalDados não encontrado.");}
    };

    // Função excluir (Chamada pelo onclick na tabela)
    window.excluir = function(id, nome){
        console.log("JS global excluir: Chamada com ID:", id, "Nome:", nome);
        $('#id-excluir').val(id);
        $('#nome-excluido').text(nome);
        var myModal = new bootstrap.Modal(document.getElementById('modalExcluir'), { })
        myModal.show();
        if($('#mensagem-excluir').length) $('#mensagem-excluir').text('');
    };

     // Função openModalConsulta (Chamada pelo onclick no botão de consulta)
    window.openModalConsulta = function(origin){
         console.log("JS global openModalConsulta: Chamada com origin:", origin);
         // NOTA: O ID 'mensagem' no modalConsulta pode dar problema. Use '#mensagemConsulta' ou algo único.
         if ($('#modalConsulta #mensagem').length) $('#modalConsulta #mensagem').text(''); // Limpa mensagem específica do modalConsulta

         if ($('#cnpj-form').length) {
             $('#cnpj-form').data('origin', origin);
         } else {
             console.warn("JS global openModalConsulta: Formulário #cnpj-form não encontrado. Não foi possível definir origin.");
         }

         var modalConsultaEl = document.getElementById('modalConsulta');
         if (modalConsultaEl) {
             var myModal = new bootstrap.Modal(modalConsultaEl, {});
             myModal.show();
         } else { console.error("JS global openModalConsulta: Modal #modalConsulta não encontrado."); }
    };


</script>

<!-- Inclua o arquivo ajax.js *DEPOIS* da definição da variável 'pag' e das funções globais -->
<script src="ajax.js"></script>

<!-- O restante do seu HTML, se houver -->
Use code with caution.
PHP
2. Arquivo ajax.js
Este arquivo agora conterá as chamadas de inicialização ($(document).ready), a função listarClientes (ou listar, mantive listarClientes para consistência com o log do seu clientes.php), e os handlers de submit dos formulários.
// Arquivo: ajax.js
// Lógica AJAX para a página de clientes

console.log("ajax.js: Arquivo carregado.");

// A variável global 'pag' DEVE ser definida em clientes.php ANTES deste arquivo ser incluído.
if (typeof pag === 'undefined') {
    console.error("ajax.js ERRO CRÍTICO: A variável global 'pag' não está definida. Verifique se clientes.php a define antes de incluir ajax.js.");
    // Considerar desabilitar funcionalidades AJAX ou mostrar um erro ao usuário
} else {
     console.log("ajax.js: Variável global 'pag' detectada:", pag);
}


// Função para carregar a lista de clientes via AJAX
// Tornada global no script inline de clientes.php ou aqui se necessário, mas mantive inline por clareza
// window.listarClientes = function() { ... }; // Se definida inline, não precisa definir de novo aqui

function listarClientes() {
    console.log("ajax.js listarClientes: Chamada.");
    // Verifica se jQuery e 'pag' estão disponíveis
    if (typeof $ === 'undefined') { console.error("ajax.js listarClientes: jQuery não está definido!"); return; }
    if (typeof pag === 'undefined' || pag === '' || pag === 'ERRO_PAG_NAO_DEFINIDA_NO_PHP') {
        console.error("ajax.js listarClientes: Variável 'pag' (" + pag + ") inválida. Não é possível montar URL.");
        // Já deve ter sido tratado em clientes.php, mas é bom verificar novamente
        if ($('#listar').length) $('#listar').html("<p class='text-danger'>Erro de configuração (var pag inválida). Não foi possível carregar dados.</p>");
        return;
    }

    var urlListar = pag + '/listar.php'; // Monta a URL usando a variável global 'pag'
    console.log("ajax.js listarClientes: Tentando carregar de:", urlListar);

    $.ajax({
        url: urlListar,
        method: 'GET', // Método GET é mais apropriado para buscar dados
        // Não precisamos enviar dados do formulário para listar
        // data: $('#form').serialize(), // Remova esta linha

        success: function(responseHtml) {
            if ($('#listar').length) {
                $('#listar').html(responseHtml);
                console.log("ajax.js listarClientes: Conteúdo de #listar atualizado. DataTables deve inicializar...");
                 // listar.php inclui o script para inicializar DataTables.
                 // Se não funcionar, remova a inicialização de listar.php e coloque-a aqui
                 // if ($.fn.DataTable.isDataTable('#example')) { $('#example').DataTable().destroy(); }
                 // if ($('#example').length) { $('#example').DataTable({"ordering": false}); }
            } else {
                 console.error("ajax.js listarClientes: Div #listar não encontrada no DOM.");
            }
        },
        error: function(xhr, status, error) {
            console.error("ajax.js listarClientes: Erro ao carregar lista via AJAX.", {
                status: status,
                error: error,
                responseText: xhr.responseText,
                url: urlListar
            });
            if ($('#listar').length) $('#listar').html("<p class='text-danger'>Erro ao carregar lista de clientes. Verifique o console (F12) e o log do servidor.</p>");
        }
    });
}


// Handler para a submissão do formulário de Inserir/Editar (#form)
$("#form").submit(function (event) {
    event.preventDefault(); // Impede o envio tradicional do formulário
    console.log("ajax.js: Formulário #form submetido.");
    var formData = new FormData(this);

     if (typeof pag === 'undefined' || pag === '' || pag === 'ERRO_PAG_NAO_DEFINIDA_NO_PHP') {
        console.error("ajax.js #form submit: Variável 'pag' inválida. Não é possível salvar.");
         // Use o campo de mensagem do modalForm para exibir o erro
        if($('#modalForm #mensagem').length) {
             $('#modalForm #mensagem').addClass('text-danger').text("Erro de configuração da página (var pag inválida).");
        }
        return; // Sai da função
    }

    var urlSalvar = pag + "/salvar.php"; // <-- **APONTA PARA O SCRIPT DE SALVAMENTO REAL**
    console.log("ajax.js #form submit: Enviando para:", urlSalvar);


    $.ajax({
        url: urlSalvar, // Use a URL correta
        type: 'POST',
        data: formData,
        dataType: 'text', // Espera uma resposta de texto simples ("Salvo com Sucesso" ou mensagem de erro)
        processData: false, // Necessário para FormData
        contentType: false, // Necessário para FormData


        success: function (mensagem) {
             console.log("ajax.js #form submit: Resposta recebida:", mensagem);
            // Limpa a mensagem anterior e classes de estilo
            var $mensagemDiv = $('#modalForm #mensagem'); // Seleciona a div de mensagem dentro do modalForm
             if ($mensagemDiv.length) {
                 $mensagemDiv.text('');
                 $mensagemDiv.removeClass('text-success text-danger'); // Remova classes anteriores
             } else { console.warn("ajax.js #form submit: Div #mensagem no modalForm não encontrada."); }


            if (mensagem.trim() == "Salvo com Sucesso") {
                // Operação bem sucedida
                 if ($mensagemDiv.length) $mensagemDiv.addClass('text-success').text(mensagem.trim()); // Opcional: mostra mensagem de sucesso brevemente
                console.log("ajax.js #form submit: Salvo com Sucesso. Fechando modal e listando...");
                $('#btn-fechar').click(); // Fecha o modal
                listarClientes(); // Recarrega a lista na tabela
            } else {
                // Houve um erro ou outra mensagem retornada pelo PHP
                 if ($mensagemDiv.length) $mensagemDiv.addClass('text-danger').text(mensagem.trim()); // Exibe a mensagem de erro do PHP
                console.error("ajax.js #form submit: Erro ou Mensagem do Servidor:", mensagem.trim());
            }
        },

        error: function(xhr, status, error) {
            // Lida com erros na requisição AJAX em si (rede, servidor, etc.)
            console.error("ajax.js #form submit: Erro na requisição AJAX:", {
                status: status,
                error: error,
                responseText: xhr.responseText,
                url: urlSalvar
            });
             // Use o campo de mensagem do modalForm para exibir o erro
            var $mensagemDiv = $('#modalForm #mensagem');
            if ($mensagemDiv.length) {
                $mensagemDiv.addClass('text-danger').text("Erro na comunicação com o servidor: " + status + " " + error);
            } else { console.warn("ajax.js #form submit: Div #mensagem no modalForm não encontrada para exibir erro."); }
        }
    });
});

// Handler para a submissão do formulário de Excluir (#form-excluir)
$("#form-excluir").submit(function (event) {
    event.preventDefault();
     console.log("ajax.js: Formulário #form-excluir submetido.");
    var formData = new FormData(this);

     if (typeof pag === 'undefined' || pag === '' || pag === 'ERRO_PAG_NAO_DEFINIDA_NO_PHP') {
        console.error("ajax.js #form-excluir submit: Variável 'pag' inválida. Não é possível excluir.");
         if ($('#modalExcluir #mensagem-excluir').length) {
             $('#modalExcluir #mensagem-excluir').addClass('text-danger').text("Erro de configuração da página (var pag inválida).");
         }
        return;
    }

    var urlExcluir = pag + "/excluir.php"; // Certifique-se que você tem este script
     console.log("ajax.js #form-excluir submit: Enviando para:", urlExcluir);


    $.ajax({
        url: urlExcluir, // Use a URL correta do script de exclusão
        type: 'POST',
        data: formData,
        dataType: 'text', // Espera texto
        processData: false,
        contentType: false,

        success: function (mensagem) {
             console.log("ajax.js #form-excluir submit: Resposta recebida:", mensagem);
            var $mensagemDiv = $('#modalExcluir #mensagem-excluir');
             if ($mensagemDiv.length) {
                 $mensagemDiv.text('');
                 $mensagemDiv.removeClass('text-success text-danger');
             } else { console.warn("ajax.js #form-excluir submit: Div #mensagem-excluir não encontrada."); }


            if (mensagem.trim() == "Excluído com Sucesso") {
                console.log("ajax.js #form-excluir submit: Excluído com Sucesso. Fechando modal e listando...");
                 if ($mensagemDiv.length) $mensagemDiv.addClass('text-success').text(mensagem.trim()); // Opcional
                $('#btn-fechar-excluir').click(); // Fecha o modal de exclusão
                listarClientes(); // Recarrega a lista
            } else {
                // Houve um erro ou outra mensagem retornada pelo PHP
                 if ($mensagemDiv.length) $mensagemDiv.addClass('text-danger').text(mensagem.trim()); // Exibe a mensagem de erro do PHP
                 console.error("ajax.js #form-excluir submit: Erro ou Mensagem do Servidor:", mensagem.trim());
            }
        },

         error: function(xhr, status, error) {
            console.error("ajax.js #form-excluir submit: Erro na requisição AJAX:", {
                status: status,
                error: error,
                responseText: xhr.responseText,
                url: urlExcluir
            });
            var $mensagemDiv = $('#modalExcluir #mensagem-excluir');
            if ($mensagemDiv.length) {
                $mensagemDiv.addClass('text-danger').text("Erro na comunicação com o servidor: " + status + " " + error);
            } else { console.warn("ajax.js #form-excluir submit: Div #mensagem-excluir não encontrada para exibir erro."); }
        }

    });
});


// Código a ser executado quando o DOM estiver completamente carregado
$(document).ready(function() {
    console.log("ajax.js: Document ready final.");

    // Inicialize as máscaras aqui
    // Use os IDs *reais* dos seus inputs no HTML
    // Certifique-se de que esses IDs existem no seu formulário (#modalForm)
    if ($('#CNPJ').length) $('#CNPJ').mask('00.000.000/0000-00'); else console.warn("ajax.js ready: Input CNPJ (#CNPJ) não encontrado para máscara.");
    if ($('#CPF').length) $('#CPF').mask('000.000.000-00'); else console.warn("ajax.js ready: Input CPF (#CPF) não encontrado para máscara.");
    // Adicione máscaras para Telefone, Celular, CEP, etc., se necessário
    if ($('#Telefone').length) $('#Telefone').mask('(00) 0000-0000'); else console.warn("ajax.js ready: Input Telefone (#Telefone) não encontrado para máscara.");
     if ($('#Celular').length) $('#Celular').mask('(00) 00000-0000'); else console.warn("ajax.js ready: Input Celular (#Celular) não encontrado para máscara.");
     if ($('#Cep').length) $('#Cep').mask('00000-000'); else console.warn("ajax.js ready: Input Cep (#Cep) não encontrado para máscara.");


    // Configura o origin no formulário de consulta CNPJ, se ele estiver incluído na página
    // (Como você incluiu common/form-consulta-cnpj.php no modalConsulta)
    if ($('#cnpj-form').length) {
        $('#cnpj-form').data('origin', 'clientes');
        // console.log("ajax.js ready: Configurado data-origin='clientes' no #cnpj-form."); // Pode ser muito verboso
    } else {
         // console.warn("ajax.js ready: Formulário #cnpj-form não encontrado."); // Avisa se o formulário não existe
    }


    // Carrega a lista de clientes ao iniciar a página se 'pag' estiver OK
    if (typeof pag !== 'undefined' && pag && pag !== 'ERRO_PAG_NAO_DEFINIDA_NO_PHP') {
        listarClientes(); // Chama a função para carregar a tabela
    } else {
         console.error("ajax.js ready: Não foi possível carregar a lista: variável 'pag' não configurada corretamente.");
         if ($('#listar').length) $('#listar').html("<p class='text-danger'>Não foi possível carregar a lista: variável 'pag' não configurada.</p>");
    }
});


</script>