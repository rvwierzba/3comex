<?php

    require_once("C:\\xampp\htdocs\\3comex\\conexao.php");

?>

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
                        <option value="Nenhum" selected>Nenhum</option>    
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
                            <div class="form-group mt-4">
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
                                <select class="form-control" id="cond-vend" name="cond-vend" required style="margin-rigth: 0.8%;">
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

                        <div>
                            <h4>Enquadramentos</h4>
                            <br>
                            <div class="d-flex grid gap-3">
                                <div class="form-group">
                                    <label for="1-campo-de-pesquisa-enquadramento">Primeiro enquadramento</label>
                                    <input type="text" class="form-control" id="1-campo-de-pesquisa-enquadramento"
                                           name="1-enquadramento" autocomplete="off">
                                    <div id="1-resultados-pesquisa-enquadramento"
                                         class="resultados-pesquisa"></div>
                                </div>
                                <div class="form-group">
                                    <label for="2-campo-de-pesquisa-enquadramento">Segundo enquadramento</label>
                                    <input type="text" class="form-control" id="2-campo-de-pesquisa-enquadramento"
                                           name="2-enquadramento" autocomplete="off">
                                    <div id="2-resultados-pesquisa-enquadramento"
                                         class="resultados-pesquisa"></div>
                                </div>
                            </div>
                            <br>
                            <div class="d-flex grid gap-3">
                                <div class="form-group">
                                    <label for="3-campo-de-pesquisa-enquadramento">Terceiro enquadramento</label>
                                    <input type="text" class="form-control" id="3-campo-de-pesquisa-enquadramento"
                                           name="3-enquadramento" autocomplete="off">
                                    <div id="3-resultados-pesquisa-enquadramento"
                                         class="resultados-pesquisa"></div>
                                </div>
                                <div class="form-group">
                                    <label for="4-campo-de-pesquisa-enquadramento">Quarto enquadramento</label>
                                    <input type="text" class="form-control" id="4-campo-de-pesquisa-enquadramento"
                                           name="4-enquadramento" autocomplete="off">
                                    <div id="4-resultados-pesquisa-enquadramento"
                                         class="resultados-pesquisa"></div>
                                </div>
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
        <button type="button" id="gerarDUE" class="btn btn-primary w-100">Gerar e Enviar DU-E</button>
    </div>

    <div id="spinner" class="spinner-border text-primary" role="status" style="display: none;">
        <span class="sr-only">Carregando...</span>
    </div>
</div>



<script type="module" src="./due/js/due-upload.mjs"></script>
<!--<script src="./due/js/script.js"></script>-->
<!--script src="./due/js/due-generate-xml.js -->

