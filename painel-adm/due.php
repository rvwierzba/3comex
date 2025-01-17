<?php
// Incluir a conexão com o banco de dados
require_once(dirname(__DIR__) . '/conexao.php'); 
?>
<div class="container mt-4">
    <h2 class="mb-4 text-center">Gerar Declaração Única de Exportação (DU-E)</h2>

    <!-- Abas para as seções -->
    <ul class="nav nav-tabs" id="dueTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="tab1" data-bs-toggle="tab" href="#importacaoNFs" role="tab" aria-controls="importacaoNFs" aria-selected="true">Importação de NFs</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="tab2" data-bs-toggle="tab" href="#dadosImportacao" role="tab" aria-controls="dadosImportacao" aria-selected="false">Dados da Declaração</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="tab3" data-bs-toggle="tab" href="#nfsInseridas" role="tab" aria-controls="nfsInseridas" aria-selected="false">NFs Inseridas</a>
        </li>
    </ul>

    <!-- Conteúdo das Abas -->
    <div class="tab-content" id="myTabContent">
        <!-- Aba 1: Importação de NFs -->
        <div class="tab-pane fade show active" id="importacaoNFs" role="tabpanel" aria-labelledby="tab1">
            <form id="dueForm" enctype="multipart/form-data">
                <div class="card mb-4">
                    <div class="card-header">Upload de Arquivos XML das Notas Fiscais</div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="xml-files">Selecionar Arquivos XML</label>
                            <input type="file" id="xml-files" class="form-control" accept=".xml" multiple>
                        </div>
                        <div id="uploadStatus" class="mt-2"></div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Aba 2: Dados de Importação -->
        <div class="tab-pane fade" id="dadosImportacao" role="tabpanel" aria-labelledby="tab2">
            <form>
                <div class="card mb-4">
                    <div class="card-header">Dados do Declarante</div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="nomeCliente">Nome do Cliente</label>
                            <input type="text" id="nomeCliente" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label for="declarant-id">ID do Declarante</label>
                            <input type="text" id="declarant-id" class="form-control" placeholder="Digite o ID do Declarante">
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">Informações da Declaração</div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="und-rfb-desp">Unidade RFB - Despacho</label>
                            <input type="text" id="und-rfb-desp" class="form-control" placeholder="Pesquise por Unidade da RFB">
                        </div>
                        <div class="form-group">
                            <label for="rec-adu">Recinto Aduaneiro</label>
                            <input type="text" id="rec-adu" class="form-control" placeholder="Pesquise por Recinto Aduaneiro">
                        </div>
                        <div class="form-group">
                            <label for="categoria-doc">Categoria do Documento</label>
                            <input type="text" id="categoria-doc" class="form-control" value="AC" readonly>
                        </div>
                        <div class="form-group">
                            <label for="hs-classification">Classificação HS</label>
                            <input type="text" id="hs-classification" class="form-control" placeholder="Digite a Classificação HS">
                        </div>
                        <div class="form-group">
                            <label for="drawback-recipient-id">ID do Recipiente do Drawback</label>
                            <input type="text" id="drawback-recipient-id" class="form-control" placeholder="Digite o ID do Recipiente">
                        </div>
                        <div class="form-group">
                            <label for="itemID">ID do Item</label>
                            <input type="text" id="itemID" class="form-control" placeholder="Digite o ID do Item">
                        </div>
                        <div class="form-group">
                            <label for="quantityQuantity">Quantidade</label>
                            <input type="number" id="quantityQuantity" class="form-control" placeholder="Digite a Quantidade">
                        </div>
                        <div class="form-group">
                            <label for="unitCode">Código da Unidade</label>
                            <input type="text" id="unitCode" class="form-control" placeholder="Digite o Código da Unidade">
                        </div>
                        <div class="form-group">
                            <label for="valueWithExchangeCoverAmount">Valor com Cobertura de Câmbio</label>
                            <input type="number" id="valueWithExchangeCoverAmount" class="form-control" placeholder="Digite o Valor com Cobertura de Câmbio">
                        </div>
                        <div class="form-group">
                            <label for="currentCode">Código Atual</label>
                            <input type="text" id="currentCode" class="form-control" placeholder="Digite o Código Atual">
                        </div>
                        <div class="form-group">
                            <label for="sequenceNumeric">Número Sequencial</label>
                            <input type="number" id="sequenceNumeric" class="form-control" value="0">
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Aba 3: NFs Inseridas -->
        <div class="tab-pane fade" id="nfsInseridas" role="tabpanel" aria-labelledby="tab3">
            <div class="card mb-4">
                <div class="card-header">Lista de Notas Fiscais</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="notasFiscaisTable">
                            <thead>
                                <tr>
                                    <th>Chave de Acesso</th>
                                    <th>Nome Importador</th>
                                    <th>País</th>
                                    <th>Nota Fiscal</th>
                                    <th>Nr. Processo</th>
                                    <th>Nr. Adição</th>
                                    <th>Incoterm</th>
                                    <th>Destino Final</th>
                                    <th>Comissão Agente (%)</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody id="nfsTableBody">
                                <!-- As NFs serão adicionadas via JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Botão para Gerar e Enviar DU-E -->
    <div class="form-group">
        <button type="button" id="gerarDUE" class="btn btn-primary w-100">Gerar e Enviar DU-E</button>
    </div>

    <!-- Spinner de carregamento -->
    <div id="spinner" class="spinner-border text-primary" role="status" style="display: none;">
        <span class="sr-only">Carregando...</span>
    </div>
</div>

<!-- Scripts Necessários -->
<script src="./due/js/main.js"></script>
<script src="./due/js/due-dynamic-search.js"></script>
<script src="./due/js/due-upload.js"></script>
