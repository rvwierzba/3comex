
<style>
    .selected-item {
    display: inline-block;
    padding: 5px;
    margin: 2px;
    background-color: #d1ecf1;
    border: 1px solid #bee5eb;
    border-radius: 4px;
}

.remove-item {
    margin-left: 10px;
    cursor: pointer;
    color: #c82333;
}
</style>



<div style="margin-top:1%; text-align:center;">
    <h1 class="center">Consultar LPCO de Exportação</h1>
</div>

<div class="container mt-4">
    <form id="formLPCOConsulta" method="post" action="consultar_lpco.php">
        <div class="form-group">
            <label for="numero_lpco">Número do LPCO</label>
            <input type="text" id="numero_lpco" name="numero_lpco" class="form-control">
        </div>
        
        <div class="form-group">
            <label for="orgao_anuente">Órgão anuente</label>
            <input type="text" id="orgao_anuente" name="orgao_anuente" placeholder="Pesquisar por COD ou Descrição" class="form-control" onkeyup="pesquisarOrgaoAnuente()">
            <div id="orgao_anuente_lista" class="list-group mt-2"></div>
        </div>
        
        <div class="form-group">
            <label for="situacao_lpco">Situação do LPCO</label>
            <select id="situacao_lpco" name="situacao_lpco" class="form-control">
                <!-- Opções da situação do LPCO -->
            </select>
        </div>
        
        <div class="form-group">
            <label for="tipo-pesquisa">Tipo de Pesquisa</label>
            <select id="tipo-pesquisa" class="form-control">
                <option value="">Selecione</option>
                <option value="agente">Agente</option>
                <option value="cliente">Cliente</option>
            </select>
        </div>

        <div class="form-group">
            <label for="search">Pesquisar</label>
            <input type="text" class="form-control" id="search" data-target="#result" placeholder="Digite o CNPJ ou Nome">
            <div id="result" class="list-group mt-2"></div>
        </div>
        
        <div class="form-group">
            <label for="data_inicio">Registrado a partir de</label>
            <input type="date" id="data_inicio" name="data_inicio" class="form-control">
        </div>
        
        <div class="form-group">
            <label for="data_fim">Registrado até</label>
            <input type="date" id="data_fim" name="data_fim" class="form-control">
        </div>
        
        <div class="form-group">
            <label for="modeloLpco">Modelo LPCO</label>
            <div id="modeloLpcoContainer" class="selected-container">
                <input type="text" id="modeloLpcoInput" class="form-control" style="border: none;" placeholder="Digite para pesquisar os Modelos de LPCO">
                <div id="modeloLpcoCheckboxList" class="list-group mt-2"></div>
            </div>
        </div>
        
             
        <div class="text-center">
            <button type="submit" class="btn btn-primary">Consultar</button>
            <button type="reset" class="btn btn-secondary">Limpar</button>
        </div>
        
        <div class="dropdown text-center mt-3">
            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Consultas salvas
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <!-- Opções de consultas salvas -->
            </div>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
<script>

