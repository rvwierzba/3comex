<?php
// Conexão com o banco (coloque *antes* de qualquer HTML, mas dentro do <html> se já existir)
include_once 'C:\\xampp\\htdocs\\3comex\\conexao.php';  // Ajuste o caminho se necessário!
?>

<style>


/* CAMPOS ITENS NFs */
.form-group { margin-bottom: 1rem; }
.form-check-inline { margin-right: 1rem; }
.form-check-input { margin-top: 0.3rem; }
#notasFiscaisTable { width: 100%; border-collapse: separate; border-spacing: 0; }
#notasFiscaisTable td, #notasFiscaisTable th { vertical-align: middle; padding: 12px; border: 1px solid #dee2e6; }
#notasFiscaisTable thead { position: sticky; top: 0; background: white; z-index: 100; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); }
.thead-light th { background-color: #f8f9fa; border-bottom: 2px solid #dee2e6; font-weight: 600; }
button[type="button"], button.toggle-details { -webkit-appearance: none; -moz-appearance: none; appearance: none; cursor: pointer; background: none; border: none; padding: 0; font: inherit; color: inherit; }
.btn.toggle-details { min-width: 32px; transition: transform 0.2s; }
.btn.toggle-details:hover { transform: scale(1.1); background-color: #f8f9fa; }
/* Estilo MUITO IMPORTANTE para as linhas de detalhes */
.details-row { display: none; /* Oculta por padrão */ background-color: #f8f9fa; /* Cor de fundo opcional */ }
.inner-table { width: 100%; border-collapse: collapse; }
.inner-table th, .inner-table td { padding: 8px; border: 1px solid #dee2e6; text-align: left; }
#tabela-nfe tr.details-row .save-nf-btn { background-color: #28a745; border-color: #28a745; color: #fff; padding: 8px 16px; border-radius: 4px; cursor: pointer; }
#tabela-nfe tr.details-row .save-nf-btn:hover { background-color: #218838; border-color: #218838; }
.meus-botoes > button { margin-right: 5px; }
#tabela-nfe td .btn { margin: 2px; display: inline-block; }
#tabela-nfe td .btn-info { color: #fff; background-color: #17a2b8; border-color: #17a2b8; }
#tabela-nfe td .btn-danger { color: #fff; background-color: #dc3545; border-color: #dc3545; }
/* Estilos para os detalhes (já definidos e adaptados) */
.item-details-table { width: 100%; border-collapse: collapse; }
.item-details-table th, .item-details-table td { padding: 8px; border: 1px solid #dee2e6; text-align: left; }
.item-details-table input[type="text"], .item-details-table select { width: 100%; padding: 6px; margin-bottom: 4px; border: 1px solid #ced4da; border-radius: 4px; box-sizing: border-box;}
.lpco-container { margin-top: 10px; }
.lista-lpcos { display: flex; flex-wrap: wrap; gap: 5px; }
.lpco-item { background-color: #e9ecef; padding: 2px 6px; border-radius: 4px; }
/* Outros estilos (abas, etc. - já definidos) */
.tab-content > .tab-pane:not(.active) { display: none !important; opacity: 0; }
.tab-content > .tab-pane.active { display: block !important; opacity: 1; transition: opacity 0.3s ease; }
.nav-tabs .nav-link.active { border-bottom: 3px solid #0d6efd !important; background: #fff; }
.tab-content { border: 1px solid #dee2e6; border-radius: 0 0 0.5rem 0.5rem; padding: 20px; }
.details-content { padding: 15px; }  /* Adicionado para espaçamento */
.meus-botoes { display: flex; justify-content: center; }
.meus-botoes > button { margin-right: 5px; }
/* Ajuste para alinhar o botão "+" */
.item-row td:last-child {  /* Última célula da linha (onde fica o botão) */
    text-align: center; /* Centraliza o conteúdo */
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
                                        echo '<option value="'. $row['codigo'] .'-'. $row['Nome'] .'">'.'</option>';
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

<script type="module">
    import NFeProcessor from './due/js/due-upload.mjs';

    // Função para criar os campos de detalhes do item (MANTIDA e adaptada)
    function createItemDetailsFields(itemData) {
        // (Mesmo código da função createItemDetailsFields - Adaptado)
        const {
            xProd, ncm, condicaoVenda, vmcvMoeda, vmleMoeda, nomeImportador, enderecoImportador, paisImportador,
            paisDestino, primeiroEnquadramento, segundoEnquadramento, terceiroEnquadramento, quartoEnquadramento,
            listaLpco, tratamentoTributario,
        } = itemData;

        const table = document.createElement('table');
        table.classList.add('item-details-table');
        const tbody = document.createElement('tbody');

         function createRow(labelText, inputType, inputValue, inputName, datalistOptions = null, readOnly = false) {
            const row = document.createElement('tr');
            const labelCell = document.createElement('th');
            labelCell.textContent = labelText;
            const inputCell = document.createElement('td');
            let input;

            if (inputType === 'select') {
                input = document.createElement('select');
                input.name = inputName;
                if (datalistOptions) {
                    const datalist = document.createElement('datalist');
                    datalist.id = inputName + '-list';
                    datalistOptions.forEach(option => {
                        const opt = document.createElement('option');
                        opt.value = option.value;
                        opt.textContent = option.text;
                        datalist.appendChild(opt);
                    });
                    input.setAttribute('list', datalist.id);
                    inputCell.appendChild(datalist);
                }
                const defaultOption = document.createElement('option');
                defaultOption.textContent = "Selecione...";
                defaultOption.value = "";
                input.appendChild(defaultOption);
                if(datalistOptions){
                    datalistOptions.forEach(option => {
                        const opt = document.createElement('option');
                        opt.value = option.value;
                        opt.textContent = option.text;
                        if (option.value === inputValue) {
                            opt.selected = true;
                        }
                        input.appendChild(opt);
                    });
                }
            } else {
                input = document.createElement('input');
                input.type = inputType;
                input.name = inputName;
                input.value = inputValue || '';
                 if (readOnly) {
                    input.readOnly = true;
                }
            }
            inputCell.appendChild(input);
            row.appendChild(labelCell);
            row.appendChild(inputCell);
            tbody.appendChild(row);
            return row;
        }

        // --- Criação das linhas (campos combinados) ---
        createRow('Item da DU-E:', 'text', itemData.item, 'item', null, true);
        createRow('Nota fiscal', 'text', itemData.chaveNF, 'chaveNF', null, true );
        createRow('Item da Nota Fiscal', 'text', itemData.itemNF, 'itemNF', null, true);
        createRow('Descrição da mercadoria:', 'text', xProd, 'xProd');
        createRow('NCM:', 'text', ncm, 'ncm');
        createRow('Unidade estatística:', 'text', itemData.uCom, 'uCom', null, true); //Reaproveitando
        createRow('Quantidade estatística:', 'text', itemData.qCom, 'qCom', null, true); //Reaproveitando
        createRow('Unidade comercializada:', 'text', itemData.uCom, 'uComercializada'); //Reaproveitando
        createRow('Quantidade comercializada:', 'text', itemData.qCom, 'qComercializada');//Reaproveitando
        createRow('Valor (R$):', 'text', itemData.vUnCom, 'vUnCom'); //Reaproveitando
        createRow('Peso líquido total (KG):', 'text', itemData.pesoLiquido, 'pesoLiquido'); //Reaproveitando
        createRow('Condição de venda:', 'select', condicaoVenda, 'condicaoVenda', [
            { value: 'EXW', text: 'EXW - EX WORKS' },
            { value: 'FCA', text: 'FCA - FREE CARRIER'}
        ]);
        createRow('VMCV (MGA):', 'text', vmcvMoeda, 'vmcvMoeda');
        createRow('VMLE (MGA):', 'text', vmleMoeda, 'vmleMoeda');
        createRow('Nome do importador:', 'text', nomeImportador, 'nomeImportador');
        createRow('Endereço do importador:', 'text', enderecoImportador, 'enderecoImportador');
        createRow('País do importador:', 'text', paisImportador, 'paisImportador');
        createRow('País de destino:', 'select', paisDestino, 'paisDestino', [
            { value: 'US', text: 'Estados Unidos' },
            { value: 'CA', text: 'Canadá' },
             { value: 'PY', text: 'Paraguai' },
        ]);
        createRow('Primeiro enquadramento:', 'select', primeiroEnquadramento, 'primeiroEnquadramento', [
            {value: '1', text: 'Enquadramento 1'},
            {value: '2', text: 'Enquadramento 2'}
        ]);
        createRow('Segundo enquadramento:', 'select', segundoEnquadramento, 'segundoEnquadramento', [
            {value: '1', text: 'Enquadramento 1'},
            {value: '2', text: 'Enquadramento 2'}
        ]);
        createRow('Terceiro enquadramento:', 'select', terceiroEnquadramento, 'terceiroEnquadramento', [
            {value: '1', text: 'Enquadramento 1'},
            {value: '2', text: 'Enquadramento 2'}
        ]);
        createRow('Quarto enquadramento:', 'select', quartoEnquadramento, 'quartoEnquadramento', [
            {value: '1', text: 'Enquadramento 1'},
            {value: '2', text: 'Enquadramento 2'}
        ]);

        const lpcoRow = createRow('Lista de LPCO:', 'text', '', 'lpco');
        const lpcoInput = lpcoRow.querySelector('input[name="lpco"]');
        const addButton = document.createElement('button');
        addButton.textContent = 'Adicionar LPCO';
        addButton.type = 'button';
        addButton.addEventListener('click', () => {
            const codigoLpco = lpcoInput.value.trim();
            if (codigoLpco) {
                const lpcoList = lpcoRow.querySelector('.lista-lpcos') || document.createElement('div');
                lpcoList.classList.add('lista-lpcos');
                const lpcoItem = document.createElement('span');
                lpcoItem.classList.add('lpco-item');
                lpcoItem.textContent = codigoLpco;
                lpcoItem.dataset.codigo = codigoLpco;
                lpcoList.appendChild(lpcoItem);
                if (!lpcoRow.querySelector('.lista-lpcos')) {
                    lpcoRow.querySelector('td').appendChild(lpcoList);
                }
                if (!itemData.listaLpco) {
                    itemData.listaLpco = [];
                }
                if (!itemData.listaLpco.includes(codigoLpco)) {
                    itemData.listaLpco.push(codigoLpco);
                }
            }
        });
        lpcoRow.querySelector('td').appendChild(addButton);
        createRow('Tratamento Tributário:', 'text', tratamentoTributario, 'tratamentoTributario');

        table.appendChild(tbody);
        return table;
    }

    // Função para adicionar listeners aos botões de salvar (MANTIDA e adaptada)
    function addSaveButtonListeners() {
        const saveButtons = document.querySelectorAll('.save-item-btn');
        saveButtons.forEach(button => {
            button.addEventListener('click', (event) => {
                const detailsRow = event.target.closest('.details-row');
                const itemDetailsTable = detailsRow.querySelector('.item-details-table');

                const itemData = {};
                itemDetailsTable.querySelectorAll('input, select').forEach(input => {
                    itemData[input.name] = input.value;
                });

                itemData.listaLpco = [];
                const lpcoContainer = detailsRow.querySelector('.lista-lpcos');
                if (lpcoContainer) {
                    Array.from(lpcoContainer.children).forEach(span => {
                        itemData.listaLpco.push(span.dataset.codigo);
                    });
                }
                console.log("Dados do item a serem salvos:", itemData);
            });
        });
    }


    document.addEventListener('DOMContentLoaded', async () => {
        const processor = new NFeProcessor();

        // Upload de XMLs (MANTIDO)
        const inputXML = document.getElementById('xml-files');
        if (inputXML) {
            inputXML.addEventListener('change', async (e) => {
                try {
                    await processor.processFiles(e.target.files);
                    renderNotasFiscaisTable(); // Chama a função de renderização
                } catch (error) {
                    console.error('Falha no processamento:', error);
                    alert(error.message || 'Erro ao processar arquivos');
                }
            });
        }

        // Função para RENDERIZAR a tabela (MODIFICADA)
        function renderNotasFiscaisTable() {
            const tbody = document.querySelector('#notasFiscaisTable tbody');
            tbody.innerHTML = ''; // Limpa a tabela

            processor.notasFiscais.forEach((nf, nfIndex) => {
                nf.itens.forEach((item, itemIndex) => {
                    // --- Linha principal do item ---
                    const itemRow = document.createElement('tr');
                    itemRow.classList.add('item-row'); // Classe para estilizar

                   const chaveCell = document.createElement('td');
                    chaveCell.textContent = nf.chave;
                    itemRow.appendChild(chaveCell);

                    const itemCell = document.createElement('td');
                    itemCell.textContent = item.item;
                    itemRow.appendChild(itemCell);

                    const descCell = document.createElement('td');
                    descCell.textContent = item.xProd;
                    itemRow.appendChild(descCell);

                    const actionsCell = document.createElement('td');
                    const toggleBtn = document.createElement('button');
                    toggleBtn.type = 'button';
                    toggleBtn.classList.add('btn', 'btn-info', 'btn-sm', 'toggle-details');
                    toggleBtn.innerHTML = '+';
                    toggleBtn.dataset.nfIndex = nfIndex;    // Índice da NF
                    toggleBtn.dataset.itemIndex = itemIndex;  // Índice do item *dentro* da NF
                    actionsCell.appendChild(toggleBtn);
                    itemRow.appendChild(actionsCell);
                    tbody.appendChild(itemRow);

                    // --- Linha de detalhes (oculta por padrão) ---
                    const detailsRow = document.createElement('tr');
                    detailsRow.classList.add('details-row');
                    const detailsCell = document.createElement('td');
                    detailsCell.colSpan = 4; // Ocupa todas as colunas da tabela
                    detailsCell.classList.add('details-content'); // Para espaçamento
                    detailsRow.appendChild(detailsCell);
                    tbody.appendChild(detailsRow); // Adiciona *após* a linha do item
                });
            });
        }

// --- Lógica do botão "+" (MODIFICADA) ---
document.querySelector('#notasFiscaisTable').addEventListener('click', (e) => {
    const btn = e.target.closest('button');
    if (!btn || !btn.classList.contains('toggle-details')) {
        return; // Sai se não for um clique no botão "+"
    }

    const itemRow = btn.closest('.item-row');
    const detailsRow = itemRow.nextElementSibling;

    // 1. Verifica se os detalhes JÁ foram criados
    if (!detailsRow.querySelector('.item-details-table')) {
        // 2. Obtém os índices CORRETOS
        const nfIndex = parseInt(btn.dataset.nfIndex, 10);
        const itemIndex = parseInt(btn.dataset.itemIndex, 10);

        // 3. Acessa os dados CORRETOS do item
        const nfData = processor.notasFiscais[nfIndex];
        if (nfData && nfData.itens && nfData.itens[itemIndex]) {
            const itemData = nfData.itens[itemIndex];

            // 4. Cria os detalhes (e preenche!)
            const detailsContent = createItemDetailsFields(itemData);
            detailsRow.querySelector('.details-content').innerHTML = ''; // Limpa
            detailsRow.querySelector('.details-content').appendChild(detailsContent);

            // 5. Adiciona o botão de salvar
            const saveButton = document.createElement('button');
            saveButton.type = 'button';
            saveButton.classList.add('btn', 'btn-success', 'save-item-btn');
            saveButton.textContent = 'Salvar Item';
            detailsRow.querySelector('.details-content').appendChild(saveButton);
            addSaveButtonListeners();
        }
    }

    // 6. Mostra ou oculta a linha de detalhes (toggle)
      detailsRow.style.display = detailsRow.style.display === 'none' ? 'table-row' : 'none';
});
    });
</script>

