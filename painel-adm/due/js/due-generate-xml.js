// painel-adm/due/js/due-generate-xml.js

document.getElementById('gerarDUE').addEventListener('click', function() {
    // Coleta de dados do formulário
    const cliente = document.getElementById('search-cliente-agente').value;
    const nomeCliente = document.getElementById('nomeCliente').value;
    const declarantId = document.getElementById('declarant-id').value;
    const undRFBDesp = document.getElementById('und-rfb-desp').value;
    const recAdu = document.getElementById('rec-adu').value;
    const categoriaDoc = document.getElementById('categoria-doc').value;
    const hsClassification = document.getElementById('hs-classification').value;
    const drawbackRecipientId = document.getElementById('drawback-recipient-id').value;
    const drawbackHsClassification = document.getElementById('drawback-hs-classification').value;
    const itemID = document.getElementById('itemID').value;
    const quantityQuantity = document.getElementById('quantityQuantity').value;
    const unitCode = document.getElementById('unitCode').value;
    const valueWithExchangeCoverAmount = document.getElementById('valueWithExchangeCoverAmount').value;
    const currentCode = document.getElementById('currentCode').value;
    const sequenceNumeric = document.getElementById('sequenceNumeric').value;
    const paisDeclarationOffice = document.getElementById('pais-declaration-office').value;
    const paisExitOffice = document.getElementById('pais-exit-office').value;

    // Validação básica dos campos (opcional)
    if (!cliente || !nomeCliente || !declarantId || !undRFBDesp || !recAdu || !categoriaDoc ||
        !hsClassification || !drawbackRecipientId || !drawbackHsClassification || !itemID ||
        !quantityQuantity || !unitCode || !valueWithExchangeCoverAmount || !currentCode ||
        !sequenceNumeric || !paisDeclarationOffice || !paisExitOffice) {
        alert('Por favor, preencha todos os campos obrigatórios.');
        return;
    }

    // Coleta de itens da tabela de itens
    const itensNF = [];
    $('#notasFiscaisTable tbody tr').each(function(){
        var row = $(this);
        var xmlLink = row.find('td:eq(0) a').attr('href');
        var nrProcesso = row.find('td:eq(1)').text().trim();
        var nrAdicao = row.find('td:eq(2)').text().trim();
        var chaveAcesso = row.find('td:eq(3)').text().trim();
        var nomeImportador = row.find('td:eq(4)').text().trim();
        var paisImportador = row.find('td:eq(5)').text().trim();
        var nrNotaFiscal = row.find('td:eq(6)').text().trim();
        var incoterm = row.find('td:eq(7)').text().trim();
        var destinoFinal = row.find('td:eq(8)').text().trim();
        var comissaoAgente = row.find('td:eq(9)').text().trim();

        itensNF.push({
            xml: xmlLink,
            nrProcesso: nrProcesso,
            nrAdicao: nrAdicao,
            chaveAcesso: chaveAcesso,
            nomeImportador: nomeImportador,
            paisImportador: paisImportador,
            nrNotaFiscal: nrNotaFiscal,
            incoterm: incoterm,
            destinoFinal: destinoFinal,
            comissaoAgente: comissaoAgente
        });
    });

    // Verificar se há pelo menos uma nota fiscal
    if (itensNF.length === 0) {
        alert('Por favor, adicione pelo menos uma Nota Fiscal.');
        return;
    }

    // Montagem dos dados a serem enviados
    const formData = new FormData();
    formData.append('cliente', cliente);
    formData.append('nomeCliente', nomeCliente);
    formData.append('declarantId', declarantId);
    formData.append('undRFBDesp', undRFBDesp);
    formData.append('recAdu', recAdu);
    formData.append('categoriaDoc', categoriaDoc);
    formData.append('hsClassification', hsClassification);
    formData.append('drawbackRecipientId', drawbackRecipientId);
    formData.append('drawbackHsClassification', drawbackHsClassification);
    formData.append('itemID', itemID);
    formData.append('quantityQuantity', quantityQuantity);
    formData.append('unitCode', unitCode);
    formData.append('valueWithExchangeCoverAmount', valueWithExchangeCoverAmount);
    formData.append('currentCode', currentCode);
    formData.append('sequenceNumeric', sequenceNumeric);
    formData.append('paisDeclarationOffice', paisDeclarationOffice);
    formData.append('paisExitOffice', paisExitOffice);
    formData.append('itensNF', JSON.stringify(itensNF)); // Envia como JSON

    // Mostrar spinner
    $('#spinner').show();

    // Enviar os dados para o PHP para geração do XML e envio para a API
    fetch('due/enviar-due.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        // Esconder spinner
        $('#spinner').hide();

        if (data.success) {
            alert('DU-E gerada e enviada com sucesso!');
            // Opcional: Limpar o formulário após o envio bem-sucedido
            document.getElementById('dueForm').reset();
            $('#notasFiscaisTable').DataTable().clear().draw();
            $('#xml-files').val(''); // Limpar os arquivos selecionados
        } else {
            alert('Erro ao enviar DU-E: ' + data.message);
        }
    })
    .catch(error => {
        // Esconder spinner
        $('#spinner').hide();
        console.error('Erro:', error);
        alert('Ocorreu um erro ao enviar a DU-E. Verifique o console para mais detalhes.');
    });
});
