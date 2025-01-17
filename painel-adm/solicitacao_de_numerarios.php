<?php
require_once('../conexao.php');

function generateUUID() {
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

$numeroSolicitacao = generateUUID();
?>


    <div class="container">
        <h1>Solicitação de Numerário</h1>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="numero-solicitacao">Número da Solicitação</label>
                    <input type="text" class="form-control" id="numero-solicitacao" value="<?php echo $numeroSolicitacao; ?>" disabled>
                </div>
                <div class="form-group">
                    <label for="data-solicitacao">Data da Solicitação</label>
                    <input type="date" class="form-control" id="data-solicitacao" required>
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
                <input type="text" class="form-control" id="search" placeholder="Digite o CNPJ ou Nome">
                <div id="result" class="list-group mt-2"></div>
            </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="observacoes">Observações</label>
                    <textarea class="form-control" id="observacoes" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label for="valor-solicitado">Valor Solicitado (R$)</label>
                    <input type="number" class="form-control" id="valor-solicitado" min="0" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="forma-pagamento">Forma de Pagamento</label>
                    <select class="form-control" id="forma-pagamento" required>
                        <option value="">Selecione</option>
                        <option value="dinheiro">Dinheiro</option>
                        <option value="cartao">Cartão</option>
                        <option value="cheque">Cheque</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="data-vencimento">Data de Vencimento</label>
                    <input type="date" class="form-control" id="data-vencimento" required>
                </div>
                <div class="form-group">
                    <label for="finalidade">Finalidade</label>
                    <textarea class="form-control" id="finalidade" rows="3"></textarea>
                </div>
            </div>
        </div>

        <h2>Itens da Solicitação</h2>
        <table class="table table-striped" id="itens-tabela">
            <thead>
                <tr>
                    <th>Descrição</th>
                    <th>Tributável</th>
                    <th>Valor (R$)</th>
                    <th>Total (R$)</th>
                    <th></th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        <div class="form-group row">
            <div class="col-md-6">
                <button type="button" class="btn btn-primary" id="add-item">Adicionar Item</button>
            </div>
            <div class="col-md-6 mt-3">
                <button type="button" class="btn btn-success w-100" id="generate-solicitacao">Gerar Solicitação</button>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="addItemModal" tabindex="-1" role="dialog" aria-labelledby="addItemModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addItemModalLabel">Adicionar Item</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="addItemForm">
                            <div class="form-group">
                                <label for="descricao">Descrição</label>
                                <select class="form-control" id="descricao" required>
                                    <option value="">Selecione</option>
                                    <?php 
                                    $query = $pdo->query("SELECT descricao, tributavel FROM taxas ORDER BY descricao");
                                    $res = $query->fetchAll(PDO::FETCH_ASSOC);
                                    foreach($res as $taxa){
                                        $descricao = $taxa['descricao'];
                                        $tributavel = $taxa['tributavel'];
                                    ?>
                                    <option value="<?php echo $descricao; ?>" data-tributavel="<?php echo $tributavel; ?>">
                                        <?php echo $descricao; ?>
                                    </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="tributavel">Tributável</label>
                                <input type="text" class="form-control" id="tributavel" disabled>
                            </div>
                            <div class="form-group">
                                <label for="valor">Valor (R$)</label>
                                <input type="number" class="form-control" id="valor" min="0" step="0.01" required>
                            </div>
                            <div class="form-group">
                                <label for="total">Total (R$)</label>
                                <input type="number" class="form-control" id="total" min="0" step="0.01" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Salvar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
            //Cria um UUID para ser o numero da Solicitação cada vez que a página é aberta e recerregada
            $(document).ready(function(){
            var numeroSolicitacao = "<?php echo $numeroSolicitacao; ?>";});

            //Verifica se cliente ou agente para a pesquisa
            //Manda txt do input para CNPJ ou Nome para consultar no banco a partir da 1º tecla digitada,
            // alem do termo da busca Numeros => CNPJ | Letras => Nome
        $(document).ready(function(){

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
            if (cnpj) {
                $(inputSelector).val(cnpj.replace(/\D/g, '')); // Remove qualquer máscara do CNPJ
                $(resultSelector).fadeOut();
            }
        });
    }

    setupClientSearch('#search', '#result', 'comum/pesquisar-cliente.php');





            // Abrir o modal
            $('#add-item').click(function() {
                $('#addItemModal').modal('show');
            });

            // Preencher o campo "Tributável" automaticamente ao selecionar a descrição
            $('#descricao').change(function() {
                var tributavel = $('#descricao option:selected').data('tributavel');
                $('#tributavel').val(tributavel == '1' ? 'Sim' : 'Não');
            });

            // Adicionar item na tabela
            $('#addItemForm').submit(function(event) {
                event.preventDefault();
                var descricao = $('#descricao option:selected').text().trim();
                var tributavel = $('#tributavel').val().toUpperCase();
                var valor = parseFloat($('#valor').val()).toFixed(2);
                var total = parseFloat($('#total').val()).toFixed(2);

                var newRow = '<tr>' +
                    '<td>' + descricao + '</td>' +
                    '<td>' + tributavel + '</td>' +
                    '<td>' + valor + '</td>' +
                    '<td>' + total + '</td>' +
                    '<td><button type="button" class="btn btn-danger btn-sm remove-item">Remover</button></td>' +
                    '</tr>';

                $('#itens-tabela tbody').append(newRow);
                $('#addItemModal').modal('hide');
                $('#addItemForm')[0].reset();
            });

            // Remover item da tabela
            $(document).on('click', '.remove-item', function() {
                $(this).closest('tr').remove();
            });

            // Gerar Solicitação
            $('#generate-solicitacao').click(function() {
                // Coletar dados do formulário
                var dataSolicitacao = $('#data-solicitacao').val();
                var cliente = $('#search').val();
                var observacoes = $('#observacoes').val();
                var valorSolicitado = parseFloat($('#valor-solicitado').val()).toFixed(2);
                var formaPagamento = $('#forma-pagamento').val();
                var dataVencimento = $('#data-vencimento').val();
                var finalidade = $('#finalidade').val();

                // Coletar dados da tabela de itens
                var itens = [];
                $('#itens-tabela tbody tr').each(function() {
                    var descricao = $(this).find('td').eq(0).text().trim();
                    var tributavel = $(this).find('td').eq(1).text();
                    var valor = $(this).find('td').eq(2).text();
                    var total = $(this).find('td').eq(3).text();
                    itens.push({ descricao, tributavel, valor, total });
                });

                // Enviar dados via POST para gerar o PDF
                $.ajax({
                    url: '../TCPDF/telas/solicitacao_de_numerarios/generate_pdf.php',
                    method: 'POST',
                    data: {
                        numeroSolicitacao: numeroSolicitacao,
                        dataSolicitacao: dataSolicitacao,
                        cliente: cliente,
                        observacoes: observacoes,
                        valorSolicitado: valorSolicitado,
                        formaPagamento: formaPagamento,
                        dataVencimento: dataVencimento,
                        finalidade: finalidade,
                        itens: JSON.stringify(itens)
                    },
                    success: function(response) {
                        // A resposta já é um objeto JSON, então não precisamos usar JSON.parse
                        if (response.error) {
                            alert(response.error);
                        } else if (response.pdf_url) {
                            // Abrir o PDF gerado em uma nova janela
                            window.open(response.pdf_url, '_blank');
                        } else {
                            alert('Erro desconhecido.');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('Erro ao gerar o PDF:', textStatus, errorThrown);
                        console.error('Resposta completa:', jqXHR.responseText);
                    }
                });
            });
        });
                                
                  
    </script>
