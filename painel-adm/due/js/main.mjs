// --- Arquivo: due/js/main.mjs ---

console.log('[main.mjs] Script carregado. VERSÃO: CORRECAO_IDS_FORM_PRINCIPAL');

// --- Estado do Módulo e Cache de Dados ---
const moduleState = {
    itemDetailsModalInstance: null,
    batchEditModalInstance: null,
    globalItemDetailsModalElement: null,
    globalBatchEditModalElement: null,
    dataSources: {
        paises: { cache: null, promise: null, url: '/3comex/painel-adm/due/ajax_buscar_paises.php', dataKey: 'paises', logPrefix: 'Paises' },
        recintos: { cache: null, promise: null, url: '/3comex/painel-adm/due/ajax_buscar_recintos.php', dataKey: 'recintos', logPrefix: 'Recintos' },
        unidadesRfb: { cache: null, promise: null, url: '/3comex/painel-adm/due/ajax_buscar_unidades_rfb.php', dataKey: 'unidades', logPrefix: 'UnidadesRFB' }
    }
};

// --- Constantes ---
const REQUIRED_DUE_FIELDS = [ 'ncm', 'descricaoDetalhadaDue', 'unidadeEstatistica', 'quantidadeEstatistica', 'pesoLiquidoItem', 'condicaoVenda', 'vmcv', 'paisDestino', 'enquadramento1' ];

