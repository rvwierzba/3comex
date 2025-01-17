$(document).ready(function () {
    var nfeData = [];

    // Função de leitura e processamento dos arquivos XML
    $('#xml-files').on('change', function () {
        var files = $('#xml-files')[0].files;

        // Mensagem de status de upload
        $('#uploadStatus').html('<span class="text-info">Processando arquivos...</span>');

        // Processar cada arquivo XML selecionado
        for (let i = 0; i < files.length; i++) {
            let reader = new FileReader();

            reader.onload = function (e) {
                var xml = $.parseXML(e.target.result); // Parsear o XML
                var $xml = $(xml);

                // Extração dos dados necessários do XML
                var chaveAcesso = $xml.find('infNFe').attr('Id').replace('NFe', '');
                var cUF = $xml.find('ide > cUF').text();
                var nNF = $xml.find('ide > nNF').text();
                var dhEmi = $xml.find('ide > dhEmi').text();
                var emitente = {
                    CNPJ: $xml.find('emit > CNPJ').text(),
                    xNome: $xml.find('emit > xNome').text(),
                    xFant: $xml.find('emit > xFant').text(),
                    enderEmit: {
                        xLgr: $xml.find('emit > enderEmit > xLgr').text(),
                        nro: $xml.find('emit > enderEmit > nro').text(),
                        xBairro: $xml.find('emit > enderEmit > xBairro').text(),
                        xMun: $xml.find('emit > enderEmit > xMun').text(),
                        UF: $xml.find('emit > enderEmit > UF').text(),
                        CEP: $xml.find('emit > enderEmit > CEP').text(),
                        cPais: $xml.find('emit > enderEmit > cPais').text(),
                        xPais: $xml.find('emit > enderEmit > xPais').text(),
                    },
                    IE: $xml.find('emit > IE').text(),
                    CRT: $xml.find('emit > CRT').text(),
                };
                var destinatario = {
                    idEstrangeiro: $xml.find('dest > idEstrangeiro').text(),
                    xNome: $xml.find('dest > xNome').text(),
                    enderDest: {
                        xLgr: $xml.find('dest > enderDest > xLgr').text(),
                        nro: $xml.find('dest > enderDest > nro').text(),
                        xBairro: $xml.find('dest > enderDest > xBairro').text(),
                        xMun: $xml.find('dest > enderDest > xMun').text(),
                        UF: $xml.find('dest > enderDest > UF').text(),
                        cPais: $xml.find('dest > enderDest > cPais').text(),
                        xPais: $xml.find('dest > enderDest > xPais').text(),
                    },
                    indIEDest: $xml.find('dest > indIEDest').text(),
                };
                var itens = [];
                $xml.find('det').each(function () {
                    var item = {
                        nItem: $(this).attr('nItem'),
                        cProd: $(this).find('prod > cProd').text(),
                        xProd: $(this).find('prod > xProd').text(),
                        NCM: $(this).find('prod > NCM').text(),
                        CFOP: $(this).find('prod > CFOP').text(),
                        uCom: $(this).find('prod > uCom').text(),
                        qCom: $(this).find('prod > qCom').text(),
                        vUnCom: $(this).find('prod > vUnCom').text(),
                        vProd: $(this).find('prod > vProd').text(),
                    };
                    itens.push(item);
                });

                // Preencher campos no formulário com base no XML (se necessário)
                $('#nomeCliente').val(emitente.xNome);

                // Adicionar os dados ao array nfeData
                nfeData.push({
                    chaveAcesso: chaveAcesso,
                    cUF: cUF,
                    nNF: nNF,
                    dhEmi: dhEmi,
                    emitente: emitente,
                    destinatario: destinatario,
                    itens: itens,
                    nrProcesso: '',
                    nrAdicao: '',
                    incoterm: '',
                    destinoFinal: destinatario.enderDest.UF,
                    comissaoAgente: '',
                    index: i,
                });

                // Atualizar a tabela com os dados extraídos
                atualizarTabelaNFe(nfeData);
            };

            // Ler o conteúdo do arquivo XML
            reader.readAsText(files[i]);
        }

        // Exibir a mensagem de sucesso após o processamento dos arquivos
        setTimeout(function () {
            $('#uploadStatus').html('<span class="text-success">Upload e extração de informações concluídos com sucesso.</span>');
        }, 500);
    });

    // Função para atualizar a tabela de Notas Fiscais
    function atualizarTabelaNFe(nfeData) {
        var table = $('#notasFiscaisTable tbody');
        table.empty(); // Limpar a tabela antes de adicionar os novos dados

        // Preencher a tabela com os dados extraídos
        nfeData.forEach(function (nfe, index) {
            var row = `
            <tr>
                <td>${nfe.chaveAcesso}</td>
                <td>${nfe.destinatario.xNome}</td>
                <td>${nfe.destinatario.enderDest.xPais}</td>
                <td>${nfe.nNF}</td>
                <td>${nfe.nrProcesso}</td>
                <td>${nfe.nrAdicao}</td>
                <td>${nfe.incoterm}</td>
                <td>${nfe.destinoFinal}</td>
                <td>${nfe.comissaoAgente}</td>
                <td>
                    <div style="display: flex; gap: 5px;">
                        <button class="btn btn-info expandirBtn" data-id="${index}">+</button>
                        <button class="btn btn-danger btn-sm removerBtn" data-id="${index}">Remover</button>
                    </div>
                </td>
            </tr>
            <tr class="detalhesNF" id="detalhesNF-${index}" style="display: none;">
                <td colspan="10">
                    <!-- Inputs e labels de detalhes da NF -->
                    <form id="formNF-${index}">
                        <div class="row">
                            <!-- Campos preenchidos do XML (não editáveis) -->
                            <div class="col-md-4">
                                <label>Chave de Acesso</label>
                                <input type="text" class="form-control" value="${nfe.chaveAcesso}" readonly>
                            </div>
                            <div class="col-md-4">
                                <label>Emitente</label>
                                <input type="text" class="form-control" value="${nfe.emitente.xNome}" readonly>
                            </div>
                            <div class="col-md-4">
                                <label>Destinatário</label>
                                <input type="text" class="form-control" value="${nfe.destinatario.xNome}" readonly>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <!-- Campos editáveis (vazios) -->
                            <div class="col-md-4">
                                <label for="nrProcesso-${index}">Nr. Processo</label>
                                <input type="text" class="form-control" id="nrProcesso-${index}" value="${nfe.nrProcesso}">
                            </div>
                            <div class="col-md-4">
                                <label for="nrAdicao-${index}">Nr. Adição</label>
                                <input type="text" class="form-control" id="nrAdicao-${index}" value="${nfe.nrAdicao}">
                            </div>
                            <div class="col-md-4">
                                <label for="incoterm-${index}">Incoterm</label>
                                <input type="text" class="form-control" id="incoterm-${index}" value="${nfe.incoterm}">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <label for="comissaoAgente-${index}">Comissão Agente (%)</label>
                                <input type="number" class="form-control" id="comissaoAgente-${index}" value="${nfe.comissaoAgente}">
                            </div>
                            <div class="col-md-4">
                                <label for="destinoFinal-${index}">Destino Final</label>
                                <input type="text" class="form-control" id="destinoFinal-${index}" value="${nfe.destinoFinal}">
                            </div>
                        </div>
                        <!-- Exibir itens da NF -->
                        <div class="mt-3">
                            <h5>Itens da Nota Fiscal</h5>
                            ${nfe.itens.map(item => `
                                <div class="card mb-2">
                                    <div class="card-body">
                                        <p><strong>Item:</strong> ${item.nItem}</p>
                                        <p><strong>Código do Produto:</strong> ${item.cProd}</p>
                                        <p><strong>Descrição:</strong> ${item.xProd}</p>
                                        <p><strong>NCM:</strong> ${item.NCM}</p>
                                        <p><strong>CFOP:</strong> ${item.CFOP}</p>
                                        <p><strong>Unidade:</strong> ${item.uCom}</p>
                                        <p><strong>Quantidade:</strong> ${item.qCom}</p>
                                        <p><strong>Valor Unitário:</strong> ${item.vUnCom}</p>
                                        <p><strong>Valor Total:</strong> ${item.vProd}</p>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                        <div class="mt-3">
                            <button type="button" class="btn btn-success btn-sm salvarBtn" data-id="${index}">Salvar</button>
                        </div>
                    </form>
                </td>
            </tr>
            `;
            table.append(row);
        });

        // Lidar com o clique no botão "+"
        $('.expandirBtn').on('click', function() {
            var nfId = $(this).data('id');
            $('#detalhesNF-' + nfId).toggle(); // Mostrar ou esconder os detalhes da NF

            // Alternar o sinal do botão entre "+" e "-"
            var btn = $(this);
            if (btn.text() === '+') {
                btn.text('-');
            } else {
                btn.text('+');
            }
        });

        // Lidar com o clique no botão "Salvar"
        $('.salvarBtn').on('click', function() {
            var nfId = $(this).data('id');

            // Atualizar os dados no array nfeData
            nfeData[nfId].nrProcesso = $('#nrProcesso-' + nfId).val();
            nfeData[nfId].nrAdicao = $('#nrAdicao-' + nfId).val();
            nfeData[nfId].incoterm = $('#incoterm-' + nfId).val();
            nfeData[nfId].comissaoAgente = $('#comissaoAgente-' + nfId).val();
            nfeData[nfId].destinoFinal = $('#destinoFinal-' + nfId).val();

            // Atualizar a tabela para refletir os novos dados
            atualizarTabelaNFe(nfeData);
        });

        // Lidar com o clique no botão "Remover"
        $('.removerBtn').on('click', function() {
            var nfId = $(this).data('id');

            // Remover a NF do array
            nfeData.splice(nfId, 1);

            // Atualizar a tabela
            atualizarTabelaNFe(nfeData);

            // Limpar todos os campos dos formulários
            limparFormularios();
        });

        // Função para limpar todos os formulários
        function limparFormularios() {
            // Limpar os campos do formulário "Importação de NFs"
            $('#dueForm')[0].reset();
            $('#uploadStatus').html(''); // Remover mensagens de status

            // Limpar os campos do formulário "Dados da Declaração"
            $('#dadosImportacao form')[0].reset();

            // Garantir que 'nomeCliente' esteja vazio
            $('#nomeCliente').val('');

            // Opcional: Se desejar, pode esconder todos os detalhes das NFs
            $('.detalhesNF').hide();

            // Opcional: Resetar o array nfeData se desejar
            // nfeData = [];
        }
    });
});
