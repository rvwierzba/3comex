<!-- form-consulta-cnpj.php -->
<div class="row">
    <div class="card-body">
        <label for="cnpj">CNPJ:</label>
        <section class="d-flex">
            <input type="text" id="cnpj" name="cnpj" class="form-control">
            <button id="btn-consultar" type="submit" class="btn btn-primary">Consultar</button>
        </section>
        <div id="mensagem" class="text-danger mt-2"></div>
    </div>

    <div class="col-md-3 col-sm-12">
        <div class="mb-3">
            <label for="cp1" class="form-label">Nome Resumido</label>
            <input type="text" class="form-control" name="cp1" id="cp1">
        </div>
    </div>

    <div class="col-md-3 col-sm-12">
        <div class="mb-3">
            <label for="cp2" class="form-label">Nome</label>
            <input type="text" class="form-control" name="cp2" id="cp2">
        </div>
    </div>

    <div class="col-md-3 col-sm-12">
        <div class="mb-3">
            <label for="cp5" class="form-label">Endereço</label>
            <input type="text" class="form-control" name="cp5" id="cp5">
        </div>
    </div>

    <div class="col-md-3 col-sm-12">
        <div class="mb-3">
            <label for="cp6" class="form-label">Bairro</label>
            <input type="text" class="form-control" name="cp6" id="cp6">
        </div>
    </div>

    <div class="col-md-3 col-sm-12">
        <div class="mb-3">
            <label for="cp30" class="form-label">Complemento</label>
            <input type="text" class="form-control" name="cp30" id="cp30">
        </div>
    </div>

    <div class="col-md-3 col-sm-12">
        <div class="mb-3">
            <label for="cp7" class="form-label">Cidade</label>
            <input type="text" class="form-control" name="cp7" id="cp7">
        </div>
    </div>

    <div class="col-md-3 col-sm-12">
        <div class="mb-3">
            <label for="cp26" class="form-label">CEP</label>
            <input type="text" class="form-control" name="cp26" id="cp26">
        </div>
    </div>

    <div class="col-md-3 col-sm-12">
        <div class="mb-3">
            <label for="cp12" class="form-label">Telefone</label>
            <input type="text" class="form-control" name="cp12" id="cp12">
        </div>
    </div>

    <div class="col-md-3 col-sm-12">
        <div class="mb-3">
            <label for="cp29" class="form-label">Site</label>
            <input type="text" class="form-control" name="cp29" id="cp29">
        </div>
    </div>

    <div class="col-md-3 col-sm-12">
        <div class="mb-3">
            <label for="cp15" class="form-label">Email</label>
            <input type="text" class="form-control" name="cp15" id="cp15">
        </div>
    </div>
</div>

<div class="modal-footer" style="margin-right:40%;">
    <button type="button" class="btn btn-secondary" onclick="limparCamposConsultaCNPJ()">Limpar</button>
    <a href="#" class="btn btn-primary" id="btn-voltar">Voltar</a>
