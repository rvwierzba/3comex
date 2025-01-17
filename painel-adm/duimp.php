<?php 
require_once(dirname(__DIR__)."/conexao.php");
//require_once(dirname(__DIR__)."/siscomex/handshake.php");
?>

<div class="container mt-4">
    <h1 class="text-center">Elaboração de DUIMP</h1>

    <!-- Identificação -->
    <form id="formIdentificacao">
        <h3>Identificação</h3>
        <hr>
        <div class="form-group">
            <label for="tipo-pesquisa">Tipo de Pesquisa</label>
            <select id="tipo-pesquisa" class="form-control" required>
                <option value="">Selecione</option>
                <option value="agente">Agente</option>
                <option value="cliente">Cliente</option>
            </select>
        </div>

        <div class="form-group">
            <label for="search">Pesquisar</label>
            <input type="text" class="form-control" id="search" data-target="#result" placeholder="Digite o CNPJ ou Nome" required>
            <div id="result" class="list-group mt-2"></div>
        </div>
        <div class="form-group">
            <label for="enderecoImportador">Endereço do Importador</label>
            <input type="text" class="form-control" id="enderecoImportador" name="enderecoImportador" disabled>
        </div>
        <div class="form-group">
            <label for="informacoesComplementares">Informações Complementares</label>
            <textarea class="form-control" id="informacoesComplementares" name="informacoesComplementares" rows="5" maxlength="7800"></textarea>
            <small class="form-text text-muted">7800 restantes</small>
        </div>
    </form>

    <hr>

    <!-- Carga -->
    <form id="formCarga">
        <h3>Carga</h3>
        <hr>
        <div class="form-group">
            <label for="und-rfb-desp" class="title-form" style="display:flex">Unidade da RFB - DESPACHO<p style="color:red; margin-left:0.2%">*</p></label>
            <input type="text" name="und-rfb-desp" id="und-rfb-desp" data-target="#result-desp" class="form-control" required>
            <div id="result-desp" class="list-group mt-2"></div>
        </div>
        <div class="form-group">
            <label for="situacaoEspecial">Situação Especial de Despacho</label>
            <input type="checkbox" id="situacaoEspecial" name="situacaoEspecial">
        </div>
        <div class="form-group">
            <label for="identificacaoCarga">Identificação da Carga</label>
            <input type="text" class="form-control" id="identificacaoCarga" name="identificacaoCarga">
        </div>
        <div class="form-group">
            <label for="und-rfb-cargDescarg" class="title-form" style="display:flex">Unidade da RFB - CARGA/DESCARGA<p style="color:red; margin-left:0.2%">*</p></label>
            <input type="text" name="und-rfb-cargDescarg" id="und-rfb-cargDescarg" data-target="#result-cargDescarg" class="form-control">
            <div id="result-cargDescarg" class="list-group mt-2"></div>
        </div>
        <h4>Dados do Transporte</h4>
        <div class="form-group">
            <label for="viaTransporte">Via de Transporte</label>
            <input type="text" class="form-control" id="viaTransporte" name="viaTransporte">
        </div>
        <div class="form-group">
            <label for="localEmbarque">Local de Embarque</label>
            <input type="text" class="form-control" id="localEmbarque" name="localEmbarque">
        </div>
        <div class="form-group">
            <label for="dataEmbarque">Data de Embarque</label>
            <input type="date" class="form-control" id="dataEmbarque" name="dataEmbarque">
        </div>
        <h4>Dados da Carga</h4>
        <div class="form-group">
            <label for="paisProcedencia">País de Procedência</label>
            <input type="text" class="form-control" id="paisProcedencia" name="paisProcedencia">
        </div>
        <div class="form-group">
            <label for="unidadeEntrada">Unidade de Entrada/Descarga</label>
            <input type="text" class="form-control" id="unidadeEntrada" name="unidadeEntrada">
        </div>
        <div class="form-group">
            <label for="pesoBruto">Peso Bruto (kg)</label>
            <input type="number" class="form-control" id="pesoBruto" name="pesoBruto">
        </div>
        <div class="form-group">
            <label for="pesoLiquido">Peso Líquido (kg)</label>
            <input type="number" class="form-control" id="pesoLiquido" name="pesoLiquido">
        </div>
    </form>

    <hr>

    <!-- Documentos -->
    <form id="formDocumentos">
        <h3>Documentos</h3>
        <hr>
        <div class="form-group">
            <label for="tipoDocumento">Tipo de Documento</label>
            <select class="form-control" id="tipoDocumento" name="tipoDocumento" required>
                <option value="">Selecione</option>
                <option value="Certificado de Origem">Certificado de Origem</option>
                <option value="Comprovante de Pagamento">Comprovante de Pagamento</option>
                <option value="Declaração de Importação">Declaração de Importação</option>
                <option value="Fatura Comercial">Fatura Comercial</option>
                <option value="Licença de Importação">Licença de Importação</option>
                <option value="Nota Fiscal">Nota Fiscal</option>
                <option value="Outros Documentos">Outros Documentos</option>
            </select>
        </div>
        <div class="form-group">
            <button type="button" id="incluirDocumento" class="btn btn-primary">Incluir Documento</button>
            <input type="file" id="uploadDocumento" style="display: none;">
        </div>
        <table class="table table-striped" id="tabelaDocumentos">
            <thead>
                <tr>
                    <th>Tipo de Documento</th>
                    <th>Arquivo</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="2">Nenhum resultado encontrado</td>
                </tr>
            </tbody>
        </table>
        <h4>Processos / Dossiês Vinculados</h4>
        <div class="form-group">
            <label for="tipoProcesso">Tipo de Processo / Dossiê</label>
            <input type="text" class="form-control" id="tipoProcesso" data-target="#result-proc" name="tipoProcesso" required>
            <div id="result-proc" class="list-group mt-2"></div>
        </div>
        <div class="form-group">
            <button type="button" id="incluirProcesso" class="btn btn-primary">Incluir Processo / Dossiê</button>
        </div>
        <table class="table table-striped" id="tabelaProcessos">
            <thead>
                <tr>
                    <th>Tipo de Processo / Dossiê</th>
                    <th>Identificação</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="2">Nenhum resultado encontrado</td>
                </tr>
            </tbody>
        </table>
    </form>

    <hr>

    <!-- Item -->
    <form id="formItem">
        <h3>Item</h3>
        <hr>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Item de DUIMP</th>
                    <th>Nota Fiscal</th>
                    <th>NCM</th>
                    <th>Item de Nota Fiscal</th>
                    <th>Quantidade Estatística</th>
                    <th>LCPO</th>
                    <th>Unidade Estatística</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><button id="btnExpandir" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i></button></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </form>

    <hr>

    <!-- Tratamento Administrativo -->
    <form id="formTratamentoAdm">
        <h3>Tratamento Administrativo</h3>
        <hr>
        <!-- Aguardando especificações -->
    </form>

    <hr>

    <!-- Resumo -->
    <form id="formResumo">
        <h3>Resumo</h3>
        <hr>
        <div class="form-group">
            <label for="numeroAto">Número do Ato</label>
            <input type="text" class="form-control" id="numeroAto" name="numeroAto" required>
        </div>
        <div class="form-group">
            <label for="numeroItens">Número de Itens</label>
            <input type="text" class="form-control" id="numeroItens" name="numeroItens" required>
        </div>
        <h4>Valores</h4>
        <div class="form-group">
            <label for="valorMercadorias">Valor total das mercadorias no local de embarque</label>
            <input type="text" class="form-control" id="valorMercadorias" name="valorMercadorias" required>
        </div>
        <h4>Cálculo dos Tributos</h4>
        <div class="form-group">
            <label for="taxa">Taxa de utilização do Siscomex</label>
            <input type="text" class="form-control" id="taxa" name="taxa" required>
        </div>
        <table class="table table-striped" id="tabelaTributos">
            <thead>
                <tr>
                    <th>Tributos</th>
                    <th>Valor</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>IPI</td>
                    <td>R$ 0,00</td>
                </tr>
                <tr>
                    <td>II</td>
                    <td>R$ 0,00</td>
                </tr>
                <tr>
                    <td>PIS</td>
                    <td>R$ 0,00</td>
                </tr>
                <tr>
                    <td>COFINS</td>
                    <td>R$ 0,00</td>
                </tr>
            </tbody>
        </table>
        <h4>Pagamentos em Dinheiro</h4>
        <div class="form-group">
            <label for="formaPagamento">Forma de Pagamento</label>
            <select class="form-control" id="formaPagamento" name="formaPagamento" required>
                <option value="Boleto">Boleto</option>
                <option value="Transferência">Transferência</option>
                <option value="Cartão de Crédito">Cartão de Crédito</option>
            </select>
        </div>
        <div class="form-group">
            <button type="button" id="incluirPagamento" class="btn btn-primary">Incluir Pagamento</button>
        </div>
        <table class="table table-striped" id="tabelaPagamentos">
            <thead>
                <tr>
                    <th>Descrição</th>
                    <th>Valor</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="2">Nenhum resultado encontrado</td>
                </tr>
            </tbody>
        </table>
        <button type="button" id="btnEnviar" class="btn btn-primary" style="width: 100%;">Enviar</button>
    </form>
