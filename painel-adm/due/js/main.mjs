import NFeProcessor from './due-upload.mjs'; // <-- O './' é importante

// --- Funções Auxiliares ---
const getSafe = (obj, path, defaultValue = '') => { try { const value = path.split('.').reduce((o, k) => (o || {})[k], obj); return value ?? defaultValue; } catch { return defaultValue; } };
const getXmlValue = (el, tag) => el?.getElementsByTagName(tag)?.[0]?.textContent?.trim() ?? '';
const getXmlAttr = (el, attr) => el?.getAttribute(attr) ?? '';
const htmlspecialchars = (str) => { if (typeof str !== 'string') return str; return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;'); };

// --- Variáveis Globais ---
let processedNFData = [];
let itemDetailsModalInstance = null;
let batchEditModalInstance = null;

// --- Definição de Campos Obrigatórios para Status DUE ---
const requiredDueFields = [
    'ncm', 'descricaoDetalhadaDue', 'unidadeEstatistica', 'quantidadeEstatistica',
    'pesoLiquidoItem', 'condicaoVenda', 'vmcv', 'vmle', 'paisDestino', 'enquadramento1',
];

/** Verifica se item está completo para DUE */
function isItemDueComplete(item) {
    if (!item) return false;
    return requiredDueFields.every(fieldName => {
        const value = item[fieldName]; let isFilled;
        if (Array.isArray(value)) { isFilled = value !== null && value !== undefined; }
        else { isFilled = value !== null && value !== undefined && value.toString().trim() !== ''; }
        return isFilled;
    });
}

// --- Parser XML (Extrai infCpl, loga exporta) ---
const parseNFeXML = (xmlString, fileName = 'arquivo') => {
    console.log(`[Parse XML] Iniciando para ${fileName}`);
    try {
        const parser = new DOMParser(); const xmlDoc = parser.parseFromString(xmlString, "text/xml");
        const parserError = xmlDoc.getElementsByTagName("parsererror");
        if (parserError.length > 0) { throw new Error(`Erro de parse no XML: ${parserError[0].textContent}`); }
        const infNFe = xmlDoc.getElementsByTagName("infNFe")[0];
        if (!infNFe) { throw new Error(`Tag <infNFe> não encontrada`); }
        const chave = getXmlAttr(infNFe, 'Id').replace('NFe', '');
        const emit = infNFe.getElementsByTagName("emit")[0]; const dest = infNFe.getElementsByTagName("dest")[0];
        const enderDest = dest?.getElementsByTagName("enderDest")[0]; const exporta = infNFe.getElementsByTagName("exporta")[0];
        const infAdic = infNFe.getElementsByTagName("infAdic")[0]; // Pega o bloco de info adicionais
        const detElements = infNFe.getElementsByTagName("det");

        const nfeData = {
            chaveAcesso: chave,
            emitente: { cnpj: getXmlValue(emit, "CNPJ"), nome: getXmlValue(emit, "xNome") },
            destinatario: { nome: getXmlValue(dest, "xNome"), idEstrangeiro: getXmlValue(dest, "idEstrangeiro"), endereco: { logradouro: getXmlValue(enderDest, "xLgr"), numero: getXmlValue(enderDest, "nro"), bairro: getXmlValue(enderDest, "xBairro"), municipio: getXmlValue(enderDest, "xMun"), uf: getXmlValue(enderDest, "UF"), paisNome: getXmlValue(enderDest, "xPais"), paisCodigo: getXmlValue(enderDest, "cPais") } },
            exportacao: { ufSaidaPais: getXmlValue(exporta, "UFSaidaPais"), localExportacao: getXmlValue(exporta, "xLocExporta") },
            infAdicional: { infCpl: getXmlValue(infAdic, "infCpl"), infAdFisco: getXmlValue(infAdic, "infAdFisco") }, // Extrai infCpl
            items: []
        };

        // Log dos dados de exportação (para referência manual)
        if(nfeData.exportacao.ufSaidaPais || nfeData.exportacao.localExportacao) {
            console.log(`[Parse XML] Dados Exportação: UF Saída: ${nfeData.exportacao.ufSaidaPais}, Local: ${nfeData.exportacao.localExportacao}`);
        }
        if(nfeData.infAdicional.infCpl) {
            console.log(`[Parse XML] Info Complementar (infCpl) encontrada.`);
        }

        for (let i = 0; i < detElements.length; i++) {
            const det = detElements[i]; const prod = det.getElementsByTagName("prod")[0];
            if (!prod) { console.warn(`[Parse Item ${i+1}] Tag <prod> não encontrada.`); continue; }
            const nItem = getXmlAttr(det, 'nItem'); console.log(`--- [Parse Item ${nItem || i+1}] ---`);
             const ncmValue = getXmlValue(prod, "NCM"); console.log(` > NCM:`, ncmValue);
             const xProdValue = getXmlValue(prod, "xProd"); console.log(` > xProd:`, xProdValue);
             const uComValue = getXmlValue(prod, "uCom"); console.log(` > uCom:`, uComValue);
             const qComValue = getXmlValue(prod, "qCom"); console.log(` > qCom:`, qComValue);
             const vUnComValue = getXmlValue(prod, "vUnCom"); console.log(` > vUnCom:`, vUnComValue);
             const vProdValue = getXmlValue(prod, "vProd"); console.log(` > vProd:`, vProdValue);
             const uTribValue = getXmlValue(prod, "uTrib"); console.log(` > uTrib:`, uTribValue);
             const qTribValue = getXmlValue(prod, "qTrib"); console.log(` > qTrib:`, qTribValue);
             const infAdProdValue = getXmlValue(det, "infAdProd"); console.log(` > infAdProd:`, infAdProdValue);
             const pesoLiquidoValue = getXmlValue(prod, "pesoL"); console.log(` > Peso Líquido (TAG: pesoL):`, pesoLiquidoValue); // AJUSTE A TAG "pesoL"
             const nomeDestinatario = getSafe(nfeData, 'destinatario.nome');
             const paisDestinatarioNome = getSafe(nfeData, 'destinatario.endereco.paisNome');
             const paisDestinatarioCodigo = getSafe(nfeData, 'destinatario.endereco.paisCodigo');
            nfeData.items.push({
                nItem, cProd: getXmlValue(prod, "cProd"), xProd: xProdValue, ncm: ncmValue, cfop: getXmlValue(prod, "CFOP"),
                uCom: uComValue, qCom: qComValue, vUnCom: vUnComValue, vProd: vProdValue, uTrib: uTribValue, qTrib: qTribValue, infAdProd: infAdProdValue,
                descricaoNcm: "", atributosNcm: "", unidadeEstatistica: uTribValue, quantidadeEstatistica: qTribValue,
                pesoLiquidoItem: pesoLiquidoValue, condicaoVenda: "", vmcv: "", vmle: vProdValue, paisDestino: paisDestinatarioCodigo, descricaoDetalhadaDue: xProdValue,
                enquadramento1: "", enquadramento2: "", enquadramento3: "", enquadramento4: "",
                lpcos: [], nfsRefEletronicas: [], nfsRefFormulario: [], nfsComplementares: [], ccptCcrom: ""
            });
        }
        console.log(`[Parse XML] ${fileName} OK - ${nfeData.items.length} itens encontrados.`);
        return nfeData;
    } catch (error) { console.error(`Erro GERAL no Parse XML de ${fileName}:`, error); const uploadStatusEl = document.getElementById('uploadStatus'); if(uploadStatusEl) uploadStatusEl.innerHTML += `<div class="text-danger small">Falha ao processar ${fileName}: ${error.message}</div>`; return null; }
};

// --- Função para Criar os Campos do Modal Detalhado ---
function createItemDetailsFields(itemData, nfData, nfIndex, itemIndex) {
    // ... (código da função createItemDetailsFields como na resposta anterior - sem alterações aqui) ...
    // (Incluindo a geração do HTML, createOptions, enqOptions, paisOptions, setupListManager, etc.)
     console.log(`[createItemDetailsFields] Iniciando. Dados recebidos para NF ${nfIndex}, Item ${itemIndex}:`, JSON.parse(JSON.stringify(itemData || {})));
    const container = document.createElement('div'); container.classList.add('item-details-form-container');
    const idPrefix = `modal-item-${nfIndex}-${itemIndex}-`;
    const val = (key, defaultValue = '') => getSafe(itemData, key, defaultValue);
    const isSelected = (value, targetValue) => value === targetValue ? 'selected' : '';
    const isChecked = (value, targetValue) => value === targetValue ? 'checked' : '';
    const createOptions = (data, valueKey, textKey, selectedValue, includeEmpty = true) => { let optionsHtml = includeEmpty ? '<option value="">Selecione...</option>' : ''; if (data && Array.isArray(data) && data.length > 0) { optionsHtml += data.map(item => { const itemValue = getSafe(item, valueKey); const itemText = getSafe(item, textKey); return `<option value="${htmlspecialchars(itemValue)}" ${isSelected(selectedValue, itemValue)}>${htmlspecialchars(itemText)}</option>` }).join(''); } return optionsHtml; };
    const enqOptions = (num) => { let html = '<option value="">Selecione...</option>'; const enqData = window.enquadramentosData || []; if (Array.isArray(enqData)) { html += enqData.map(enq => `<option value="${htmlspecialchars(enq.CODIGO)}" ${isSelected(val(`enquadramento${num}`), enq.CODIGO)}>${htmlspecialchars(enq.CODIGO)} - ${htmlspecialchars(enq.DESCRICAO)}</option>`).join(''); } html += `<option value="99999" ${isSelected(val(`enquadramento${num}`), '99999')}>99999 - OPERACAO SEM ENQUADRAMENTO</option>`; return html; }
    const enqSelectHTML = (num) => `<select id="${idPrefix}enquadramento${num}" name="enquadramento${num}" class="form-select form-select-sm">${enqOptions(num)}</select>`;
    const incotermTextMap = (window.incotermsData || []).map(i => ({...i, DisplayText: `${getSafe(i, 'Sigla')} - ${getSafe(i, 'Descricao')}`}));
    const incotermOptionsHTML = createOptions(incotermTextMap, 'Sigla', 'DisplayText', val('condicaoVenda'));
    const paisOptionsHTML = createOptions(window.paisesData || [], 'CodigoBACEN', 'Nome', val('paisDestino'));
    container.innerHTML = `
    <h5 class="mb-3 border-bottom pb-2">Item ${htmlspecialchars(val('nItem', itemIndex + 1))} (NF-e: ...${htmlspecialchars(getSafe(nfData, 'chaveAcesso', 'N/A').slice(-6))})</h5>
    <h6>Dados Básicos e NCM</h6> <div class="row g-3 mb-4"> <div class="col-md-6"> <label class="form-label">Exportador:</label> <input type="text" class="form-control form-control-sm bg-light" value="${htmlspecialchars(getSafe(nfData, 'emitente.nome', 'N/A'))}" readonly> </div> <div class="col-md-6"> <label for="${idPrefix}ncm" class="form-label">NCM:</label> <input type="text" id="${idPrefix}ncm" name="ncm" class="form-control form-control-sm" value="${htmlspecialchars(val('ncm'))}" required> </div> <div class="col-md-6"> <label for="${idPrefix}descricao_ncm" class="form-label">Descrição NCM:</label> <input type="text" id="${idPrefix}descricao_ncm" name="descricaoNcm" class="form-control form-control-sm" value="${htmlspecialchars(val('descricaoNcm'))}" placeholder="Consultar externamente se necessário"> </div> <div class="col-md-6"> <label for="${idPrefix}atributos_ncm" class="form-label">Atributos NCM:</label> <input type="text" id="${idPrefix}atributos_ncm" name="atributosNcm" class="form-control form-control-sm" value="${htmlspecialchars(val('atributosNcm'))}" placeholder="Consultar/definir atributos"> </div> </div>
    <h6>Descrição da Mercadoria</h6> <div class="mb-3"> <label for="${idPrefix}descricao_mercadoria" class="form-label">Descrição Conforme NF-e:</label> <textarea id="${idPrefix}descricao_mercadoria" class="form-control form-control-sm bg-light" rows="2" readonly>${htmlspecialchars(val('xProd'))}</textarea> </div> <div class="mb-3"> <label for="${idPrefix}descricao_complementar" class="form-label">Descrição Complementar (NF-e - infAdProd):</label> <textarea id="${idPrefix}descricao_complementar" name="infAdProd" class="form-control form-control-sm" rows="2">${htmlspecialchars(val('infAdProd'))}</textarea> </div> <div class="mb-4"> <label for="${idPrefix}descricao_detalhada_due" class="form-label">Descrição Detalhada para DU-E:</label> <textarea id="${idPrefix}descricao_detalhada_due" name="descricaoDetalhadaDue" class="form-control form-control-sm" rows="4" placeholder="Descrição completa e detalhada exigida para a DU-E" required>${htmlspecialchars(val('descricaoDetalhadaDue'))}</textarea> </div>
    <h6>Quantidades e Valores</h6> <div class="row g-3 mb-4"> <div class="col-md-4"> <label for="${idPrefix}unidade_estatistica" class="form-label">Unid. Estatística (NCM):</label> <input type="text" id="${idPrefix}unidade_estatistica" name="unidadeEstatistica" class="form-control form-control-sm" value="${htmlspecialchars(val('unidadeEstatistica'))}" placeholder="Unid. conforme NCM"> </div> <div class="col-md-4"> <label for="${idPrefix}quantidade_estatistica" class="form-label">Qtd. Estatística:</label> <input type="number" step="any" id="${idPrefix}quantidade_estatistica" name="quantidadeEstatistica" class="form-control form-control-sm" value="${htmlspecialchars(val('quantidadeEstatistica'))}"> </div> <div class="col-md-4"> <label for="${idPrefix}peso_liquido" class="form-label">Peso Líquido Total Item (KG):</label> <input type="number" step="any" id="${idPrefix}peso_liquido" name="pesoLiquidoItem" class="form-control form-control-sm" value="${htmlspecialchars(val('pesoLiquidoItem'))}" required> </div> <div class="col-md-3"> <label for="${idPrefix}unidade_comercializada" class="form-label">Unid. Comercial.:</label> <input type="text" id="${idPrefix}unidade_comercializada" class="form-control form-control-sm bg-light" value="${htmlspecialchars(val('uCom'))}" readonly> </div> <div class="col-md-3"> <label for="${idPrefix}quantidade_comercializada" class="form-label">Qtd. Comercial.:</label> <input type="number" step="any" id="${idPrefix}quantidade_comercializada" class="form-control form-control-sm bg-light" value="${htmlspecialchars(val('qCom'))}" readonly> </div> <div class="col-md-3"> <label for="${idPrefix}valor_unit_comercial" class="form-label">Vlr Unit. Com. (R$):</label> <input type="number" step="any" id="${idPrefix}valor_unit_comercial" class="form-control form-control-sm bg-light" value="${htmlspecialchars(val('vUnCom'))}" readonly> </div> <div class="col-md-3"> <label class="form-label">Vlr Total Item (R$):</label> <input type="number" class="form-control form-control-sm bg-light" value="${htmlspecialchars(val('vProd'))}" readonly> </div> <div class="col-md-4"> <label for="${idPrefix}condicao_venda" class="form-label">Condição Venda (Incoterm):</label> <select id="${idPrefix}condicao_venda" name="condicaoVenda" class="form-select form-select-sm" required> ${incotermOptionsHTML} </select> </div> <div class="col-md-4"> <label for="${idPrefix}vmle" class="form-label">VMLE (R$):</label> <input type="number" step="any" id="${idPrefix}vmle" name="vmle" class="form-control form-control-sm" value="${htmlspecialchars(val('vmle'))}" title="Valor da Mercadoria no Local de Embarque" required> </div> <div class="col-md-4"> <label for="${idPrefix}vmcv" class="form-label">VMCV (Moeda Negoc.):</label> <input type="number" step="any" id="${idPrefix}vmcv" name="vmcv" class="form-control form-control-sm" value="${htmlspecialchars(val('vmcv'))}" title="Valor da Mercadoria na Condição de Venda (na moeda de negociação)" required> </div> </div>
    <h6>Importador e Destino</h6> <div class="row g-3 mb-4"> <div class="col-md-6"> <label class="form-label">Nome Importador (NF-e):</label> <input type="text" class="form-control form-control-sm bg-light" value="${htmlspecialchars(getSafe(nfData, 'destinatario.nome', 'N/A'))}" readonly> </div> <div class="col-md-6"> <label class="form-label">País Importador (NF-e):</label> <input type="text" class="form-control form-control-sm bg-light" value="${htmlspecialchars(getSafe(nfData, 'destinatario.endereco.paisNome', 'N/A'))} (${htmlspecialchars(getSafe(nfData, 'destinatario.endereco.paisCodigo', 'N/A'))})" readonly> </div> <div class="col-12"> <label class="form-label">Endereço Importador (NF-e):</label> <input type="text" class="form-control form-control-sm bg-light" value="${htmlspecialchars([getSafe(nfData, 'destinatario.endereco.logradouro'), getSafe(nfData, 'destinatario.endereco.numero'), getSafe(nfData, 'destinatario.endereco.bairro'), getSafe(nfData, 'destinatario.endereco.municipio'), getSafe(nfData, 'destinatario.endereco.uf')].filter(Boolean).join(', ') || '(Não informado na NF-e)')}" readonly> </div> <div class="col-md-6"> <label for="${idPrefix}pais_destino" class="form-label">País Destino Final (DU-E):</label> <select id="${idPrefix}pais_destino" name="paisDestino" class="form-select form-select-sm" required> ${paisOptionsHTML} </select> </div> </div>
    <h6>Enquadramentos da Operação</h6> <div class="row g-3 mb-4"> ${[1, 2, 3, 4].map(num => `<div class="col-md-6"> <label for="${idPrefix}enquadramento${num}" class="form-label">${num}º Enquadramento:</label> ${enqSelectHTML(num)} </div>`).join('')} </div>
    <h6>LPCO (Licenças, Permissões, Certificados e Outros)</h6> <div class="lpco-container mb-4" id="${idPrefix}lpco-section"> <div class="input-group input-group-sm"> <input type="text" id="${idPrefix}lpco-input" class="form-control" placeholder="Digite o número do LPCO"> <button type="button" class="btn btn-success add-lpco-btn">Adicionar</button> </div> <div class="mt-2"> <label class="form-label small text-muted">LPCOs Adicionados:</label> <div class="border p-2 rounded bg-light lpco-list min-h-40px"> ${ (val('lpcos', []) || []).map(lpco => `<span class="badge bg-secondary me-1 mb-1 lpco-item" data-value="${htmlspecialchars(lpco)}">${htmlspecialchars(lpco)} <button type="button" class="btn-close btn-close-white btn-sm remove-lpco" aria-label="Remover LPCO"></button></span>`).join('')} </div> <input type="hidden" name="lpcos" value="${htmlspecialchars( (val('lpcos', []) || []).join(','))}"> </div> </div>
    <h6>Referências e Tratamento Tributário</h6> <div class="row g-3"> <div class="col-md-7"> <div class="border p-3 rounded mb-3" id="${idPrefix}nfe-ref-section"> <label class="form-label fw-bold small mb-1">NF-e Referenciada</label> <div class="input-group input-group-sm mb-2"> <input type="text" class="form-control nfe-ref-input" placeholder="Chave de Acesso (44 dígitos)"> <button class="btn btn-outline-secondary add-nfe-ref-btn" type="button">Add</button> </div> <ul class="list-group list-group-flush nfe-ref-list small ps-1"> ${ (val('nfsRefEletronicas', []) || []).map(k => `<li class="list-group-item py-1 px-0 d-flex justify-content-between align-items-center" data-value="${htmlspecialchars(k)}">${htmlspecialchars(k)}<button type="button" class="btn-close btn-sm remove-ref" aria-label="Remover"></button></li>`).join('')} </ul> <input type="hidden" name="nfsRefEletronicas" value="${htmlspecialchars( (val('nfsRefEletronicas', []) || []).join(','))}"> </div> <div class="border p-3 rounded mb-3" id="${idPrefix}nf_form-ref-section"> <label class="form-label fw-bold small mb-1">NF Formulário Referenciada</label> <div class="input-group input-group-sm mb-2"> <input type="text" class="form-control nf_form-ref-input" placeholder="Série, Número, Modelo, etc."> <button class="btn btn-outline-secondary add-nf_form-ref-btn" type="button">Add</button> </div> <ul class="list-group list-group-flush nf_form-ref-list small ps-1"> ${ (val('nfsRefFormulario', []) || []).map(d => `<li class="list-group-item py-1 px-0 d-flex justify-content-between align-items-center" data-value="${htmlspecialchars(d)}">${htmlspecialchars(d)}<button type="button" class="btn-close btn-sm remove-ref" aria-label="Remover"></button></li>`).join('')} </ul> <input type="hidden" name="nfsRefFormulario" value="${htmlspecialchars( (val('nfsRefFormulario', []) || []).join(','))}"> </div> <div class="border p-3 rounded mb-3 mb-md-0" id="${idPrefix}nfc-ref-section"> <label class="form-label fw-bold small mb-1">NF Complementar</label> <div class="input-group input-group-sm mb-2"> <input type="text" class="form-control nfc-ref-input" placeholder="Chave de Acesso (44 dígitos)"> <button class="btn btn-outline-secondary add-nfc-ref-btn" type="button">Add</button> </div> <ul class="list-group list-group-flush nfc-ref-list small ps-1"> ${ (val('nfsComplementares', []) || []).map(k => `<li class="list-group-item py-1 px-0 d-flex justify-content-between align-items-center" data-value="${htmlspecialchars(k)}">${htmlspecialchars(k)}<button type="button" class="btn-close btn-sm remove-ref" aria-label="Remover"></button></li>`).join('')} </ul> <input type="hidden" name="nfsComplementares" value="${htmlspecialchars( (val('nfsComplementares', []) || []).join(','))}"> </div> </div> <div class="col-md-5"> <div class="border p-3 rounded h-100"> <h6 class="mb-3">Acordo Mercosul</h6> <div class="form-check mb-2"> <input class="form-check-input" type="radio" name="ccptCcrom" id="${idPrefix}ccpt_ccrom_none" value="" ${isChecked(val('ccptCcrom'), '')}> <label class="form-check-label small" for="${idPrefix}ccpt_ccrom_none">N/A (Não se aplica)</label> </div> <div class="form-check mb-2"> <input class="form-check-input" type="radio" name="ccptCcrom" id="${idPrefix}ccpt" value="CCPT" ${isChecked(val('ccptCcrom'), 'CCPT')}> <label class="form-check-label small" for="${idPrefix}ccpt">CCPT</label> </div> <div class="form-check"> <input class="form-check-input" type="radio" name="ccptCcrom" id="${idPrefix}ccrom" value="CCROM" ${isChecked(val('ccptCcrom'), 'CCROM')}> <label class="form-check-label small" for="${idPrefix}ccrom">CCROM</label> </div> </div> </div> </div>
    `;

    // --- Listeners Dinâmicos para Adicionar/Remover Itens (LPCO, Refs) ---
     const setupListManager = (sectionIdSuffix, addButtonClass, removeButtonClass, inputClass, listClass, hiddenInputName) => { /* ...código como antes ... */ };
    setupListManager('lpco-section', 'add-lpco-btn', 'remove-lpco', 'form-control[type="text"]', 'lpco-list', 'lpcos');
    setupListManager('nfe-ref-section', 'add-nfe-ref-btn', 'remove-ref', 'nfe-ref-input', 'nfe-ref-list', 'nfsRefEletronicas');
    setupListManager('nf_form-ref-section', 'add-nf_form-ref-btn', 'remove-ref', 'nf_form-ref-input', 'nf_form-ref-list', 'nfsRefFormulario');
    setupListManager('nfc-ref-section', 'add-nfc-ref-btn', 'remove-ref', 'nfc-ref-input', 'nfc-ref-list', 'nfsComplementares');

    return container;
} // --- Fim createItemDetailsFields ---


// --- Renderização da Tabela de Itens (COM COLUNA STATUS POR ÚLTIMO) ---
function renderNotasFiscaisTable() {
    // ... (código da função renderNotasFiscaisTable como na resposta anterior - sem alterações aqui) ...
     console.log("[Render Tabela] Iniciando renderização..."); const tbody = document.querySelector('#notasFiscaisTable tbody'); const theadRow = document.querySelector('#notasFiscaisTable thead tr'); const batchButton = document.getElementById('batchEditButton'); if (!tbody || !theadRow) { console.error("Elem tbody/thead>tr #notasFiscaisTable ?"); return; } tbody.innerHTML = ''; let statusHeader = theadRow.querySelector('.status-header'); if (!statusHeader) { statusHeader = document.createElement('th'); statusHeader.textContent = 'Status DUE'; statusHeader.classList.add('status-header'); statusHeader.style.width = '80px'; statusHeader.style.textAlign = 'center'; theadRow.appendChild(statusHeader); } else if (statusHeader !== theadRow.lastElementChild) { theadRow.appendChild(statusHeader); } let hasItems = false; let totalItemsRendered = 0; const colCount = theadRow.cells.length; if (processedNFData.length === 0) { tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center text-muted fst-italic">Carregue arquivos XML...</td></tr>`; if (batchButton) batchButton.disabled = true; console.log("[Render Tabela] Nenhuma NF processada."); return; } processedNFData.forEach((nfEntry, nfIndex) => { const nf = nfEntry.nf; const items = nfEntry.items; const chaveNFeShort = getSafe(nf, 'chaveAcesso', 'N/A').slice(-9); const nomeDest = getSafe(nf, 'destinatario.nome', 'N/A'); const paisDest = getSafe(nf, 'destinatario.endereco.paisNome', 'N/A'); if (!items || !Array.isArray(items) || items.length === 0) { console.log(`[Render Tabela] NF ...${chaveNFeShort} sem itens.`); return; } items.forEach((item, itemIndex) => { if (!item) { console.warn(`[Render Tabela] Item inválido NF ${nfIndex}, idx ${itemIndex}`); return; } hasItems = true; totalItemsRendered++; const row = document.createElement('tr'); row.classList.add('item-row'); row.dataset.nfIndex = nfIndex; row.dataset.itemIndex = itemIndex; row.innerHTML = `<td>...${htmlspecialchars(chaveNFeShort)}</td> <td class="text-center">${htmlspecialchars(getSafe(item, 'nItem', itemIndex + 1))}</td> <td>${htmlspecialchars(getSafe(item, 'ncm', 'N/A'))}</td> <td>${htmlspecialchars(getSafe(item, 'xProd', 'N/A'))}</td> <td>${htmlspecialchars(nomeDest)}</td> <td>${htmlspecialchars(paisDest)}</td>`; const actionsCell = document.createElement('td'); actionsCell.classList.add('text-center'); const toggleBtn = document.createElement('button'); toggleBtn.type = 'button'; toggleBtn.classList.add('btn', 'toggle-details'); toggleBtn.title = `Detalhes Item ${htmlspecialchars(getSafe(item, 'nItem', itemIndex + 1))}`; toggleBtn.dataset.nfIndex = nfIndex; toggleBtn.dataset.itemIndex = itemIndex; toggleBtn.innerHTML = '+'; actionsCell.appendChild(toggleBtn); row.appendChild(actionsCell); const statusCell = document.createElement('td'); const completo = isItemDueComplete(item); statusCell.style.textAlign = 'center'; statusCell.style.verticalAlign = 'middle'; if (completo) { statusCell.innerHTML = '<span style="color: green; font-size: 1.2em; font-weight: bold;" title="Completo para DU-E">&#x2705;</span>'; } else { statusCell.innerHTML = '<span style="color: red; font-size: 1.2em; font-weight: bold;" title="Incompleto para DU-E">&#x274C;</span>'; } row.appendChild(statusCell); tbody.appendChild(row); }); }); if (!hasItems && processedNFData.length > 0) { tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center text-warning fst-italic">Nenhum item válido encontrado.</td></tr>`; if (batchButton) batchButton.disabled = true; } else if (hasItems) { if (batchButton) batchButton.disabled = false; console.log(`[Render Tabela] FIM. ${totalItemsRendered} itens.`); } else { tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center text-muted fst-italic">Carregue arquivos XML...</td></tr>`; if (batchButton) batchButton.disabled = true; }
}

// --- Preencher Campos da Aba 1 (Com infCpl) ---
const populateMainForm = (nfData) => {
    const cnpjValue = nfData ? getSafe(nfData, 'emitente.cnpj', '') : '';
    const nomeValue = nfData ? getSafe(nfData, 'emitente.nome', '') : '';
    const infCplValue = nfData ? getSafe(nfData, 'infAdicional.infCpl', '') : ''; // Pega infCpl

    const elCNPJ = document.getElementById('text-cnpj-cpf-select');
    const elNome = document.getElementById('nomeCliente');
    const elInfoCompl = document.getElementById('info-compl'); // Pega a textarea

    if (elCNPJ) elCNPJ.value = cnpjValue;
    if (elNome) elNome.value = nomeValue;
    if (elInfoCompl) elInfoCompl.value = infCplValue; // Preenche info complementar

    console.log(nfData ? `[Aba 1] Emitente: ${cnpjValue}. Info Compl preenchida.` : `[Aba 1] Limpando dados emitente/info compl.`);

    // Log dados de exportação (opcional)
    if(nfData && (nfData.exportacao?.ufSaidaPais || nfData.exportacao?.localExportacao)) {
         console.log(`[Aba 1] Info Exportação (Ref. Manual): UF Saída: ${nfData.exportacao.ufSaidaPais}, Local: ${nfData.exportacao.localExportacao}`);
     }
};

 // --- Código Principal (Inicialização e Listeners) ---
 document.addEventListener('DOMContentLoaded', () => {
     console.log("DOM Carregado. Iniciando script main.mjs.");

     // --- Verificação Dados PHP ---
     console.log("JS: Verificando dados pré-carregados do PHP...");
     if (typeof window.incotermsData === 'undefined' || !Array.isArray(window.incotermsData)) { console.warn("window.incotermsData não carregado/array."); window.incotermsData = []; }
     if (typeof window.enquadramentosData === 'undefined' || !Array.isArray(window.enquadramentosData)) { console.warn("window.enquadramentosData não carregado/array."); window.enquadramentosData = []; }
     if (typeof window.paisesData === 'undefined' || !Array.isArray(window.paisesData)) { console.warn("window.paisesData não carregado/array."); window.paisesData = []; }
     console.log("JS: Dados auxiliares verificados:", { incoterms: window.incotermsData, enquadramentos: window.enquadramentosData, paises: window.paisesData });

     // --- Referências UI ---
     const inputXML = document.getElementById('xml-files'); const uploadStatus = document.getElementById('uploadStatus'); const spinner = document.getElementById('spinner'); const notasTable = document.querySelector('#notasFiscaisTable'); const itemDetailsModalElement = document.getElementById('itemDetailsModal'); const saveItemButtonModal = document.getElementById('saveItemDetails'); const batchEditButton = document.getElementById('batchEditButton'); const batchEditModalElement = document.getElementById('batchEditModal'); const saveBatchButton = document.getElementById('saveBatchEdit'); const mainForm = document.getElementById('dueForm'); const gerarDueButton = document.getElementById('gerarDUE');

     // --- Verificação Elementos Essenciais ---
     if (!inputXML || !uploadStatus || !spinner || !notasTable || !itemDetailsModalElement || !saveItemButtonModal || !batchEditButton || !batchEditModalElement || !saveBatchButton || !mainForm || !gerarDueButton) { console.error("ERRO FATAL: Elementos UI!"); alert("Erro crítico inicialização."); return; }
     console.log("Elementos UI referenciados OK.");

     // --- Inicializar Modais ---
     try { /* ... código como antes ... */ if (window.bootstrap && bootstrap.Modal) { itemDetailsModalInstance = new bootstrap.Modal(itemDetailsModalElement); batchEditModalInstance = new bootstrap.Modal(batchEditModalElement); itemDetailsModalElement.addEventListener('hidden.bs.modal', () => { if (saveItemButtonModal) { delete saveItemButtonModal.dataset.nfIndex; delete saveItemButtonModal.dataset.itemIndex; } const mb = itemDetailsModalElement.querySelector('.modal-body'); if (mb) mb.innerHTML = '<div class="text-center p-5">...</div>'; console.log("Modal item fechado."); }); batchEditModalElement.addEventListener('hidden.bs.modal', () => { const bf = document.getElementById('batchEditForm'); if (bf) { bf.reset(); const ra = bf.querySelector('#batchCcptCcromAlterar'); if(ra) ra.checked = true; } console.log("Modal lote fechado."); }); console.log("Modais OK."); } else { throw new Error("Bootstrap Modal?"); } } catch (e) { console.error("Falha modais:", e); alert("Erro modais."); if(batchEditButton) batchEditButton.disabled = true; }

     // --- Renderizar Tabela Inicial ---
     renderNotasFiscaisTable();

     // --- Listener Input XML ---
     inputXML.addEventListener('change', async (event) => {
         console.log(">>> EVENTO 'change' DO INPUT DE ARQUIVO DETECTADO! <<<"); // LOG DE VERIFICAÇÃO
         console.log("[Input XML] Evento 'change' detectado."); const files = event.target.files; if (!files || files.length === 0) { uploadStatus.textContent = 'Nenhum selecionado.'; processedNFData = []; renderNotasFiscaisTable(); populateMainForm(null); console.log("[Input XML] Nenhum."); return; }
         uploadStatus.innerHTML = `<div class="spinner-grow spinner-grow-sm"></div> Proc ${files.length}...`; spinner.style.display = 'block'; inputXML.disabled = true; if (gerarDueButton) gerarDueButton.disabled = true; if (batchEditButton) batchEditButton.disabled = true;
         processedNFData = []; let promises = []; let errorCount = 0; let warningCount = 0; let statusMessagesHTML = ''; console.log(`[Input XML] Lendo ${files.length}...`);
         for (const file of files) { if (file.name.toLowerCase().endsWith('.xml') && file.type === 'text/xml') { promises.push( file.text().then(xml => { const data = parseNFeXML(xml, file.name); if (data?.items?.length > 0) { processedNFData.push({ nf: data, items: data.items }); statusMessagesHTML += `<div class="text-success small">${htmlspecialchars(file.name)}: OK (${data.items.length})</div>`; } else if (data) { statusMessagesHTML += `<div class="text-warning small">${htmlspecialchars(file.name)}: Sem itens.</div>`; warningCount++; } else { errorCount++; /* Msg erro no parse */ } }).catch(err => { console.error(`Erro LER ${file.name}:`, err); statusMessagesHTML += `<div class="text-danger small">Falha LER ${htmlspecialchars(file.name)}.</div>`; errorCount++; }) ); } else { statusMessagesHTML += `<div class="text-secondary small">${htmlspecialchars(file.name)}: Ignorado.</div>`; warningCount++; } }
         try { await Promise.all(promises); console.log("[Input XML] Promises OK."); } catch (err) { console.error("Erro GERAL async:", err); statusMessagesHTML += `<div class="text-danger">Erro inesperado.</div>`; errorCount++; }
         finally {
             spinner.style.display = 'none'; inputXML.disabled = false; if (gerarDueButton) gerarDueButton.disabled = (processedNFData.length === 0); event.target.value = null;
             const totalItems = processedNFData.reduce((sum, entry) => sum + (entry.items ? entry.items.length : 0), 0); const totalNFs = processedNFData.length; uploadStatus.innerHTML = statusMessagesHTML;
             if (totalItems > 0) { uploadStatus.insertAdjacentHTML('beforeend', `<hr class="my-1"><div class="text-primary fw-bold small">Total: ${totalItems} item(ns) em ${totalNFs} NF-e(s).</div>`); if (errorCount > 0) uploadStatus.insertAdjacentHTML('beforeend', `<div class="text-danger small">(${errorCount} erro(s))</div>`); populateMainForm(processedNFData[0]?.nf); }
             else if (totalNFs === 0 && errorCount === 0 && warningCount === 0) { uploadStatus.textContent = "Nenhuma NF-e válida."; populateMainForm(null); }
             else if (errorCount > 0) { uploadStatus.insertAdjacentHTML('beforeend', `<hr class="my-1"><div class="text-danger fw-bold small">Falha. Nenhum item. Ver console.</div>`); populateMainForm(null); }
             else { uploadStatus.insertAdjacentHTML('beforeend', `<hr class="my-1"><div class="text-warning small">Nenhum item válido.</div>`); populateMainForm(processedNFData[0]?.nf); }
             renderNotasFiscaisTable(); console.log("[Input XML] FIM.");
         }
     });

    // --- Listener Abrir Modal Item ---
    notasTable.addEventListener('click', (e) => {
        const detailsButton = e.target.closest('button.toggle-details'); if (!detailsButton) return;
        const nfIndex = parseInt(detailsButton.dataset.nfIndex, 10); const itemIndex = parseInt(detailsButton.dataset.itemIndex, 10);
        console.log(`[Abrir Modal Item] NF ${nfIndex}, Item ${itemIndex}`);
        if (isNaN(nfIndex) || isNaN(itemIndex) || !processedNFData[nfIndex]?.items?.[itemIndex]) { console.error("Índices/dados inválidos modal:", nfIndex, itemIndex, processedNFData); alert("Erro: Dados item não encontrados."); return; }
        try {
            const nfData = processedNFData[nfIndex].nf; const itemData = processedNFData[nfIndex].items[itemIndex];
            const modalBody = itemDetailsModalElement.querySelector('.modal-body'); const modalTitle = itemDetailsModalElement.querySelector('.modal-title');
            if (!modalBody || !modalTitle || !itemDetailsModalInstance || !saveItemButtonModal) { console.error("Modal elems?"); alert("Erro interno modal."); return; }
            modalTitle.textContent = `Detalhes Item ${htmlspecialchars(getSafe(itemData, 'nItem', itemIndex + 1))} (NF: ...${htmlspecialchars(getSafe(nfData, 'chaveAcesso', 'N/A').slice(-6))})`;
            modalBody.innerHTML = '<div class="text-center p-5"><div class="spinner-border"></div></div>'; // Spinner
            saveItemButtonModal.dataset.nfIndex = nfIndex; saveItemButtonModal.dataset.itemIndex = itemIndex;
            setTimeout(() => { // Gera conteúdo async
                try { console.time("createItemDetailsFields"); modalBody.innerHTML = ''; modalBody.appendChild(createItemDetailsFields(itemData, nfData, nfIndex, itemIndex)); console.timeEnd("createItemDetailsFields"); itemDetailsModalInstance.show(); console.log(`[Abrir Modal Item] Exibido.`); }
                catch (renderErr) { console.error("Erro renderizar modal:", renderErr); modalBody.innerHTML = `<div class="alert alert-danger">Erro carregar detalhes.</div>`; if (!itemDetailsModalInstance._isShown) itemDetailsModalInstance.show(); }
            }, 50);
        } catch (err) { console.error("Erro geral abrir modal:", err); alert(`Erro abrir detalhes: ${err.message}`); }
    });

    // --- Listener Salvar Modal Item (COM PARSEFLOAT E LOGS DETALHADOS) ---
    saveItemButtonModal.addEventListener('click', () => {
        const nfIndex = parseInt(saveItemButtonModal.dataset.nfIndex, 10); const itemIndex = parseInt(saveItemButtonModal.dataset.itemIndex, 10);
        console.log(`[Salvar Modal Item] Tentando salvar NF ${nfIndex}, Item ${itemIndex}`);
        if (isNaN(nfIndex) || isNaN(itemIndex) || !processedNFData[nfIndex]?.items?.[itemIndex]) { console.error("Ref. inválida salvar."); alert("Erro salvar item."); return; }

        const itemDataRef = processedNFData[nfIndex].items[itemIndex]; // Referência ao objeto original
        const idPrefix = `modal-item-${nfIndex}-${itemIndex}-`;
        const modalContent = itemDetailsModalElement.querySelector('.modal-body .item-details-form-container'); if (!modalContent) { console.error("Corpo modal salvar?"); return; }

        console.log("[Salvar Modal Item] --- Lendo valores do Modal ---");
        try {
            const getModalValue = (fieldIdSuffix, isNumber = false) => {
                const el = modalContent.querySelector(`#${idPrefix}${fieldIdSuffix}`);
                let value = el?.value?.trim() ?? null;
                if (value !== null && isNumber) {
                    const num = parseFloat(value.replace(',', '.')); // Tenta converter para número (aceita vírgula)
                    value = isNaN(num) ? null : num; // Se não for número válido, fica null
                }
                console.log(`  - Campo ${fieldIdSuffix}: lido '${el?.value}', processado para:`, value);
                return value;
            };
            const getModalRadioValue = (radioName) => {
                const el = modalContent.querySelector(`input[name="${radioName}"]:checked`);
                const value = el?.value ?? "";
                console.log(`  - Radio ${radioName}: selecionado '${value}'`);
                return value;
            };
            const getHiddenListValue = (hiddenInputName) => {
                const el = modalContent.querySelector(`input[name="${hiddenInputName}"]`);
                const value = (el?.value || '').split(',').filter(Boolean);
                console.log(`  - Lista ${hiddenInputName}: processado para:`, value);
                return value;
             };

             // Cria um objeto temporário com os NOVOS dados lidos
             const newData = {
                 ncm: getModalValue('ncm'),
                 descricaoNcm: getModalValue('descricao_ncm'),
                 atributosNcm: getModalValue('atributos_ncm'),
                 infAdProd: getModalValue('descricao_complementar'),
                 descricaoDetalhadaDue: getModalValue('descricao_detalhada_due'),
                 unidadeEstatistica: getModalValue('unidade_estatistica'),
                 quantidadeEstatistica: getModalValue('quantidade_estatistica', true), // true para número
                 pesoLiquidoItem: getModalValue('peso_liquido', true), // true para número
                 condicaoVenda: getModalValue('condicao_venda'),
                 vmle: getModalValue('vmle', true), // true para número
                 vmcv: getModalValue('vmcv', true), // true para número
                 paisDestino: getModalValue('pais_destino'), // Deve pegar o Código BACEN
                 enquadramento1: getModalValue('enquadramento1'),
                 enquadramento2: getModalValue('enquadramento2'),
                 enquadramento3: getModalValue('enquadramento3'),
                 enquadramento4: getModalValue('enquadramento4'),
                 lpcos: getHiddenListValue('lpcos'),
                 nfsRefEletronicas: getHiddenListValue('nfsRefEletronicas'),
                 nfsRefFormulario: getHiddenListValue('nfsRefFormulario'),
                 nfsComplementares: getHiddenListValue('nfsComplementares'),
                 ccptCcrom: getModalRadioValue('ccptCcrom')
             };

             console.log("[Salvar Modal Item] --- Atualizando objeto itemDataRef ---");
             console.log("  Objeto ANTES:", JSON.parse(JSON.stringify(itemDataRef)));

             // Atualiza o objeto itemDataRef com os novos dados
             Object.assign(itemDataRef, newData); // Usa Object.assign para mesclar

             console.log("  Objeto DEPOIS:", JSON.parse(JSON.stringify(itemDataRef)));

             alert("Dados do item atualizados com sucesso!");
             if (itemDetailsModalInstance) itemDetailsModalInstance.hide();

             // Log ANTES de re-renderizar para confirmar estado
             console.log(`[Salvar Modal Item] Estado final do item ${itemIndex} antes de renderizar:`, JSON.parse(JSON.stringify(processedNFData[nfIndex].items[itemIndex])));
             renderNotasFiscaisTable(); // RE-RENDERIZA a tabela para atualizar o status

         } catch (saveErr) { console.error("Erro durante a atualização:", saveErr); alert(`Erro ao salvar: ${saveErr.message}.`); }
     });

     // --- Listener Botão Lote ---
      batchEditButton.addEventListener('click', () => { /* ... código como antes ... */ });
      // --- Listener Salvar Modal Lote ---
      saveBatchButton.addEventListener('click', () => { /* ... código como antes ... */ });
      // --- Listener Abas ---
      const tabLinks = document.querySelectorAll('#dueTabs .nav-link'); if (tabLinks.length > 0 && typeof bootstrap !== 'undefined' && bootstrap.Tab) { /* ... código como antes ... */ } else { console.warn("Nav abas não config."); }
     // --- Listener Botão Gerar DUE (com validação) ---
     gerarDueButton.addEventListener('click', () => { /* ... código como antes ... */ });

    console.log("Script principal: Listeners configurados. Aplicação pronta.");

 }); // --- FIM DOMContentLoaded ---