$(document).ready(function() {
    function setupDynamicSearch(inputSelector, resultSelector, ajaxUrl, isCheckbox) {
        function bindInputEvent() {
            $(inputSelector).on('input', function() {
                var query = $(this).val();
                if (query !== '') {
                    // Antes de fazer a nova busca, salvar as seleções atuais
                    let selectedValues = [];
                    $(resultSelector + ' input[type="checkbox"]:checked').each(function() {
                        selectedValues.push($(this).val());
                    });

                    $.ajax({
                        url: ajaxUrl,
                        method: "POST",
                        data: { 
                            query: query, 
                            selection_type: isCheckbox ? 'multiple' : 'single'
                        },
                        success: function(data) {
                            $(resultSelector).fadeIn();
                            if (isCheckbox) {
                                $(resultSelector).html(data);
                                
                                // Restaurar as seleções anteriores
                                selectedValues.forEach(function(value) {
                                    $(resultSelector + ' input[type="checkbox"][value="' + value + '"]').prop('checked', true);
                                });

                                $(resultSelector + ' input[type="checkbox"]').on('change', function() {
                                    updateModeloLpcoInput(); 
                                });
                            } else {
                                $(resultSelector).html(data);
                                $(document).on('click', resultSelector + ' .list-group-item', function() {
                                    $(inputSelector).val($(this).text());
                                    $(resultSelector).fadeOut();
                                });
                            }
                        },
                        error: function() {
                            $(resultSelector).fadeOut();
                            $(resultSelector).html('');
                        }
                    });
                } else {
                    $(resultSelector).fadeOut();
                    $(resultSelector).html('');
                }
            });
        }

        // Fecha a lista quando clicar fora dela
        $(document).click(function(e) {
            if (!$(e.target).closest(resultSelector).length && !$(e.target).is(inputSelector)) {
                $(resultSelector).fadeOut();
            }
        });

        bindInputEvent(); // Vincula o evento de input ao campo inicialmente
    }

    function updateModeloLpcoInput() {
        let selectedValues = [];
        $('#modeloLpcoCheckboxList input[type="checkbox"]:checked').each(function() {
            selectedValues.push($(this).val());
        });

        // Limpa o container antes de adicionar os itens selecionados
        $('#modeloLpcoContainer').children('.selected-item').remove();

        selectedValues.forEach(function(value) {
            let itemHtml = '<span class="selected-item">' + value + '<span class="remove-item">&times;</span></span>';
            $('#modeloLpcoContainer').prepend(itemHtml);
        });

        // Re-bind the remove-item click event
        $('.remove-item').on('click', function() {
            let itemValue = $(this).parent().text().slice(0, -1);
            $('#modeloLpcoCheckboxList input[type="checkbox"][value="' + itemValue + '"]').prop('checked', false);
            updateModeloLpcoInput();
        });

        // Rebind the input event after updating the container
        bindInputEvent();

        // Focus de volta no input
        $('#modeloLpcoInput').focus();
    }

    setupDynamicSearch('#orgao_anuente', '#orgao_anuente_lista', 'comum/pesquisa-orgAnu.php', false);
    setupDynamicSearch('#modeloLpcoInput', '#modeloLpcoCheckboxList', 'comum/pesquisarLpcoDb.php', true);  
    setupDynamicSearch('#search', '#result', 'comum/pesquisar-cliente.php', false);
});

//=========================================================================================================
/*
$(document).ready(function(){
    function setupDynamicSearch(inputSelector, resultSelector, ajaxUrl, isCheckbox) {
        $(inputSelector).on('input', function() {
            var query = $(this).val();
            if (query !== '') {
                $.ajax({
                    url: ajaxUrl,
                    method: "POST",
                    data: { query: query },
                    success: function(data) {
                        $(resultSelector).fadeIn();
                        $(resultSelector).html('');

                        if (isCheckbox) {
                            // Assuming data is a JSON array of objects
                            var resultados = JSON.parse(data);

                            resultados.forEach(function(item) {
                                var checkbox = '<div class="list-group-item">';
                                checkbox += '<input type="checkbox" class="form-check-input" value="' + item.codigo + ' - ' + item.descricao + '"> ';
                                checkbox += '<label class="form-check-label">' + item.descricao + '</label>';
                                checkbox += '</div>';
                                $(resultSelector).append(checkbox);
                            });

                            // Update the selected models input field when checkboxes are changed
                            $(resultSelector + ' input[type="checkbox"]').on('change', function() {
                                let selectedValues = [];
                                $(resultSelector + ' input[type="checkbox"]:checked').each(function() {
                                    selectedValues.push($(this).val());
                                });
                                $('#modelos_lpco_selecionados').val(selectedValues.join(', '));
                            });
                        } else {
                            $(resultSelector).html(data);
                            $(document).on('click', resultSelector + ' .list-group-item', function() {
                                $(inputSelector).val($(this).text());
                                $(resultSelector).fadeOut();
                            });
                        }
                    },
                    error: function() {
                        $(resultSelector).fadeOut();
                        $(resultSelector).html('');
                    }
                });
            } else {
                $(resultSelector).fadeOut();
                $(resultSelector).html('');
            }
        });
    }

    setupDynamicSearch('#orgao_anuente', '#orgao_anuente_lista', 'comum/pesquisa-orgAnu.php', false);
    setupDynamicSearch('#pesquisar_modelo_lpco', '#resultado_modelo_lpco', 'comum/pesquisarLpcoDb.php', true);
});

*/
    
</script>




