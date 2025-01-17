$(document).ready(function () {
    // Função de busca para Unidade RFB
    $('#und-rfb-desp').on('input', function () {
        var query = $(this).val();
        if (query.length >= 3) {
            $.ajax({
                url: 'buscar-unidade-rfb.php',
                type: 'POST',
                data: { query: query },
                success: function (response) {
                    $('#result-desp').html(response); // Exibir os resultados em uma lista
                },
                error: function () {
                    $('#result-desp').html('<p>Erro ao buscar Unidade RFB</p>');
                }
            });
        }
    });

    // Função de busca para Recintos Aduaneiros
    $('#rec-adu').on('input', function () {
        var query = $(this).val();
        if (query.length >= 3) {
            $.ajax({
                url: 'buscar-recinto-aduaneiro.php',
                type: 'POST',
                data: { query: query },
                success: function (response) {
                    $('#recAduList').html(response); // Exibir os resultados em uma lista
                },
                error: function () {
                    $('#recAduList').html('<p>Erro ao buscar Recinto Aduaneiro</p>');
                }
            });
        }
    });
});
