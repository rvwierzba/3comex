<style>

    #tabela-nfe {
        width: 100%;
        border-collapse: collapse;
    }

    #tabela-nfe th,
    #tabela-nfe td {
        padding: 8px;
        text-align: left;
        vertical-align: middle;
        border: 1px solid #dee2e6;
    }

    #tabela-nfe th {
        font-weight: bold;
        background-color: #f8f9fa;
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

    /* Estilos específicos para a coluna de ações */
    #tabela-nfe td:nth-child(10) { /* Mudando para a 10ª coluna (índice 9) */
        width: 150px;
        white-space: nowrap;
        text-align: center;
    }

    /* Estilos para a linha de detalhes (oculta por padrão) */
    #tabela-nfe tr.details-row {
        background-color: #f8f9fa;
    }

    #tabela-nfe tr.details-row td {
        padding: 16px;
        border: none;
    }

    /* Estilos para os inputs dentro da linha de detalhes */
    #tabela-nfe tr.details-row input[type="text"],
    #tabela-nfe tr.details-row select {
        width: 100%;
        padding: 8px;
        margin-bottom: 8px;
        border: 1px solid #ced4da;
        border-radius: 4px;
    }

    /* Estilos para o botão de salvar dentro da linha de detalhes */
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
    margin-right: 5px; /* Espaçamento de 5px à direita de cada botão */
    }

</style>

<!-- HTML -->