</div>


  <script>
    function getCNPJ() {
        let inputCNPJ = $('#cnpj');
        let cnpjMasked = inputCNPJ.val();
        let cnpj = cnpjMasked.replace(/[^\d]+/g, '');
        return cnpj;
    }

    $('#btn-consultar').on('click', function(event) {
    event.preventDefault();

    const cnpj = getCNPJ();
    if (!cnpj) return; // Se o CNPJ for inválido, a função retorna e não faz a consulta
    
    var url = "../painel-adm/comum/consultar-cnpj.php";

    $.ajax({
        url: url,
        type: "post",
        data: { cnpj: cnpj },
        dataType: "json",
        success: function(data) {
            if (data.status === 429) {
                // Exibir mensagem de erro para excesso de requisições
                $('#mensagem').text('Limite de 3 consultas por minuto excedido. Aguarde e tente novamente.');

                // Iniciar o timer de 1 minuto para liberação
                iniciarTimer(60);
            } else if (data.estabelecimento) {
                // Preenche os campos com os dados retornados
                $('#cp1').val(data.estabelecimento.nome_fantasia || '');
                $('#cp2').val(data.razao_social || '');
                $('#cp5').val(data.estabelecimento.logradouro || '');
                $('#cp6').val(data.estabelecimento.bairro || '');
                $('#cp7').val(data.cidade?.nome || '');
                $('#cp26').val(data.estabelecimento.cep || '');
                $('#cp12').val(data.estabelecimento.telefone || '');
                $('#cp15').val(data.estabelecimento.email || '');
                $('#cp30').val(data.estabelecimento.complemento || '');
            } else {
                $('#mensagem').text('Dados não encontrados para o CNPJ fornecido.');
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            if (jqXHR.status === 429) {
                $('#mensagem').text('Limite de 3 consultas por minuto excedido. Aguarde e tente novamente.');

                // Iniciar o timer de 1 minuto para liberação
                iniciarTimer(60);
            } else {
                let message = 'Erro ao consultar CNPJ';
                if (jqXHR.responseJSON && jqXHR.responseJSON.error) {
                    message = jqXHR.responseJSON.error;
                } else if (textStatus) {
                    message = textStatus;
                }
                $('#mensagem').html(`Erro ao consultar CNPJ: ${message}`);
            }
        }
    });
});

function iniciarTimer(duracao) {
    var timer = duracao, minutos, segundos;
    var intervalo = setInterval(function () {
        minutos = parseInt(timer / 60, 10);
        segundos = parseInt(timer % 60, 10);

        minutos = minutos < 10 ? "0" + minutos : minutos;
        segundos = segundos < 10 ? "0" + segundos : segundos;

        $('#mensagem').text(`Por favor, aguarde ${minutos}:${segundos} antes de realizar uma nova consulta.`);
        
        if (--timer < 0) {
            clearInterval(intervalo);
            $('#mensagem').text('Você pode realizar uma nova consulta agora.');
        }
    }, 1000);
}


function limparCamposConsultaCNPJ() {
    $('#cnpj').val('');
    $('#cp1').val('');
    $('#cp2').val('');
    $('#cp5').val('');
    $('#cp6').val('');
    $('#cp7').val('');
    $('#cp26').val('');
    $('#cp12').val('');
    $('#cp29').val('');
    $('#cp15').val('');
    $('#cp30').val('');
}

$('#btn-voltar').on('click', function(event) {
    event.preventDefault();

    // Obtém a origem armazenada (clientes ou agentes)
    var origin = $('#modalConsulta').data('origin');
    //console.log('Origem:', origin);

    if (origin === 'clientes') {
        preencherCamposClientes();
    } else if (origin === 'agentes') {
        preencherCamposAgentes();
    }

    // Fecha o modal de consulta e reabre o modal original
    $('#modalConsulta').modal('hide');
    $('#modalForm').modal('show');
});

function preencherCamposClientes() {
    //console.log('Preenchendo campos para Clientes...');
    if ($('#cnpj').val()) {
        $('#CNPJ').val($('#cnpj').val());
    }    
    if ($('#cp1').val()) {
        $('#NomeRes').val($('#cp1').val());
        //console.log('NomeRes:', $('#cp1').val());
    }
    if ($('#cp2').val()) {
        $('#Nome').val($('#cp2').val());
        //console.log('Nome:', $('#cp2').val());
    }
    if ($('#cp5').val()) {
        $('#Endereco').val($('#cp5').val());
        //console.log('Endereco:', $('#cp5').val());
    }
    if ($('#cp6').val()) {
        $('#Bairro').val($('#cp6').val());
        //console.log('Bairro:', $('#cp6').val());
    }
    if ($('#cp7').val()) {
        $('#Cidade').val($('#cp7').val());
        //console.log('Cidade:', $('#cp7').val());
    }
    if ($('#cp26').val()) {
        $('#Cep').val($('#cp26').val());
        //console.log('Cep:', $('#cp26').val());
    }
    if ($('#cp12').val()) {
        $('#Telefone').val($('#cp12').val());
        //console.log('Telefone:', $('#cp12').val());
    }
    if ($('#cp29').val()) {
        $('#Site').val($('#cp29').val());
        //console.log('Site:', $('#cp29').val());
    }
    if ($('#cp15').val()) {
        $('#Email').val($('#cp15').val());
        //console.log('Email:', $('#cp15').val());
    }
    if ($('#cp30').val()) {
        $('#Complemento').val($('#cp30').val());
        //console.log('Complemento:', $('#cp30').val());
    }
}

function preencherCamposAgentes() {
   //console.log('Preenchendo campos para Agentes...');
    if ($('#cnpj').val()) {
        $('#CNPJ').val($('#cnpj').val());
    }
    if ($('#cp1').val()) {
        $('#NomeRes').val($('#cp1').val());
        //console.log('NomeRes:', $('#cp1').val());
    }
    if ($('#cp2').val()) {
        $('#Nome').val($('#cp2').val());
        //console.log('Nome:', $('#cp2').val());
    }
    if ($('#cp5').val()) {
        $('#Endereco').val($('#cp5').val());
        //console.log('Endereco:', $('#cp5').val());
    }
    if ($('#cp6').val()) {
        $('#Bairro').val($('#cp6').val());
        //console.log('Bairro:', $('#cp6').val());
    }
    if ($('#cp7').val()) {
        $('#Cidade').val($('#cp7').val());
        //console.log('Cidade:', $('#cp7').val());
    }
    if ($('#cp26').val()) {
        $('#Cep').val($('#cp26').val());
        //console.log('Cep:', $('#cp26').val());
    }
    if ($('#cp12').val()) {
        $('#Telefone').val($('#cp12').val());
        //console.log('Telefone:', $('#cp12').val());
    }
    if ($('#cp29').val()) {
        $('#Site').val($('#cp29').val());
        //console.log('Site:', $('#cp29').val());
    }
    if ($('#cp15').val()) {
        $('#Email').val($('#cp15').val());
        //console.log('Email:', $('#cp15').val());
    }
    if ($('#cp30').val()) {
        $('#Complemento').val($('#cp30').val());
        //console.log('Complemento:', $('#cp30').val());
    }
}




</script>


