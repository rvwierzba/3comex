<?php
// Conexão com o banco (coloque *antes* de qualquer HTML, mas dentro do <html> se já existir)
include_once 'C:\\xampp\\htdocs\\3comex\\conexao.php';  // Ajuste o caminho se necessário!
?>


<style>

.form-group {
        margin-bottom: 1rem;
    }
    .form-check-inline {
        margin-right: 1rem;
    }
    .form-check-input {
        margin-top: 0.3rem;
    }
    #notasFiscaisTable {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    #notasFiscaisTable td,
    #notasFiscaisTable th {
        vertical-align: middle;
        padding: 12px;
        border: 1px solid #dee2e6;
    }
    #notasFiscaisTable thead {
        position: sticky;
        top: 0;
        background: white;
        z-index: 100;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    .thead-light th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
    }
    button[type="button"],
    button.toggle-details {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        cursor: pointer;
        background: none;
        border: none;
        padding: 0;
        font: inherit;
        color: inherit;
    }
    .btn.toggle-details {
        min-width: 32px;
        transition: transform 0.2s;
    }
    .btn.toggle-details:hover {
        transform: scale(1.1);
        background-color: #f8f9fa;
    }
    .details-row {
        transition: all 0.3s ease;
    }
    .inner-table {
        width: 100%;
        border-collapse: collapse;
    }
    .inner-table th,
    .inner-table td {
        padding: 8px;
        border: 1px solid #dee2e6;
        text-align: left;
    }
    #tabela-nfe tr.details-row input[type="text"],
    #tabela-nfe tr.details-row select {
        width: 100%;
        padding: 8px;
        margin-bottom: 8px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        box-sizing: border-box;
    }
    #tabela-nfe tr.details-row .save-nf-btn {
        background-color: #28a745;
        border-color: #28a745;
        color: #fff;
        padding: 8px 16px;
        border-radius: 4px;
        cursor: pointer;
    }
    #tabela-nfe tr.details-row .save-nf-btn:hover {
        background-color: #218838;
        border-color: #218838;
    }
    .meus-botoes > button {
        margin-right: 5px;
    }
    #tabela-nfe td .btn {
        margin: 2px;
        display: inline-block;
    }
    #tabela-nfe td .btn-info {
        color: #fff;
        background-color: #17a2b8;
        border-color: #17a2b8;
    }
    #tabela-nfe td .btn-danger {
        color: #fff;
        background-color: #dc3545;
        border-color: #dc3545;
    }
    #tabela-nfe td:nth-child(4) {
        width: 150px;
        white-space: nowrap;
        text-align: center;
    }
    #tabela-nfe tr.details-row {
        background-color: #f8f9fa;
    }
    #tabela-nfe tr.details-row td {
        padding: 0px!important;
        border: none;
    }
    #tabela-nfe tr.details-row input[type="text"],
    #tabela-nfe tr.details-row select {
        width: 100%;
        padding: 8px;
        margin-bottom: 8px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        box-sizing: border-box;
    }
    .inner-table {
        width: 100%;
        border-collapse: collapse;
    }
    .inner-table th, .inner-table td {
        padding: 8px;
        border: 1px solid #dee2e6;
        text-align: left;
    }
    #tabela-nfe tr.details-row .save-nf-btn {
        background-color: #28a745;
        border-color: #28a745;
        color: #fff;
        padding: 8px 16px;
        border-radius: 4px;
        cursor: pointer;
    }
    #tabela-nfe tr.details-row .save-nf-btn:hover {
        background-color: #218838;
        border-color: #218838;
    }

     /* Garanta que apenas a aba ativa seja visível */
     .tab-content > .tab-pane:not(.active) {
        display: none !important; /* Força a ocultação */
        opacity: 0; /* Adicione para transições suaves */
    }

    .tab-content > .tab-pane.active {
        display: block !important; /* Garante a exibição */
        opacity: 1;
        transition: opacity 0.3s ease; /* Opcional: efeito de fade */
    }

    /* Mantenha o restante do seu CSS personalizado abaixo */
    .nav-tabs .nav-link.active {
        border-bottom: 3px solid #0d6efd !important;
        background: #fff;
    }

    .tab-content {
        border: 1px solid #dee2e6;
        border-radius: 0 0 0.5rem 0.5rem;
        padding: 20px;
    }

    .details-row {
    display: none; /* Oculta a linha de detalhes por padrão */
    background-color: #f8f9fa; /* Cor de fundo opcional */
}

