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

.modal-xl {
    max-width: 1200px;
}
.card {
    margin-bottom: 1rem;
    border: 1px solid #dee2e6;
}
.card-header {
    background-color: #f8f9fa;
    font-weight: bold;
}
.input-group {
    margin-bottom: 0.5rem;
}
.badge {
    margin-right: 0.5rem;
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
                                    foreach($pdo->query('SELECT Codigo, Nome, Simbolo FROM moeda ORDER BY Nome') as $row){
                                        echo '<option value="'. $row['Codigo'] .'-'. $row['Nome'] .'" data-simbolo="'. $row['Simbolo'] .'">'. $row['Nome'] .'</option>';
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

<div class="modal fade" id="itemDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Edição Completa do Item de DU-E</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="itemForm">
                    <!-- Seção 1: Dados Básicos -->
                    <div class="card mb-4">
                        <div class="card-header">Detalhes do Item</div>
                        <div class="card-body row g-3">
                            <div class="col-md-4">
                                <label>Código do Item</label>
                                <input type="text" id="itemCodigo" class="form-control">
                            </div>
                            <div class="col-md-8">
                                <label>Descrição Completa</label>
                                <textarea id="itemDescricao" class="form-control" rows="2"></textarea>
                            </div>
                            
                            <div class="col-md-3">
                                <label>NCM</label>
                                <input type="text" id="itemNCM" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label>CFOP</label>
                                <input type="text" id="itemCFOP" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label>CCPT/CCROM</label>
                                <select id="ccpt_ccrom" class="form-select">
                                    <option value="">Selecione...</option>
                                    <option value="CCPT-01">CCPT 01 - Operação Comercial</option>
                                    <option value="CCPT-02">CCPT 02 - Amostra Grátis</option>
                                    <option value="CCROM-A">CCROM A - Material de Propaganda</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>Quantidade Estatística</label>
                                <input type="text" id="quantidadeEstatistica" class="form-control">
                            </div>
                        </div>
                    </div>

                    <!-- Seção 2: Dados Específicos das Imagens -->
                    <div class="card mb-4">
                        <div class="card-header">Dados Avaliários</div>
                        <div class="card-body row g-3">
                            <div class="col-md-3">
                                <label>Comissão Avaliária (%)</label>
                                <input type="number" id="comissaoAvaliaria" class="form-control" step="0.01">
                            </div>
                            <div class="col-md-3">
                                <label>LFCO</label>
                                <input type="text" id="lfco" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label>Usador Avaliário</label>
                                <input type="text" id="usuarioAvaliario" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label>Descrição Complementar</label>
                                <textarea id="descricaoComplementar" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Seção 3: Valores Comerciais -->
                    <div class="card mb-4">
                        <div class="card-header">Valores e Medidas</div>
                        <div class="card-body row g-3">
                            <div class="col-md-4">
                                <label>ValCV (MGA)</label>
                                <input type="number" id="valcv" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label>VAL.E (MGA)</label>
                                <input type="number" id="valE" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label>Peso Líquido Total (KG)</label>
                                <input type="number" id="pesoLiquido" class="form-control">
                            </div>
                        </div>
                    </div>

                    <!-- Seção 4: LPCOs -->
                    <div class="card mb-4">
                        <div class="card-header">Licenças e Permissões</div>
                        <div class="card-body">
                            <div class="lpcos-list mb-3"></div>
                            <div class="input-group">
                                <input type="text" id="novoLpco" class="form-control" placeholder="Número do LPCO">
                                <button class="btn btn-primary" type="button" onclick="adicionarLPCO()">
                                    <i class="fas fa-plus"></i> Adicionar
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Seção 5: Empacotamentos -->
                    <div class="card mb-4">
                        <div class="card-header">Empacotamentos</div>
                        <div class="card-body row g-3">
                            <div class="col-md-3">
                                <label>Primeiro Empacotamento</label>
                                <select class="form-select" id="empacotamento1">
                                    <option value="">Selecione...</option>
                                    <option value="Container">Container</option>
                                    <option value="Palete">Palete</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>Segundo Empacotamento</label>
                                <select class="form-select" id="empacotamento2">
                                    <option value="">Selecione...</option>
                                    <option value="Caixas">Caixas</option>
                                    <option value="Sacos">Sacos</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="salvarAlteracoes">Salvar Alterações</button>
            </div>
        </div>
    </div>
</div>

<script src="./due/js/add-lpcos.js"></script>
<script src="./due/js/due-generate-xml.js"></script>
<script type="module" src="./due/js/campos-itens-nfe.mjs"></script>

<script def>

$(document).ready(function() {
    let nfeData = [];
    let currentIndex = -1;

    // Configuração do DataTable
    const dataTable = $('#notasFiscaisTable').DataTable({
        language: {
            url: "https://cdn.datatables.net/plug-ins/2.0.3/i18n/pt-BR.json"
        },
        columns: [
            { data: 'chave', title: 'Chave de Acesso' },
            { data: 'importador.nome', title: 'Importador' },
            { data: 'importador.pais', title: 'País' },
            {
                data: null,
                title: 'Ações',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return `
                        <button type="button" class="btn btn-sm btn-primary btn-editar me-2" 
                            data-index="${row.index}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger btn-excluir" 
                            data-index="${row.index}">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;
                }
            }
        ]
    });

    // Processar XML
    $('#xml-files').on('change', function(e) {
        const files = e.target.files;
        Array.from(files).forEach(file => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const xml = new DOMParser().parseFromString(e.target.result, "text/xml");
                processarNFe(xml);
            };
            reader.readAsText(file);
        });
    });

    function processarNFe(xml) {
        const ns = 'http://www.portalfiscal.inf.br/nfe';
        const infNFe = xml.getElementsByTagNameNS(ns, 'infNFe')[0];
        const dest = xml.getElementsByTagNameNS(ns, 'dest')[0];
        const ide = xml.getElementsByTagNameNS(ns, 'ide')[0];

        // Processar dados gerais
        $('#text-cnpj-cpf-select').val(
            getTextNS(dest, ns, 'CNPJ') || getTextNS(dest, ns, 'CPF')
        );
        $('#nomeCliente').val(getTextNS(dest, ns, 'xNome'));
        $('#ruc').val(infNFe.getAttribute('Id').replace('NFe', ''));

        // Processar itens
        const itens = xml.getElementsByTagNameNS(ns, 'det');
        nfeData = Array.from(itens).map((item, index) => {
            const prod = item.getElementsByTagNameNS(ns, 'prod')[0];
            return {
                index: index,
                chave: infNFe.getAttribute('Id'),
                importador: {
                    nome: getTextNS(dest, ns, 'xNome'),
                    pais: getTextNS(dest, ns, 'xPais'),
                    endereco: montarEndereco(dest, ns)
                },
                codigo: getTextNS(prod, ns, 'cProd'),
                descricao: getTextNS(prod, ns, 'xProd'),
                ncm: getTextNS(prod, ns, 'NCM'),
                cfop: getTextNS(prod, ns, 'CFOP'),
                quantidadeEstatistica: getTextNS(prod, ns, 'qCom'),
                unidadeEstatistica: getTextNS(prod, ns, 'uCom'),
                pesoLiquido: parseFloat(getTextNS(prod, ns, 'nCM')) || 0,
                condicaoVenda: 'CIF',
                valcv: 0,
                valE: 0,
                lpcos: [],
                empacotamentos: Array(4).fill(''),
                ccpt_ccrom: '',
                descricaoComplementar: '',
                comissaoAvaliaria: 0,
                lfco: '',
                usuarioAvaliario: ''
            };
        });

        refreshDataTable();
    }

    function montarEndereco(dest, ns) {
        return [
            getTextNS(dest, ns, 'xLgr'),
            getTextNS(dest, ns, 'nro'),
            getTextNS(dest, ns, 'xCpl'),
            getTextNS(dest, ns, 'xBairro'),
            getTextNS(dest, ns, 'xMun'),
            getTextNS(dest, ns, 'UF')
        ].filter(Boolean).join(', ');
    }

    function getTextNS(parent, ns, tagName) {
        const element = parent?.getElementsByTagNameNS(ns, tagName)[0];
        return element?.textContent?.trim() || '';
    }

    function refreshDataTable() {
        dataTable.clear().rows.add(nfeData).draw();
    }

    // Abrir modal de edição
    $('#notasFiscaisTable').on('click', '.btn-editar', function() {
        currentIndex = $(this).data('index');
        const item = nfeData[currentIndex];
        
        // Preencher campos do modal
        $('#itemCodigo').val(item.codigo);
        $('#itemDescricao').val(item.descricao);
        $('#itemNCM').val(item.ncm);
        $('#itemCFOP').val(item.cfop);
        $('#quantidadeEstatistica').val(item.quantidadeEstatistica);
        $('#unidadeEstatistica').val(item.unidadeEstatistica);
        $('#pesoLiquido').val(item.pesoLiquido);
        $('#condicaoVenda').val(item.condicaoVenda);
        $('#valcv').val(item.valcv);
        $('#valE').val(item.valE);
        $('#importadorNome').val(item.importador.nome);
        $('#importadorPais').val(item.importador.pais);
        $('#importadorEndereco').val(item.importador.endereco);
        $('#ccpt_ccrom').val(item.ccpt_ccrom);
        $('#descricaoComplementar').val(item.descricaoComplementar);
        $('#comissaoAvaliaria').val(item.comissaoAvaliaria);
        $('#lfco').val(item.lfco);
        $('#usuarioAvaliario').val(item.usuarioAvaliario);

        // Preencher LPCOs
        $('.lpcos-list').html(item.lpcos.map(lpco => `
            <div class="lpco-item">
                ${lpco}
                <button type="button" class="btn-close" onclick="removerLPCO('${lpco}')"></button>
            </div>
        `).join(''));

        // Preencher empacotamentos
        item.empacotamentos.forEach((emp, idx) => {
            $(`#empacotamento${idx + 1}`).val(emp);
        });

        $('#itemDetailsModal').modal('show');
    });

    // Salvar alterações
    $('#salvarAlteracoes').on('click', function() {
        if (currentIndex > -1) {
            nfeData[currentIndex] = {
                ...nfeData[currentIndex],
                codigo: $('#itemCodigo').val(),
                descricao: $('#itemDescricao').val(),
                ncm: $('#itemNCM').val(),
                cfop: $('#itemCFOP').val(),
                quantidadeEstatistica: $('#quantidadeEstatistica').val(),
                unidadeEstatistica: $('#unidadeEstatistica').val(),
                pesoLiquido: parseFloat($('#pesoLiquido').val()) || 0,
                condicaoVenda: $('#condicaoVenda').val(),
                valcv: parseFloat($('#valcv').val()) || 0,
                valE: parseFloat($('#valE').val()) || 0,
                importador: {
                    ...nfeData[currentIndex].importador,
                    nome: $('#importadorNome').val(),
                    pais: $('#importadorPais').val(),
                    endereco: $('#importadorEndereco').val()
                },
                empacotamentos: Array.from($('.empacotamento')).map(input => input.value),
                ccpt_ccrom: $('#ccpt_ccrom').val(),
                descricaoComplementar: $('#descricaoComplementar').val(),
                comissaoAvaliaria: parseFloat($('#comissaoAvaliaria').val()),
                lfco: $('#lfco').val(),
                usuarioAvaliario: $('#usuarioAvaliario').val()
            };
            
            refreshDataTable();
            $('#itemDetailsModal').modal('hide');
        }
    });

    // Funções LPCO
    window.adicionarLPCO = function() {
        const novoLpco = $('#novoLpco').val();
        if (novoLpco && !nfeData[currentIndex].lpcos.includes(novoLpco)) {
            nfeData[currentIndex].lpcos.push(novoLpco);
            $('.lpcos-list').append(`
                <div class="lpco-item">
                    ${novoLpco}
                    <button type="button" class="btn-close" onclick="removerLPCO('${novoLpco}')"></button>
                </div>
            `);
            $('#novoLpco').val('');
        }
    };

    window.removerLPCO = function(lpco) {
        nfeData[currentIndex].lpcos = nfeData[currentIndex].lpcos.filter(l => l !== lpco);
        $(`button:contains('${lpco}')`).closest('.lpco-item').remove();
    };

    // Excluir item
    $('#notasFiscaisTable').on('click', '.btn-excluir', function() {
        const index = $(this).data('index');
        nfeData = nfeData.filter((_, i) => i !== index);
        refreshDataTable();
    });
});
   
</script>