<div class="container mt-4">
    <h2 class="mb-4 text-center">Gerar Declaração Única de Exportação (DU-E)</h2>

    <ul class="nav nav-tabs" id="dueTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="tab1" data-bs-toggle="tab" href="#importacaoNFs" role="tab"
               aria-controls="importacaoNFs" aria-selected="true">Importação de NFs</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="tab2" data-bs-toggle="tab" href="#dadosImportacao" role="tab"
               aria-controls="dadosImportacao" aria-selected="false">Dados da Declaração</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="tab3" data-bs-toggle="tab" href="#nfsInseridas" role="tab"
               aria-controls="nfsInseridas" aria-selected="false">NFs Inseridas</a>
        </li>
    </ul>

    <div class="tab-content" id="myTabContent">
       
        <div class="tab-pane fade show active" id="importacaoNFs" role="tabpanel" aria-labelledby="tab1">
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
                        <label for="cnpj-cpf">CNPJ/CPF:</label>
                        <input class="form-control" type="text" id="cnpj-cpf-select" name="cnpj-cpf">
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
                        <label for="campo-de-pesquisa-moeda">Moeda:</label>
                        <select class="form-control" id="moeda" name="moeda" required style="margin-rigth: 0.8%;">
                            <option selected>Selecione...</option>
                                    <?php
                                        foreach($pdo->query('SELECT Codigo, Nome FROM moeda ORDER BY Nome') as $row){
                                            echo '<option value="'.$row['Codigo'].'">'.$row['Nome'].'</option>';
                                        }       
                                    ?>
                                </select>
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
                        <label for="campo-de-pesquisa-unidades-rfb">Unidade da RFB:</label>
                        <select type="text" class="form-control" id="campo-de-pesquisa-unidades-rfb-d"
                               name="unidades_rfb" autocomplete="off">
                                <option selected>Selecione...</option>
                                 <?php
                                         foreach($pdo->query('SELECT id, Nome FROM unidades_rfb ORDER BY Nome') as $row){
                                              echo '<option value="'.$row['id'].'">'.$row['Nome'].'</option>';
                                         }       
                                    ?>
                        </select>
                    </div>

                    <br>

                    <div class="d-flex grid gap-3">
                        <div class="form-group">
                            <label for="em-ra-d">Local de Despacho:</label>
                            <br><INPUT TYPE="RADIO" NAME="em-ra-d" id="sd" VALUE="sim"> Sim
                            <br><INPUT TYPE="RADIO" NAME="em-ra-d" id="sn" VALUE="nao"> Não
                        </div>
                        <div class="form-group">
                            <label for="campo-de-pesquisa-recinto-alfandegado-d">Recinto Aduaneiro:</label>
                            <select type="text" class="form-control" id="campo-de-pesquisa-recinto-alfandegado-d"
                                   name="recinto_alfandegado" autocomplete="off">
                                   <option selected>Selecione...</option>
                                    <?php
                                         foreach($pdo->query('SELECT id, Nome FROM recinto_aduaneiro ORDER BY Nome') as $row){
                                              echo '<option value="'.$row['id'].'">'.$row['Nome'].'</option>';
                                         }       
                                    ?>
                            </select>
                            
                        </div>
                    </div>
                    <br>
                </div>

                <div>
                    <h4 id="lbl-local-embarque">Local de Embarque / Transposição de Fronteira:</h4>

                    <div class="form-group">
                        <label for="campo-de-pesquisa-unidades-rfb">Unidade da RFB:</label>
                            <select type="text" class="form-control" id="campo-de-pesquisa-unidades-rfb-e"
                               name="unidades_rfb" autocomplete="off">
                               <option selected>Selecione...</option>
                                    <?php
                                            foreach($pdo->query('SELECT id, Nome FROM unidades_rfb ORDER BY Nome') as $row){
                                                echo '<option value="'.$row['id'].'">'.$row['Nome'].'</option>';
                                            }       
                                        ?>
                            </select>
                    </div>

                    <br>

                    <div class="d-flex grid gap-3">
                        <div class="form-group">
                            <label for="em-ra-e">Local de Despacho:</label>
                            <br><INPUT TYPE="RADIO" NAME="em-ra-e" id="sd" VALUE="sim"> Sim
                            <br><INPUT TYPE="RADIO" NAME="em-ra-e" id="sn" VALUE="nao"> Não
                        </div>
                        <div class="form-group">
                            <label for="campo-de-pesquisa-recinto-alfandegado-e">Recinto Aduaneiro:</label>
                            <select type="text" class="form-control" id="campo-de-pesquisa-recinto-alfandegado-e"
                                   name="recinto_alfandegado" autocomplete="off">
                                   <option selected>Selecione...</option>
                                   <?php
                                            foreach($pdo->query('SELECT id, Nome FROM recinto_aduaneiro ORDER BY Nome') as $row){
                                                echo '<option value="'.$row['id'].'">'.$row['Nome'].'</option>';
                                            }       
                                        ?>
                            </select>
                        </div>
                    </div>
                </div>

                <br>

                <div id=complementos>
                    <h4 id="lbl-complementos">Complementos</h4>
                    <div class="form-group">
                        <label>Via especial de transporte</label>
                        <select type="text" class="form-control" id="via-especial-transport"
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

        <!-- ABA Dados da Declaração -->
        <div class="tab-pane fade" id="dadosImportacao" role="tabpanel" aria-labelledby="tab2">
            <form id="declaracaoForm">
                <div class="card mb-4">
                    <div class="card-header">Informações da Declaração</div>
                    <div class="card-body">

                        <div class="d-flex grid gap-3">
                            <div class="form-group">
                                <label for="und-estatis">Unidade estatística</label>
                                <input type="text" id="und-estatis" name="und-estatis" class="form-control"
                                       placeholder="KG">
                            </div>
                            <div class="form-group">
                                <label for="qdt-estatis">Quantidade estatística</label>
                                <input type="number" id="qdt-estatis" name="qdt-estatis" class="form-control"
                                       placeholder="500.00000">
                            </div>
                            <div class="form-group">
                                <label for="pes-liq-ttl">Peso líquido total (KG)</label>
                                <input required type="number" id="pes-liq-ttl" name="pes-liq-ttl" class="form-control"
                                       placeholder="KG">
                            </div>
                        </div>

                        <br>

                        <div class="d-flex grid gap-3">
                            <div class="form-group">
                                <label for="und-comerc">Unidade comercializada</label>
                                <input type="text" id="und-comerc" name="und-comerc" class="form-control"
                                       placeholder="KG">
                            </div>
                            <div class="form-group">
                                <label for="qdt-comerc">Quantidade comercializada</label>
                                <input type="number" id="qdt-comerc" name="qdt-comerc" class="form-control"
                                       placeholder="500.00000">
                            </div>
                            <div class="form-group">
                                <label for="val-merc">Valor (R$)</label>
                                <input type="number" id="val" name="val" class="form-control"
                                       placeholder="9.828,41">
                            </div>
                            <div class="form-group">
                                <label for="comiss-agnt">Comissão do agente (%)</label>
                                <input type="number" id="comiss-agnt" name="comiss-agnt" class="form-control">
                            </div>
                        </div>

                        <br>
                       
                            <div class="form-group">
                                <label for="cond-vend">Condição da venda:</label>
                                <select class="form-control" id="cond-vend" name="cond-vend" required>
                                    <option selected>Selecione...</option>
                                    <?php
                                        foreach($pdo->query('SELECT Codigo, Descricao FROM incoterms ORDER BY Codigo') as $row){
                                            echo '<option value="'.$row['Codigo'].'">'.$row['Descricao'].'</option>';
                                        }       
                                    ?>
                                </select>
                            </div>

                        <br>

                        <div class="d-flex grid gap-3">
                            <div class="form-group">
                                <label for="vmcv">VMCV (USD)</label>
                                <input type="number" id="vmcv" name="vmcv" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="vmle">VMLE (USD)</label>
                                <input type="number" id="vmle" name="vmle" class="form-control">
                            </div>
                            <div class="form-group">
                                <p>VMCV: Valor da mercadoria na condição de venda</p>
                                <p>VMLE: Valor da mercadoria no local de embarque</p>
                            </div>
                        </div>

                        <br>

                        <div class="d-flex grid gap-3">
                            <div class="form-group">
                                <label for="xNome">Nome do importador:</label>
                                <p id="xNome" name="xNome"></p>
                            </div>
                            <div class="form-group">
                                <label for="xLgr">Endereço do importador:</label>
                                <p id="xLgr" name="xLgr"></p>
                            </div>
                            <div class="form-group">
                                <label for="xPais">País do importador:</label>
                                <p id="xPaisImp" name="xPaisImp"></p>
                            </div>
                        </div>

                        <br>

                        <div class="form-group">
                            <label for="xPais">País de destino</label>
                            <input type="text" id="xPais" name="xPais" class="form-control" required>
                        </div>

                        <br>

                        <div class="form-group">
                            <label for="condVenda">Condição de venda:</label>
                            <select class="form-control">
                                <option selected>Selecione...</option>
                                
                            </select>
                        </div>

                        <br>

                        <div class="form-group">
                            <label for="tart-priori">Tratamento prioritário</label>
                            <select name="tart-priori" id="tart-priori" class="form-control">
                                <option selected>Selecione...</option>
                                <option>CARGA VIVA</option>
                                <option>CARGA PERECÍVEL</option>
                                <option>CARGA PERIGOSA</option>
                                <option>PARTES/PEÇAS DE AERONAVES</option>
                            </select>
                        </div>

                        <br>

                        <div>
                            <h4>Enquadramentos</h4>
                            <br>
                            <div class="d-flex grid gap-3">
                                <div class="form-group">
                                    <label for="1-campo-de-pesquisa-enquadramento">Primeiro enquadramento</label>
                                    <select class="form-control" id="1-campo-de-pesquisa-enquadramento"
                                           name="1-enquadramento" autocomplete="off">
                                        <option selected>Selecione...</option>
                                        <?php
                                            foreach($pdo->query('SELECT Codigo, Descricao FROM enquadramento ORDER BY Codigo') as $row){
                                                echo '<option value="'.$row['Codigo'].'">'.$row['Descricao'].'</option>';
                                            }       
                                        ?>   
                                    </select>
                                  </div>
                                <div class="form-group">
                                    <label for="2-campo-de-pesquisa-enquadramento">Segundo enquadramento</label>
                                    <select class="form-control" id="2-campo-de-pesquisa-enquadramento"
                                           name="2-enquadramento" autocomplete="off">
                                           <option selected>Selecione...</option>
                                        <?php
                                            foreach($pdo->query('SELECT Codigo, Descricao FROM enquadramento ORDER BY Codigo') as $row){
                                                echo '<option value="'.$row['Codigo'].'">'.$row['Descricao'].'</option>';
                                            }       
                                        ?>      
                                    </select>
                                </div>
                            </div>
                            <br>
                            <div class="d-flex grid gap-3">
                                <div class="form-group">
                                    <label for="3-campo-de-pesquisa-enquadramento">Terceiro enquadramento</label>
                                    <select class="form-control" id="3-campo-de-pesquisa-enquadramento"
                                           name="3-enquadramento" autocomplete="off">
                                        <option selected>Selecione...</option>
                                        <?php
                                            foreach($pdo->query('SELECT Codigo, Descricao FROM enquadramento ORDER BY Codigo') as $row){
                                                echo '<option value="'.$row['Codigo'].'">'.$row['Descricao'].'</option>';
                                            }       
                                        ?>       
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="4-campo-de-pesquisa-enquadramento">Quarto enquadramento</label>
                                    <select class="form-control" id="4-campo-de-pesquisa-enquadramento"
                                           name="4-enquadramento" autocomplete="off">
                                        <option selected>Selecione...</option>
                                        <?php
                                            foreach($pdo->query('SELECT Codigo, Descricao FROM enquadramento ORDER BY Codigo') as $row){
                                                echo '<option value="'.$row['Codigo'].'">'.$row['Descricao'].'</option>';
                                            }       
                                        ?>      
                                    </select>
                                 
                                </div>
                            </div>
                        </div>

                        <br>

                        <div>
                            <h4>Lista de LPCO</h4>
                            <br>
                            <div class="form-group">
                                <label for="add-lpcos">Número do LPCO: </label>
                                <select id="add-lpcos" name="add-lpcos" class="form-control">
                                    <option selected>Selecione...</option>
                                    <?php
                                       foreach($pdo->query('SELECT Codigo, Descricao FROM lpco ORDER BY Codigo') as $row){
                                        echo '<option value="'.$row['Descricao'].'">'.$row['Codigo'].'</option>';
                                    }        
                                    ?>
                                </select>    
                            </div>
                             
                            <div class="form-group">
                                <label for="lista-lpcos">LPCOs Adicionados:</label>
                                <div id="lista-lpcos" class="border p-2" style="min-height: 50px;">
                                    </div>
                                <input type="hidden" id="lpcos-hidden" name="lpcos" value="">
                            </div>

                        </div>

                        <input type="hidden" id="id-due" name="id_due">

                    </div>
                </div>
            </form>
        </div>

        <div class="tab-pane fade" id="nfsInseridas" role="tabpanel" aria-labelledby="tab3">
    <form>
        <div class="card mb-4">
            <div class="card-header">Lista de Notas Fiscais</div>
            <div class="card-body">
                <div id="tabelaContainer" class="table-responsive">
                    <table class="table table-bordered" id="notasFiscaisTable">
                        <thead>
                            <tr>
                                <th>Chave de Acesso</th>
                                <th>Nome Importador</th>
                                <th>País</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>

                        <div id="detalhes-NFe-{{chaveAcesso}}">
                            <table class="table table-bordered" hidden>
                                <tr>
                                <td><strong>Número da NF-e:</strong></td>
                                <td id="nNF-{{chaveAcesso}}"></td>
                                </tr>
                                <tr>
                                <td><strong>Série:</strong></td>
                                <td id="serie-{{chaveAcesso}}"></td>
                                </tr>
                                <tr>
                                <td><strong>Data de Emissão:</strong></td>
                                <td id="dhEmi-{{chaveAcesso}}"></td>
                                </tr>
                                <tr>
                                <td><strong>Modalidade do Frete:</strong></td>
                                <td id="modFrete-{{chaveAcesso}}"></td>
                                </tr>
                                <tr>
                                <td><strong>Exportador:</strong></td>
                                <td id="xNome-{{chaveAcesso}}"></td>
                                </tr>
                                <tr>
                                <td><strong>CNPJ do Exportador:</strong></td>
                                <td id="cnpjEmitente-{{chaveAcesso}}"></td>
                                </tr>
                                <tr>
                                <td><strong>Nome do Importador:</strong></td>
                                <td id="xNomeImportador-{{chaveAcesso}}"></td>
                                </tr>
                                <tr>
                                <td><strong>Endereço do Exportador:</strong></td>
                                <td id="xLgr-{{chaveAcesso}}"></td>
                                </tr>
                                <tr>
                                <td><strong>Endereço do Importador:</strong></td>
                                <td id="xLgrImportador-{{chaveAcesso}}"></td>
                                </tr>
                                <tr>
                                <td><strong>País do Importador:</strong></td>
                                <td id="xPaisImportador-{{chaveAcesso}}"></td>
                                </tr>
                                <tr>
                                <td colspan="2"><strong>Detalhes dos Itens da DU-E</strong></td>
                                </tr>
                                <tbody id="itens-{{chaveAcesso}}">
                                </tbody>
                            </table>
                        </div>            

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

