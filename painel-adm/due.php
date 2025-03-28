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

<div class="modal fade" id="itemDetailsModal" tabindex="-1" aria-labelledby="itemDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg"> <!-- modal-lg para um modal maior -->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="itemDetailsModalLabel">Detalhes do Item</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- A tabela de detalhes do item será inserida aqui via JavaScript -->
       

        <div class="form-group">
            <label for="descricao-${itemId}">Descrição Detalhada do Item:</label>
            <textarea id="descricao-${itemId}" name="descricao-${itemId}" class="form-control" rows="5"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
        <button type="button" class="btn btn-primary" id="saveItemDetails">Salvar</button>
      </div>
    </div>
  </div>
</div>

<scrit src="./due/js/add-lpcos.js"></script>
<scrippt src="./due/js/due-generate-xml.js"></script>
<script type="module" src="./due/js/campos-itens-nfe.mjs"></script>

//<script type="module"> // Ou apenas <script>

    console.log("SCRIPT COM PARSER INICIADO");

    // --- Funções Auxiliares ---
    const getSafe = (obj, path, defaultValue = '') => { try { const value = path.split('.').reduce((o, k) => (o || {})[k], obj); return value ?? defaultValue; } catch { return defaultValue; } };
    const getXmlValue = (el, tag) => el?.getElementsByTagName(tag)?.[0]?.textContent?.trim() ?? '';
    const getXmlAttr = (el, attr) => el?.getAttribute(attr) ?? '';

    // --- Variáveis Globais ---
    let processedNFData = []; // Guarda NFs processadas {nf: {...}, items: [...]}
    let itemDetailsModalInstance = null;

    // --- Parser XML (Integrado) ---
    const parseNFeXML = (xmlString, fileName = 'arquivo') => {
        console.log(`[Parse XML] ${fileName}`);
        try {
            const parser = new DOMParser(); const xmlDoc = parser.parseFromString(xmlString, "text/xml");
            const parserError = xmlDoc.getElementsByTagName("parsererror");
            if (parserError.length > 0) { console.error(`Erro PARSE XML ${fileName}:`, parserError[0].textContent); return null; }
            const infNFe = xmlDoc.getElementsByTagName("infNFe")[0]; if (!infNFe) { console.error(`Tag <infNFe> não encontrada em ${fileName}`); return null; }
            const chave = getXmlAttr(infNFe, 'Id').replace('NFe', ''); const emit = infNFe.getElementsByTagName("emit")[0]; const dest = infNFe.getElementsByTagName("dest")[0]; const enderDest = dest?.getElementsByTagName("enderDest")[0]; const exporta = infNFe.getElementsByTagName("exporta")[0]; const detElements = infNFe.getElementsByTagName("det");
            const nfeData = {
                chaveAcesso: chave, emitente: { cnpj: getXmlValue(emit, "CNPJ"), nome: getXmlValue(emit, "xNome") }, destinatario: { nome: getXmlValue(dest, "xNome"), idEstrangeiro: getXmlValue(dest, "idEstrangeiro"), endereco: { logradouro: getXmlValue(enderDest, "xLgr"), numero: getXmlValue(enderDest, "nro"), bairro: getXmlValue(enderDest, "xBairro"), municipio: getXmlValue(enderDest, "xMun"), uf: getXmlValue(enderDest, "UF"), paisNome: getXmlValue(enderDest, "xPais"), paisCodigo: getXmlValue(enderDest, "cPais") } }, exportacao: { ufSaidaPais: getXmlValue(exporta, "UFSaidaPais"), localExportacao: getXmlValue(exporta, "xLocExporta") }, items: []
            };
            for (let i = 0; i < detElements.length; i++) {
                const det = detElements[i]; const prod = det.getElementsByTagName("prod")[0]; if (!prod) continue;
                const uTrib = getXmlValue(prod, "uTrib"); const qTrib = getXmlValue(prod, "qTrib"); const vProd = getXmlValue(prod, "vProd");
                nfeData.items.push({
                    nItem: getXmlAttr(det, 'nItem'), cProd: getXmlValue(prod, "cProd"), xProd: getXmlValue(prod, "xProd"), ncm: getXmlValue(prod, "NCM"), cfop: getXmlValue(prod, "CFOP"), uCom: getXmlValue(prod, "uCom"), qCom: getXmlValue(prod, "qCom"), vUnCom: getXmlValue(prod, "vUnCom"), vProd: vProd, uTrib: uTrib, qTrib: qTrib, infAdProd: getXmlValue(det, "infAdProd"),
                    // Campos editáveis
                    descricaoNcm: "", atributosNcm: "", unidadeEstatistica: uTrib, quantidadeEstatistica: qTrib, pesoLiquidoItem: (uTrib.toUpperCase() === 'KG') ? qTrib : "", condicaoVenda: "", vmcv: "", vmle: vProd, paisDestino: nfeData.destinatario.endereco.paisNome,
                    enquadramento1: "", enquadramento2: "", enquadramento3: "", enquadramento4: "", lpcos: [], nfsRefEletronicas: [], nfsRefFormulario: [], nfsComplementares: [], ccptCcrom: ""
                });
            }
            console.log(`[Parse XML] ${fileName} OK - ${nfeData.items.length} itens.`);
            return nfeData;
        } catch (error) { console.error(`Erro GERAL Parse XML ${fileName}:`, error); return null; }
    };

     // --- Função para Criar o Modal Detalhado ---
    function createItemDetailsFields(itemData, nfData, nfIndex, itemIndex) {
        const container = document.createElement('div'); container.classList.add('item-details-form-container');
        const idPrefix = `modal-item-${nfIndex}-${itemIndex}-`; const val = (key, defaultValue = '') => getSafe(itemData, key, defaultValue);
        // *** HTML Completo do Modal ***
        container.innerHTML = `<h5>Item ${val('nItem', itemIndex + 1)} (NF: ...${getSafe(nfData, 'chaveAcesso', 'N/A').slice(-6)})</h5><div class="row g-3 mb-3"><div class="col-md-6"><label class="form-label">Exportador:</label><input type="text" class="form-control" value="${getSafe(nfData, 'emitente.nome', 'N/A')}" readonly></div><div class="col-md-6"><label for="${idPrefix}ncm" class="form-label">NCM:</label><input type="text" id="${idPrefix}ncm" name="ncm" class="form-control" value="${val('ncm')}" required></div><div class="col-md-6"><label for="${idPrefix}descricao_ncm" class="form-label">Descrição NCM:</label><input type="text" id="${idPrefix}descricao_ncm" name="descricao_ncm" class="form-control" value="${val('descricaoNcm')}" placeholder="Consulta externa"></div><div class="col-md-6"><label for="${idPrefix}atributos_ncm" class="form-label">Atributos NCM:</label><input type="text" id="${idPrefix}atributos_ncm" name="atributos_ncm" class="form-control" value="${val('atributosNcm')}" placeholder="Consulta/definição"></div></div><div class="mb-3"><label for="${idPrefix}descricao_mercadoria" class="form-label">Descrição Mercadoria (NF-e):</label><textarea id="${idPrefix}descricao_mercadoria" name="descricao_mercadoria" class="form-control" rows="3" required>${val('xProd')}</textarea></div><div class="mb-3"><label for="${idPrefix}descricao_complementar" class="form-label">Descrição Complementar (NF-e):</label><textarea id="${idPrefix}descricao_complementar" name="descricao_complementar" class="form-control" rows="3">${val('infAdProd')}</textarea></div><h5>Quantidades e Valores</h5><div class="row g-3 mb-3"><div class="col-md-4"><label for="${idPrefix}unidade_estatistica" class="form-label">Unid. Estatística:</label><input type="text" id="${idPrefix}unidade_estatistica" name="unidade_estatistica" class="form-control" value="${val('unidadeEstatistica', val('uTrib'))}" placeholder="Unid. NCM"></div><div class="col-md-4"><label for="${idPrefix}quantidade_estatistica" class="form-label">Qtd. Estatística:</label><input type="number" step="any" id="${idPrefix}quantidade_estatistica" name="quantidade_estatistica" class="form-control" value="${val('quantidadeEstatistica', val('qTrib'))}"></div><div class="col-md-4"><label for="${idPrefix}peso_liquido" class="form-label">Peso Líquido (KG):</label><input type="number" step="any" id="${idPrefix}peso_liquido" name="peso_liquido" class="form-control" value="${val('pesoLiquidoItem', (val('uTrib','').toUpperCase() === 'KG' ? val('qTrib') : ''))}" required></div><div class="col-md-4"><label for="${idPrefix}unidade_comercializada" class="form-label">Unid. Comercial.:</label><input type="text" id="${idPrefix}unidade_comercializada" name="unidade_comercializada" class="form-control" value="${val('uCom')}" required></div><div class="col-md-4"><label for="${idPrefix}quantidade_comercializada" class="form-label">Qtd. Comercial.:</label><input type="number" step="any" id="${idPrefix}quantidade_comercializada" name="quantidade_comercializada" class="form-control" value="${val('qCom')}" required></div><div class="col-md-4"><label for="${idPrefix}valor_unit_comercial" class="form-label">Vlr Unit. Com. (R$):</label><input type="number" step="any" id="${idPrefix}valor_unit_comercial" name="valor_unit_comercial" class="form-control" value="${val('vUnCom')}" required></div><div class="col-md-4"><label class="form-label">Vlr Total Item (R$):</label><input type="number" class="form-control" value="${val('vProd')}" readonly></div><div class="col-md-4"><label for="${idPrefix}vmle" class="form-label">VMLE (R$):</label><input type="number" step="any" id="${idPrefix}vmle" name="vmle" class="form-control" value="${val('vmle', val('vProd'))}"></div><div class="col-md-4"><label for="${idPrefix}vmcv" class="form-label">VMCV (Moeda Negoc.):</label><input type="number" step="any" id="${idPrefix}vmcv" name="vmcv" class="form-control" value="${val('vmcv')}"></div><div class="col-md-12"><label for="${idPrefix}condicao_venda" class="form-label">Condição Venda:</label><input type="text" id="${idPrefix}condicao_venda" name="condicao_venda" class="form-control" value="${val('condicaoVenda')}" placeholder="Ex: FOB, CIF..."></div></div><h5>Importador / Destino</h5><div class="row g-3 mb-3"><div class="col-md-6"><label class="form-label">Nome Importador:</label><input type="text" class="form-control" value="${getSafe(nfData, 'destinatario.nome', 'N/A')}" readonly></div><div class="col-md-6"><label class="form-label">País Importador:</label><input type="text" class="form-control" value="${getSafe(nfData, 'destinatario.endereco.paisNome', 'N/A')} (${getSafe(nfData, 'destinatario.endereco.paisCodigo', 'N/A')})" readonly></div><div class="col-12"><label class="form-label">Endereço Importador:</label><input type="text" class="form-control" value="${[getSafe(nfData, 'destinatario.endereco.logradouro'), getSafe(nfData, 'destinatario.endereco.numero'), getSafe(nfData, 'destinatario.endereco.bairro'), getSafe(nfData, 'destinatario.endereco.municipio'), getSafe(nfData, 'destinatario.endereco.uf')].filter(Boolean).join(', ') || '(Não encontrado)'}" readonly></div><div class="col-md-6"><label for="${idPrefix}pais_destino" class="form-label">País Destino Final:</label><input type="text" id="${idPrefix}pais_destino" name="pais_destino" class="form-control" value="${val('paisDestino', getSafe(nfData, 'destinatario.endereco.paisNome'))}"></div></div><h5>Enquadramentos</h5><div class="row g-3 mb-3">${[1, 2, 3, 4].map(num => `<div class="col-md-6"><label for="${idPrefix}enquadramento${num}" class="form-label">${num}º Enq.:</label><select id="${idPrefix}enquadramento${num}" name="enquadramento${num}" class="form-select"><option value="">Selecione...</option><option value="80101" ${val(`enquadramento${num}`) === '80101' ? 'selected' : ''}>80101</option><option value="80102" ${val(`enquadramento${num}`) === '80102' ? 'selected' : ''}>80102</option><option value="99999" ${val(`enquadramento${num}`) === '99999' ? 'selected' : ''}>99999</option></select></div>`).join('')}</div><h5>LPCO</h5><div class="lpco-container mb-3" id="${idPrefix}lpco-section"><div class="input-group"><select id="${idPrefix}lpco-select" class="form-select"><option value="">Selecione LPCO...</option><option value="LPCO-001">LPCO-001</option><option value="BR24/1234567">BR24/1234567</option></select><button type="button" class="btn btn-success add-lpco-btn">Add</button></div><div class="mt-2"><label class="form-label">Adicionados:</label><div class="border p-2 rounded bg-light lpco-list min-h-40px">${val('lpcos', []).map(lpco => `<span class="badge bg-secondary me-1 mb-1 lpco-item" data-value="${lpco}">${lpco} <button type="button" class="btn-close btn-close-white btn-sm remove-lpco"></button></span>`).join('')}</div><input type="hidden" class="lpcos-hidden" value="${val('lpcos', []).join(',')}"></div></div><h5>Tratamento Tributário / Refs</h5><div class="mb-3"><div class="border p-3 rounded mb-2" id="${idPrefix}nfe-ref-section"><label class="form-label fw-bold">NF-e Ref.:</label><div class="input-group mb-2"><input type="text" class="form-control nfe-ref-input" placeholder="Chave (44)"><button class="btn btn-outline-secondary add-nfe-ref-btn" type="button">Add</button></div><ul class="list-group nfe-ref-list">${val('nfsRefEletronicas', []).map(k => `<li class="list-group-item py-1 d-flex justify-content-between" data-value="${k}">${k}<button type="button" class="btn-close remove-ref"></button></li>`).join('')}</ul><input type="hidden" class="nfsRefEletronicas-hidden" value="${val('nfsRefEletronicas', []).join(',')}"></div><div class="border p-3 rounded mb-2" id="${idPrefix}nf_form-ref-section"><label class="form-label fw-bold">NF Form. Ref.:</label><div class="input-group mb-2"><input type="text" class="form-control nf_form-ref-input" placeholder="Detalhes"><button class="btn btn-outline-secondary add-nf_form-ref-btn" type="button">Add</button></div><ul class="list-group nf_form-ref-list">${val('nfsRefFormulario', []).map(d => `<li class="list-group-item py-1 d-flex justify-content-between" data-value="${d}">${d}<button type="button" class="btn-close remove-ref"></button></li>`).join('')}</ul><input type="hidden" class="nfsRefFormulario-hidden" value="${val('nfsRefFormulario', []).join(',')}"></div><div class="border p-3 rounded mb-2" id="${idPrefix}nfc-ref-section"><label class="form-label fw-bold">NF Comp. Ref.:</label><div class="input-group mb-2"><input type="text" class="form-control nfc-ref-input" placeholder="Chave (44)"><button class="btn btn-outline-secondary add-nfc-ref-btn" type="button">Add</button></div><ul class="list-group nfc-ref-list">${val('nfsComplementares', []).map(k => `<li class="list-group-item py-1 d-flex justify-content-between" data-value="${k}">${k}<button type="button" class="btn-close remove-ref"></button></li>`).join('')}</ul><input type="hidden" class="nfsComplementares-hidden" value="${val('nfsComplementares', []).join(',')}"></div></div><div class="mb-3"><h6>CCPT/CCROM</h6><div class="form-check"><input class="form-check-input" type="radio" name="${idPrefix}ccpt_ccrom" id="${idPrefix}ccpt_ccrom_none" value="" ${val('ccptCcrom') === '' ? 'checked' : ''}><label class="form-check-label" for="${idPrefix}ccpt_ccrom_none">N/A</label></div><div class="form-check"><input class="form-check-input" type="radio" name="${idPrefix}ccpt_ccrom" id="${idPrefix}ccpt" value="CCPT" ${val('ccptCcrom') === 'CCPT' ? 'checked' : ''}><label class="form-check-label" for="${idPrefix}ccpt">CCPT</label></div><div class="form-check"><input class="form-check-input" type="radio" name="${idPrefix}ccpt_ccrom" id="${idPrefix}ccrom" value="CCROM" ${val('ccptCcrom') === 'CCROM' ? 'checked' : ''}><label class="form-check-label" for="${idPrefix}ccrom">CCROM</label></div></div>`;
        // --- Listeners Dinâmicos ---
        container.addEventListener('click', (e) => { if (e.target.classList.contains('remove-lpco')) { const b = e.target.closest('.lpco-item'); const h = b?.closest('.lpco-container')?.querySelector('.lpcos-hidden'); if (b && h) { const v = b.dataset.value; h.value = (h.value || '').split(',').filter(i => i && i !== v).join(','); b.remove(); } return; } if (e.target.classList.contains('remove-ref')) { const li = e.target.closest('li[data-value]'); const h = li?.closest('.border')?.querySelector('input[type="hidden"]'); if (li && h) { const v = li.dataset.value; h.value = (h.value || '').split(',').filter(i => i && i !== v).join(','); li.remove(); } return; } if (e.target.classList.contains('add-lpco-btn')) { const s = e.target.closest('.lpco-container'); const sel = s?.querySelector('select'); const l = s?.querySelector('.lpco-list'); const h = s?.querySelector('.lpcos-hidden'); if (sel && l && h) { const v = sel.value; const t = sel.options[sel.selectedIndex]?.text || v; if (v && !(h.value || '').split(',').includes(v)) { l.insertAdjacentHTML('beforeend', `<span class="badge bg-secondary me-1 mb-1 lpco-item" data-value="${v}">${t} <button type="button" class="btn-close btn-close-white btn-sm remove-lpco"></button></span>`); h.value = [...(h.value || '').split(','), v].filter(Boolean).join(','); sel.value = ''; } } return; } if (e.target.classList.contains('add-nfe-ref-btn')) { const s = e.target.closest('.border'); const i = s?.querySelector('.nfe-ref-input'); const u = s?.querySelector('.nfe-ref-list'); const h = s?.querySelector('.nfsRefEletronicas-hidden'); if (i && u && h) { const k = i.value.trim().replace(/\D/g, ''); if (k.length === 44 && !(h.value || '').split(',').includes(k)) { u.insertAdjacentHTML('beforeend', `<li class="list-group-item py-1 d-flex justify-content-between" data-value="${k}">${k}<button type="button" class="btn-close remove-ref"></button></li>`); h.value = [...(h.value || '').split(','), k].filter(Boolean).join(','); i.value = ''; } else if (k.length !== 44) alert('Chave NF-e inválida.'); else alert('NF-e já adicionada.'); } return; } if (e.target.classList.contains('add-nf_form-ref-btn')) { const s = e.target.closest('.border'); const i = s?.querySelector('.nf_form-ref-input'); const u = s?.querySelector('.nf_form-ref-list'); const h = s?.querySelector('.nfsRefFormulario-hidden'); if (i && u && h) { const d = i.value.trim(); if (d && !(h.value || '').split(',').includes(d)) { u.insertAdjacentHTML('beforeend', `<li class="list-group-item py-1 d-flex justify-content-between" data-value="${d}">${d}<button type="button" class="btn-close remove-ref"></button></li>`); h.value = [...(h.value || '').split(','), d].filter(Boolean).join(','); i.value = ''; } else if (!d) alert('Insira detalhes.'); else alert('Ref. já adicionada.'); } return; } if (e.target.classList.contains('add-nfc-ref-btn')) { const s = e.target.closest('.border'); const i = s?.querySelector('.nfc-ref-input'); const u = s?.querySelector('.nfc-ref-list'); const h = s?.querySelector('.nfsComplementares-hidden'); if (i && u && h) { const k = i.value.trim().replace(/\D/g, ''); if (k.length === 44 && !(h.value || '').split(',').includes(k)) { u.insertAdjacentHTML('beforeend', `<li class="list-group-item py-1 d-flex justify-content-between" data-value="${k}">${k}<button type="button" class="btn-close remove-ref"></button></li>`); h.value = [...(h.value || '').split(','), k].filter(Boolean).join(','); i.value = ''; } else if (k.length !== 44) alert('Chave NF Comp. inválida.'); else alert('NF Comp. já adicionada.'); } return; } });
        return container;
    }

    // --- Renderização da Tabela ---
    function renderNotasFiscaisTable() {
        console.log("[Render Tabela]"); const tbody = document.querySelector('#notasFiscaisTable tbody'); if (!tbody) { console.error("tbody?"); return; } tbody.innerHTML = ''; let hasItems = false;
        if (processedNFData.length === 0) { tbody.innerHTML = '<tr><td colspan="4" class="text-center">Nenhuma NF-e carregada.</td></tr>'; return; }
        processedNFData.forEach((nfEntry, nfIndex) => {
            const nf = nfEntry.nf; const items = nfEntry.items; const chaveNFe = getSafe(nf, 'chaveAcesso', 'N/A'); const nomeDest = getSafe(nf, 'destinatario.nome', 'N/A'); const paisDest = getSafe(nf, 'destinatario.endereco.paisNome', 'N/A');
            if (!items || items.length === 0) { tbody.insertAdjacentHTML('beforeend', `<tr><td colspan="4" class="text-muted fst-italic">NF ${chaveNFe} s/ itens.</td></tr>`); return; }
            items.forEach((item, itemIndex) => { hasItems = true; const row = document.createElement('tr'); row.classList.add('item-row'); row.innerHTML = `<td>${chaveNFe}</td><td>${nomeDest}</td><td>${paisDest}</td><td class="text-center"><button type="button" class="btn btn-info btn-sm toggle-details" title="Detalhes Item ${getSafe(item, 'nItem', itemIndex + 1)}" data-nf-index="${nfIndex}" data-item-index="${itemIndex}">+</button></td>`; tbody.appendChild(row); });
        });
        if (!hasItems) { tbody.innerHTML = '<tr><td colspan="4" class="text-center">Nenhum item encontrado.</td></tr>'; } console.log("[Render Tabela] FIM.");
    }

    // --- Preencher Aba 1 ---
    const populateMainForm = (nfData) => { const elCNPJ = document.getElementById('text-cnpj-cpf-select'); const elNome = document.getElementById('nomeCliente'); if (elCNPJ) elCNPJ.value = getSafe(nfData, 'emitente.cnpj'); if (elNome) elNome.value = getSafe(nfData, 'emitente.nome'); console.log(`[Aba 1] Emitente.`); };

    // --- Código Principal ---
    document.addEventListener('DOMContentLoaded', () => {
        console.log("DOM Carregado.");
        const inputXML = document.getElementById('xml-files'); const uploadStatus = document.getElementById('uploadStatus'); const spinner = document.getElementById('spinner'); const notasTable = document.querySelector('#notasFiscaisTable'); const modalElement = document.getElementById('itemDetailsModal'); const saveButtonModal = document.getElementById('saveItemDetails');
        if (!inputXML || !uploadStatus || !spinner || !notasTable || !modalElement || !saveButtonModal) { console.error("ERRO Interface!"); alert("Erro Interface."); return; }

        try { itemDetailsModalInstance = new bootstrap.Modal(modalElement); modalElement.addEventListener('hidden.bs.modal', () => { const sb = document.getElementById('saveItemDetails'); if (sb) { delete sb.dataset.nfIndex; delete sb.dataset.itemIndex; } const mb = modalElement.querySelector('.modal-body'); if (mb) mb.innerHTML = ''; }); console.log("Modal OK."); }
        catch (e) { console.error("Falha Modal:", e); }

        renderNotasFiscaisTable(); // Tabela vazia

        // Input XML Listener (COM PARSER INTEGRADO)
        inputXML.addEventListener('change', async (event) => {
            console.log("[Input XML] 'change'"); const files = event.target.files; if (!files || files.length === 0) { uploadStatus.textContent = 'Nenhum.'; processedNFData = []; renderNotasFiscaisTable(); populateMainForm(null); return; }
            uploadStatus.textContent = `Processando ${files.length}...`; spinner.style.display = 'inline-block'; inputXML.disabled = true; processedNFData = []; let promises = [];
            for (const file of files) { if (file.name.toLowerCase().endsWith('.xml')) { promises.push( file.text().then(xml => { const data = parseNFeXML(xml, file.name); if (data) { processedNFData.push({ nf: data, items: data.items }); } }).catch(err => console.error(`Erro LER ${file.name}:`, err)) ); } else { console.warn(`Ignorado: ${file.name}`); } }
            try { await Promise.all(promises); } catch (err) { console.error("Erro GERAL:", err); }
            finally { spinner.style.display = 'none'; inputXML.disabled = false; event.target.value = null; const totalItems = processedNFData.reduce((s, e) => s + e.items.length, 0); uploadStatus.textContent = `OK: ${totalItems} itens em ${processedNFData.length} NF(s).`; populateMainForm(processedNFData[0]?.nf); renderNotasFiscaisTable(); console.log("[Input XML] FIM."); }
        });

        // Abrir Modal Listener
        notasTable.addEventListener('click', (e) => {
            const btn = e.target.closest('button.toggle-details'); if (!btn) return;
            const nfIndex = parseInt(btn.dataset.nfIndex, 10); const itemIndex = parseInt(btn.dataset.itemIndex, 10); console.log(`[Abrir Modal] NF ${nfIndex}, Item ${itemIndex}`);
            if (isNaN(nfIndex) || isNaN(itemIndex) || !processedNFData[nfIndex]?.items?.[itemIndex]) { console.error("Dados inválidos."); return; }
            try {
                const nfData = processedNFData[nfIndex].nf; const itemData = processedNFData[nfIndex].items[itemIndex]; const modalBody = modalElement.querySelector('.modal-body'); const modalTitle = modalElement.querySelector('.modal-title'); if (!modalBody || !modalTitle || !itemDetailsModalInstance) { console.error("Modal elems/instância?"); return; }
                modalTitle.textContent = `Detalhes Item ${getSafe(itemData, 'nItem', itemIndex + 1)}`; modalBody.innerHTML = '<div class="text-center p-3"><div class="spinner-border spinner-border-sm"></div></div>'; saveButtonModal.dataset.nfIndex = nfIndex; saveButtonModal.dataset.itemIndex = itemIndex;
                setTimeout(() => { try { modalBody.innerHTML = ''; modalBody.appendChild(createItemDetailsFields(itemData, nfData, nfIndex, itemIndex)); itemDetailsModalInstance.show(); console.log("[Abrir Modal] Exibido."); } catch (err) { console.error("Erro criar/mostrar modal:", err); modalBody.innerHTML = `<div class="alert alert-danger">Erro.</div>`; itemDetailsModalInstance.show(); } }, 50);
            } catch (err) { console.error("Erro geral abrir modal:", err); }
        });

        // Salvar Modal Listener
        saveButtonModal.addEventListener('click', () => {
            console.log("[Salvar Modal]"); const nfIndex = parseInt(saveButtonModal.dataset.nfIndex, 10); const itemIndex = parseInt(saveButtonModal.dataset.itemIndex, 10); if (isNaN(nfIndex) || isNaN(itemIndex) || !processedNFData[nfIndex]?.items?.[itemIndex]) { console.error("Ref. inválida."); alert("Erro salvar."); return; }
            const itemData = processedNFData[nfIndex].items[itemIndex]; const idPrefix = `modal-item-${nfIndex}-${itemIndex}-`; const modalContent = modalElement.querySelector('.modal-body'); if (!modalContent) { console.error("Corpo modal?"); return; }
            try {
                const getModalValue = (sel) => modalContent.querySelector(`#${idPrefix}${sel}`)?.value ?? null; const getRadioValue = (n) => modalContent.querySelector(`input[name="${idPrefix}${n}"]:checked`)?.value ?? ""; const getHidden = (sec, cls) => modalContent.querySelector(`${sec} .${cls}`)?.value ?? "";
                // Atualiza o objeto itemData diretamente
                itemData.ncm = getModalValue('ncm'); itemData.descricaoNcm = getModalValue('descricao_ncm'); itemData.atributosNcm = getModalValue('atributos_ncm'); itemData.xProd = getModalValue('descricao_mercadoria'); itemData.infAdProd = getModalValue('descricao_complementar'); itemData.unidadeEstatistica = getModalValue('unidade_estatistica'); itemData.quantidadeEstatistica = getModalValue('quantidade_estatistica'); itemData.pesoLiquidoItem = getModalValue('peso_liquido'); itemData.uCom = getModalValue('unidade_comercializada'); itemData.qCom = getModalValue('quantidade_comercializada'); itemData.vUnCom = getModalValue('valor_unit_comercial'); itemData.vmle = getModalValue('vmle'); itemData.vmcv = getModalValue('vmcv'); itemData.condicaoVenda = getModalValue('condicao_venda'); itemData.paisDestino = getModalValue('pais_destino');
                for (let i = 1; i <= 4; i++) itemData[`enquadramento${i}`] = getModalValue(`enquadramento${i}`);
                itemData.lpcos = (getHidden(`#${idPrefix}lpco-section`, 'lpcos-hidden') || '').split(',').filter(Boolean);
                itemData.nfsRefEletronicas = (getHidden(`#${idPrefix}nfe-ref-section`, 'nfsRefEletronicas-hidden') || '').split(',').filter(Boolean);
                itemData.nfsRefFormulario = (getHidden(`#${idPrefix}nf_form-ref-section`, 'nfsRefFormulario-hidden') || '').split(',').filter(Boolean);
                itemData.nfsComplementares = (getHidden(`#${idPrefix}nfc-ref-section`, 'nfsComplementares-hidden') || '').split(',').filter(Boolean);
                itemData.ccptCcrom = getRadioValue('ccpt_ccrom');
                console.log("[Salvar Modal] OK:", JSON.parse(JSON.stringify(itemData))); alert("Dados atualizados."); if (itemDetailsModalInstance) itemDetailsModalInstance.hide();
            } catch (saveErr) { console.error("Erro salvar:", saveErr); alert("Erro ao salvar."); }
        });

        // Config Tabs
        const tabLinks = document.querySelectorAll('.nav-tabs .nav-link'); if (tabLinks.length > 0 && typeof bootstrap !== 'undefined' && bootstrap.Tab) { tabLinks.forEach(link => link.addEventListener('click', (e) => { e.preventDefault(); try { new bootstrap.Tab(link).show(); } catch (tabErr) { console.error("Erro Tabs:", tabErr); } })); console.log("Tabs OK."); } else { console.warn("Tabs não OK."); }

        console.log("DOMContentLoaded FIM.");
    }); // --- FIM DOMContentLoaded ---

//</script>