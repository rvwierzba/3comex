$(document).ready(function () {
    $('#gerarDUE').on('click', function () {
        var dueData = {
            nomeCliente: $('#nomeCliente').val(),
            declarantId: $('#declarant-id').val(),
            undRfbDesp: $('#und-rfb-desp').val(),
            recAdu: $('#rec-adu').val(),
            categoriaDoc: $('#categoria-doc').val(),
            hsClassification: $('#hs-classification').val(),
            drawbackRecipientId: $('#drawback-recipient-id').val(),
            itemId: $('#itemID').val(),
            quantidade: $('#quantityQuantity').val(),
            unitCode: $('#unitCode').val(),
            valorComCambio: $('#valueWithExchangeCoverAmount').val(),
            currentCode: $('#currentCode').val(),
            sequenceNumeric: $('#sequenceNumeric').val(),
            notasFiscais: [] // Armazenar as NFe carregadas na tabela
        };

        // Coletar as notas fiscais da tabela, com base na ordem das colunas
        $('#notasFiscaisTable tbody tr').each(function () {
            var notaFiscal = {
                chaveAcesso: $(this).find('td').eq(1).text(),         // Coluna 1: Chave de Acesso
                nomeImportador: $(this).find('td').eq(2).text(),      // Coluna 2: Nome Importador
                pais: $(this).find('td').eq(3).text(),               // Coluna 3: País
                notaFiscal: $(this).find('td').eq(4).text(),         // Coluna 4: Nota Fiscal
                nrProcesso: $(this).find('td').eq(5).text(),         // Coluna 5: Nr. Processo
                nrAdicao: $(this).find('td').eq(6).text(),           // Coluna 6: Nr. Adição
                incoterm: $(this).find('td').eq(7).text(),           // Coluna 7: Incoterm
                destinoFinal: $(this).find('td').eq(8).text(),       // Coluna 8: Destino Final
                comissaoAgente: $(this).find('td').eq(9).text()      // Coluna 9: Comissão Agente
            };
            dueData.notasFiscais.push(notaFiscal);
        });

        // Exibir o spinner enquanto processa
        $('#spinner').show();

        // Enviar os dados da DUE via POST para o servidor
        $.ajax({
            url: 'enviar-due.php',
            type: 'POST',
            data: JSON.stringify(dueData),
            contentType: 'application/json',
            success: function (response) {
                $('#spinner').hide();
                if (response.success) {
                    alert('DU-E enviada com sucesso!');
                } else {
                    alert('Erro ao enviar DU-E: ' + response.message);
                }
            },
            error: function () {
                $('#spinner').hide();
                alert('Erro ao processar o envio da DU-E.');
            }
        });
    });
});