.details-content {
    padding: 15px; /* Espaçamento interno */
}

.meus-botoes {
  display: flex;
  justify-content: center; /* Centraliza os botões */
}
.meus-botoes > button {
    margin-right: 5px; /* Espaço entre os botões, se precisar de mais no futuro */
}
.toggle-details{
  display: flex;
}

/* Estilo do botão de salvar dentro da linha de detalhes */
#tabela-nfe tr.details-row .save-nf-btn {
    /* ... (Seus estilos para o botão) ... */
}

/* Garante que a tabela interna não quebre o layout */
.inner-table {
  width: 100%;
  border-collapse: collapse;
}

.inner-table th, .inner-table td{
  padding: 8px;
  border: 1px solid #dee2e6;
}

</style>



<!-- HTML -->

<div class="container mt-4">
    <h2 class="mb-4 text-center">Gerar Declaração Única de Exportação (DU-E)</h2>

    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link active" href="#aba1">Dados gerais</a>
        </li>
       <li class="nav-item">
            <a class="nav-link" href="#aba3">NFE(s) inserida(s)</a>
        </li>
    </ul>   
    

    <div class="tab-content" id="dueTabsContent">
             <!-- Tab Importação NFs -->
            <div class="tab-pane fade show active" id="aba1">Conteúdo da Aba 1</div>
                <form id="dueForm" enctype="multipart/form-data">
                    <div class="card mb-4">
                        <div class="card-header">Informações Gerais</div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="xml-files">Selecionar Arquivos XML</label>
                                <input type="file" id="xml-files" class="form-control" accept=".xml" multiple>
                            </div>
                            <div id="uploadStatus" class="mt-2"></div>
                        </div>
                    </div>
                    <div class="d-flex grid gap-3">
                        <div class="form-group">
                            <label for="cnpj-cpf-select">CNPJ/CPF:</label>
                            <input class="form-control" type="text" id="text-cnpj-cpf-select" name="cnpj-cpf" list="cnpj-cpf-list">
                            <datalist id="cnpj-cpf-list"></datalist>
                        </div>
                        </div>
                        <div class="form-group">
                            <label for="nomeCliente">Nome do Cliente</label>
                            <input type="text" id="nomeCliente" class="form-control" readonly>
                        </div>
                    </div>
                    <br>
                    <div class="form-group">
                        <h5>Forma de exportação</h5>
                        <input type="radio" id="por-conta-propria" name="forma-export" value="Por conta própria">
                        <label for="por-conta-propria">Por conta própria</label>
                        <br>
                        <input type="radio" id="p-conta-ordem-terceiros" name="forma-export"
                            value="Por conta ou ordem de terceiros">
                        <label for="p-conta-ordem-terceiros">Por conta ou ordem de terceiros</label>
                        <br>
                        <input type="radio" id="p-op-rm-post-ou-remss" name="forma-export"
                            value="Por operador de remessa postal ou expressa">
                        <label for="p-op-rm-post-ou-remss">Por operador de remessa postal ou expressa</label>
                    </div>
                    <br>

                    <div class="form-group d-flex grid gap-2">
                    <label for="export-cons">Exportação Consosorciada: </label> 
                    <input type="checkbox" id="export-cons" name="export-cons" value="Exportação Consosorciada">
                    </div>

                    <br>
                    <div class="form-group">
                        <h5>Situação especial de despacho</h5>
                        <select name="situacao-espec-despacho" class="form-control" id="situacao-espec-despacho">
                            <option selected>Selecione...</option>   
                            <option value="DU-E a posteriori">DU-E a posteriori</option>
                            <option value="Embarque antecipado">Embarque antecipado</option>
                            <option value="Exportação sem saída da mercadoria do país">Exportação sem saída da mercadoria do país</option>
                        </select>
                    </div>
                    <br>
                    <div class="form-group">
                        <h5>Tipo de documento fiscal que ampara as mercadorias a serem exportadas:</h5>
                        <input type="radio" id="nfe" name="tp-doc-amp-merc-export" value="Nota fiscal eletronica">
                        <label for="nfe">Nota Fiscal Eletrônica (NF-e)</label>
                        <br>
                        <input type="radio" id="nf-form" name="tp-doc-amp-merc-export" value="Nota fiscal formulario">
                        <label for="nf-form">Nota Fiscal Formulário</label>
                        <br>
                        <input type="radio" id="s-nf" name="tp-doc-amp-merc-export" value="Sem nota fiscal">
                        <label for="s-nf">Sem nota fiscal</label>
                    </div>
                    <br>
                    <div class="d-flex grid gap-3">
                        <div class="form-group">
                            <label for="moeda">Moeda:</label>
                            <input id="text-moeda" type="text" class="form-control" list="moeda" name="moeda" style="margin-rigth: 0.8%;">
                            <datalist id="moeda">
                                <?php
                                    foreach($pdo->query('SELECT Codigo, Nome FROM moeda ORDER BY Nome') as $row){
                                        echo '<option value="'. $row['Codigo'] .'-'. $row['Nome'] .'">'.'</option>';
                                        }       
                                ?>
                            </datalist>
                        </div>
                        <div class="form-group">
                            <label for="ruc">Referência Única de Carga (RUC):</label>
                            <input type="text" class="form-control" id="ruc" name="ruc">
                        </div>

                    </div>
                    <br>

                    <div>
                        <h4 id="lbl-local-despacho">Local de Despacho:</h4>

                        <div class="form-group">
                            <label for="campo-de-pesquisa-unidades-rfb-d">Unidade da RFB:</label>
                            <input id="text-campo-de-pesquisa-unidades-rfb-d" type="text" class="form-control" list="campo-de-pesquisa-unidades-rfb-d" name="campo-de-pesquisa-unidades-rfb-d">
                            <datalist id="campo-de-pesquisa-unidades-rfb-d">
                                <?php
                                    foreach($pdo->query('SELECT Codigo, Nome FROM unidades_rfb ORDER BY Nome') as $row){
                                        echo '<option value="'. $row['Codigo'] .'-'. $row['Nome'] .'">'.'</option>';
                                        }       
                                ?>
                            </datalist>
                        </div>

                        <br>

                        <div class="d-flex grid gap-3">
                            <div class="form-group">
                                <label for="em-ra-d">Local de Despacho:</label>
                                <br><INPUT TYPE="RADIO" NAME="em-ra-d" id="sd" VALUE="sim"> Sim
                                <br><INPUT TYPE="RADIO" NAME="em-ra-d" id="sn" VALUE="nao"> Não
                            </div>
                            <div class="form-group">
                            <input id="txt-campo-de-pesquisa-recinto-alfandegado-d" type="text" class="form-control" list="campo-de-pesquisa-recinto-alfandegado-d" name="campo-de-pesquisa-recinto-alfandegado-d">
                            <datalist id="campo-de-pesquisa-recinto-alfandegado-d">
                                <?php
                                    foreach($pdo->query('SELECT codigo, Nome FROM recinto_aduaneiro ORDER BY Nome') as $row){
                                        echo '<option value="'. $row['codigo'] .'-'. $row['Nome'] .'">'.'</option>';
                                    }       
                                ?>
                            </datalist>
                            </div>
                        </div>
                        <br>
                    </div>

                    <div>
                        <h4 id="lbl-local-embarque">Local de Embarque / Transposição de Fronteira:</h4>

                        <div class="form-group">
                            <label for="campo-de-pesquisa-unidades-rfb-e">Unidade da RFB:</label>
                            <input id="text-campo-de-pesquisa-unidades-rfb-e" type="text" class="form-control" list="campo-de-pesquisa-unidades-rfb-e" name="campo-de-pesquisa-unidades-rfb-e">
                            <datalist id="campo-de-pesquisa-unidades-rfb-e">
                                <?php
                                    foreach($pdo->query('SELECT codigo, Nome FROM unidades_rfb ORDER BY Nome') as $row){
                                        echo '<option value="'. $row['Codigo'] .'-'. $row['Nome'] .'">'.'</option>';
                                    }       
                                ?>
                            </datalist>
                            </div>

                        <br>

                        <div class="d-flex grid gap-3">
                            <div class="form-group">
                                <label for="em-ra-e">Local de Despacho:</label>
                                <br><INPUT TYPE="RADIO" NAME="em-ra-e" id="sd-embarque" VALUE="sim"> Sim
                                <br><INPUT TYPE="RADIO" NAME="em-ra-e" id="sn-embarque" VALUE="nao"> Não
                                
                            </div>
                            <div class="form-group">
                                <label for="campo-de-pesquisa-recinto-alfandegado-e">Recinto Aduaneiro:</label>
                                <input id="text-campo-de-pesquisa-recinto-alfandegado-e" type="text" class="form-control" list="campo-de-pesquisa-recinto-alfandegado-e" name="campo-de-pesquisa-recinto-alfandegado-e">
                                <datalist id="campo-de-pesquisa-recinto-alfandegado-e">
                                    <?php
                                        foreach($pdo->query('SELECT codigo, Nome FROM recinto_aduaneiro ORDER BY Nome') as $row){
                                            echo '<option value="'. $row['codigo'] .'-'. $row['Nome'] .'">'.'</option>';
                                        }       
                                    ?>
                                </datalist>
                            </div>
                        </div>
                    </div>

                    <br>

                    <div id=complementos>
                        <h4 id="lbl-complementos">Complementos</h4>
                        <div class="form-group">
                            <label>Via especial de transporte</label>
                            <select class="form-control" id="via-especial-transport"
                                name="via-especial-transport">
                                <option selected>Selecione...</option>
                                <option value="MEIOS PRÓPRIOS">MEIOS PRÓPRIOS</option>
                                <option value="DUTOS">DUTOS</option>
                                <option value="LINHAS DE TRANSMISSÃO">LINHAS DE TRANSMISSÃO</option>
                                <option value="EM MÃO">EM MÃOS</option>
                                <option value="POR REBOQUE">POR REBOQUE</option>
                                <option value="TRANSPORTE VICINAL FRONTEIRIÇO">TRANSPORTE VICINAL FRONTEIRIÇO</option>
                            </select>
                        </div>
                        <br>
                        <div class="form-group">
                            <label>Informações complementares</label>
                            <textarea class="form-control" id="info-compl" name="info-compl"></textarea>
                        </div>
                    </div>
                </form>
            </div>

        
        <!-- Tab NFs Inseridas -->
        <div class="tab-pane fade" id="aba3">Conteúdo da Aba 3</div>
        <form>
            <div class="card mb-4">
                <div class="form-group" style="margin-left: 40%;">
                    <br>
                <h4>CCPTC/CCROM</h4>
                <label for="ccpt-ccrom">A mercadoria é amparada por: </label>
                        <br>              

                        <input type="radio" id="naoAmparada" name="ccpt-ccrom-op" value="Não amparada" />
                        <label for="naoAmparada">Não Amparada</label>

                        <br>
                
                        <input type="radio" id="ccptc" name="ccpt-ccrom-op" value="CCPTC" />
                        <label for="ccptc">CCPTC</label>

                        <br>
                
                        <input type="radio" id="ccrom" name="ccpt-ccrom-op" value="CCROM" />
                        <label for="ccrom">CCROM</label>
                        
                </div>
                <br>
                <div class="card-header">Lista de Notas Fiscais</div>
                <div class="card-body">
                    <div id="tabelaContainer" class="table-responsive">
                        <table class="table table-bordered" id="notasFiscaisTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>Chave de Acesso</th>
                                    <th>Nome Importador</th>
                                    <th>País</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- As linhas serão populadas via JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </form>
    </div>                              

    </div>

    <div class="form-group mt-4">
        <button type="button" id="gerarDUE" class="btn btn-primary w-100">Salvar e Avançar</button>
    </div>

    <div id="spinner" class="spinner-border text-primary" role="status" style="display: none;">
        <span class="sr-only">Carregando...</span>
    </div>
