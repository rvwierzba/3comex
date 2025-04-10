// --- Arquivo: due/js/main.mjs ---
// Versão com logs de depuração para preenchimento e lógica de populate revisada

// --- Funções Auxiliares ---
const getSafe = (obj, path, defaultValue = '') => { try { const value = path.split('.').reduce((o, k) => (o || {})[k], obj); return value ?? defaultValue; } catch { return defaultValue; } };
const getXmlValue = (el, tag) => el?.getElementsByTagName(tag)?.[0]?.textContent?.trim() ?? '';
const getXmlAttr = (el, attr) => el?.getAttribute(attr) ?? '';
const htmlspecialchars = (str) => { if (typeof str !== 'string') return str; return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;'); };

// --- Variáveis Globais ---
// window.processedNFData é inicializado pelo PHP em inserir.php
let itemDetailsModalInstance = null;
let batchEditModalInstance = null;

// --- Definição de Campos Obrigatórios para Status DUE (Controla o ícone ✅/❌) ---
const requiredDueFields = [ 'ncm', 'descricaoDetalhadaDue', 'unidadeEstatistica', 'quantidadeEstatistica', 'pesoLiquidoItem', 'condicaoVenda', 'vmcv', 'vmle', 'paisDestino', 'enquadramento1' ];
// console.log("Status check fields:", requiredDueFields); // Log opcional

// --- Função para verificar se item está completo (para status ✅/❌) ---
function isItemDueComplete(item) {
    if (!item) return false;
    return requiredDueFields.every(fieldName => {
        const value = item[fieldName]; let isFilled;
        if (Array.isArray(value)) { isFilled = value !== null && value !== undefined; }
        else if (typeof value === 'number') { isFilled = value !== null && value !== undefined && !isNaN(value); }
        else { isFilled = value !== null && value !== undefined && value.toString().trim() !== ''; }
        return isFilled;
    });
}

// --- Parser XML ---
const parseNFeXML = (xmlString, fileName = 'arquivo') => {
    console.log(`[main.mjs] Iniciando parseNFeXML para ${fileName}`);
    try {
        const parser = new DOMParser(); const xmlDoc = parser.parseFromString(xmlString, "text/xml"); const parserError = xmlDoc.getElementsByTagName("parsererror"); if (parserError.length > 0) { throw new Error(`Erro parse XML: ${parserError[0].textContent}`); } const infNFe = xmlDoc.getElementsByTagName("infNFe")[0]; if (!infNFe) { throw new Error(`<infNFe> não encontrada`); } const chave = getXmlAttr(infNFe, 'Id').replace('NFe', ''); const emit = infNFe.getElementsByTagName("emit")[0]; const dest = infNFe.getElementsByTagName("dest")[0]; const enderDest = dest?.getElementsByTagName("enderDest")[0]; const exporta = infNFe.getElementsByTagName("exporta")[0]; const infAdic = infNFe.getElementsByTagName("infAdic")[0]; const detElements = infNFe.getElementsByTagName("det"); const nfeData = { chaveAcesso: chave, emitente: { cnpj: getXmlValue(emit, "CNPJ"), nome: getXmlValue(emit, "xNome") }, destinatario: { nome: getXmlValue(dest, "xNome"), idEstrangeiro: getXmlValue(dest, "idEstrangeiro"), endereco: { logradouro: getXmlValue(enderDest, "xLgr"), numero: getXmlValue(enderDest, "nro"), bairro: getXmlValue(enderDest, "xBairro"), municipio: getXmlValue(enderDest, "xMun"), uf: getXmlValue(enderDest, "UF"), paisNome: getXmlValue(enderDest, "xPais"), paisCodigo: getXmlValue(enderDest, "cPais") } }, exportacao: { ufSaidaPais: getXmlValue(exporta, "UFSaidaPais"), localExportacao: getXmlValue(exporta, "xLocExporta") }, infAdicional: { infCpl: getXmlValue(infAdic, "infCpl"), infAdFisco: getXmlValue(infAdic, "infAdFisco") }, items: [] }; for (let i = 0; i < detElements.length; i++) { const det = detElements[i]; const prod = det.getElementsByTagName("prod")[0]; if (!prod) { console.warn(`[Parse Item ${i+1}] <prod> não encontrada.`); continue; } const nItem = getXmlAttr(det, 'nItem') || (i + 1).toString(); const xProdValue = getXmlValue(prod, "xProd"); const qCom = parseFloat(getXmlValue(prod, "qCom")) || 0; const vUnCom = parseFloat(getXmlValue(prod, "vUnCom")) || 0; const vProd = parseFloat(getXmlValue(prod, "vProd")) || 0; const qTrib = parseFloat(getXmlValue(prod, "qTrib")) || null; const pesoLiquidoXml = getXmlValue(prod, "pesoL") || getXmlValue(prod, "PESOL") || getXmlValue(prod, "PesoLiquido"); const pesoL = pesoLiquidoXml ? parseFloat(pesoLiquidoXml.replace(',', '.')) : null; const pesoLiquidoItem = isNaN(pesoL) ? null : pesoL; const paisDestinoInicial = getSafe(nfeData, 'destinatario.endereco.paisCodigo', null); nfeData.items.push({ nItem: nItem, cProd: getXmlValue(prod, "cProd"), xProd: xProdValue, ncm: getXmlValue(prod, "NCM"), cfop: getXmlValue(prod, "CFOP"), uCom: getXmlValue(prod, "uCom"), qCom: qCom, vUnCom: vUnCom, vProd: vProd, uTrib: getXmlValue(prod, "uTrib"), qTrib: qTrib, infAdProd: getXmlValue(det, "infAdProd"), descricaoNcm: "", atributosNcm: "", unidadeEstatistica: getXmlValue(prod, "uTrib"), quantidadeEstatistica: qTrib, pesoLiquidoItem: pesoLiquidoItem, condicaoVenda: "", vmcv: null, vmle: vProd, paisDestino: paisDestinoInicial, descricaoDetalhadaDue: xProdValue, enquadramento1: "", enquadramento2: "", enquadramento3: "", enquadramento4: "", lpcos: [], nfsRefEletronicas: [], nfsRefFormulario: [], nfsComplementares: [], ccptCcrom: "" }); } console.log(`[main.mjs] Parse XML OK: ${fileName} - ${nfeData.items.length} itens.`); return nfeData;
    } catch (error) { console.error(`[main.mjs] Erro GERAL Parse XML ${fileName}:`, error); const uploadStatusEl = document.getElementById('uploadStatus'); if(uploadStatusEl) uploadStatusEl.innerHTML += `<div class="text-danger small"><i class="bi bi-x-octagon-fill me-1"></i>Falha processar ${htmlspecialchars(fileName)}: ${htmlspecialchars(error.message)}</div>`; return null; }
};

// --- Função para Criar os Campos do Modal Detalhado ---
function createItemDetailsFields(itemData, nfData, nfIndex, itemIndex) {
    const container = document.createElement('div'); container.classList.add('item-details-form-container'); const idPrefix = `modal-item-${nfIndex}-${itemIndex}-`; const val = (key, defaultValue = '') => getSafe(itemData, key, defaultValue); const isSelected = (v, t) => (v!==null && t!==null && v==t)?'selected':''; const isChecked = (v, t) => v===t?'checked':'';
    const createOptions = (data, valueKey, textKey, selectedValue, includeEmpty = true) => { let optionsHtml = includeEmpty ? '<option value="">Selecione...</option>' : ''; if (data?.length) { optionsHtml += data.map(item => `<option value="${htmlspecialchars(getSafe(item, valueKey))}" ${isSelected(selectedValue, getSafe(item, valueKey))}>${htmlspecialchars(getSafe(item, textKey))}</option>`).join(''); } return optionsHtml; };
    const enqOptions = (num) => { let html = '<option value="">Selecione...</option>'; const enqData = window.enquadramentosData || []; const currentVal = val(`enquadramento${num}`); if(Array.isArray(enqData)) html += enqData.map(enq => `<option value="${htmlspecialchars(enq.CODIGO)}" ${isSelected(currentVal, enq.CODIGO)}>${htmlspecialchars(enq.CODIGO)} - ${htmlspecialchars(enq.DESCRICAO)}</option>`).join(''); html += `<option value="99999" ${isSelected(currentVal,'99999')}>99999 - OPERACAO SEM ENQUADRAMENTO</option>`; return html; };
    const enqSelectHTML = (num) => `<select id="${idPrefix}enquadramento${num}" name="enquadramento${num}" class="form-select form-select-sm">${enqOptions(num)}</select>`;
    const incotermTextMap = (window.incotermsData || []).map(i => ({ ...i, DisplayText: `${getSafe(i, 'Sigla')} - ${getSafe(i, 'Descricao')}` }));
    const incotermOptionsHTML = createOptions(incotermTextMap, 'Sigla', 'DisplayText', val('condicaoVenda'));
    // Ajuste: Usa CodigoBACEN como VALUE no select de país, mas busca por NOME no window.paisesData para texto
    const paisOptionsHTML = createOptions(window.paisesData || [], 'CodigoBACEN', 'Nome', val('paisDestino')); // Value = CodigoBACEN, Texto = Nome
    // (HTML interno do modal como antes, usando os helpers acima)
    container.innerHTML = ` <h5 class="mb-3 border-bottom pb-2">Item ${htmlspecialchars(val('nItem', itemIndex + 1))} (NF-e: ...${htmlspecialchars(getSafe(nfData, 'chaveAcesso', 'N/A').slice(-6))})</h5> <h6>Dados Básicos e NCM</h6> <div class="row g-3 mb-4"> <div class="col-md-6"> <label class="form-label">Exportador:</label> <input type="text" class="form-control form-control-sm bg-light" value="${htmlspecialchars(getSafe(nfData, 'emitente.nome', 'N/A'))}" readonly> </div> <div class="col-md-6"> <label for="${idPrefix}ncm" class="form-label">NCM:</label> <input type="text" id="${idPrefix}ncm" name="ncm" class="form-control form-control-sm" value="${htmlspecialchars(val('ncm'))}" required> </div> <div class="col-md-6"> <label for="${idPrefix}descricao_ncm" class="form-label">Descrição NCM:</label> <input type="text" id="${idPrefix}descricao_ncm" name="descricaoNcm" class="form-control form-control-sm" value="${htmlspecialchars(val('descricaoNcm'))}" placeholder="Consultar"> </div> <div class="col-md-6"> <label for="${idPrefix}atributos_ncm" class="form-label">Atributos NCM:</label> <input type="text" id="${idPrefix}atributos_ncm" name="atributosNcm" class="form-control form-control-sm" value="${htmlspecialchars(val('atributosNcm'))}" placeholder="Consultar/definir"> </div> </div><h6>Descrição Mercadoria</h6> <div class="mb-3"> <label for="${idPrefix}descricao_mercadoria" class="form-label">Descrição NF-e:</label> <textarea id="${idPrefix}descricao_mercadoria" class="form-control form-control-sm bg-light" rows="2" readonly>${htmlspecialchars(val('xProd'))}</textarea> </div> <div class="mb-3"> <label for="${idPrefix}descricao_complementar" class="form-label">Descrição Compl. (NF-e infAdProd):</label> <textarea id="${idPrefix}descricao_complementar" name="infAdProd" class="form-control form-control-sm" rows="2">${htmlspecialchars(val('infAdProd'))}</textarea> </div> <div class="mb-4"> <label for="${idPrefix}descricao_detalhada_due" class="form-label">Descrição Detalhada DU-E:</label> <textarea id="${idPrefix}descricao_detalhada_due" name="descricaoDetalhadaDue" class="form-control form-control-sm" rows="4" required>${htmlspecialchars(val('descricaoDetalhadaDue'))}</textarea> </div><h6>Quantidades e Valores</h6> <div class="row g-3 mb-4"> <div class="col-md-4"> <label for="${idPrefix}unidade_estatistica" class="form-label">Unid. Estatística:</label> <input type="text" id="${idPrefix}unidade_estatistica" name="unidadeEstatistica" class="form-control form-control-sm" value="${htmlspecialchars(val('unidadeEstatistica'))}" required> </div> <div class="col-md-4"> <label for="${idPrefix}quantidade_estatistica" class="form-label">Qtd. Estatística:</label> <input type="number" step="any" id="${idPrefix}quantidade_estatistica" name="quantidadeEstatistica" class="form-control form-control-sm" value="${htmlspecialchars(val('quantidadeEstatistica', ''))}" required> </div> <div class="col-md-4"> <label for="${idPrefix}peso_liquido" class="form-label">Peso Líquido Item (KG):</label> <input type="number" step="any" id="${idPrefix}peso_liquido" name="pesoLiquidoItem" class="form-control form-control-sm" value="${htmlspecialchars(val('pesoLiquidoItem', ''))}" required> </div> <div class="col-md-3"> <label for="${idPrefix}unidade_comercializada" class="form-label">Unid. Com.:</label> <input type="text" id="${idPrefix}unidade_comercializada" class="form-control form-control-sm bg-light" value="${htmlspecialchars(val('uCom'))}" readonly> </div> <div class="col-md-3"> <label for="${idPrefix}quantidade_comercializada" class="form-label">Qtd. Com.:</label> <input type="number" step="any" id="${idPrefix}quantidade_comercializada" class="form-control form-control-sm bg-light" value="${htmlspecialchars(val('qCom'))}" readonly> </div> <div class="col-md-3"> <label for="${idPrefix}valor_unit_comercial" class="form-label">Vlr Unit. Com. (R$):</label> <input type="number" step="any" id="${idPrefix}valor_unit_comercial" class="form-control form-control-sm bg-light" value="${htmlspecialchars(val('vUnCom'))}" readonly> </div> <div class="col-md-3"> <label class="form-label">Vlr Total (R$):</label> <input type="number" step="any" class="form-control form-control-sm bg-light" value="${htmlspecialchars(val('vProd'))}" readonly> </div> <div class="col-md-4"> <label for="${idPrefix}condicao_venda" class="form-label">Condição Venda:</label> <select id="${idPrefix}condicao_venda" name="condicaoVenda" class="form-select form-select-sm" required> ${incotermOptionsHTML} </select> </div> <div class="col-md-4"> <label for="${idPrefix}vmle" class="form-label">VMLE (R$):</label> <input type="number" step="any" id="${idPrefix}vmle" name="vmle" class="form-control form-control-sm" value="${htmlspecialchars(val('vmle', ''))}" required> </div> <div class="col-md-4"> <label for="${idPrefix}vmcv" class="form-label">VMCV (Moeda Negoc.):</label> <input type="number" step="any" id="${idPrefix}vmcv" name="vmcv" class="form-control form-control-sm" value="${htmlspecialchars(val('vmcv', ''))}" required> </div> </div><h6>Importador e Destino</h6> <div class="row g-3 mb-4"> <div class="col-md-6"> <label class="form-label">Nome Imp. (NF-e):</label> <input type="text" class="form-control form-control-sm bg-light" value="${htmlspecialchars(getSafe(nfData, 'destinatario.nome', 'N/A'))}" readonly> </div> <div class="col-md-6"> <label class="form-label">País Imp. (NF-e):</label> <input type="text" class="form-control form-control-sm bg-light" value="${htmlspecialchars(getSafe(nfData, 'destinatario.endereco.paisNome', 'N/A'))} (${htmlspecialchars(getSafe(nfData, 'destinatario.endereco.paisCodigo', 'N/A'))})" readonly> </div> <div class="col-12"> <label class="form-label">Endereço Imp. (NF-e):</label> <input type="text" class="form-control form-control-sm bg-light" value="${htmlspecialchars([getSafe(nfData, 'destinatario.endereco.logradouro'), getSafe(nfData, 'destinatario.endereco.numero'), getSafe(nfData, 'destinatario.endereco.bairro'), getSafe(nfData, 'destinatario.endereco.municipio'), getSafe(nfData, 'destinatario.endereco.uf')].filter(Boolean).join(', ') || '(Não informado)')}" readonly> </div> <div class="col-md-6"> <label for="${idPrefix}pais_destino" class="form-label">País Destino Final (DU-E):</label> <select id="${idPrefix}pais_destino" name="paisDestino" class="form-select form-select-sm" required> ${paisOptionsHTML} </select> </div> </div><h6>Enquadramentos</h6> <div class="row g-3 mb-4"> ${[1, 2, 3, 4].map(num => `<div class="col-md-6"> <label for="${idPrefix}enquadramento${num}" class="form-label">${num}º Enq.:</label> ${enqSelectHTML(num)} </div>`).join('')} <small class="text-muted">1º obrigatório.</small> </div><h6>LPCO</h6> <div class="lpco-container mb-4 list-manager-section" id="${idPrefix}lpco-section"> </div><h6>Referências e Trat. Trib.</h6> <div class="row g-3"> <div class="col-md-7"> </div> <div class="col-md-5"> <div class="border p-3 rounded h-100"> <h6 class="mb-3">Acordo Mercosul</h6> <div class="form-check mb-2"> <input class="form-check-input" type="radio" name="ccptCcrom" id="${idPrefix}ccpt_ccrom_none" value="" ${isChecked(val('ccptCcrom'), '')}> <label class="form-check-label small" for="${idPrefix}ccpt_ccrom_none">N/A</label> </div> <div class="form-check mb-2"> <input class="form-check-input" type="radio" name="ccptCcrom" id="${idPrefix}ccpt" value="CCPT" ${isChecked(val('ccptCcrom'), 'CCPT')}> <label class="form-check-label small" for="${idPrefix}ccpt">CCPT</label> </div> <div class="form-check"> <input class="form-check-input" type="radio" name="ccptCcrom" id="${idPrefix}ccrom" value="CCROM" ${isChecked(val('ccptCcrom'), 'CCROM')}> <label class="form-check-label small" for="${idPrefix}ccrom">CCROM</label> </div> </div> </div> </div> `;
    const setupListManager = (sectionElement) => { /* ... (código list manager como antes) ... */ };
    container.querySelectorAll('.list-manager-section, .lpco-container').forEach(setupListManager); return container;
}

// --- Renderização da Tabela de Itens ---
function renderNotasFiscaisTable() {
    console.log('[main.mjs] EXECUTANDO renderNotasFiscaisTable()');
    const tbody = document.querySelector('#notasFiscaisTable tbody');
    const theadRow = document.querySelector('#notasFiscaisTable thead tr');
    const batchButton = document.getElementById('batchEditButton');
    if (!tbody || !theadRow) { console.error("Tabela #notasFiscaisTable ou thead não encontrada."); return; }
    tbody.innerHTML = ''; // Limpa corpo

    // Garante cabeçalho Status DUE
    let statusHeader = theadRow.querySelector('.status-header');
    if (!statusHeader) { statusHeader = document.createElement('th'); statusHeader.textContent = 'Status DUE'; statusHeader.classList.add('status-header'); statusHeader.style.width = '80px'; statusHeader.style.textAlign = 'center'; theadRow.appendChild(statusHeader); }
    else if (statusHeader !== theadRow.lastElementChild) { theadRow.appendChild(statusHeader); } // Move para o final se já existe mas não está lá

    const colCount = theadRow.cells.length;
    let hasItems = false;
    let totalItemsRendered = 0;
    const currentNFData = window.processedNFData || []; // Usa dados globais

    if (!Array.isArray(currentNFData) || currentNFData.length === 0) {
        tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center text-muted fst-italic">Carregue arquivos XML ou dados existentes...</td></tr>`;
        if (batchButton) batchButton.disabled = true;
        return;
    }

    currentNFData.forEach((nfEntry, nfIndex) => {
        // Adaptação: A estrutura pode ser [{nf:{...}, items:[...]}, ...] ou só [{items:[...]}], ou só [item1, item2]
        // Vamos tratar o caso mais provável vindo do XML parse: [{ nf:{...}, items:[...] }]
        // E também o caso onde window.processedNFData pode ser apenas o array de items vindo do banco
        let nf = nfEntry.nf || {}; // Dados da NF (pode ser vazio se veio só items)
        let items = nfEntry.items || (Array.isArray(nfEntry) ? nfEntry : (nfEntry.nItem ? [nfEntry] : [])); // Pega items de dentro OU assume a entrada é o array de items OU um único item

        if (!Array.isArray(items) || items.length === 0) {
             console.warn(`[Render Table] Entrada ${nfIndex} sem array de 'items' válido.`);
             return; // Pula esta entrada se não achar itens
        }

        const chaveNFeShort = getSafe(nf, 'chaveAcesso', 'N/A').slice(-9);
        const nomeDest = getSafe(nf, 'destinatario.nome', 'Desconhecido'); // Nome do destinatário (pode não existir se dados vieram do banco sem NF completa)
        const paisDestCodXml = getSafe(nf, 'destinatario.endereco.paisCodigo', null);
        let paisDestNome = getSafe(nf, 'destinatario.endereco.paisNome', 'Desconhecido');

        items.forEach((item, itemIndex) => {
            if (!item || typeof item !== 'object') {
                 console.warn(`[Render Table] Item inválido no índice ${itemIndex} da NF ${nfIndex}`);
                 return; // Pula item inválido
            }
            hasItems = true;
            totalItemsRendered++;

            // Tenta pegar nome do país do item se não veio da NF ou se o código é diferente
            if (item.paisDestino && (!paisDestNome || paisDestNome === 'Desconhecido' || item.paisDestino !== paisDestCodXml) && window.paisesData) {
                 const paisEncontrado = window.paisesData.find(p => p.CodigoBACEN == item.paisDestino); // Compara com CodigoBACEN
                 if (paisEncontrado) paisDestNome = paisEncontrado.Nome;
                 else paisDestNome = `Código ${item.paisDestino}`;
            } else if (!paisDestNome || paisDestNome === 'Desconhecido') {
                 paisDestNome = item.paisDestino ? `Código ${item.paisDestino}` : 'N/A';
            }


            const row = document.createElement('tr');
            row.classList.add('item-row');
            row.dataset.nfIndex = nfIndex; // Ou um índice global se structure for diferente
            row.dataset.itemIndex = itemIndex;

            // Colunas: Chave, Item N., NCM, Descrição, Importador, País Imp., Ações, Status
            row.innerHTML = `
                <td>...${htmlspecialchars(chaveNFeShort)}</td>
                <td class="text-center">${htmlspecialchars(getSafe(item, 'nItem', itemIndex + 1))}</td>
                <td>${htmlspecialchars(getSafe(item, 'ncm', 'N/A'))}</td>
                <td>${htmlspecialchars(getSafe(item, 'xProd', 'N/A'))}</td>
                <td>${htmlspecialchars(nomeDest)}</td>
                <td>${htmlspecialchars(paisDestNome)}</td>
                <td class="text-center">
                    <button type="button" class="btn toggle-details" title="Detalhes Item ${htmlspecialchars(getSafe(item, 'nItem', itemIndex + 1))}" data-nf-index="${nfIndex}" data-item-index="${itemIndex}">+</button>
                </td>
            `;

            // Adiciona Célula de Status
            const statusCell = document.createElement('td');
            const completo = isItemDueComplete(item);
            statusCell.style.textAlign = 'center'; statusCell.style.verticalAlign = 'middle';
            statusCell.innerHTML = completo ? '<span style="color: green; font-size: 1.2em; font-weight: bold;" title="Completo">&#x2705;</span>' : '<span style="color: red; font-size: 1.2em; font-weight: bold;" title="Incompleto">&#x274C;</span>';
            row.appendChild(statusCell); // Adiciona no final

            tbody.appendChild(row);
        });
    });

    if (!hasItems && currentNFData.length > 0) {
        tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center text-warning fst-italic">Nenhum item válido encontrado nos dados carregados.</td></tr>`;
        if (batchButton) batchButton.disabled = true;
    } else if (hasItems) {
        if (batchButton) batchButton.disabled = false;
    } else { // Caso de currentNFData ser array vazio
        tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center text-muted fst-italic">Carregue XMLs ou dados salvos...</td></tr>`;
        if (batchButton) batchButton.disabled = true;
    }
}


// --- Preencher Campos da Aba 1 ---
// REVISADO para não limpar campos no modo edição
const populateMainForm = (nfData) => {
    console.log('[main.mjs] EXECUTANDO populateMainForm(). Dados recebidos (nfData):', nfData);
    const formElements = { cnpjCpf: document.getElementById('text-cnpj-cpf-select'), nomeCliente: document.getElementById('nomeCliente'), infoCompl: document.getElementById('info-compl'), /* outros se precisar */ };
    const editId = document.getElementById('due_id_hidden')?.value;

    if (nfData) { // Veio do XML Upload
        console.log('[main.mjs] populateMainForm: Preenchendo com dados do Upload XML.');
        if (formElements.cnpjCpf) formElements.cnpjCpf.value = getSafe(nfData, 'emitente.cnpj', '');
        if (formElements.nomeCliente) {
            formElements.nomeCliente.value = getSafe(nfData, 'emitente.nome', '');
            console.log(`[main.mjs] populateMainForm: Nome definido (XML): '${formElements.nomeCliente.value}'`);
        }
        // Só preenche infoCompl se estiver vazio
        if (formElements.infoCompl && !formElements.infoCompl.value.trim()) {
             formElements.infoCompl.value = getSafe(nfData, 'infAdicional.infCpl', '');
        }
    } else { // Chamado com null/undefined
         console.warn('[main.mjs] populateMainForm: Recebeu null/undefined nfData.');
         // NÃO FAZ NADA se estiver editando (ID existe), pois o PHP já preencheu.
         if (editId) {
             console.log('[main.mjs] populateMainForm: Modo Edição detectado (ID existe). Nenhum campo limpo por JS.');
         } else {
             // Se for NOVA DU-E (sem ID), pode resetar campos se necessário ou definir padrões
             console.log('[main.mjs] populateMainForm: Nova DU-E. Resetando radios padrão.');
             const radioPropria = document.getElementById('por-conta-propria');
             const radioNfe = document.getElementById('nfe');
             if(radioPropria) radioPropria.checked = true;
             if(radioNfe) radioNfe.checked = true;
             // Não limpar CNPJ/Nome aqui, pois podem ter sido preenchidos pelo PHP na carga inicial (embora não devesse no modo INSERT)
         }
    }
};


// --- Código Principal ---
document.addEventListener('DOMContentLoaded', () => {
    console.log('[main.mjs] DOM Carregado. Iniciando script...');
    console.log('[main.mjs] Verificando window.processedNFData inicial:', window.processedNFData);

    // --- Referências UI ---
    const inputXML = document.getElementById('xml-files');
    const uploadStatus = document.getElementById('uploadStatus');
    const spinner = document.getElementById('spinner');
    const notasTable = document.querySelector('#notasFiscaisTable');
    const itemDetailsModalElement = document.getElementById('itemDetailsModal');
    const saveItemButtonModal = document.getElementById('saveItemDetails'); // Precisa verificar ID
    const batchEditButton = document.getElementById('batchEditButton');
    const batchEditModalElement = document.getElementById('batchEditModal');
    const saveBatchButton = document.getElementById('saveBatchEdit'); // Precisa verificar ID
    const mainForm = document.getElementById('dueForm');
    const salvarDueButton = document.getElementById('salvarDUE');
    const enviarDueButton = document.getElementById('enviarDUE');
    const dueIdHiddenInput = document.getElementById('due_id_hidden');

    // --- Verificação Elementos Essenciais ---
    const essentialElements = { inputXML, uploadStatus, spinner, notasTable, itemDetailsModalElement, /*saveItemButtonModal,*/ batchEditButton, batchEditModalElement, /*saveBatchButton,*/ mainForm, salvarDueButton, enviarDueButton, dueIdHiddenInput };
     // Removido saveItemButtonModal e saveBatchButton da checagem inicial, pois podem não existir se modal não for carregado
    let missingElement = false;
    for (const key in essentialElements) {
        if (!essentialElements[key]) {
            console.error(`[main.mjs] ERRO FATAL: Elemento UI essencial NÃO encontrado: ${key}`);
            missingElement = true;
        }
    }
    // Checa botões de modal separadamente SE o modal existir
    if(itemDetailsModalElement && !document.getElementById('saveItemDetails')){
        console.error(`[main.mjs] ERRO FATAL: Botão #saveItemDetails não encontrado no modal.`);
        missingElement = true;
    }
     if(batchEditModalElement && !document.getElementById('saveBatchEdit')){
        console.error(`[main.mjs] ERRO FATAL: Botão #saveBatchEdit não encontrado no modal.`);
        missingElement = true;
    }

    if (missingElement) { alert("Erro crítico inicialização UI. Ver console (F12)."); if (salvarDueButton) salvarDueButton.disabled = true; if (enviarDueButton) enviarDueButton.disabled = true; return; }
    console.log("[main.mjs] Elementos UI essenciais OK.");

    // --- Inicializar Modais ---
    try {
        if (window.bootstrap?.Modal && itemDetailsModalElement && batchEditModalElement) {
            itemDetailsModalInstance = new bootstrap.Modal(itemDetailsModalElement);
            batchEditModalInstance = new bootstrap.Modal(batchEditModalElement);
            // Listeners hidden.bs.modal para limpar/resetar modais
            itemDetailsModalElement.addEventListener('hidden.bs.modal', () => { const btn = document.getElementById('saveItemDetails'); if(btn){ delete btn.dataset.nfIndex; delete btn.dataset.itemIndex; } const mb = itemDetailsModalElement.querySelector('.modal-body'); if(mb) mb.innerHTML = '<div class="text-center p-5">...</div>'; });
            batchEditModalElement.addEventListener('hidden.bs.modal', () => { const bf = document.getElementById('batchEditForm'); if(bf) { bf.reset(); const ra = bf.querySelector('#batchCcptCcromAlterar'); if(ra) ra.checked = true;} });
            console.log("[main.mjs] Modais Bootstrap OK.");
        } else { if(!window.bootstrap?.Modal) throw new Error("Bootstrap Modal JS não encontrado."); if(!itemDetailsModalElement) throw new Error("Modal #itemDetailsModal não encontrado."); if(!batchEditModalElement) throw new Error("Modal #batchEditModal não encontrado.");}
    } catch (e) { console.error("[main.mjs] Falha inicializar Modais:", e); /* Não desabilitar botão de lote ainda */ }

    // --- Renderizar Tabela Inicial (com dados do PHP, se houver) ---
    console.log('[main.mjs] Antes de chamar renderNotasFiscaisTable() inicial.');
    renderNotasFiscaisTable();
    console.log('[main.mjs] Depois de chamar renderNotasFiscaisTable() inicial.');

    // --- Listener Input XML ---
    if(inputXML) {
        inputXML.addEventListener('change', async (event) => {
            console.log("[main.mjs] XML Input 'change' event.");
            const files = event.target.files;
            if (!files?.length) { uploadStatus.innerHTML = 'Nenhum arquivo selecionado.'; return; }
            uploadStatus.innerHTML = `<div class="d-flex align-items-center"><div class="spinner-grow spinner-grow-sm me-2"></div> Processando ${files.length}...</div>`;
            spinner.style.display = 'block'; inputXML.disabled = true;

            let tempProcessedData = []; // Processa em array temporário
            let promises = []; let errorCount = 0; let warningCount = 0; let statusMessagesHTML = '';

            for (const file of files) {
                 if (file.name.toLowerCase().endsWith('.xml') && (file.type === 'text/xml' || file.type === 'application/xml' || file.type === '')) {
                     promises.push( file.text().then(xml => {
                         const data = parseNFeXML(xml, file.name);
                         if (data?.items?.length > 0) {
                             // Adiciona à estrutura temporária
                             tempProcessedData.push({ nf: data, items: data.items });
                             statusMessagesHTML += `<div class="text-success small"><i class="bi bi-check-circle-fill me-1"></i>${htmlspecialchars(file.name)}: OK (${data.items.length})</div>`;
                         } else if (data) { statusMessagesHTML += `<div class="text-warning small"><i class="bi bi-exclamation-triangle-fill me-1"></i>${htmlspecialchars(file.name)}: Sem itens.</div>`; warningCount++; }
                         else { errorCount++; /* Mensagem de erro já foi logada pelo parser */ }
                     }).catch(err => { console.error(`Erro LER ${file.name}:`, err); statusMessagesHTML += `<div class="text-danger small"><i class="bi bi-x-octagon-fill me-1"></i>Falha LER ${htmlspecialchars(file.name)}.</div>`; errorCount++; }) );
                 } else { statusMessagesHTML += `<div class="text-secondary small"><i class="bi bi-slash-circle-fill me-1"></i>${htmlspecialchars(file.name)}: Ignorado.</div>`; warningCount++; }
            }

            try { await Promise.all(promises); } catch (err) { console.error("[main.mjs] Erro GERAL no processamento async XML:", err); statusMessagesHTML += `<div class="text-danger">Erro inesperado no processamento.</div>`; errorCount++; }
            finally {
                spinner.style.display = 'none'; inputXML.disabled = false; event.target.value = null; // Limpa seleção
                uploadStatus.innerHTML = statusMessagesHTML;
                const totalItems = tempProcessedData.reduce((sum, entry) => sum + (entry.items?.length || 0), 0);
                const totalNFs = tempProcessedData.length;

                if (totalItems > 0) {
                    // ATENÇÃO: DECISÃO - Substituir dados existentes ou mesclar? Vamos SUBSTITUIR.
                    console.log(`[main.mjs] Upload concluído. ${totalItems} itens encontrados. SUBSTITUINDO dados existentes.`);
                    window.processedNFData = tempProcessedData; // Substitui dados globais
                    populateMainForm(window.processedNFData[0]?.nf); // Popula form com dados da PRIMEIRA NF carregada
                    uploadStatus.insertAdjacentHTML('beforeend', `<hr class="my-1"><div class="text-primary fw-bold small">Total: ${totalItems} item(ns) em ${totalNFs} NF(s) carregadas.</div>`);
                    if (errorCount > 0) uploadStatus.insertAdjacentHTML('beforeend', `<div class="text-danger small">(${errorCount} erro(s))</div>`);
                } else {
                    // Não encontrou itens válidos, não altera processedNFData existente
                    console.warn("[main.mjs] Upload concluído, mas nenhum item válido encontrado nos arquivos XML.");
                     if (errorCount > 0) { uploadStatus.insertAdjacentHTML('beforeend', `<hr class="my-1"><div class="text-danger fw-bold small">Falha no processamento XML.</div>`); }
                     else { uploadStatus.insertAdjacentHTML('beforeend', `<hr class="my-1"><div class="text-warning small">Nenhum item válido encontrado nos arquivos XML.</div>`); }
                }
                renderNotasFiscaisTable(); // Re-renderiza tabela com dados novos ou existentes
            }
        });
    }

    // --- Listener Abrir Modal Item ---
    if(notasTable) {
        notasTable.addEventListener('click', (e) => {
            const detailsButton = e.target.closest('button.toggle-details'); if (!detailsButton) return;
            const nfIndex = parseInt(detailsButton.dataset.nfIndex, 10); const itemIndex = parseInt(detailsButton.dataset.itemIndex, 10);
            if (isNaN(nfIndex) || isNaN(itemIndex) || !window.processedNFData?.[nfIndex]?.items?.[itemIndex]) { console.error("Índices/dados inválidos modal:", nfIndex, itemIndex, window.processedNFData); alert("Erro: Dados do item não encontrados."); return; }
            try {
                const nfData = window.processedNFData[nfIndex].nf || {}; // Usa NF do registro ou objeto vazio
                const itemData = window.processedNFData[nfIndex].items[itemIndex];
                const modalBody = itemDetailsModalElement?.querySelector('.modal-body');
                const modalTitle = itemDetailsModalElement?.querySelector('.modal-title');
                const saveBtn = document.getElementById('saveItemDetails'); // Pega o botão aqui

                if (!modalBody || !modalTitle || !itemDetailsModalInstance || !saveBtn) { console.error("Elementos do modal de detalhes não encontrados."); alert("Erro interno ao abrir detalhes."); return; }
                modalTitle.textContent = `Detalhes Item ${htmlspecialchars(getSafe(itemData, 'nItem', itemIndex + 1))} (NF: ...${htmlspecialchars(getSafe(nfData, 'chaveAcesso', 'N/A').slice(-6))})`;
                modalBody.innerHTML = '<div class="text-center p-5"><div class="spinner-border"></div></div>'; // Loading
                saveBtn.dataset.nfIndex = nfIndex; saveBtn.dataset.itemIndex = itemIndex; // Associa indices ao botão salvar
                setTimeout(() => { try { modalBody.innerHTML = ''; modalBody.appendChild(createItemDetailsFields(itemData, nfData, nfIndex, itemIndex)); itemDetailsModalInstance.show(); } catch (renderErr) { console.error("Erro renderizar modal:", renderErr); modalBody.innerHTML = `<div class="alert alert-danger">Erro carregar detalhes.</div>`; if (!itemDetailsModalInstance._isShown) itemDetailsModalInstance.show(); } }, 50);
            } catch (err) { console.error("Erro geral abrir modal:", err); alert(`Erro abrir detalhes: ${err.message}`); }
        });
    }

    // --- Listener Salvar Modal Item ---
    const saveItemBtn = document.getElementById('saveItemDetails');
    if (saveItemBtn) {
        saveItemBtn.addEventListener('click', () => {
             const nfIndex = parseInt(saveItemBtn.dataset.nfIndex, 10); const itemIndex = parseInt(saveItemBtn.dataset.itemIndex, 10);
             if (isNaN(nfIndex) || isNaN(itemIndex) || !window.processedNFData?.[nfIndex]?.items?.[itemIndex]) { console.error("Ref inválida salvar modal item."); alert("Erro salvar."); return; }
             const itemDataRef = window.processedNFData[nfIndex].items[itemIndex];
             const idPrefix = `modal-item-${nfIndex}-${itemIndex}-`; const modalContent = itemDetailsModalElement?.querySelector('.modal-body .item-details-form-container');
             if (!modalContent) { console.error("Conteúdo do modal não encontrado para salvar."); return; }
             try {
                 // Coleta dados do modal (lógica como antes)
                 const getModalValue = (idSuffix, num=false, flt=true) => { const el=modalContent.querySelector(`#${idPrefix}${idSuffix}`); let v = el?.value ?? null; if(v!==null){ v=v.trim(); if(num){ if(v===''){v=null;}else{const c=v.replace(',','.'); const n=flt?parseFloat(c):parseInt(c,10); v=isNaN(n)?null:n;}}} return v;};
                 const getModalRadio = (rName) => modalContent.querySelector(`input[name="${rName}"]:checked`)?.value ?? "";
                 const getHiddenList = (hName) => (modalContent.querySelector(`input[name="${hName}"]`)?.value || '').split(',').map(i=>i.trim()).filter(Boolean);
                 const newData = { ncm: getModalValue('ncm'), descricaoNcm: getModalValue('descricao_ncm'), atributosNcm: getModalValue('atributos_ncm'), infAdProd: getModalValue('descricao_complementar'), descricaoDetalhadaDue: getModalValue('descricao_detalhada_due'), unidadeEstatistica: getModalValue('unidade_estatistica'), quantidadeEstatistica: getModalValue('quantidade_estatistica', true, true), pesoLiquidoItem: getModalValue('peso_liquido', true, true), condicaoVenda: getModalValue('condicao_venda'), vmle: getModalValue('vmle', true, true), vmcv: getModalValue('vmcv', true, true), paisDestino: getModalValue('pais_destino'), enquadramento1: getModalValue('enquadramento1'), enquadramento2: getModalValue('enquadramento2'), enquadramento3: getModalValue('enquadramento3'), enquadramento4: getModalValue('enquadramento4'), lpcos: getHiddenList('lpcos'), nfsRefEletronicas: getHiddenList('nfsRefEletronicas'), nfsRefFormulario: getHiddenList('nfsRefFormulario'), nfsComplementares: getHiddenList('nfsComplementares'), ccptCcrom: getModalRadio('ccptCcrom') };
                 Object.assign(itemDataRef, newData); // Atualiza o objeto no array global
                 const btnTxt = saveItemBtn.innerHTML; saveItemBtn.innerHTML = `<span class="spinner-border spinner-border-sm"></span>`; saveItemBtn.disabled = true;
                 setTimeout(() => { itemDetailsModalInstance?.hide(); alert("Item atualizado localmente!"); saveItemBtn.innerHTML = btnTxt; saveItemBtn.disabled = false; renderNotasFiscaisTable(); }, 300); // Re-renderiza tabela principal
             } catch (saveErr) { console.error("Erro salvar item modal:", saveErr); alert(`Erro: ${saveErr.message}.`); saveItemBtn.innerHTML = 'Salvar Alterações Item'; saveItemBtn.disabled = false; }
        });
    }

    // --- Listener Botão Lote (Abrir Modal) ---
     if(batchEditButton) {
        batchEditButton.addEventListener('click', () => { if(!batchEditModalInstance){alert('Erro: Modal Lote não iniciado'); return;} if (!window.processedNFData?.length || window.processedNFData.every(nf => !nf.items?.length)) { alert("Não há itens para editar em lote."); return; } batchEditModalInstance.show(); });
     }

    // --- Listener Salvar Modal Lote ---
    const saveBatchBtn = document.getElementById('saveBatchEdit');
    if (saveBatchBtn) {
        saveBatchBtn.addEventListener('click', () => {
            const batchForm = document.getElementById('batchEditForm');
            if (!batchForm || !window.processedNFData?.length || window.processedNFData.every(nf => !nf.items?.length)) { alert("Form/itens não encontrados."); batchEditModalInstance?.hide(); return; }
            const incotermLote = batchForm.querySelector('#batchIncotermSelect')?.value;
            const paisNomeLoteInput = batchForm.querySelector('#batchPaisDestinoInput');
            const paisNomeLote = paisNomeLoteInput?.value.trim();
            let paisCodigoLote = null;
            // Achar código BACEN correspondente ao nome selecionado/digitado no Datalist
            if (paisNomeLote && window.paisesData) { const opt = Array.from(batchForm.querySelector('#paisesDestinoList')?.options || []).find(o => o.value.toLowerCase() === paisNomeLote.toLowerCase()); if (opt && opt.dataset.codigo) { paisCodigoLote = opt.dataset.codigo; } else { console.warn(`País Lote "${paisNomeLote}" não encontrado no datalist ou sem código BACEN.`); alert(`Atenção: País "${paisNomeLote}" não encontrado.`); } }
            const enqsLote = [1, 2, 3, 4].map(i => batchForm.querySelector(`#batchEnquadramento${i}Select`)?.value);
            const ccptCcromLote = batchForm.querySelector('input[name="batchCcptCcrom"]:checked')?.value;
            let itemsChangedCount = 0;
            window.processedNFData.forEach((nfEntry) => { if (nfEntry.items?.length) { nfEntry.items.forEach((item) => { let changed = false; if (incotermLote && item.condicaoVenda !== incotermLote) { item.condicaoVenda = incotermLote; changed = true; } if (paisCodigoLote && item.paisDestino !== paisCodigoLote) { item.paisDestino = paisCodigoLote; changed = true; } enqsLote.forEach((enq, i) => { const key = `enquadramento${i+1}`; if (enq && item[key] !== enq) { item[key] = enq; changed = true; } }); if (ccptCcromLote !== "" && ccptCcromLote !== "NA" && item.ccptCcrom !== ccptCcromLote) { item.ccptCcrom = ccptCcromLote; changed = true; } else if (ccptCcromLote === "NA" && item.ccptCcrom !== "") { item.ccptCcrom = ""; changed = true; } if (changed) itemsChangedCount++; }); } });
            batchEditModalInstance?.hide(); renderNotasFiscaisTable(); alert(`${itemsChangedCount} item(ns) atualizados localmente via lote.`); console.log(`[Edição Lote] ${itemsChangedCount} itens atualizados.`);
        });
    }

    // --- Listener Botão SALVAR DU-E ---
    if (salvarDueButton) {
        salvarDueButton.addEventListener('click', async () => {
             console.log("[main.mjs] Botão Salvar DU-E clicado.");
             spinner.style.display = 'block'; salvarDueButton.disabled = true; if(enviarDueButton) enviarDueButton.disabled = true;
             const formDataObj = {}; const formDataEntries = new FormData(mainForm);
             for (const [key, value] of formDataEntries.entries()) { /* ... lógica coleta como antes ... */ const el=mainForm.elements[key];if(el?.type==='checkbox'){formDataObj[key]=el.checked;}else if(el?.type==='radio'){const chk=mainForm.querySelector(`input[name="${key}"]:checked`);formDataObj[key]=chk?chk.value:null;}else if(el?.tagName==='SELECT'&&value===''){formDataObj[key]=null;}else{formDataObj[key]=value.trim()===''?null:value;} }
             formDataObj[HTML_DUE_ID_HIDDEN] = dueIdHiddenInput.value || null; // Usa constante HTML para ID
             const itemsToSave = window.processedNFData;
             // VALIDAÇÃO MÍNIMA: Garante que haja itens para salvar
             if (!Array.isArray(itemsToSave) || itemsToSave.reduce((count, nf) => count + (nf.items?.length || 0), 0) === 0) { alert("Não há itens válidos para salvar. Carregue um XML."); spinner.style.display = 'none'; salvarDueButton.disabled = false; if(enviarDueButton) enviarDueButton.disabled = !dueIdHiddenInput.value; return; }
             const payload = { formData: formDataObj, itemsData: itemsToSave };
             console.log("[main.mjs] Enviando payload para salvar_due.php:", payload);
             console.log(`>>> [main.mjs] Valor nomeCliente ENVIADO: '${formDataObj[HTML_DUE_NOME_CLIENTE]}'`); // Usa constante HTML

             try {
                 const response = await fetch('due/salvar_due.php', { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' }, body: JSON.stringify(payload) });
                 if (!response.ok) { const txt=await response.text(); throw new Error(`HTTP ${response.status}: ${txt}`); }
                 const result = await response.json();
                 if (result.success) { console.log("Salvo OK:", result); alert(result.message || 'Salvo!'); if (result.due_id) { dueIdHiddenInput.value = result.due_id; document.title = `DU-E: ${result.due_id}`; } if(enviarDueButton) enviarDueButton.disabled = false; }
                 else { console.error("Erro lógico salvar:", result); alert(`Falha: ${result.message || 'Erro servidor.'}`); if(enviarDueButton) enviarDueButton.disabled = true; }
             } catch (error) { console.error("[main.mjs] Erro fetch/salvar:", error); alert(`Erro comunicação servidor: ${error.message}.`); if(enviarDueButton) enviarDueButton.disabled = true; }
             finally { spinner.style.display = 'none'; salvarDueButton.disabled = false; if(enviarDueButton) enviarDueButton.disabled = !dueIdHiddenInput.value; }
        });
         console.log("[main.mjs] Listener Salvar Adicionado OK.");
    }

    // --- Listener Botão ENVIAR DU-E ---
     if (enviarDueButton) { enviarDueButton.addEventListener('click', () => { const id=dueIdHiddenInput.value; if(!id){alert('Salve primeiro.');return;} if(confirm(`Enviar DU-E ${id}?`)){alert(`Envio NÃO IMPLEMENTADO (DU-E: ${id}).`);}}); console.log("[main.mjs] Listener Enviar Adicionado OK."); }

    // --- Estado Inicial dos Botões ---
    if(salvarDueButton) salvarDueButton.disabled = false; // Habilitado por padrão, validação ocorre no clique/submit
    if(enviarDueButton) enviarDueButton.disabled = !dueIdHiddenInput.value; // Desabilitado se não tiver ID (nova DU-E)
    if(batchEditButton) batchEditButton.disabled = !(window.processedNFData && window.processedNFData.length > 0 && window.processedNFData.some(nf => nf.items?.length > 0)); // Desabilita se não houver itens iniciais

    console.log("[main.mjs] Script principal: Aplicação pronta.");
});