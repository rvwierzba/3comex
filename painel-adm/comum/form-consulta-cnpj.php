<div class="row">
    <div class="col-12 card-body">
        <label for="cnpj-input">CNPJ:</label>
        <section class="d-flex">
            <input type="text" id="cnpj-input" name="cnpj-input" class="form-control" placeholder="Digite o CNPJ">
            <button id="btn-consultar" type="button" class="btn btn-primary ms-2">Consultar</button>
        </section>
        <div id="mensagem-consulta-api" class="mt-2"></div>
    </div>
</div>

<hr>

<div class="row">
    <div class="col-md-4 col-sm-12">
        <div class="mb-3">
            <label for="cp1" class="form-label">Nome Fantasia</label>
            <input type="text" class="form-control" name="cp1" id="cp1" readonly>
        </div>
    </div>
    <div class="col-md-4 col-sm-12">
        <div class="mb-3">
            <label for="cp2" class="form-label">Razão Social</label>
            <input type="text" class="form-control" name="cp2" id="cp2" readonly>
        </div>
    </div>
     <div class="col-md-4 col-sm-12">
        <div class="mb-3">
            <label for="cp12" class="form-label">Telefone</label>
            <input type="text" class="form-control" name="cp12" id="cp12" readonly>
        </div>
    </div>
    <div class="col-md-6 col-sm-12">
        <div class="mb-3">
            <label for="cp5" class="form-label">Endereço</label>
            <input type="text" class="form-control" name="cp5" id="cp5" readonly>
        </div>
    </div>
     <div class="col-md-3 col-sm-12">
        <div class="mb-3">
            <label for="cp30" class="form-label">Complemento</label>
            <input type="text" class="form-control" name="cp30" id="cp30" readonly>
        </div>
    </div>
    <div class="col-md-3 col-sm-12">
        <div class="mb-3">
            <label for="cp6" class="form-label">Bairro</label>
            <input type="text" class="form-control" name="cp6" id="cp6" readonly>
        </div>
    </div>
    <div class="col-md-4 col-sm-12">
        <div class="mb-3">
            <label for="cp7" class="form-label">Cidade</label>
            <input type="text" class="form-control" name="cp7" id="cp7" readonly>
        </div>
    </div>
    <div class="col-md-2 col-sm-12">
        <div class="mb-3">
            <label for="cp8" class="form-label">Estado</label>
            <input type="text" class="form-control" name="cp8" id="cp8" readonly>
        </div>
    </div>
    <div class="col-md-3 col-sm-12">
        <div class="mb-3">
            <label for="cp26" class="form-label">CEP</label>
            <input type="text" class="form-control" name="cp26" id="cp26" readonly>
        </div>
    </div>
    <div class="col-md-3 col-sm-12">
        <div class="mb-3">
            <label for="cp15" class="form-label">Email</label>
            <input type="text" class="form-control" name="cp15" id="cp15" readonly>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" onclick="limparCamposConsulta()">Limpar</button>
    <button type="button" class="btn btn-primary" id="btn-usar-dados" disabled>Usar Estes Dados</button>
</div>

<script>
$(document).ready(function() {
    let dadosDaConsulta = null;

    $('#cnpj-input').mask('00.000.000/0000-00');

    function limparCamposConsulta() {
        // Limpa os inputs de visualização e a variável de dados
        $('#form-consulta-container input[type="text"]').val('');
        $('#cnpj-input').val('');
        $('#mensagem-consulta-api').text('');
        $('#btn-usar-dados').prop('disabled', true);
        dadosDaConsulta = null;
    }
    // Tornar a função de limpar acessível globalmente se chamada por onclick
    window.limparCamposConsulta = limparCamposConsulta;

    $('#btn-consultar').on('click', function() {
        const cnpj = $('#cnpj-input').val().replace(/[^\d]+/g, '');
        if (cnpj.length !== 14) {
            $('#mensagem-consulta-api').text('Por favor, digite um CNPJ válido.').addClass('text-danger');
            return;
        }

        const btn = $(this);
        btn.prop('disabled', true).text('Consultando...');
        limparCamposConsulta(); // Limpa os campos antes de uma nova consulta
        $('#cnpj-input').val(cnpj.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, "$1.$2.$3/$4-$5")); // Recoloca a máscara


        $.ajax({
            url: "comum/consultar-cnpj.php",
            type: "post",
            data: { cnpj: cnpj },
            dataType: "json",
            success: function(data) {
                if (data && data.estabelecimento) {
                    dadosDaConsulta = data; // Armazena a resposta completa

                    // **Preenche os campos de visualização que você pediu de volta**
                    $('#cp1').val(data.estabelecimento.nome_fantasia || '');
                    $('#cp2').val(data.razao_social || '');
                    $('#cp5').val(`${data.estabelecimento.logradouro || ''}, ${data.estabelecimento.numero || ''}`);
                    $('#cp6').val(data.estabelecimento.bairro || '');
                    $('#cp7').val(data.cidade?.nome || '');
                    $('#cp8').val(data.estado?.sigla || ''); // Adicionado campo para Estado (UF)
                    $('#cp26').val(data.estabelecimento.cep || '');
                    $('#cp12').val(data.estabelecimento.ddd1 + ' ' + data.estabelecimento.telefone1 || '');
                    $('#cp15').val(data.estabelecimento.email || '');
                    $('#cp30').val(data.estabelecimento.complemento || '');
                    // O campo 'Site' não costuma vir nesta API, por isso não foi mapeado.
                    
                    $('#btn-usar-dados').prop('disabled', false); // Libera o botão principal
                    $('#mensagem-consulta-api').removeClass('text-danger').addClass('text-success').text('Consulta bem-sucedida!');
                } else {
                    $('#mensagem-consulta-api').addClass('text-danger').text('Dados não encontrados.');
                }
            },
            error: function() {
                $('#mensagem-consulta-api').addClass('text-danger').text('Erro ao consultar. Tente novamente.');
            },
            complete: function() {
                btn.prop('disabled', false).text('Consultar');
            }
        });
    });

    $('#btn-usar-dados').on('click', function() {
        if (dadosDaConsulta && typeof window.preencherFormularioComDadosCNPJ === 'function') {
            // A lógica principal de transferência de dados continua a mesma,
            // usando a variável segura `dadosDaConsulta` e não os campos.
            window.preencherFormularioComDadosCNPJ(dadosDaConsulta);
        } else {
            alert('Erro ao transferir dados. Verifique o console (F12).');
        }
    });
});
</script>