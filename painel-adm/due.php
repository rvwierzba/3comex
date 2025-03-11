<?php
// Conexão com o banco (coloque *antes* de qualquer HTML, mas dentro do <html> se já existir)
include_once '../conexao.php';  // Ajuste o caminho se necessário!
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

     /* Adicione isso */
     .nav-tabs .nav-link.active {
            border-bottom: 3px solid #0d6efd !important;
            background: #fff;
        }
        .tab-content {
            border: 1px solid #dee2e6;
            border-radius: 0 0 0.5rem 0.5rem;
            padding: 20px;
        }

</style>

<script>
// Script de inicialização das abas (sem type="module")
document.addEventListener('DOMContentLoaded', function () {
    console.log("Script de inicialização executado!");
    var tabList = [].slice.call(document.querySelectorAll('#dueTabs [data-bs-toggle="tab"]'))
    tabList.forEach(function (tabEl) {
        tabEl.addEventListener('click', function (event) {
            event.preventDefault();
            var tab = new bootstrap.Tab(tabEl);
            tab.show();
        });
    });
});
</script>

<!-- HTML -->

<div class="container mt-4">
    <h2 class="mb-4 text-center">Gerar Declaração Única de Exportação (DU-E)</h2>

         
    <ul class="nav nav-tabs" id="dueTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" id="tab1-tab" data-bs-toggle="tab" href="#tab1-tab" 
            role="tab" aria-controls="importacaoNFs" aria-selected="true">
                <i class="fas fa-file-import me-2"></i>
                Importação de NFs
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="tab2-tab" data-bs-toggle="tab" href="#tab2-tab" 
            role="tab" aria-controls="dadosImportacao" aria-selected="false">
                <i class="fas fa-database me-2"></i>
                Dados da Declaração
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="tab3-tab" data-bs-toggle="tab" href="#tab3-tab" 
            role="tab" aria-controls="nfsInseridas" aria-selected="false">
                <i class="fas fa-receipt me-2"></i>
                NFs Inseridas
            </a>
        </li>
    </ul>

    

    <div class="tab-content" id="dueTabsContent">
       
    <div class="tab-pane fade show active" id="importacaoNFs" role="tabpanel" aria-labelledby="tab1-tab">
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

        <!-- ABA Dados da Importaçao -->
        <div class="tab-pane fade" id="dadosImportacao" role="tabpanel" aria-labelledby="tab2-tab">
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
                                <input id="cond-vend" type="text" class="form-control" id="cond-vend" name="cond-vend">
                                <datalist list="cond-vend">
                                    <?php
                                        foreach($pdo->query('SELECT sigla, Descricao FROM incoterms ORDER BY sigla') as $row){
                                            echo '<option value="' . $row['sigla'] . '-' . $row['Descricao'] . '"></option>';
                                        }       
                                        ?>
                                </datalist>
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
                                    <input id="1-campo-de-pesquisa-enquadramento" type="text" class="form-control" id="1-campo-de-pesquisa-enquadramento" name="1-campo-de-pesquisa-enquadramento">
                                    <datalist list="1-campo-de-pesquisa-enquadramento">
                                        <?php
                                            foreach($pdo->query('SELECT Codigo, Descricao FROM enquadramento ORDER BY Codigo') as $row){
                                                            echo '<option value="' . $row['Codigo'] . '-' . $row['Descricao'] . '"></option>';
                                            }         
                                        ?>
                                    </datalist>
                                 </div>
                                <div class="form-group">
                                    <label for="2-campo-de-pesquisa-enquadramento">Primeiro enquadramento</label>
                                    <input id="2-campo-de-pesquisa-enquadramento" type="text" class="form-control" id="2-campo-de-pesquisa-enquadramento" name="2-campo-de-pesquisa-enquadramento">
                                    <datalist list="2-campo-de-pesquisa-enquadramento">
                                        <?php
                                            foreach($pdo->query('SELECT Codigo, Descricao FROM enquadramento ORDER BY Codigo') as $row){
                                                echo '<option value="' . $row['Codigo'] . '-' . $row['Descricao'] . '"></option>';
                                            }         
                                        ?>
                                    </datalist>
                                </div>
                            </div>
                            <br>
                            <div class="form-group">
                                    <label for="3-campo-de-pesquisa-enquadramento">Primeiro enquadramento</label>
                                    <input id="3-campo-de-pesquisa-enquadramento" type="text" class="form-control" id="3-campo-de-pesquisa-enquadramento" name="3-campo-de-pesquisa-enquadramento">
                                    <datalist list="3-campo-de-pesquisa-enquadramento">
                                        <?php
                                            foreach($pdo->query('SELECT Codigo, Descricao FROM enquadramento ORDER BY Codigo') as $row){
                                             echo '<option value="' . $row['Codigo'] . '-' . $row['Descricao'] . '"></option>';
                                            }         
                                        ?>
                                    </datalist>
                                   </div>
                                <div class="form-group">
                                    <label for="4-campo-de-pesquisa-enquadramento">Primeiro enquadramento</label>
                                    <input idt="4-campo-de-pesquisa-enquadramento" type="text" class="form-control" id="4-campo-de-pesquisa-enquadramento" name="4-campo-de-pesquisa-enquadramento">
                                    <datalist list="4-campo-de-pesquisa-enquadramento">
                                        <?php
                                            foreach($pdo->query('SELECT Codigo, Descricao FROM enquadramento ORDER BY Codigo') as $row){
                                                echo '<option value="' . $row['Codigo'] . '-' . $row['Descricao'] . '"></option>';
                                            }         
                                        ?>
                                    </datalist>
                                </div>
                            </div>
                        </div>

                        <br>

                        <div>
                            <h4>Lista de LPCO</h4>
                            <br>
                            <div class="form-group">
                                <label for="add-lpcos">Número do LPCO: </label>
                                <input id="add-lpcos" type="text" name="add-lpcos" id="add-lpcos">
                                <datalist list="add-lpcos">
                                    <?php
                                       foreach($pdo->query('SELECT Codigo, Descricao FROM lpco ORDER BY Codigo') as $row){
                                            echo '<option value="' . $row['Codigo'] . '-' . $row['Nome'] . '"></option>';
                                    }        
                                    ?>
                                </datalist>    
                            </div>
                             
                            <div class="form-group">
                                <label for="lista-lpcos">LPCOs Adicionados:</label>
                                <div id="lista-lpcos" class="border p-2" style="min-height: 50px;">
                                    </div>
                                <input type="hidden" id="lpcos-hidden" name="lpcos" value="">
                            </div>

                            <br>

                            <div class="form-group">
                                <h4>Tratamento Tributário</h4>
                                <p>Este item não possui tratamento tributário<p>
                            </div>

                            <br>

                            <div class="form-group">
                                <h4>Notas Fiscais Referenciadas Eletrônicas</h4>
                                <button type="button" class="btn btn-outline-primary" id="btn-add-nfe-ref-eletro">Adicionar Nota Fiscal Referenciada Eletrônica</button>
                            </div>

                            <br>

                            <div class="form-group">
                                <h4>Notas Fiscais Referenciadas Formulário</h4>
                                <button type="button" class="btn btn-outline-primary" id="btn-add-nfe-ref-form">Adicionar Nota Fiscal Referenciada Formulário</button>
                            </div>

                            <br>

                            <div class="form-group">
                                <h4>Notas Fiscais Complementares</h4>
                                <button type="button" class="btn btn-outline-primary" id="btn-add-nf-compl">Adicionar Nota Fiscal Complementar</button>
                            </div>

                        </div>

                        <input type="hidden" id="id-due" name="id_due">

                    </div>
                </div>
            </form>
        </div>

    <div class="tab-pane fade" id="nfsInseridas" role="tabpanel" aria-labelledby="tab3-tab">
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