</div>


<script src="./due/js/add-lpcos.js"></script>
<script src="./due/js/due-generate-xml.js"></script>
<script type="module" src="./due/js/campos-itens-nfe.mjs"></script>

<!-- SCRIPT UNIFICADO (UM ÚNICO BLOCO type="module") -->
<script type="module">
    import NFeProcessor from './due/js/due-upload.mjs';

    document.addEventListener('DOMContentLoaded', async () => {

        // --- FUNÇÃO addSaveButtonListeners (DEFINIDA AQUI) ---
        function addSaveButtonListeners() {
            console.log("addSaveButtonListeners chamada!"); // Log para verificar

            // Adiciona event listeners aos botões de salvar de cada item:
            const saveButtons = document.querySelectorAll('.btn-success'); // Seletor para os botões
            saveButtons.forEach(button => {
                button.addEventListener('click', () => {
                    // Lógica para salvar os dados do item (ADAPTE PARA SUA NECESSIDADE)
                    console.log("Botão salvar do item clicado!");
                    const row = button.closest('tr'); // Encontra a linha do item
                    const xProd = row.querySelector('.campo-xProd').value; // Exemplo
                    const ncm = row.querySelector('.campo-ncm').value;     // Exemplo
                    // ... Acesse os outros campos da mesma forma ...

                    // Exemplo de como pegar os LPCOs adicionados (DENTRO da linha)
                    const lpcoContainer = row.querySelector('.lpco-container');
                    if (lpcoContainer) {
                        const listaLpcos = lpcoContainer.querySelector('.lista-lpcos');
                        if (listaLpcos) {
                            const lpcos = Array.from(listaLpcos.children).map(span => span.dataset.codigo);
                            console.log("LPCOs do item:", lpcos);
                        }
                    }

                    // ... Faça o que você precisa fazer para salvar os dados (AJAX, etc.) ...
                });
            });
        }


        const processor = new NFeProcessor();

        // Upload de XMLs
        const inputXML = document.getElementById('xml-files');
        if (inputXML) {
            inputXML.addEventListener('change', async (e) => {
                try {
                    await processor.processFiles(e.target.files);
                    addSaveButtonListeners();  // Chama a função DEPOIS do processamento
                } catch (error) {
                    console.error('Falha no processamento:', error);
                    alert(error.message || 'Erro ao processar arquivos');
                }
            });
        }

        // Delegation para elementos dinâmicos da tabela principal (toggle e remover)
        document.querySelector('#notasFiscaisTable').addEventListener('click', (e) => {
            const btn = e.target.closest('button');
            if (!btn) return;

            if (btn.classList.contains('toggle-details')) {
                processor.toggleDetails(btn);
            }
            if (btn.classList.contains('remove-nf')) {
                processor.removeNota(btn);
            }
        });

        // Carregamento dinâmico dos scripts na aba 3
        const aba3 = document.querySelector('a[href="#aba3"]');
        aba3.addEventListener('shown.bs.tab', () => {
            // Carrega campos-itens-nfe.mjs (como módulo)
            const scriptCampos = document.createElement('script');
            scriptCampos.type = 'module'; //  <---  IMPORTANTE!
            scriptCampos.src = './due/js/campos-itens-nfe.mjs';
            document.body.appendChild(scriptCampos);

            // Carrega add-lpcos.js (normalmente, SEM type="module")
            const scriptLpcos = document.createElement('script');
            scriptLpcos.src = './due/js/add-lpcos.js';
            document.body.appendChild(scriptLpcos);
        });
    });
</script>