// --- Funções Auxiliares ---
const getSafe = (obj, path, defaultValue = '') => { try { const value = path.split('.').reduce((o, k) => (o || {})[k], obj); return value !== null && value !== undefined ? value : defaultValue; } catch { return defaultValue; } };
const getXmlValue = (el, tag) => el?.getElementsByTagName(tag)?.[0]?.textContent?.trim() ?? '';
const getXmlAttr = (el, attr) => el?.getAttribute(attr) ?? '';
const htmlspecialchars = (str) => { if (typeof str !== 'string') str = String(str ?? ''); return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;'); };

// DENTRO DO SEU main.mjs
function showToast(message, type = 'info') {
    const toastContainer = document.getElementById('toast-container') || (() => {
        const tc = document.createElement('div');
        tc.id = 'toast-container';
        Object.assign(tc.style, { position: 'fixed', top: '20px', right: '20px', zIndex: '1090' });
        document.body.appendChild(tc);
        return tc;
    })();

    const toastEl = document.createElement('div');
    toastEl.className = `alert alert-${type === 'error' ? 'danger' : (type === 'success' ? 'success' : 'info')} alert-dismissible fade show m-1`;
    toastEl.setAttribute('role', 'alert');

    // Processa a mensagem para HTML seguro e lida com quebras de linha
    let processedMessage = String(message ?? ''); // Garante que é string
    processedMessage = processedMessage.replace(/\r\n|\r|\n/g, '<br>'); // Substitui quebras de linha por <br>
    const safeMessage = htmlspecialchars(processedMessage); // Escapa o HTML

    toastEl.innerHTML = `${safeMessage}<button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>`;
    toastContainer.appendChild(toastEl);

    if (window.bootstrap?.Alert && typeof bootstrap.Alert.getOrCreateInstance === 'function') {
        const bsAlert = bootstrap.Alert.getOrCreateInstance(toastEl);
        setTimeout(() => { if (toastEl.parentNode) bsAlert.close(); }, 5000);
    } else if (window.bootstrap?.Alert) { // Fallback para Bootstrap 5 mais antigo
        const bsAlert = new bootstrap.Alert(toastEl);
        setTimeout(() => { if (toastEl.parentNode) bsAlert.close(); }, 5000);
    } else {
        setTimeout(() => { if (toastEl.parentNode) { toastEl.classList.remove('show'); setTimeout(() => { if (toastEl.parentNode) toastEl.remove(); }, 150); } }, 5000);
    }
    console.log(`[Toast Executado] ${type.toUpperCase()}: ${message}`); // Log da mensagem original
}

// --- Funções de Busca de Dados AJAX com Cache ---
async function fetchGenericData(sourceName) {
    const source = moduleState.dataSources[sourceName]; if (!source) { console.error(`[FetchData][${sourceName}] Fonte desconhecida.`); return []; }
    const logPrefix = `[FetchData][${source.logPrefix}]`;
    if (source.cache && source.cache.length > 0) { console.log(`${logPrefix} Usando cache (${source.cache.length} itens).`); return source.cache; }
    if (source.promise) { console.log(`${logPrefix} Aguardando promise existente.`); return source.promise; }
    console.log(`${logPrefix} Buscando dados via AJAX: ${source.url}`);
    source.promise = fetch(source.url)
        .then(response => { if (!response.ok) { return response.text().then(text => { console.error(`${logPrefix} Erro HTTP ${response.status}. Resposta:`, text); throw new Error(`Erro HTTP ${response.status} ao buscar ${source.dataKey}.`); }); } return response.json(); })
        .then(data => {
            if (data.sucesso && data[source.dataKey] && Array.isArray(data[source.dataKey])) {
                source.cache = data[source.dataKey];
                console.log(`${logPrefix} Dados carregados: ${source.cache.length} ${source.dataKey}. Amostra [0]:`, source.cache.length > 0 ? JSON.stringify(source.cache[0]) : 'Array vazio');
                return source.cache;
            } else {
                console.error(`${logPrefix} Resposta AJAX inválida ou sem sucesso para ${source.dataKey}:`, data);
                source.cache = []; return [];
            }
        })
        .catch(error => { console.error(`${logPrefix} Erro CRÍTICO na requisição AJAX para ${source.dataKey}:`, error); showToast(`Erro ao carregar ${source.dataKey}. Verifique o console.`, 'error'); source.cache = []; return []; })
        .finally(() => { source.promise = null; });
    return source.promise;
}
const fetchPaisesDataIfNeeded = () => fetchGenericData('paises');
const fetchRecintosAduaneirosIfNeeded = () => fetchGenericData('recintos');
const fetchUnidadesRfbIfNeeded = () => fetchGenericData('unidadesRfb');

// --- Função Genérica de Autocomplete (input.value SEMPRE será o NOME) ---
function setupGenericAutocomplete(inputElement, dataProviderFn, options) {
    const AC_LOG_PREFIX = `[Autocomplete][${options.idPrefix || inputElement.id || 'unnamed'}]`;
    if (!inputElement) { console.error(`${AC_LOG_PREFIX} ERRO GRAVE: inputElement não fornecido para Autocomplete.`); return; }
    console.log(`${AC_LOG_PREFIX} Configurando para input: ${inputElement.id}. Opções:`, JSON.stringify(options));

    const {
        valueKey, displayKey,
        labelFormatter = item => `${item[displayKey] || ''} (${item[valueKey] || ''})`,
        idPrefix = `ac-${inputElement.id}-${Date.now().toString().slice(-5)}`,
        initialCodeToGetName = null,
        modalElement = null,
        sourceNameForDebug = 'unspecified',
        storeCodeCallback = (code, item) => {}
    } = options;

    const listContainerId = `${idPrefix}-list`;
    let listContainer = document.getElementById(listContainerId);
    if (listContainer) listContainer.remove();

    listContainer = document.createElement('div'); listContainer.id = listContainerId;
    listContainer.className = 'd-none position-absolute w-100 bg-white border rounded mt-1 shadow-sm';
    Object.assign(listContainer.style, { maxHeight: '200px', overflowY: 'auto', zIndex: modalElement ? '1055' : '1000' });
    if (getComputedStyle(inputElement.parentNode).position === 'static') inputElement.parentNode.style.position = 'relative';
    inputElement.parentNode.appendChild(listContainer);

    let currentData = []; let dataFetched = false; let fetchInProgress = false;

    const fetchDataAndSetInitialName = async () => {
        if (fetchInProgress) return; fetchInProgress = true;
        console.log(`${AC_LOG_PREFIX}[${sourceNameForDebug}] Buscando dados (chamada inicial/foco)...`);
        try {
            currentData = await dataProviderFn(); dataFetched = true;
            console.log(`${AC_LOG_PREFIX}[${sourceNameForDebug}] Dados recebidos: ${currentData.length} itens. Amostra[0]:`, currentData.length > 0 ? JSON.stringify(currentData[0]) : 'vazio');

            if (initialCodeToGetName !== null && initialCodeToGetName !== '' && currentData.length > 0) {
                console.log(`${AC_LOG_PREFIX}[${sourceNameForDebug}] Tentando encontrar item com ${valueKey} = '${initialCodeToGetName}'`);
                const selectedItem = currentData.find(i => i && String(i[valueKey]) === String(initialCodeToGetName));
                if (selectedItem && selectedItem[displayKey] !== undefined) {
                    inputElement.value = selectedItem[displayKey];
                    storeCodeCallback(selectedItem[valueKey], selectedItem);
                    console.log(`${AC_LOG_PREFIX}[${sourceNameForDebug}] Código inicial '${initialCodeToGetName}' ENCONTRADO. Input.value (NOME): '${inputElement.value}'`);
                } else {
                    console.warn(`${AC_LOG_PREFIX}[${sourceNameForDebug}] Código inicial '${initialCodeToGetName}' NÃO encontrado nos dados ou item não tem ${displayKey}. Input.value setado para o código como fallback.`);
                    inputElement.value = initialCodeToGetName;
                    storeCodeCallback(initialCodeToGetName, null);
                }
            } else if (initialCodeToGetName !== null && initialCodeToGetName !== '') {
                 console.warn(`${AC_LOG_PREFIX}[${sourceNameForDebug}] Código inicial '${initialCodeToGetName}' fornecido, mas currentData está vazio. Input.value setado para o código.`);
                 inputElement.value = initialCodeToGetName;
                 storeCodeCallback(initialCodeToGetName, null);
            } else {
                console.log(`${AC_LOG_PREFIX}[${sourceNameForDebug}] Sem código inicial válido fornecido. Limpando input.value.`);
                inputElement.value = '';
                storeCodeCallback(null, null);
            }
        } catch (error) { console.error(`${AC_LOG_PREFIX}[${sourceNameForDebug}] Erro ao buscar/processar dados iniciais:`, error); currentData = []; inputElement.value = ''; storeCodeCallback(null, null); }
        finally { fetchInProgress = false; }
    };

    const updateList = (searchTerm = '') => {
        const term = String(searchTerm).trim().toLowerCase();
        console.log(`${AC_LOG_PREFIX}[${sourceNameForDebug}] updateList com termo: "${term}" (Original: "${searchTerm}")`);
        if (!dataFetched && !fetchInProgress) {
             if (!dataFetched) {
                console.log(`${AC_LOG_PREFIX}[${sourceNameForDebug}] Dados não buscados, tentando buscar para updateList...`);
                fetchDataAndSetInitialName().then(() => updateList(searchTerm));
            }
            listContainer.innerHTML = '<div class="p-2 text-muted small">Carregando...</div>';
            listContainer.classList.remove('d-none'); return;
        }
        if (!dataFetched && fetchInProgress) { listContainer.innerHTML = '<div class="p-2 text-muted small">Carregando...</div>'; listContainer.classList.remove('d-none'); return; }

        listContainer.innerHTML = '';
        if (!currentData || currentData.length === 0) { console.warn(`${AC_LOG_PREFIX}[${sourceNameForDebug}] Sem dados (currentData) para filtrar.`); listContainer.innerHTML = `<div class="p-2 text-danger small">Lista de dados indisponível.</div>`; if (inputElement === document.activeElement || term) listContainer.classList.remove('d-none'); return; }

        const filteredData = currentData.filter(item => {
            if (!item) return false;
            const displayName = String(item[displayKey] || '').toLowerCase();
            const codeValue = String(item[valueKey] || '').toLowerCase();
            if (term === '' && inputElement === document.activeElement) return true;
            if (term === '') return false;
            return displayName.includes(term) || codeValue.includes(term);
        });
        console.log(`${AC_LOG_PREFIX}[${sourceNameForDebug}] Dados filtrados: ${filteredData.length} de ${currentData.length} para termo "${term}"`);
        if (filteredData.length > 0) {
            filteredData.forEach(item => {
                const itemDiv = document.createElement('div'); itemDiv.className = 'list-group-item list-group-item-action py-1 px-2'; Object.assign(itemDiv.style, { cursor: 'pointer' });
                itemDiv.textContent = labelFormatter(item);
                itemDiv.addEventListener('click', () => {
                    console.log(`${AC_LOG_PREFIX}[${sourceNameForDebug}] Item clicado:`, item);
                    inputElement.value = item[displayKey] || '';
                    storeCodeCallback(item[valueKey], item);
                    console.log(`${AC_LOG_PREFIX} Input.value (NOME) definido para: '${inputElement.value}'`);
                    listContainer.classList.add('d-none');
                    inputElement.dispatchEvent(new Event('change', { bubbles: true }));
                });
                listContainer.appendChild(itemDiv);
            });
            listContainer.classList.remove('d-none');
        } else {
            if (term.length > 0) listContainer.innerHTML = '<div class="p-2 text-muted small">Nenhum resultado encontrado.</div>'; else listContainer.classList.add('d-none');
            if (term.length > 0 || (currentData.length === 0 && term.length > 0)) listContainer.classList.remove('d-none');
        }
    };

    inputElement.addEventListener('input', (e) => updateList(e.target.value));
    inputElement.addEventListener('focus', () => {
        console.log(`${AC_LOG_PREFIX}[${sourceNameForDebug}] Input focado.`);
        if (!dataFetched) {
            fetchDataAndSetInitialName().then(() => updateList(inputElement.value));
        } else {
            updateList(inputElement.value);
        }
    });
    const clickOutsideHandler = (e) => { if (!listContainer.classList.contains('d-none') && !listContainer.contains(e.target) && e.target !== inputElement) { listContainer.classList.add('d-none'); } };
    document.addEventListener('click', clickOutsideHandler, true);
    if (modalElement) { const onModalHidden = () => { document.removeEventListener('click', clickOutsideHandler, true); modalElement.removeEventListener('hidden.bs.modal', onModalHidden); }; modalElement.removeEventListener('hidden.bs.modal', onModalHidden); modalElement.addEventListener('hidden.bs.modal', onModalHidden); }
    
    fetchDataAndSetInitialName();
}

// --- Validação de Item ---
function isItemDueComplete(item) { if (!item) return false; return REQUIRED_DUE_FIELDS.every(fieldName => { const value = item[fieldName]; if (Array.isArray(value)) return value.length > 0; if (typeof value === 'number') return !isNaN(value); return value !== null && value !== undefined && String(value).trim() !== ''; }); }

// --- Processamento de XML NFe ---
const parseNFeXML = (xmlString, fileName = 'arquivo') => { const PXML_LOG = `[ParseNFeXML][${fileName}]`; console.log(`${PXML_LOG} Iniciando.`); try { const parser = new DOMParser(); const xmlDoc = parser.parseFromString(xmlString, "text/xml"); const parserError = xmlDoc.getElementsByTagName("parsererror"); if (parserError.length > 0) { console.error(`${PXML_LOG} Erro no parse:`, parserError[0].textContent); throw new Error(`Erro parse XML: ${parserError[0].textContent}`); } const infNFe = xmlDoc.getElementsByTagName("infNFe")[0]; if (!infNFe) { console.error(`${PXML_LOG} <infNFe> não encontrada.`); throw new Error("<infNFe> não encontrada"); } const chave = getXmlAttr(infNFe, 'Id').replace('NFe', ''); const emit = infNFe.getElementsByTagName("emit")[0]; const dest = infNFe.getElementsByTagName("dest")[0]; const enderDest = dest?.getElementsByTagName("enderDest")[0]; const exporta = infNFe.getElementsByTagName("exporta")[0]; const infAdic = infNFe.getElementsByTagName("infAdic")[0]; const detElements = infNFe.getElementsByTagName("det"); const nfeData = { chaveAcesso: chave, emitente: { cnpj: getXmlValue(emit, "CNPJ"), nome: getXmlValue(emit, "xNome") }, destinatario: { nome: getXmlValue(dest, "xNome"), idEstrangeiro: getXmlValue(dest, "idEstrangeiro"), endereco: { logradouro: getXmlValue(enderDest, "xLgr"), numero: getXmlValue(enderDest, "nro"), bairro: getXmlValue(enderDest, "xBairro"), municipio: getXmlValue(enderDest, "xMun"), uf: getXmlValue(enderDest, "UF"), paisNome: getXmlValue(enderDest, "xPais"), paisCodigo: getXmlValue(enderDest, "cPais") } }, exportacao: { ufSaidaPais: getXmlValue(exporta, "UFSaidaPais"), localExportacao: getXmlValue(exporta, "xLocExporta"), codigoRecintoAduaneiro: getXmlValue(exporta, "xLocDespacho"), codigoUnidadeRfbDespacho: getXmlValue(exporta, "xLocDespacho"), codigoUnidadeRfbEmbarque: getXmlValue(exporta, "xLocEmbarque") }, infAdicional: { infCpl: getXmlValue(infAdic, "infCpl"), infAdFisco: getXmlValue(infAdic, "infAdFisco") }, items: [] }; for (let i = 0; i < detElements.length; i++) { const det = detElements[i]; const prod = det.getElementsByTagName("prod")[0]; if (!prod) { console.warn(`${PXML_LOG} Item ${i+1}: <prod> não encontrada.`); continue; } const nItem = getXmlAttr(det, 'nItem') || (i + 1).toString(); const xProdValue = getXmlValue(prod, "xProd"); const qCom = parseFloat(getXmlValue(prod, "qCom")) || 0; const vUnCom = parseFloat(getXmlValue(prod, "vUnCom")) || 0; const vProd = parseFloat(getXmlValue(prod, "vProd")) || 0; const qTrib = parseFloat(getXmlValue(prod, "qTrib")); const pesoLXml = getXmlValue(prod, "pesoL") || getXmlValue(prod, "PESOL") || getXmlValue(prod, "PesoLiquido"); const pesoL = pesoLXml ? parseFloat(pesoLXml.replace(',', '.')) : null; const paisDestinoInicialXML = getSafe(nfeData, 'destinatario.endereco.paisCodigo', null); nfeData.items.push({ nItem, cProd: getXmlValue(prod, "cProd"), xProd: xProdValue, ncm: getXmlValue(prod, "NCM"), cfop: getXmlValue(prod, "CFOP"), uCom: getXmlValue(prod, "uCom"), qCom, vUnCom, vProd, uTrib: getXmlValue(prod, "uTrib"), qTrib: isNaN(qTrib) ? null : qTrib, infAdProd: getXmlValue(det, "infAdProd"), descricaoNcm: "", atributosNcm: "", unidadeEstatistica: getXmlValue(prod, "uTrib"), quantidadeEstatistica: isNaN(qTrib) ? null : qTrib, pesoLiquidoItem: isNaN(pesoL) ? null : pesoL, condicaoVenda: "", vmcv: null, vmle: null, paisDestino: paisDestinoInicialXML, descricaoDetalhadaDue: xProdValue, enquadramento1: "", enquadramento2: "", enquadramento3: "", enquadramento4: "", lpcos: [], nfsRefEletronicas: [], nfsRefFormulario: [], nfsComplementares: [], ccptCcrom: "" }); } console.log(`${PXML_LOG} Parse OK: ${nfeData.items.length} itens.`, nfeData); return nfeData; } catch (error) { console.error(`${PXML_LOG} Erro GERAL:`, error); const uploadStatusEl = document.getElementById('uploadStatus'); if (uploadStatusEl) uploadStatusEl.innerHTML += `<div class="text-danger small"><i class="bi bi-x-octagon-fill me-1"></i>Falha processar ${htmlspecialchars(fileName)}: ${htmlspecialchars(error.message)}</div>`; return null; } };

// --- Criação dos Campos do Modal de Detalhes do Item ---
async function createItemDetailsFields(itemData, nfData, nfIndex, itemIndex) {
    const CIDF_LOG = `[CreateItemFields][${nfIndex}-${itemIndex}]`; console.log(`${CIDF_LOG} ItemData:`, itemData);
    await fetchPaisesDataIfNeeded(); const container = document.createElement('div'); container.classList.add('item-details-form-container');
    const idPrefix = `modal-item-${nfIndex}-${itemIndex}-`; const val = (key, defaultValue = '') => getSafe(itemData, key, defaultValue);
    const isSelected = (v, t) => (String(v) === String(t) && v !== '' && v !== null) ? 'selected' : ''; const isChecked = (v, t) => v === t ? 'checked' : '';
    const createOptions = (data, valueK, textK, selectedV, includeEmpty = true, formatter = null) => { let html = includeEmpty ? '<option value="">Selecione...</option>' : ''; if (data?.length) { html += data.map(item => { const text = formatter ? formatter(item) : getSafe(item, textK); return `<option value="${htmlspecialchars(getSafe(item, valueK))}" ${isSelected(selectedV, getSafe(item, valueK))}>${htmlspecialchars(text)}</option>`; }).join(''); } return html; };
    let nomePaisInicial = ''; if (itemData?.paisDestino) { const paises = moduleState.dataSources.paises.cache || []; const pais = paises.find(p => String(p.CODIGO_NUMERICO) === String(itemData.paisDestino)); nomePaisInicial = pais ? pais.NOME : `Cód: ${itemData.paisDestino}`; }

    container.innerHTML = `
        <h5 class="mb-3 border-bottom pb-2">Item ${htmlspecialchars(val('nItem', itemIndex + 1))} (NF-e: ...${htmlspecialchars(getSafe(nfData, 'chaveAcesso', 'N/A').slice(-6))})</h5>
        <h6>Dados Básicos e NCM</h6>
        <div class="row g-3 mb-4">
            <div class="col-md-6"><label class="form-label">Exportador:</label><input type="text" class="form-control form-control-sm bg-light" value="${htmlspecialchars(getSafe(nfData, 'emitente.nome', 'N/A'))}" readonly></div>
            <div class="col-md-6"><label for="${idPrefix}ncm" class="form-label">NCM:</label><input type="text" id="${idPrefix}ncm" name="ncm" class="form-control form-control-sm" value="${htmlspecialchars(val('ncm'))}" required></div>
            <div class="col-md-6"><label for="${idPrefix}descricao_ncm" class="form-label">Descrição NCM:</label><input type="text" id="${idPrefix}descricao_ncm" name="descricaoNcm" class="form-control form-control-sm" value="${htmlspecialchars(val('descricaoNcm'))}" placeholder="Consultar"></div>
            <div class="col-md-6"><label for="${idPrefix}atributos_ncm" class="form-label">Atributos NCM:</label><input type="text" id="${idPrefix}atributos_ncm" name="atributosNcm" class="form-control form-control-sm" value="${htmlspecialchars(val('atributosNcm'))}" placeholder="Consultar/definir"></div>
        </div>
        <h6>Descrição Mercadoria</h6>
        <div class="mb-3"><label for="${idPrefix}descricao_mercadoria" class="form-label">Descrição NF-e:</label><textarea id="${idPrefix}descricao_mercadoria" class="form-control form-control-sm bg-light" rows="2" readonly>${htmlspecialchars(val('xProd'))}</textarea></div>
        <div class="mb-3"><label for="${idPrefix}descricao_complementar" class="form-label">Descrição Complementar (Item):</label><textarea id="${idPrefix}descricao_complementar" name="infAdProd" class="form-control form-control-sm" rows="2">${htmlspecialchars(val('infAdProd'))}</textarea></div>
        <div class="mb-4"><label for="${idPrefix}descricao_detalhada_due" class="form-label">Descrição Detalhada DU-E:</label><textarea id="${idPrefix}descricao_detalhada_due" name="descricaoDetalhadaDue" class="form-control form-control-sm" rows="4" required>${htmlspecialchars(val('descricaoDetalhadaDue'))}</textarea></div>
        <h6>Quantidades e Valores</h6>
        <div class="row g-3 mb-4">
            <div class="col-md-4"><label for="${idPrefix}unidade_estatistica" class="form-label">Unid. Estatística:</label><input type="text" id="${idPrefix}unidade_estatistica" name="unidadeEstatistica" class="form-control form-control-sm" value="${htmlspecialchars(val('unidadeEstatistica'))}" required></div>
            <div class="col-md-4"><label for="${idPrefix}quantidade_estatistica" class="form-label">Qtd. Estatística:</label><input type="number" step="any" id="${idPrefix}quantidade_estatistica" name="quantidadeEstatistica" class="form-control form-control-sm" value="${htmlspecialchars(val('quantidadeEstatistica', ''))}" required></div>
            <div class="col-md-4"><label for="${idPrefix}peso_liquido" class="form-label">Peso Líquido (KG):</label><input type="number" step="any" id="${idPrefix}peso_liquido" name="pesoLiquidoItem" class="form-control form-control-sm" value="${htmlspecialchars(val('pesoLiquidoItem', ''))}" required></div>
            <div class="col-md-3"><label class="form-label">Unid. Comercial:</label><input type="text" class="form-control form-control-sm bg-light" value="${htmlspecialchars(val('uCom'))}" readonly></div>
            <div class="col-md-3"><label class="form-label">Qtd. Comercial:</label><input type="number" step="any" class="form-control form-control-sm bg-light" value="${htmlspecialchars(val('qCom'))}" readonly></div>
            <div class="col-md-3"><label class="form-label">Vlr Unit. Com.:</label><input type="number" step="any" class="form-control form-control-sm bg-light" value="${htmlspecialchars(val('vUnCom'))}" readonly></div>
            <div class="col-md-3"><label class="form-label">Vlr Total:</label><input type="number" step="any" class="form-control form-control-sm bg-light" value="${htmlspecialchars(val('vProd'))}" readonly></div>
            <div class="col-md-4"><label for="${idPrefix}condicao_venda" class="form-label">Condição Venda:</label><select id="${idPrefix}condicao_venda" name="condicaoVenda" class="form-select form-select-sm" required>${createOptions((window.incotermsData || []), 'Sigla', 'Sigla', val('condicaoVenda'), true, item => `${item.Sigla} - ${item.Descricao}`)}</select></div>
            <div class="col-md-4"><label for="${idPrefix}vmle" class="form-label">VMLE (R$):</label><input type="number" step="any" id="${idPrefix}vmle" name="vmle" class="form-control form-control-sm" value="${htmlspecialchars(val('vmle', ''))}" required></div>
            <div class="col-md-4"><label for="${idPrefix}vmcv" class="form-label">VMCV (Moeda Negociada):</label><input type="number" step="any" id="${idPrefix}vmcv" name="vmcv" class="form-control form-control-sm" value="${htmlspecialchars(val('vmcv', ''))}" required></div>
        </div>
        <h6>Importador e Destino</h6>
        <div class="row g-3 mb-4">
            <div class="col-md-6"><label class="form-label">Nome Importador (NF):</label><input type="text" class="form-control form-control-sm bg-light" value="${htmlspecialchars(getSafe(nfData, 'destinatario.nome', 'N/A'))}" readonly></div>
            <div class="col-md-6"><label class="form-label">País Importador (NF):</label><input type="text" class="form-control form-control-sm bg-light" value="${htmlspecialchars(getSafe(nfData, 'destinatario.endereco.paisNome', 'N/A'))} (${htmlspecialchars(getSafe(nfData, 'destinatario.endereco.paisCodigo', 'N/A'))})" readonly></div>
            <div class="col-12"><label class="form-label">Endereço (NF):</label><input type="text" class="form-control form-control-sm bg-light" value="${htmlspecialchars([getSafe(nfData, 'destinatario.endereco.logradouro'), getSafe(nfData, 'destinatario.endereco.numero'), getSafe(nfData, 'destinatario.endereco.bairro'), getSafe(nfData, 'destinatario.endereco.municipio'), getSafe(nfData, 'destinatario.endereco.uf')].filter(Boolean).join(', ') || 'Não informado')}" readonly></div>
            <div class="col-md-6">
                <label for="${idPrefix}pais_destino" class="form-label">País Destino Final (DU-E):</label>
                <input type="text" id="${idPrefix}pais_destino" class="form-control form-control-sm" value="${htmlspecialchars(nomePaisInicial)}" placeholder="Digite para buscar..." autocomplete="off" required>
            </div>
        </div>
        <h6>Enquadramentos</h6>
        <div class="row g-3 mb-4">
            ${[1,2,3,4].map(num => {
                const enquadramentoValue = val(`enquadramento${num}`);
                const optionsHtml = createOptions((window.enquadramentosData || []), 'CODIGO', 'CODIGO', enquadramentoValue, true, item => `${item.CODIGO} - ${item.DESCRICAO}`);
                const semEnquadramentoSelected = isSelected(enquadramentoValue, '99999');
                const requiredAttr = num === 1 ? 'required' : '';
                return `
                    <div class="col-md-6">
                        <label for="${idPrefix}enquadramento${num}" class="form-label">${num}º Enquadramento:</label>
                        <select id="${idPrefix}enquadramento${num}" name="enquadramento${num}" class="form-select form-select-sm" ${requiredAttr}>
                            ${optionsHtml}
                            <option value="99999" ${semEnquadramentoSelected}>99999 - SEM ENQUADRAMENTO</option>
                        </select>
                    </div>`;
            }).join('')}
        </div>
        <h6>Acordos Comerciais (Exportador Original)</h6>
        <div class="row g-3">
            <div class="col-md-5">
                <div class="border p-3 rounded">
                    <div class="form-check"><input class="form-check-input" type="radio" name="ccptCcrom" id="${idPrefix}ccpt_ccrom_none" value="" ${isChecked(val('ccptCcrom'), '')}><label class="form-check-label" for="${idPrefix}ccpt_ccrom_none">Nenhum</label></div>
                    <div class="form-check"><input class="form-check-input" type="radio" name="ccptCcrom" id="${idPrefix}ccpt" value="CCPT" ${isChecked(val('ccptCcrom'), 'CCPT')}><label class="form-check-label" for="${idPrefix}ccpt">CCPT</label></div>
                    <div class="form-check"><input class="form-check-input" type="radio" name="ccptCcrom" id="${idPrefix}ccrom" value="CCROM" ${isChecked(val('ccptCcrom'), 'CCROM')}><label class="form-check-label" for="${idPrefix}ccrom">CCROM</label></div>
                </div>
            </div>
        </div>
    `;

    const paisDestinoInput = container.querySelector(`#${idPrefix}pais_destino`);
    if (paisDestinoInput && moduleState.globalItemDetailsModalElement) {
        setupGenericAutocomplete(paisDestinoInput, fetchPaisesDataIfNeeded, {
            valueKey: 'CODIGO_NUMERICO', displayKey: 'NOME',
            labelFormatter: item => `${item.NOME || ''} (${item.CODIGO_NUMERICO || ''})`,
            initialCodeToGetName: itemData.paisDestino,
            idPrefix: `${idPrefix}pais-dest-modal`,
            sourceNameForDebug: 'paises_modal_item',
            modalElement: moduleState.globalItemDetailsModalElement,
            storeCodeCallback: (code, selectedItem) => {
                if (itemData) {
                    itemData.paisDestino = code || null;
                }
            }
        });
    }
    return container;
}

// --- Renderização da Tabela de Itens ---
function renderNotasFiscaisTable() { const RNF_LOG = '[RenderNFTable]'; console.log(`${RNF_LOG} Executando.`); const tbody = document.querySelector('#notasFiscaisTable tbody'); const theadRow = document.querySelector('#notasFiscaisTable thead tr'); const batchBtn = document.getElementById('batchEditButton'); if (!tbody || !theadRow) { console.error(`${RNF_LOG} Tabela ou thead não encontrada.`); return; } tbody.innerHTML = ''; let statusHdr = theadRow.querySelector('.status-header'); if (!statusHdr) { statusHdr = document.createElement('th'); statusHdr.textContent = 'Status DUE'; statusHdr.classList.add('status-header', 'text-center'); statusHdr.style.width = '80px'; theadRow.appendChild(statusHdr); } else if (statusHdr !== theadRow.lastElementChild) { theadRow.appendChild(statusHdr); } const colCount = theadRow.cells.length; let hasItems = false; const nfs = window.processedNFData || []; if (!Array.isArray(nfs) || nfs.length === 0) { tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center text-muted fst-italic">Carregue XMLs...</td></tr>`; if (batchBtn) batchBtn.disabled = true; return; } nfs.forEach((nfEntry, nfIdx) => { const nf = nfEntry.nf || {}; const items = nfEntry.items || []; if (!items.length) { console.warn(`${RNF_LOG} NF ${nfIdx} sem itens.`); return; } const chaveShort = getSafe(nf, 'chaveAcesso', 'N/A').slice(-9); const nomeDest = getSafe(nf, 'destinatario.nome', 'Desconhecido'); items.forEach((item, itemIdx) => { if (!item || typeof item !== 'object') { console.warn(`${RNF_LOG} Item inválido ${itemIdx} NF ${nfIdx}`); return; } hasItems = true; let paisDestDisp = 'N/A'; if (item.paisDestino) { const paises = moduleState.dataSources.paises.cache || []; const pFound = paises.find(p => String(p.CODIGO_NUMERICO) === String(item.paisDestino)); paisDestDisp = pFound ? pFound.NOME : `Cód: ${item.paisDestino}`; } const row = document.createElement('tr'); row.classList.add('item-row'); row.dataset.nfIndex = nfIdx; row.dataset.itemIndex = itemIdx; row.innerHTML = `<td>...${htmlspecialchars(chaveShort)}</td><td class="text-center">${htmlspecialchars(getSafe(item, 'nItem', itemIdx + 1))}</td><td>${htmlspecialchars(getSafe(item, 'ncm', 'N/A'))}</td><td>${htmlspecialchars(getSafe(item, 'xProd', 'N/A'))}</td><td>${htmlspecialchars(nomeDest)}</td><td>${htmlspecialchars(paisDestDisp)}</td><td class="text-center"><button type="button" class="btn btn-sm btn-outline-primary toggle-details" title="Detalhes Item ${htmlspecialchars(getSafe(item, 'nItem', itemIdx + 1))}" data-nf-index="${nfIdx}" data-item-index="${itemIdx}"><i class="bi bi-pencil-fill"></i></button></td>`; const statusCell = document.createElement('td'); const completo = isItemDueComplete(item); statusCell.style.textAlign = 'center'; statusCell.style.verticalAlign = 'middle'; statusCell.innerHTML = completo ? '<span class="text-success" title="Completo">✅</span>' : '<span class="text-danger" title="Incompleto">❌</span>'; row.appendChild(statusCell); tbody.appendChild(row); }); }); if (!hasItems && nfs.length > 0) { tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center text-warning fst-italic">Nenhum item válido.</td></tr>`; if (batchBtn) batchBtn.disabled = true; } else if (hasItems) { if (batchBtn) batchBtn.disabled = false; } else { tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center text-muted fst-italic">Carregue XMLs...</td></tr>`; if (batchBtn) batchBtn.disabled = true; } }

// --- Preencher Campos do Formulário Principal (Aba 1) ---
const populateMainForm = async (nfDataPrimeiraNF) => {
    const PMF_LOG = '[PopulateMainForm]'; console.log(`${PMF_LOG} Iniciando. Dados NF:`, nfDataPrimeiraNF);
    // !!! IDs ATUALIZADOS PARA CORRESPONDER AO SEU LOG [LogFormIDs] !!!
    const ID_CNPJ_EXP = 'text-cnpj-cpf-select', ID_NOME_EXP = 'nomeCliente', ID_INFO_COMPL = 'info-compl',
          ID_REC_ADU_D = 'text-campo-de-pesquisa-recinto-alfandegado-d',
          ID_REC_ADU_E = 'text-campo-de-pesquisa-recinto-alfandegado-e',
          ID_UND_DESP = 'text-campo-de-pesquisa-unidades-rfb-d',
          ID_UND_EMB = 'text-campo-de-pesquisa-unidades-rfb-e'; 
    
    const formEls = {
        cnpjCpf: document.getElementById(ID_CNPJ_EXP),
        nomeCliente: document.getElementById(ID_NOME_EXP),
        infoComplGeral: document.getElementById(ID_INFO_COMPL),
        recintoAduaneiroD: document.getElementById(ID_REC_ADU_D),
        recintoAduaneiroE: document.getElementById(ID_REC_ADU_E),
        unidadeRfbDespacho: document.getElementById(ID_UND_DESP),
        unidadeRfbEmbarque: document.getElementById(ID_UND_EMB)
    };

    if (!formEls.recintoAduaneiroD) console.error(`${PMF_LOG} ERRO: Elemento recintoAduaneiroD (ID: ${ID_REC_ADU_D}) NÃO ENCONTRADO!`);
    if (!formEls.recintoAduaneiroE) console.error(`${PMF_LOG} ERRO: Elemento recintoAduaneiroE (ID: ${ID_REC_ADU_E}) NÃO ENCONTRADO!`);
    if (!formEls.unidadeRfbDespacho) console.error(`${PMF_LOG} ERRO: Elemento unidadeRfbDespacho (ID: ${ID_UND_DESP}) NÃO ENCONTRADO!`);
    if (!formEls.unidadeRfbEmbarque) console.warn(`${PMF_LOG} AVISO: Elemento unidadeRfbEmbarque (ID: ${ID_UND_EMB}) não encontrado (pode ser opcional).`);

    const editId = document.getElementById('due_id_hidden')?.value;

    const setupAcComNomeNoValue = (element, dataProvider, initialCodeFromSource, sourceName) => {
        if (!element) { console.warn(`${PMF_LOG} Elemento para Autocomplete ${sourceName} é NULO.`); return; }
        console.log(`${PMF_LOG} Configurando Autocomplete para ${element.id} (source: ${sourceName}). InitialCode: '${initialCodeFromSource}'`);
        setupGenericAutocomplete(element, dataProvider, {
            valueKey: 'CODIGO',
            displayKey: 'NOME',
            labelFormatter: item => `${item.NOME || ''} (${item.CODIGO || ''})`,
            initialCodeToGetName: initialCodeFromSource,
            idPrefix: `${element.id}-ac`,
            sourceNameForDebug: sourceName
        });
    };

    if (nfDataPrimeiraNF && Object.keys(nfDataPrimeiraNF).length > 0) {
        console.log(`${PMF_LOG} Preenchendo com dados XML.`);
        if (formEls.cnpjCpf) formEls.cnpjCpf.value = getSafe(nfDataPrimeiraNF, 'emitente.cnpj', '');
        if (formEls.nomeCliente) formEls.nomeCliente.value = getSafe(nfDataPrimeiraNF, 'emitente.nome', '');
        if (formEls.infoComplGeral && !editId) formEls.infoComplGeral.value = getSafe(nfDataPrimeiraNF, 'infAdicional.infCpl', '');
        
        const recintoXMLCode = getSafe(nfDataPrimeiraNF, 'exportacao.codigoRecintoAduaneiro');
        const undDespXMLCode = getSafe(nfDataPrimeiraNF, 'exportacao.codigoUnidadeRfbDespacho');
        const undEmbXMLCode = getSafe(nfDataPrimeiraNF, 'exportacao.codigoUnidadeRfbEmbarque');
        console.log(`${PMF_LOG} Códigos do XML: Recinto='${recintoXMLCode}', UndDesp='${undDespXMLCode}', UndEmb='${undEmbXMLCode}'`);

        setupAcComNomeNoValue(formEls.recintoAduaneiroD, fetchRecintosAduaneirosIfNeeded, recintoXMLCode, 'recintos_main');
        setupAcComNomeNoValue(formEls.recintoAduaneiroE, fetchRecintosAduaneirosIfNeeded, recintoXMLCode, 'recintos_main');
        setupAcComNomeNoValue(formEls.unidadeRfbDespacho, fetchUnidadesRfbIfNeeded, undDespXMLCode, 'unidadesRfb_main_desp');
        if (formEls.unidadeRfbEmbarque) setupAcComNomeNoValue(formEls.unidadeRfbEmbarque, fetchUnidadesRfbIfNeeded, undEmbXMLCode, 'unidadesRfb_main_emb');

    } else if (editId && window.dueDataPrincipalPHP) {
        console.log(`${PMF_LOG} Preenchendo com dados de edição (PHP).`);
        const php = window.dueDataPrincipalPHP;
        if (formEls.cnpjCpf) formEls.cnpjCpf.value = getSafe(php, 'cnpj_exportador', '');
        if (formEls.nomeCliente) formEls.nomeCliente.value = getSafe(php, 'nome_exportador', '');
        if (formEls.infoComplGeral) formEls.infoComplGeral.value = getSafe(php, 'info_complementar_geral', '');
        
        // ** MUITO IMPORTANTE: AJUSTE ESTAS CHAVES para os NOMES DOS CAMPOS que contêm os CÓDIGOS no seu objeto window.dueDataPrincipalPHP **
        const recintoDBCode = getSafe(php, 'AQUI_CHAVE_CODIGO_RECINTO_PHP');
        const undDespDBCode = getSafe(php, 'AQUI_CHAVE_CODIGO_UND_DESP_PHP');
        const undEmbDBCode = getSafe(php, 'AQUI_CHAVE_CODIGO_UND_EMB_PHP'); 

        console.log(`${PMF_LOG} Códigos do DB (PHP): Recinto='${recintoDBCode}', UndDesp='${undDespDBCode}', UndEmb='${undEmbDBCode}'`);

        setupAcComNomeNoValue(formEls.recintoAduaneiroD, fetchRecintosAduaneirosIfNeeded, recintoDBCode, 'recintos_main_edit');
        setupAcComNomeNoValue(formEls.recintoAduaneiroE, fetchRecintosAduaneirosIfNeeded, recintoDBCode, 'recintos_main_edit');
        setupAcComNomeNoValue(formEls.unidadeRfbDespacho, fetchUnidadesRfbIfNeeded, undDespDBCode, 'unidadesRfb_main_desp_edit');
        if (formEls.unidadeRfbEmbarque) setupAcComNomeNoValue(formEls.unidadeRfbEmbarque, fetchUnidadesRfbIfNeeded, undEmbDBCode, 'unidadesRfb_main_emb_edit');
    } else {
        console.warn(`${PMF_LOG} Nova DU-E sem XML/dados de edição. Configurando campos vazios.`);
        if (formEls.infoComplGeral) formEls.infoComplGeral.value = '';
        setupAcComNomeNoValue(formEls.recintoAduaneiroD, fetchRecintosAduaneirosIfNeeded, null, 'recintos_main_new');
        setupAcComNomeNoValue(formEls.recintoAduaneiroE, fetchRecintosAduaneirosIfNeeded, null, 'recintos_main_new');
        setupAcComNomeNoValue(formEls.unidadeRfbDespacho, fetchUnidadesRfbIfNeeded, null, 'unidadesRfb_main_desp_new');
        if (formEls.unidadeRfbEmbarque) setupAcComNomeNoValue(formEls.unidadeRfbEmbarque, fetchUnidadesRfbIfNeeded, null, 'unidadesRfb_main_emb_new');
        const radioPropria = document.getElementById('por-conta-propria'); if(radioPropria && !radioPropria.checked && !document.querySelector('input[name="tipo_operacao_due"]:checked')) radioPropria.checked = true;
        const radioNfe = document.getElementById('nfe'); if(radioNfe && !radioNfe.checked && !document.querySelector('input[name="tipo_documento_base"]:checked')) radioNfe.checked = true;
    }
};

// --- Código Principal (DOMContentLoaded) ---
document.addEventListener('DOMContentLoaded', () => {
    const DCL_LOG = '[DOMContentLoaded]'; console.log(`${DCL_LOG} Iniciando...`);
    moduleState.globalItemDetailsModalElement = document.getElementById('itemDetailsModal');
    moduleState.globalBatchEditModalElement = document.getElementById('batchEditModal');
    const inputXML = document.getElementById('xml-files'), uploadStatus = document.getElementById('uploadStatus'),
          spinner = document.getElementById('spinner'), notasTableQuery = '#notasFiscaisTable',
          batchEditBtn = document.getElementById('batchEditButton'), mainForm = document.getElementById('dueForm'),
          salvarDueBtn = document.getElementById('salvarDUE'), enviarDueBtn = document.getElementById('enviarDUE'),
          dueIdHidden = document.getElementById('due_id_hidden');

    if (!mainForm) {
        console.error(`${DCL_LOG} ERRO FATAL: Formulário principal #dueForm NÃO encontrado. O script não pode prosseguir.`);
        showToast("Erro crítico: Formulário principal #dueForm não encontrado.", "error");
        return; 
    }
    const notasTableElement = document.querySelector(notasTableQuery);
    if (!notasTableElement) console.warn(`${DCL_LOG} AVISO: Tabela ${notasTableQuery} não encontrada.`);
    
    logTodosOsElementosNoFormularioPrincipal();

    try { if (window.bootstrap?.Modal) { if (moduleState.globalItemDetailsModalElement) moduleState.itemDetailsModalInstance = new bootstrap.Modal(moduleState.globalItemDetailsModalElement); if (moduleState.globalBatchEditModalElement) { moduleState.batchEditModalInstance = new bootstrap.Modal(moduleState.globalBatchEditModalElement); moduleState.globalBatchEditModalElement.addEventListener('hidden.bs.modal', () => { const bf = moduleState.globalBatchEditModalElement.querySelector('#batchEditForm'); if (bf) { bf.reset(); const r = bf.querySelector('input[name="batchCcptCcromModal"][value=""]'); if(r) r.checked = true;} }); } } else console.warn(`${DCL_LOG} Bootstrap Modal não encontrado.`); } catch (e) { console.error(`${DCL_LOG} Falha inicializar Modais:`, e); }
    
    renderNotasFiscaisTable();
    
    // CORREÇÃO PARA window.processedNFData = 0
    if (typeof window.processedNFData === 'number' && window.processedNFData === 0) {
        console.warn(`${DCL_LOG} window.processedNFData era 0. Convertendo para array vazio []. CORRIJA A INICIALIZAÇÃO NO SEU PHP.`);
        window.processedNFData = [];
    } else if (!Array.isArray(window.processedNFData)) {
        console.warn(`${DCL_LOG} window.processedNFData não é um array. Definindo como array vazio []. Verifique a inicialização no PHP. Valor atual:`, window.processedNFData);
        window.processedNFData = [];
    }

    const initialNF = window.processedNFData?.[0]?.nf;
    populateMainForm(initialNF || (window.dueDataPrincipalPHP ? null : null) );
    
    Promise.all([fetchPaisesDataIfNeeded(), fetchRecintosAduaneirosIfNeeded(), fetchUnidadesRfbIfNeeded()])
        .then(() => {
            console.log(`${DCL_LOG} Dados de lookup (países, recintos, unidades) pré-carregados ou busca iniciada.`);
        })
        .catch(err => console.error(`${DCL_LOG} Erro no pré-carregamento de dados de lookup:`, err));

    if (inputXML) { inputXML.addEventListener('change', async (event) => { const IXML_LOG = `${DCL_LOG}[InputXML]`; console.log(`${IXML_LOG} Evento 'change'.`); const files = event.target.files; if (!files?.length) { if(uploadStatus) uploadStatus.innerHTML = 'Nenhum arquivo selecionado.'; return; } if(uploadStatus) uploadStatus.innerHTML = `<div class="d-flex align-items-center"><div class="spinner-border spinner-border-sm me-2 text-primary"></div>Processando ${files.length} arquivo(s)...</div>`; if(spinner) spinner.style.display = 'flex'; inputXML.disabled = true; let tempProcData = [], promises = [], errCount = 0, statusHTML = ''; for (const file of files) { if (file.name.toLowerCase().endsWith('.xml') && (file.type === 'text/xml' || file.type === 'application/xml' || file.type === '')) { promises.push( file.text().then(xml => { const data = parseNFeXML(xml, file.name); if (data?.items?.length > 0) { tempProcData.push({ nf: data, items: data.items }); statusHTML += `<div class="alert alert-success alert-sm py-1 px-2 mb-1 small"><i class="bi bi-check-circle-fill me-1"></i>${htmlspecialchars(file.name)}: OK (${data.items.length} itens)</div>`; } else if (data) { statusHTML += `<div class="alert alert-warning alert-sm py-1 px-2 mb-1 small"><i class="bi bi-exclamation-triangle-fill me-1"></i>${htmlspecialchars(file.name)}: XML válido, sem itens.</div>`; } else { errCount++; } }).catch(err => { console.error(`${IXML_LOG} Erro ao LER ${file.name}:`, err); statusHTML += `<div class="alert alert-danger alert-sm py-1 px-2 mb-1 small"><i class="bi bi-x-octagon-fill me-1"></i>Falha LER ${htmlspecialchars(file.name)}.</div>`; errCount++; }) ); } else { statusHTML += `<div class="alert alert-secondary alert-sm py-1 px-2 mb-1 small"><i class="bi bi-slash-circle-fill me-1"></i>${htmlspecialchars(file.name)}: Ignorado (não XML).</div>`; } } try { await Promise.all(promises); } catch (err) { console.error(`${IXML_LOG} Erro GERAL async XML:`, err); statusHTML += `<div class="alert alert-danger mt-2">Erro inesperado no processamento.</div>`; errCount++; } finally { if(spinner) spinner.style.display = 'none'; inputXML.disabled = false; if (event.target) event.target.value = null; if(uploadStatus) uploadStatus.innerHTML = statusHTML; const totalItens = tempProcData.reduce((s, e) => s + (e.items?.length || 0), 0); if (totalItens > 0) { console.log(`${IXML_LOG} Sucesso: ${totalItens} itens em ${tempProcData.length} NFs. SUBSTITUINDO dados.`); window.processedNFData = tempProcData; await populateMainForm(window.processedNFData[0]?.nf); if(uploadStatus) uploadStatus.insertAdjacentHTML('beforeend', `<hr class="my-1"><div class="alert alert-primary alert-sm py-1 px-2 small fw-bold">Total: ${totalItens} item(ns) em ${tempProcData.length} NF(s) carregadas.</div>`); } else { console.warn(`${IXML_LOG} Concluído, sem itens válidos.`); if (!errCount && uploadStatus) uploadStatus.insertAdjacentHTML('beforeend', `<hr class="my-1"><div class="alert alert-warning alert-sm py-1 px-2 small">Nenhum item válido encontrado.</div>`); else if (errCount > 0 && uploadStatus) uploadStatus.insertAdjacentHTML('beforeend', `<hr class="my-1"><div class="alert alert-danger alert-sm py-1 px-2 small fw-bold">Houve ${errCount} erro(s).</div>`); } renderNotasFiscaisTable(); } }); }
    
    const notasTableElementForModal = document.querySelector(notasTableQuery);
    if (notasTableElementForModal && moduleState.itemDetailsModalInstance) {
        notasTableElementForModal.addEventListener('click', async (e) => {
            const MODAL_ITEM_LOG = `${DCL_LOG}[ModalItem]`; const btn = e.target.closest('button.toggle-details'); if (!btn) return;
            console.log(`${MODAL_ITEM_LOG} Botão detalhes clicado.`); const nfIdx = parseInt(btn.dataset.nfIndex, 10), itemIdx = parseInt(btn.dataset.itemIndex, 10);
            if (isNaN(nfIdx) || isNaN(itemIdx) || !window.processedNFData?.[nfIdx]?.items?.[itemIdx]) { console.error(`${MODAL_ITEM_LOG} Índices/dados inválidos. NFidx: ${nfIdx}, ItemIdx: ${itemIdx}`); showToast("Erro: Dados do item não encontrados.", "error"); return; }
            const modalBody = moduleState.globalItemDetailsModalElement.querySelector('.modal-body'); const modalTitle = moduleState.globalItemDetailsModalElement.querySelector('.modal-title'); const saveBtn = moduleState.globalItemDetailsModalElement.querySelector('#saveItemDetails');
            if (!modalBody || !modalTitle || !saveBtn) { console.error(`${MODAL_ITEM_LOG} Elementos internos do modal não achados.`); showToast("Erro interno ao preparar modal.", "error"); return; }
            const nfData = window.processedNFData[nfIdx].nf || {}; const itemData = window.processedNFData[nfIdx].items[itemIdx];
            modalTitle.textContent = `Detalhes Item ${htmlspecialchars(getSafe(itemData, 'nItem', itemIdx + 1))} (NF: ...${htmlspecialchars(getSafe(nfData, 'chaveAcesso', 'N/A').slice(-6))})`;
            modalBody.innerHTML = '<div class="d-flex justify-content-center p-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Carregando...</span></div></div>';
            saveBtn.dataset.nfIndex = nfIdx; saveBtn.dataset.itemIndex = itemIdx;
            try {
                const formContainer = await createItemDetailsFields(itemData, nfData, nfIdx, itemIdx);
                modalBody.innerHTML = ''; modalBody.appendChild(formContainer);
                moduleState.itemDetailsModalInstance.show(); console.log(`${MODAL_ITEM_LOG} Modal exibido.`);
            } catch (err) {
                console.error(`${MODAL_ITEM_LOG} Erro ao abrir modal:`, err);
                if (modalBody) modalBody.innerHTML = `<div class="alert alert-danger p-3">Erro ao carregar detalhes: ${htmlspecialchars(err.message || 'Desconhecido')}.</div>`;
                if (moduleState.itemDetailsModalInstance && !moduleState.itemDetailsModalInstance._isShown) moduleState.itemDetailsModalInstance.show();
            }
        });
    } else if (!notasTableElementForModal && notasTableQuery) {
        console.warn(`${DCL_LOG} Tabela ${notasTableQuery} não encontrada para adicionar listener de clique do modal.`);
    }

    const saveItemBtn = document.getElementById('saveItemDetails'); if (saveItemBtn && moduleState.globalItemDetailsModalElement) { saveItemBtn.addEventListener('click', () => { const SAVE_ITEM_LOG = `${DCL_LOG}[SaveItemModal]`; const nfIdx = parseInt(saveItemBtn.dataset.nfIndex, 10), itemIdx = parseInt(saveItemBtn.dataset.itemIndex, 10); if (isNaN(nfIdx) || isNaN(itemIdx) || !window.processedNFData?.[nfIdx]?.items?.[itemIdx]) { console.error(`${SAVE_ITEM_LOG} Índices/dados inválidos.`); showToast("Erro: Dados do item não encontrados para salvar.", "error"); return; } const item = window.processedNFData[nfIdx].items[itemIdx]; const formCont = moduleState.globalItemDetailsModalElement.querySelector('.item-details-form-container'); if (!formCont) { console.error(`${SAVE_ITEM_LOG} Container do formulário não encontrado.`); showToast("Erro interno ao salvar.", "error"); return; } const fields = ['ncm', 'descricaoNcm', 'atributosNcm', 'infAdProd', 'descricaoDetalhadaDue', 'unidadeEstatistica', 'quantidadeEstatistica', 'pesoLiquidoItem', 'condicaoVenda', 'vmle', 'vmcv', 'enquadramento1', 'enquadramento2', 'enquadramento3', 'enquadramento4']; fields.forEach(fName => { const input = formCont.querySelector(`[name="${fName}"]`); if (input) { if (input.type === 'number') {const val = parseFloat(input.value); item[fName] = isNaN(val) ? null : val;} else item[fName] = input.value.trim(); }}); const ccptRadio = formCont.querySelector('input[name="ccptCcrom"]:checked'); if (ccptRadio) item.ccptCcrom = ccptRadio.value; console.log(`${SAVE_ITEM_LOG} Item atualizado:`, item); showToast("Dados do item salvos localmente.", "success"); moduleState.itemDetailsModalInstance.hide(); renderNotasFiscaisTable(); }); }
    if (batchEditBtn && moduleState.batchEditModalInstance) { batchEditBtn.addEventListener('click', async () => { const BATCH_MODAL_LOG = `${DCL_LOG}[BatchEditModalOpen]`; if (!window.processedNFData?.length || window.processedNFData.every(nf => !nf.items?.length)) { showToast("Não há itens para edição em lote.", "warning"); return; } console.log(`${BATCH_MODAL_LOG} Abrindo...`); moduleState.batchEditModalInstance.show(); }); }
    const saveBatchBtn = document.getElementById('saveBatchEdit'); if (saveBatchBtn && moduleState.globalBatchEditModalElement) { saveBatchBtn.addEventListener('click', () => { const SAVE_BATCH_LOG = `${DCL_LOG}[SaveBatchEdit]`; const form = moduleState.globalBatchEditModalElement.querySelector('#batchEditForm'); if (!form) { console.error(`${SAVE_BATCH_LOG} Formulário de lote não encontrado.`); showToast("Erro: Formulário de lote não encontrado.", "error"); return; } const updates = {}; let itemsUpdatedCount = 0; form.querySelectorAll('input[type="checkbox"][data-field-name]').forEach(cb => { if (cb.checked) { const fieldName = cb.dataset.fieldName; const input = form.querySelector(`[name="${fieldName}"]`); if (input) { if (input.type === 'radio') { const checkedRadio = form.querySelector(`input[name="${fieldName}"]:checked`); updates[fieldName] = checkedRadio ? checkedRadio.value : ""; } else if (input.id === 'batchPaisDestino') { updates[fieldName] = input.dataset.codigoPais || null; } else { updates[fieldName] = input.value; } } else console.warn(`${SAVE_BATCH_LOG} Input para campo ${fieldName} não encontrado.`); } }); if (Object.keys(updates).length === 0) { showToast("Nenhum campo selecionado para atualização em lote.", "info"); return; } console.log(`${SAVE_BATCH_LOG} Aplicando atualizações em lote:`, updates); window.processedNFData.forEach(nfEntry => nfEntry.items.forEach(item => { let itemChanged = false; for (const field in updates) { if (item.hasOwnProperty(field)) { let valueToApply = updates[field]; if (['quantidadeEstatistica', 'pesoLiquidoItem', 'vmle', 'vmcv'].includes(field)) { valueToApply = parseFloat(updates[field]); if (isNaN(valueToApply)) valueToApply = null; } if (item[field] !== valueToApply) { item[field] = valueToApply; itemChanged = true; } } } if (itemChanged) itemsUpdatedCount++; })); if (itemsUpdatedCount > 0) showToast(`${itemsUpdatedCount} item(ns) atualizado(s) em lote.`, "success"); else showToast("Nenhum item foi modificado.", "info"); moduleState.batchEditModalInstance.hide(); renderNotasFiscaisTable(); }); }
    if (salvarDueBtn) salvarDueBtn.addEventListener('click', async () => { console.log(`${DCL_LOG}[SalvarDUE] Clicado.`); showToast("Lógica de Salvar DU-E a ser implementada.", "info"); });
    if (enviarDueBtn) enviarDueBtn.addEventListener('click', () => { console.log(`${DCL_LOG}[EnviarDUE] Clicado.`); showToast("Lógica de Enviar DU-E a ser implementada.", "info"); });
    if(salvarDueBtn) salvarDueBtn.disabled = false; if(enviarDueBtn && dueIdHidden) enviarDueBtn.disabled = !dueIdHidden.value; if(batchEditBtn) batchEditBtn.disabled = !(window.processedNFData?.length && window.processedNFData.some(nf => nf.items?.length > 0));
    console.log(`${DCL_LOG} Script principal pronto e listeners configurados.`);
});
function logTodosOsElementosNoFormularioPrincipal() { const form = document.getElementById('dueForm'); const LOG_FORM_IDS = '[LogFormIDs]'; if (form) { console.warn(`${LOG_FORM_IDS} --- IDs no Formulário Principal '#dueForm' ---`); const elementsWithId = form.querySelectorAll('[id]'); if (elementsWithId.length === 0) { console.warn(`${LOG_FORM_IDS} Nenhum elemento com 'id' encontrado em #dueForm.`); } else { elementsWithId.forEach(el => { console.log(`${LOG_FORM_IDS}   TAG: ${el.tagName}, ID: '${el.id}' (Name: ${el.name || 'N/A'})`); }); } console.warn(`${LOG_FORM_IDS} --- Fim da Lista de IDs ---`); } else { console.error(`${LOG_FORM_IDS} ERRO CRÍTICO: Formulário #dueForm NÃO encontrado.`); } }
console.log('[main.mjs] Script finalizou execução global.');