<script type="module" src="./due/js/due-upload.mjs"></script>
<script src="./due/js/add-lpcos.js"></script>
<script src="./due/js/due-generate-xml.js"></script>

<script>

document.addEventListener('DOMContentLoaded', function() {

const nfeProcessor = new NFeProcessor(); // Instancia a classe (já está no seu código)
const fileInput = document.getElementById('xml-files');
const tabelaNFs = document.querySelector("#notasFiscaisTable tbody");
const detalhesContainer = document.getElementById("detalhesNFeContainer");
const detalhesRow = document.querySelector(".details-row"); // Linha completa de detalhes


fileInput.addEventListener('change', async (event) => {
    const files = event.target.files;
    if (files.length > 0) {
        await nfeProcessor.processFiles(files);  // Processa os arquivos
        updateTable(); // Atualiza a tabela *após* o processamento
    }
});


 function updateTable() {
    tabelaNFs.innerHTML = ''; // Limpa o conteúdo atual da tabela
    nfeProcessor.notasFiscais.forEach(nf => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${nf.chaveAcesso}</td>
            <td>${nf.xNomeImportador}</td>
            <td>${nf.xPaisImportador}</td>
            <td class="meus-botoes">
                <button class="btn btn-dark btn-sm toggle-details" data-chave="${nf.chaveAcesso}" type="button">+</button>
                <button class="btn btn-danger btn-sm remove-nf" data-chave="${nf.chaveAcesso}" type="button">Remover</button>
            </td>
        `;
        tabelaNFs.appendChild(row);
    });

     // Adiciona os event listeners *após* a criação das linhas
     addEventListeners();
     nfeProcessor.preencherCampos();
}


function addEventListeners() {
    // Botão de mostrar/ocultar detalhes
    document.querySelectorAll(".toggle-details").forEach(button => {
        button.addEventListener("click", function() {
            const chave = this.dataset.chave;
            const nf = nfeProcessor.notasFiscais.find(n => n.chaveAcesso === chave);

            if (nf) {
                if (this.textContent === "+") {
                    // Mostrar detalhes
                    mostrarDetalhes(nf);
                    this.textContent = "-"; // Muda o botão para "-"
                } else {
                  // Ocultar detalhes
                    detalhesContainer.innerHTML = ''; // Limpa os detalhes
                    detalhesRow.style.display = "none"; // Oculta a linha
                    this.textContent = "+";  // Muda o botão para "+"

                }
            }
        });
    });

    // Botão de remover
    document.querySelectorAll(".remove-nf").forEach(button => {
       button.addEventListener("click", function() {
            const chave = this.dataset.chave;
            nfeProcessor.removeNotaFiscal(chave);
            updateTable(); // Atualiza a tabela *após* remover
            // Oculta os detalhes, se a NF-e removida for a que está sendo exibida
            if (detalhesRow.style.display !== "none" && detalhesContainer.dataset.chave === chave) {
                detalhesContainer.innerHTML = "";
                detalhesRow.style.display = "none";
            }
        });
    });
}


function mostrarDetalhes(nf) {
    let htmlDetalhes = `
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Detalhes da NF-e: ${nf.chaveAcesso}</h5>

            <div class="row">
                <div class="col-md-6">
                    <p><strong>Número:</strong> ${nf.nNF}</p>
                    <p><strong>Série:</strong> ${nf.serie}</p>
                    <p><strong>Data de Emissão:</strong> ${nf.dhEmi}</p>
                    <p><strong>Exportador:</strong> ${nf.xNome}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Importador:</strong> ${nf.xNomeImportador}</p>
                     <p><strong>País do Importador:</strong> ${nf.xPaisImportador}</p>
                    <p><strong>Modalidade do Frete:</strong> ${nf.modFrete}</p>

                </div>
            </div>
            <hr>
            <h6>Itens da NF-e</h6>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Cód. Prod.</th>
                            <th>Descrição</th>
                            <th>NCM</th>
                            <th>CFOP</th>
                            <th>Qtd.</th>
                            <th>Valor Unit.</th>
                             <th>Valor Total</th>
                            <th>Imagem</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${nf.itens.map(item => `
                            <tr>
                                <td>${item.nItem}</td>
                                <td>${item.cProd}</td>
                                <td>${item.xProd}</td>
                                <td>${item.NCM}</td>
                                <td>${item.CFOP}</td>
                                <td>${item.qCom} ${item.uCom}</td>
                                <td>${item.vUnCom}</td>
                                 <td>${item.vProd}</td>
                                <td>
                                    ${nf.imagens.find(img => img.cProd === item.cProd) ? `<img src="${nf.imagens.find(img => img.cProd === item.cProd).path}" alt="Imagem do Produto ${item.cProd}" style="max-width: 100px; max-height: 100px;">` : 'Sem Imagem'}
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    `;

    detalhesContainer.innerHTML = htmlDetalhes; // Insere o HTML no container
    detalhesContainer.dataset.chave = nf.chaveAcesso; // *IMPORTANTE* Guarda a chave da NF-e atual
    detalhesRow.style.display = ""; // Mostra a linha de detalhes (remove o display: none)
}

// Botão SALVAR (fora do loop, evento único)
document.getElementById('gerarDUE').addEventListener('click', function() {
//Coletar dados do formulario
   const formData = {
      action: 'salvarDUE', // Ação para o PHP saber o que fazer
      id_due: document.getElementById('id-due').value,
      forma_export: document.querySelector('input[name="forma-export"]:checked')?.value || '', // Radio buttons
      export_cons: document.getElementById('export-cons').checked ? 1 : 0,  // Checkbox
      situacao_espec_despacho: document.getElementById('situacao-espec-despacho').value,
      tp_doc_amp_merc_export: document.querySelector('input[name="tp-doc-amp-merc-export"]:checked')?.value || '',
      moeda: document.getElementById('moeda').value,
      ruc: document.getElementById('ruc').value,
      campo_de_pesquisa_unidades_rfb_d: document.getElementById('campo-de-pesquisa-unidades-rfb-d').value,
      em_ra_d: document.querySelector('input[name="em-ra-d"]:checked')?.value || '',
      campo_de_pesquisa_recinto_alfandegado_d: document.getElementById('campo-de-pesquisa-recinto-alfandegado-d').value,
      campo_de_pesquisa_unidades_rfb_e: document.getElementById('campo-de-pesquisa-unidades-rfb-e').value,
      em_ra_e: document.querySelector('input[name="em-ra-e"]:checked')?.value || '',
      campo_de_pesquisa_recinto_alfandegado_e: document.getElementById('campo-de-pesquisa-recinto-alfandegado-e').value,
      via_especial_transport: document.getElementById('via-especial-transport').value,
      info_compl: document.getElementById('info-compl').value,
      und_estatis: document.getElementById('und-estatis').value,
      qdt_estatis: document.getElementById('qdt-estatis').value,
      pes_liq_ttl: document.getElementById('pes-liq-ttl').value,
      und_comerc: document.getElementById('und-comerc').value,
      qdt_comerc: document.getElementById('qdt-comerc').value,
      val: document.getElementById('val').value,
      comiss_agnt: document.getElementById('comiss-agnt').value,
      cond_vend: document.getElementById('cond-vend').value,
      vmcv: document.getElementById('vmcv').value,
      vmle: document.getElementById('vmle').value,
      xPais: document.getElementById('xPais').value,
      '1_enquadramento': document.getElementById('1-campo-de-pesquisa-enquadramento').value,
      '2_enquadramento': document.getElementById('2-campo-de-pesquisa-enquadramento').value,
      '3_enquadramento': document.getElementById('3-campo-de-pesquisa-enquadramento').value,
      '4_enquadramento': document.getElementById('4-campo-de-pesquisa-enquadramento').value,
      notasFiscais: JSON.stringify(nfeProcessor.notasFiscais) // Envia as NFs processadas

  };


    // Mostra o spinner e desabilita o botão
    const spinner = document.getElementById('spinner');
    const gerarDUEButton = document.getElementById('gerarDUE');
    spinner.style.display = 'block';
    gerarDUEButton.disabled = true;


    // Envia a requisição AJAX
    fetch('due.php', {  // Mesmo arquivo
        method: 'POST',
         headers: {
            'Content-Type': 'application/x-www-form-urlencoded', //  Importante para enviar dados de formulário
        },
        body: new URLSearchParams(formData) // Codifica os dados do formulário
    })
    .then(response => response.json())
    .then(data => {
        // Processa a resposta do servidor
        if (data.error) {
            alert('Erro ao salvar DUE: ' + data.error);
        } else {
            alert('DUE salva com sucesso!');
            // Limpar os campos do formulário ou redirecionar, se necessário
        }
    })
    .catch(error => {
        console.error('Erro na requisição AJAX:', error);
        alert('Erro ao salvar DUE. Verifique o console para detalhes.');
    })
    .finally(() => {
        spinner.style.display = 'none'; // Oculta o spinner
        gerarDUEButton.disabled = false; // Habilita o botão
    });
});
});                                    

</script>