<div style="margin-top:1%; text-align:center;">
    <h1 class="center">Inclusão de Pedido de LPCO</h1>
</div>

<div class="container mt-4">
    <form id="formLPCO" method="post" action="processar_lpcoE.php">
        
        <!-- Step 1: Órgão Anuente, Modelo LPCO e Solicitar Pedido -->
        <div id="step1" class="form-step">
            <div class="form-group">
                <label for="orgao_anuente">Órgão anuente</label>
                <input type="text" id="orgao_anuente" name="orgao_anuente" placeholder="Pesquisar por COD ou Descrição" class="form-control" onkeyup="pesquisarOrgaoAnuente()">
                <div id="orgao_anuente_lista" class="list-group mt-2"></div>
            </div>

            <div class="form-group">
                <label for="modelo_lpco">Modelo LPCO</label>
                <input type="text" id="modelo_lpco" name="modelo_lpco" placeholder="Pesquisar por COD ou Descrição" class="form-control" onkeyup="pesquisarModeloLpco()">
                <div id="modelo_lpco_lista" class="list-group mt-2"></div>
            </div>

            <div class="form-group">
                <label for="solicitar_pedido">Solicitar pedido novo a partir de LPCO existente</label>
                <input type="text" id="solicitar_pedido" name="solicitar_pedido" class="form-control">
            </div>

            <div class="text-center">
                <button type="button" class="btn btn-primary" onclick="nextStep(2)">Prosseguir</button>
            </div>
        </div>

        <!-- Step 2: Dados Gerais -->
        <div id="step2" class="form-step" style="display:none;">
            <div class="form-group">
                <label for="importador">Importador</label>
                <input type="text" id="importador" name="importador" class="form-control" placeholder="Nome do Importador">
            </div>
            <div class="form-group">
                <label for="dados_gerais">Descrição Geral da Importação</label>
                <textarea id="dados_gerais" name="dados_gerais" placeholder="Informe a descrição geral da importação" class="form-control"></textarea>
            </div>
            <div class="text-center">
                <button type="button" class="btn btn-secondary" onclick="previousStep(1)">Voltar</button>
                <button type="button" class="btn btn-primary" onclick="nextStep(3)">Prosseguir</button>
            </div>
        </div>

        <!-- Step 3: Itens do LPCO -->
        <div id="step3" class="form-step" style="display:none;">
            <div class="form-group">
                <label for="item_ncm">NCM do Item</label>
                <input type="text" id="item_ncm" name="item_ncm" class="form-control" placeholder="Pesquisar por COD ou Descrição" onkeyup="pesquisarNcm()">
                <div id="item_ncm_lista" class="list-group mt-2"></div>
            </div>
            <div class="form-group">
                <label for="descricao_item">Descrição do Item</label>
                <input type="text" id="descricao_item" name="descricao_item" class="form-control" readonly>
            </div>
            <div class="form-group">
                <label for="fabricante_item">Fabricante/Produtor</label>
                <input type="text" id="fabricante_item" name="fabricante_item" class="form-control" placeholder="Nome do Fabricante/Produtor">
            </div>
            <div class="form-group">
                <label for="quantidade_item">Quantidade</label>
                <input type="number" id="quantidade_item" name="quantidade_item" class="form-control" placeholder="Informe a quantidade">
            </div>
            <div class="form-group">
                <label for="valor_item">Valor do Item</label>
                <input type="text" id="valor_item" name="valor_item" class="form-control" placeholder="Informe o valor do item">
            </div>
            <div class="text-center">
                <button type="button" class="btn btn-secondary" onclick="previousStep(2)">Voltar</button>
                <button type="button" class="btn btn-primary" onclick="nextStep(4)">Prosseguir</button>
            </div>
        </div>

        <!-- Step 4: Informações Adicionais e Anexos -->
        <div id="step4" class="form-step" style="display:none;">
            <div class="form-group">
                <label for="informacoes_adicionais">Informações Adicionais</label>
                <textarea id="informacoes_adicionais" name="informacoes_adicionais" class="form-control" placeholder="Insira informações adicionais se necessário"></textarea>
            </div>
            <div class="form-group">
                <label for="anexar_documento">Anexar Documento</label>
                <input type="file" id="anexar_documento" name="anexar_documento" class="form-control">
            </div>
            <div class="text-center">
                <button type="button" class="btn btn-secondary" onclick="previousStep(3)">Voltar</button>
                <button type="submit" class="btn btn-success">Enviar LPCO</button>
            </div>
        </div>

    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function(){
        function setupDynamicSearch(inputSelector, resultSelector, ajaxUrl) {
            $(inputSelector).on('input', function() {
                var query = $(this).val();
                if (query !== '') {
                    $.ajax({
                        url: ajaxUrl,
                        method: "POST",
                        data: { query: query },
                        success: function(data) {
                            $(resultSelector).fadeIn();
                            $(resultSelector).html(data);
                        }
                    });
                } else {
                    $(resultSelector).fadeOut();
                    $(resultSelector).html('');
                }
            });

            $(document).on('click', resultSelector + ' .list-group-item', function() {
                $(inputSelector).val($(this).data('codigo'));
                $('#descricao_item').val($(this).data('descricao'));
                $(resultSelector).fadeOut();
            });
        }

        setupDynamicSearch('#orgao_anuente', '#orgao_anuente_lista', 'comum/pesquisa-orgAnu.php');
        setupDynamicSearch('#modelo_lpco', '#modelo_lpco_lista', 'comum/pesquisarLpcoDb.php');
        setupDynamicSearch('#item_ncm', '#item_ncm_lista', 'comum/pesquisa-ncm-cod.php');
    });

    function nextStep(step) {
        $('.form-step').hide();
        $('#step' + step).show();
    }

    function previousStep(step) {
        $('.form-step').hide();
        $('#step' + step).show();
    }
</script>