</div>

<script>
   
   $(document).ready(function() {
    let tipoPesquisa = '';

    $('#tipo-pesquisa').on('change', function() {
        tipoPesquisa = $(this).val();
    });

    function setupClientSearch(inputSelector, resultSelector, ajaxUrl) {
        $(inputSelector).on('input', function() {
            var query = $(this).val();
            if (query !== '' && tipoPesquisa !== '') {
                $.ajax({
                    url: ajaxUrl,
                    method: "POST",
                    data: {
                        query: query,
                        tipoPesquisa: tipoPesquisa
                    },
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
            const cnpj = $(this).data('cnpj') || $(this).data('CNPJ'); // Verifica ambos os casos
            const endereco = $(this).data('endereco');
            if (cnpj && endereco) {
                $(inputSelector).val(cnpj.replace(/\D/g, '')); // Remove qualquer máscara do CNPJ
                $('#enderecoImportador').val(endereco);
                $(resultSelector).fadeOut();
            }
        });
    }

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
            $(inputSelector).val($(this).text());
            $(resultSelector).fadeOut();
        });
    }

    setupClientSearch('#search', '#result', 'duimp/pesquisar-cliente.php');
    setupDynamicSearch('#und-rfb-desp', '#result-desp', 'comum/pesquisar-unidade.php');
    setupDynamicSearch('#und-rfb-cargDescarg', '#result-cargDescarg', 'comum/pesquisar-unidade.php');
    setupDynamicSearch('#tipoProcesso', '#result-proc', 'duimp/pesquisar-processo.php');

    // Incluir Documento
    $('#incluirDocumento').on('click', function() {
        var tipoDocumento = $('#tipoDocumento').val();
        if (tipoDocumento !== '') {
            $('#uploadDocumento').click();
        } else {
            alert('Por favor, selecione um tipo de documento.');
        }
    });

    $('#uploadDocumento').on('change', function() {
        var file = this.files[0];
        var tipoDocumento = $('#tipoDocumento').val();
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var filename = file.name;
                $('#tabelaDocumentos tbody').append(`
                    <tr>
                        <td>${tipoDocumento}</td>
                        <td>${filename}</td>
                    </tr>
                `);
                $('#tipoDocumento').val(''); // Limpa o campo após inclusão
                $('#uploadDocumento').val(''); // Limpa o campo de upload
            };
            reader.readAsDataURL(file);
        }
    });

    // Incluir Processo
    $('#incluirProcesso').on('click', function() {
        var tipoProcesso = $('#tipoProcesso').val();
        if (tipoProcesso !== '') {
            $('#tabelaProcessos tbody').append(`
                <tr>
                    <td>${tipoProcesso}</td>
                    <td>Processo / Dossiê</td>
                </tr>
            `);
            $('#tipoProcesso').val(''); // Limpa o campo após inclusão
        } else {
            alert('Por favor, selecione um tipo de processo.');
        }
    });

    // Incluir Pagamento
    $('#incluirPagamento').on('click', function() {
        var formaPagamento = $('#formaPagamento').val();
        var valorPagamento = $('#valorPagamento').val();
        if (formaPagamento !== '' && valorPagamento !== '') {
            $('#tabelaPagamentos tbody').append(`
                <tr>
                    <td>${formaPagamento}</td>
                    <td>${valorPagamento}</td>
                </tr>
            `);
            $('#formaPagamento').val(''); // Limpa o campo após inclusão
            $('#valorPagamento').val(''); // Limpa o campo após inclusão
        } else {
            alert('Por favor, preencha todos os campos de pagamento.');
        }
    });

    // Enviar DUIMP
    $('#btnEnviar').on('click', function() {
        if (validateForm()) {
            var identificacao = $('#formIdentificacao').serializeArray();
            var carga = $('#formCarga').serializeArray();
            var documentos = [];
            $('#tabelaDocumentos tbody tr').each(function() {
                documentos.push({
                    tipo: $(this).find('td').eq(0).text(),
                    arquivo: $(this).find('td').eq(1).text()
                });
            });
            var processos = [];
            $('#tabelaProcessos tbody tr').each(function() {
                processos.push({
                    tipo: $(this).find('td').eq(0).text(),
                    processo: $(this).find('td').eq(1).text()
                });
            });
            var pagamentos = [];
            $('#tabelaPagamentos tbody tr').each(function() {
                pagamentos.push({
                    descricao: $(this).find('td').eq(0).text(),
                    valor: $(this).find('td').eq(1).text()
                });
            });
            var resumo = $('#formResumo').serializeArray();

            $.ajax({
                url: 'duimp/elaborar.php',
                method: 'POST',
                data: {
                    identificacao: JSON.stringify(identificacao),
                    carga: JSON.stringify(carga),
                    documentos: JSON.stringify(documentos),
                    processos: JSON.stringify(processos),
                    pagamentos: JSON.stringify(pagamentos),
                    resumo: JSON.stringify(resumo)
                },
                success: function(response) {
                    alert(response);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert('Erro ao enviar DUIMP: ' + errorThrown);
                }
            });
        } else {
            alert('Por favor, preencha todos os campos obrigatórios.');
        }
    });

    function validateForm() {
        // Adicione aqui a validação dos campos obrigatórios se necessário
        return true;
    }
});


</script>