<script>
    // Script de inicialização das abas
    document.addEventListener('DOMContentLoaded', function () {
    console.log("Script de inicialização executado!");
    var tabList = [].slice.call(document.querySelectorAll('#dueTabs [data-bs-toggle="tab"]'))
    tabList.forEach(function (tabEl) {
        tabEl.addEventListener('click', function (event) {
            event.preventDefault();
            var tab = new bootstrap.Tab(tabEl);
            tab.show();
            });
        });
    });
</script>

<script>

document.addEventListener('DOMContentLoaded', () => {
    const inputs = document.querySelectorAll('input[list]'); // Seleciona inputs com o atributo 'list'

    inputs.forEach(input => {
        const datalistId = input.getAttribute('list');
        const datalist = document.getElementById(datalistId);

        if (!datalist) {
            console.error(`Datalist com ID "${datalistId}" não encontrado para o input "${input.id}".`);
            return; // Sai do loop para este input
        }

        async function fetchDataOptions(inputValue) {
            const encodedSearchTerm = encodeURIComponent(inputValue);
            const url = `due/get_options.php?datalist=${datalistId}&search=${encodedSearchTerm}`;

            try {
                const response = await fetch(url);

                if (!response.ok) {
                    console.error('Erro na requisição:', response.status, response.statusText);
                    return;
                }

                const data = await response.json();

                if (data.error) {
                    console.error('Erro do servidor:', data.error);
                    return;
                }

                // Limpa as opções existentes
                while (datalist.firstChild) {
                    datalist.removeChild(datalist.firstChild);
                }

                // Adiciona as novas opções
                data.forEach(option => {
                    const optionElement = document.createElement('option');
                    optionElement.value = option.text; // Usa option.text, que contém o código e o nome
                    datalist.appendChild(optionElement);
                });

            } catch (error) {
                console.error('Erro na função fetchDataOptions:', error);
            }
        }

        input.addEventListener('input', () => {
            const inputValue = input.value.trim();
            if (inputValue.length > 0) {
                fetchDataOptions(inputValue);
            } else {
                // Limpa as opções quando o input estiver vazio
                while (datalist.firstChild) {
                    datalist.removeChild(datalist.firstChild);
                }
            }
        });
    });
});

</script>

