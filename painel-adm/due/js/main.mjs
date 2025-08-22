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

    let processedMessage = String(message ?? '');
    processedMessage = processedMessage.replace(/\r\n|\r|\n/g, '<br>');
    const safeMessage = htmlspecialchars(processedMessage);

    toastEl.innerHTML = `${safeMessage}<button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>`;
    toastContainer.appendChild(toastEl);

    if (window.bootstrap?.Alert && typeof bootstrap.Alert.getOrCreateInstance === 'function') {
        const bsAlert = bootstrap.Alert.getOrCreateInstance(toastEl);
        setTimeout(() => { if (toastEl.parentNode) bsAlert.close(); }, 5000);
    } else if (window.bootstrap?.Alert) {
        const bsAlert = new bootstrap.Alert(toastEl);
        setTimeout(() => { if (toastEl.parentNode) bsAlert.close(); }, 5000);
    } else {
        setTimeout(() => { if (toastEl.parentNode) { toastEl.classList.remove('show'); setTimeout(() => { if (toastEl.parentNode) toastEl.remove(); }, 150); } }, 5000);
    }
    console.log(`[Toast Executado] ${type.toUpperCase()}: ${message}`);
}

async function fetchGenericData(sourceName, params = null) {
    const source = moduleState.dataSources[sourceName];
    if (!source) {
        console.error(`[FetchData][${sourceName}] Fonte desconhecida.`);
        return [];
    }
    const logPrefix = `[FetchData][${source.logPrefix}]`;

    if (!params && source.cache && source.cache.length > 0) {
        return source.cache;
    }

    if (source.promise && !params) {
        return source.promise;
    }

    let finalUrl = source.url;
    if (params) {
        const queryParams = new URLSearchParams(params);
        finalUrl += `?${queryParams.toString()}`;
    }

    const fetchAction = fetch(finalUrl)
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => { throw new Error(`Erro HTTP ${response.status} ao buscar ${source.dataKey}.`); });
            }
            return response.json();
        })
        .then(data => {
            const dataArray = data.dados || data[source.dataKey];
            if (data.sucesso && Array.isArray(dataArray)) {
                if (!params) { source.cache = dataArray; }
                return dataArray;
            } else {
                return [];
            }
        })
        .catch(error => {
            console.error(`${logPrefix} Erro CRÍTICO na requisição AJAX para ${source.dataKey}:`, error);
            showToast(`Erro ao carregar ${source.dataKey}.`, 'error');
            if (!params) source.cache = [];
            return [];
        });

    if (!params) {
        source.promise = fetchAction.finally(() => { source.promise = null; });
        return source.promise;
    }
    return fetchAction;
}

const fetchPaisesDataIfNeeded = () => fetchGenericData('paises');
const fetchRecintosAduaneirosIfNeeded = () => fetchGenericData('recintos');
const fetchUnidadesRfbIfNeeded = () => fetchGenericData('unidadesRfb');

function setupGenericAutocomplete(inputElement, dataProviderFn, options) {
    const AC_LOG_PREFIX = `[Autocomplete][${options.idPrefix || inputElement.id || 'unnamed'}]`;
    if (!inputElement) { console.error(`${AC_LOG_PREFIX} ERRO GRAVE: inputElement não fornecido para Autocomplete.`); return; }
    
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
        try {
            currentData = await dataProviderFn(); dataFetched = true;
            if (initialCodeToGetName && currentData.length > 0) {
                const selectedItem = currentData.find(i => i && String(i[valueKey]) === String(initialCodeToGetName));
                if (selectedItem && selectedItem[displayKey] !== undefined) {
                    inputElement.value = selectedItem[displayKey];
                    storeCodeCallback(selectedItem[valueKey], selectedItem);
                } else {
                    inputElement.value = initialCodeToGetName;
                    storeCodeCallback(initialCodeToGetName, null);
                }
            } else if (initialCodeToGetName) {
                inputElement.value = initialCodeToGetName;
                storeCodeCallback(initialCodeToGetName, null);
            } else {
                inputElement.value = '';
                storeCodeCallback(null, null);
            }
        } catch (error) { currentData = []; inputElement.value = ''; storeCodeCallback(null, null); }
        finally { fetchInProgress = false; }
    };

    const updateList = (searchTerm = '') => {
        const term = String(searchTerm).trim().toLowerCase();
        if (!dataFetched) {
            if(!fetchInProgress) fetchDataAndSetInitialName().then(() => updateList(searchTerm));
            listContainer.innerHTML = '<div class="p-2 text-muted small">Carregando...</div>';
            listContainer.classList.remove('d-none'); return;
        }
        listContainer.innerHTML = '';
        if (!currentData || currentData.length === 0) { listContainer.innerHTML = `<div class="p-2 text-danger small">Lista de dados indisponível.</div>`; if (inputElement === document.activeElement || term) listContainer.classList.remove('d-none'); return; }

        const filteredData = currentData.filter(item => {
            if (!item) return false;
            const displayName = String(item[displayKey] || '').toLowerCase();
            const codeValue = String(item[valueKey] || '').toLowerCase();
            if (term === '' && inputElement === document.activeElement) return true;
            if (term === '') return false;
            return displayName.includes(term) || codeValue.includes(term);
        });
        
        if (filteredData.length > 0) {
            filteredData.forEach(item => {
                const itemDiv = document.createElement('div'); itemDiv.className = 'list-group-item list-group-item-action py-1 px-2'; itemDiv.style.cursor = 'pointer';
                itemDiv.textContent = labelFormatter(item);
                itemDiv.addEventListener('click', () => {
                    inputElement.value = item[displayKey] || '';
                    storeCodeCallback(item[valueKey], item);
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
    inputElement.addEventListener('focus', () => { if (!dataFetched) { fetchDataAndSetInitialName().then(() => updateList(inputElement.value)); } else { updateList(inputElement.value); } });
    const clickOutsideHandler = (e) => { if (!listContainer.classList.contains('d-none') && !listContainer.contains(e.target) && e.target !== inputElement) { listContainer.classList.add('d-none'); } };
    document.addEventListener('click', clickOutsideHandler, true);
    if (modalElement) { const onModalHidden = () => { document.removeEventListener('click', clickOutsideHandler, true); modalElement.removeEventListener('hidden.bs.modal', onModalHidden); }; modalElement.removeEventListener('hidden.bs.modal', onModalHidden); modalElement.addEventListener('hidden.bs.modal', onModalHidden); }
    
    fetchDataAndSetInitialName();
    
    // ALTERAÇÃO 3: Retorna um objeto para controle externo
    inputElement.autocompleteControl = {
        updateDataProvider: (newDataProvider) => {
            dataProviderFn = newDataProvider;
            dataFetched = false;
            currentData = [];
            fetchDataAndSetInitialName().then(() => {
                inputElement.focus();
                updateList('');
            });
        }
    };
}

function vincularFiltroUnidadeRecinto(idUnidadeInput, idRecintoInput, initialUnidadeCode, initialRecintoCode) {
    const unidadeInput = document.getElementById(idUnidadeInput);
    const recintoInput = document.getElementById(idRecintoInput);
    if (!unidadeInput || !recintoInput) { return; }

    // Cria o autocomplete da Unidade e do Recinto uma vez
    setupGenericAutocomplete(unidadeInput, () => fetchUnidadesRfbIfNeeded(), {
        valueKey: 'CODIGO', displayKey: 'NOME',
        initialCodeToGetName: initialUnidadeCode,
        idPrefix: `${unidadeInput.id}-ac`,
        sourceNameForDebug: `unidades_${idUnidadeInput}`,
        storeCodeCallback: (unidadeCode) => {
            // Quando uma unidade é selecionada, atualiza o provedor de dados do recinto
            if (recintoInput.autocompleteControl) {
                const novoDataProvider = unidadeCode ? () => fetchGenericData('recintos', { unidade_rfb_codigo: unidadeCode }) : () => Promise.resolve([]);
                recintoInput.autocompleteControl.updateDataProvider(novoDataProvider);
                recintoInput.disabled = !unidadeCode;
            }
        }
    });
    
    setupGenericAutocomplete(recintoInput, initialUnidadeCode ? () => fetchGenericData('recintos', { unidade_rfb_codigo: initialUnidadeCode }) : () => Promise.resolve([]), {
        valueKey: 'codigo', displayKey: 'nome',
        initialCodeToGetName: initialRecintoCode,
        idPrefix: `${recintoInput.id}-ac`,
        sourceNameForDebug: `recintos_iniciais_para_${idUnidadeInput}`
    });
    recintoInput.disabled = !initialUnidadeCode;
}

const populateMainForm = async () => {
    const PMF_LOG = '[PopulateMainForm]';
    console.log(`${PMF_LOG} Iniciando preenchimento do formulário principal.`);
    const ID_REC_ADU_D = 'text-campo-de-pesquisa-recinto-alfandegado-d';
    const ID_UND_DESP = 'text-campo-de-pesquisa-unidades-rfb-d';
    const ID_REC_ADU_E = 'text-campo-de-pesquisa-recinto-alfandegado-e';
    const ID_UND_EMB = 'text-campo-de-pesquisa-unidades-rfb-e';
    const dadosIniciais = window.dueDataPrincipalPHP;
    
    // ALTERAÇÃO 4: Corrigido para buscar os _codigo do objeto PHP
    const unidadeDespachoInicial = getSafe(dadosIniciais, 'unidade_rfb_despacho_codigo');
    const recintoDespachoInicial = getSafe(dadosIniciais, 'recinto_aduaneiro_despacho_codigo');
    const unidadeEmbarqueInicial = getSafe(dadosIniciais, 'unidade_rfb_embarque_codigo');
    const recintoEmbarqueInicial = getSafe(dadosIniciais, 'recinto_aduaneiro_embarque_codigo');

    console.log(`${PMF_LOG} Dados Iniciais Despacho: Unidade=${unidadeDespachoInicial}, Recinto=${recintoDespachoInicial}`);
    console.log(`${PMF_LOG} Dados Iniciais Embarque: Unidade=${unidadeEmbarqueInicial}, Recinto=${recintoEmbarqueInicial}`);
    vincularFiltroUnidadeRecinto(ID_UND_DESP, ID_REC_ADU_D, unidadeDespachoInicial, recintoDespachoInicial);
    vincularFiltroUnidadeRecinto(ID_UND_EMB, ID_REC_ADU_E, unidadeEmbarqueInicial, recintoEmbarqueInicial);
};

// --- Funções Originais (sem alterações) ---
function isItemDueComplete(item) { if (!item) return false; return REQUIRED_DUE_FIELDS.every(fieldName => { const value = item[fieldName]; if (Array.isArray(value)) return value.length > 0; if (typeof value === 'number') return !isNaN(value); return value !== null && value !== undefined && String(value).trim() !== ''; }); }
const parseNFeXML = (xmlString, fileName = 'arquivo') => { const PXML_LOG = `[ParseNFeXML][${fileName}]`; try { const parser = new DOMParser(); const xmlDoc = parser.parseFromString(xmlString, "text/xml"); const parserError = xmlDoc.getElementsByTagName("parsererror")[0]; if (parserError) throw new Error(parserError.textContent); const infNFe = xmlDoc.getElementsByTagName("infNFe")[0]; if (!infNFe) throw new Error("<infNFe> não encontrada"); const chave = getXmlAttr(infNFe, 'Id').replace('NFe', ''); const emit = infNFe.getElementsByTagName("emit")[0]; const dest = infNFe.getElementsByTagName("dest")[0]; const enderDest = dest?.getElementsByTagName("enderDest")[0]; const exporta = infNFe.getElementsByTagName("exporta")[0]; const infAdic = infNFe.getElementsByTagName("infAdic")[0]; const detElements = infNFe.getElementsByTagName("det"); const nfeData = { chaveAcesso: chave, emitente: { cnpj: getXmlValue(emit, "CNPJ"), nome: getXmlValue(emit, "xNome") }, destinatario: { nome: getXmlValue(dest, "xNome"), idEstrangeiro: getXmlValue(dest, "idEstrangeiro"), endereco: { logradouro: getXmlValue(enderDest, "xLgr"), numero: getXmlValue(enderDest, "nro"), bairro: getXmlValue(enderDest, "xBairro"), municipio: getXmlValue(enderDest, "xMun"), uf: getXmlValue(enderDest, "UF"), paisNome: getXmlValue(enderDest, "xPais"), paisCodigo: getXmlValue(enderDest, "cPais") } }, exportacao: { ufSaidaPais: getXmlValue(exporta, "UFSaidaPais"), localExportacao: getXmlValue(exporta, "xLocExporta") }, infAdicional: { infCpl: getXmlValue(infAdic, "infCpl"), infAdFisco: getXmlValue(infAdic, "infAdFisco") }, items: [] }; for (let i = 0; i < detElements.length; i++) { const det = detElements[i]; const prod = det.getElementsByTagName("prod")[0]; if (!prod) continue; const nItem = getXmlAttr(det, 'nItem') || (i + 1).toString(); const xProdValue = getXmlValue(prod, "xProd"); const qCom = parseFloat(getXmlValue(prod, "qCom")) || 0; const vUnCom = parseFloat(getXmlValue(prod, "vUnCom")) || 0; const vProd = parseFloat(getXmlValue(prod, "vProd")) || 0; const qTrib = parseFloat(getXmlValue(prod, "qTrib")); const pesoLXml = getXmlValue(prod, "pesoL") || getXmlValue(prod, "PESOL") || getXmlValue(prod, "PesoLiquido"); const pesoL = pesoLXml ? parseFloat(pesoLXml.replace(',', '.')) : null; const paisDestinoInicialXML = getSafe(nfeData, 'destinatario.endereco.paisCodigo', null); nfeData.items.push({ nItem, cProd: getXmlValue(prod, "cProd"), xProd: xProdValue, ncm: getXmlValue(prod, "NCM"), cfop: getXmlValue(prod, "CFOP"), uCom: getXmlValue(prod, "uCom"), qCom, vUnCom, vProd, uTrib: getXmlValue(prod, "uTrib"), qTrib: isNaN(qTrib) ? null : qTrib, infAdProd: getXmlValue(det, "infAdProd"), descricaoNcm: "", atributosNcm: "", unidadeEstatistica: getXmlValue(prod, "uTrib"), quantidadeEstatistica: isNaN(qTrib) ? null : qTrib, pesoLiquidoItem: isNaN(pesoL) ? null : pesoL, condicaoVenda: "", vmcv: null, vmle: null, paisDestino: paisDestinoInicialXML, descricaoDetalhadaDue: xProdValue, enquadramento1: "", enquadramento2: "", enquadramento3: "", enquadramento4: "", lpcos: [], nfsRefEletronicas: [], nfsRefFormulario: [], nfsComplementares: [], ccptCcrom: "" }); } return nfeData; } catch (error) { console.error(`${PXML_LOG} Erro GERAL:`, error); if (document.getElementById('uploadStatus')) document.getElementById('uploadStatus').innerHTML += `<div class="text-danger small">Falha processar ${htmlspecialchars(fileName)}: ${htmlspecialchars(error.message)}</div>`; return null; } };
async function createItemDetailsFields(itemData, nfData, nfIndex, itemIndex) { await fetchPaisesDataIfNeeded(); const container = document.createElement('div'); container.classList.add('item-details-form-container'); const idPrefix = `modal-item-${nfIndex}-${itemIndex}-`; const val = (key, defaultValue = '') => getSafe(itemData, key, defaultValue); const isSelected = (v, t) => (String(v) === String(t) && v !== '' && v !== null) ? 'selected' : ''; const isChecked = (v, t) => v === t ? 'checked' : ''; const createOptions = (data, valueK, textK, selectedV, includeEmpty = true, formatter = null) => { let html = includeEmpty ? '<option value="">Selecione...</option>' : ''; if (data?.length) { html += data.map(item => { const text = formatter ? formatter(item) : getSafe(item, textK); return `<option value="${htmlspecialchars(getSafe(item, valueK))}" ${isSelected(selectedV, getSafe(item, valueK))}>${htmlspecialchars(text)}</option>`; }).join(''); } return html; }; let nomePaisInicial = ''; if (itemData?.paisDestino) { const pais = moduleState.dataSources.paises.cache.find(p => String(p.CODIGO_NUMERICO) === String(itemData.paisDestino)); nomePaisInicial = pais ? pais.NOME : `Cód: ${itemData.paisDestino}`; } container.innerHTML = `<h5 class="mb-3 border-bottom pb-2">Item ${htmlspecialchars(val('nItem', itemIndex + 1))} (NF-e: ...${htmlspecialchars(getSafe(nfData, 'chaveAcesso', 'N/A').slice(-6))})</h5><h6>Dados Básicos e NCM</h6> <div class="row g-3 mb-4"><div class="col-md-6"><label class="form-label">Exportador:</label><input type="text" class="form-control form-control-sm bg-light" value="${htmlspecialchars(getSafe(nfData, 'emitente.nome', 'N/A'))}" readonly></div><div class="col-md-6"><label for="${idPrefix}ncm" class="form-label">NCM:</label><input type="text" id="${idPrefix}ncm" name="ncm" class="form-control form-control-sm" value="${htmlspecialchars(val('ncm'))}" required></div><div class="col-md-6"><label for="${idPrefix}descricao_ncm" class="form-label">Descrição NCM:</label><input type="text" id="${idPrefix}descricao_ncm" name="descricaoNcm" class="form-control form-control-sm" value="${htmlspecialchars(val('descricaoNcm'))}" placeholder="Consultar"></div><div class="col-md-6"><label for="${idPrefix}atributos_ncm" class="form-label">Atributos NCM:</label><input type="text" id="${idPrefix}atributos_ncm" name="atributosNcm" class="form-control form-control-sm" value="${htmlspecialchars(val('atributosNcm'))}" placeholder="Consultar/definir"></div></div><h6>Descrição Mercadoria</h6><div class="mb-3"><label for="${idPrefix}descricao_mercadoria" class="form-label">Descrição NF-e:</label><textarea id="${idPrefix}descricao_mercadoria" class="form-control form-control-sm bg-light" rows="2" readonly>${htmlspecialchars(val('xProd'))}</textarea></div><div class="mb-3"><label for="${idPrefix}descricao_complementar" class="form-label">Descrição Complementar (Item):</label><textarea id="${idPrefix}descricao_complementar" name="infAdProd" class="form-control form-control-sm" rows="2">${htmlspecialchars(val('infAdProd'))}</textarea></div><div class="mb-4"><label for="${idPrefix}descricao_detalhada_due" class="form-label">Descrição Detalhada DU-E:</label><textarea id="${idPrefix}descricao_detalhada_due" name="descricaoDetalhadaDue" class="form-control form-control-sm" rows="4" required>${htmlspecialchars(val('descricaoDetalhadaDue'))}</textarea></div><h6>Quantidades e Valores</h6><div class="row g-3 mb-4"><div class="col-md-4"><label for="${idPrefix}unidade_estatistica" class="form-label">Unid. Estatística:</label><input type="text" id="${idPrefix}unidade_estatistica" name="unidadeEstatistica" class="form-control form-control-sm" value="${htmlspecialchars(val('unidadeEstatistica'))}" required></div><div class="col-md-4"><label for="${idPrefix}quantidade_estatistica" class="form-label">Qtd. Estatística:</label><input type="number" step="any" id="${idPrefix}quantidade_estatistica" name="quantidadeEstatistica" class="form-control form-control-sm" value="${htmlspecialchars(val('quantidadeEstatistica', ''))}" required></div><div class="col-md-4"><label for="${idPrefix}peso_liquido" class="form-label">Peso Líquido (KG):</label><input type="number" step="any" id="${idPrefix}peso_liquido" name="pesoLiquidoItem" class="form-control form-control-sm" value="${htmlspecialchars(val('pesoLiquidoItem', ''))}" required></div><div class="col-md-3"><label class="form-label">Unid. Comercial:</label><input type="text" class="form-control form-control-sm bg-light" value="${htmlspecialchars(val('uCom'))}" readonly></div><div class="col-md-3"><label class="form-label">Qtd. Comercial:</label><input type="number" step="any" class="form-control form-control-sm bg-light" value="${htmlspecialchars(val('qCom'))}" readonly></div><div class="col-md-3"><label class="form-label">Vlr Unit. Com.:</label><input type="number" step="any" class="form-control form-control-sm bg-light" value="${htmlspecialchars(val('vUnCom'))}" readonly></div><div class="col-md-3"><label class="form-label">Vlr Total:</label><input type="number" step="any" class="form-control form-control-sm bg-light" value="${htmlspecialchars(val('vProd'))}" readonly></div><div class="col-md-4"><label for="${idPrefix}condicao_venda" class="form-label">Condição Venda:</label><select id="${idPrefix}condicao_venda" name="condicaoVenda" class="form-select form-select-sm" required>${createOptions((window.incotermsData || []), 'Sigla', 'Sigla', val('condicaoVenda'), true, item => `${item.Sigla} - ${item.Descricao}`)}</select></div><div class="col-md-4"><label for="${idPrefix}vmle" class="form-label">VMLE (R$):</label><input type="number" step="any" id="${idPrefix}vmle" name="vmle" class="form-control form-control-sm" value="${htmlspecialchars(val('vmle', ''))}" required></div><div class="col-md-4"><label for="${idPrefix}vmcv" class="form-label">VMCV (Moeda Negociada):</label><input type="number" step="any" id="${idPrefix}vmcv" name="vmcv" class="form-control form-control-sm" value="${htmlspecialchars(val('vmcv', ''))}" required></div></div><h6>Importador e Destino</h6><div class="row g-3 mb-4"><div class="col-md-6"><label class="form-label">Nome Importador (NF):</label><input type="text" class="form-control form-control-sm bg-light" value="${htmlspecialchars(getSafe(nfData, 'destinatario.nome', 'N/A'))}" readonly></div><div class="col-md-6"><label class="form-label">País Importador (NF):</label><input type="text" class="form-control form-control-sm bg-light" value="${htmlspecialchars(getSafe(nfData, 'destinatario.endereco.paisNome', 'N/A'))} (${htmlspecialchars(getSafe(nfData, 'destinatario.endereco.paisCodigo', 'N/A'))})" readonly></div><div class="col-12"><label class="form-label">Endereço (NF):</label><input type="text" class="form-control form-control-sm bg-light" value="${htmlspecialchars([getSafe(nfData, 'destinatario.endereco.logradouro'), getSafe(nfData, 'destinatario.endereco.numero'), getSafe(nfData, 'destinatario.endereco.bairro'), getSafe(nfData, 'destinatario.endereco.municipio'), getSafe(nfData, 'destinatario.endereco.uf')].filter(Boolean).join(', ') || 'Não informado')}" readonly></div><div class="col-md-6"><label for="${idPrefix}pais_destino" class="form-label">País Destino Final (DU-E):</label><input type="text" id="${idPrefix}pais_destino" class="form-control form-control-sm" value="${htmlspecialchars(nomePaisInicial)}" placeholder="Digite para buscar..." autocomplete="off" required></div></div><h6>Enquadramentos</h6><div class="row g-3 mb-4">${[1,2,3,4].map(num => `<div class="col-md-6"><label for="${idPrefix}enquadramento${num}" class="form-label">${num}º Enquadramento:</label><select id="${idPrefix}enquadramento${num}" name="enquadramento${num}" class="form-select form-select-sm" ${num === 1 ? 'required' : ''}>${createOptions((window.enquadramentosData || []), 'CODIGO', 'CODIGO', val(`enquadramento${num}`), true, item => `${item.CODIGO} - ${item.DESCRICAO}`)}<option value="99999" ${isSelected(val(`enquadramento${num}`), '99999')}>99999 - SEM ENQUADRAMENTO</option></select></div>`).join('')}</div><h6>Acordos Comerciais (Exportador Original)</h6><div class="row g-3"><div class="col-md-5"><div class="border p-3 rounded"><div class="form-check"><input class="form-check-input" type="radio" name="ccptCcrom" id="${idPrefix}ccpt_ccrom_none" value="" ${isChecked(val('ccptCcrom'), '')}><label class="form-check-label" for="${idPrefix}ccpt_ccrom_none">Nenhum</label></div><div class="form-check"><input class="form-check-input" type="radio" name="ccptCcrom" id="${idPrefix}ccpt" value="CCPT" ${isChecked(val('ccptCcrom'), 'CCPT')}><label class="form-check-label" for="${idPrefix}ccpt">CCPT</label></div><div class="form-check"><input class="form-check-input" type="radio" name="ccptCcrom" id="${idPrefix}ccrom" value="CCROM" ${isChecked(val('ccptCcrom'), 'CCROM')}><label class="form-check-label" for="${idPrefix}ccrom">CCROM</label></div></div></div></div>`; const paisDestinoInput = container.querySelector(`#${idPrefix}pais_destino`); if (paisDestinoInput && moduleState.globalItemDetailsModalElement) { setupGenericAutocomplete(paisDestinoInput, fetchPaisesDataIfNeeded, { valueKey: 'CODIGO_NUMERICO', displayKey: 'NOME', labelFormatter: item => `${item.NOME || ''} (${item.CODIGO_NUMERICO || ''})`, initialCodeToGetName: itemData.paisDestino, idPrefix: `${idPrefix}pais-dest-modal`, sourceNameForDebug: 'paises_modal_item', modalElement: moduleState.globalItemDetailsModalElement, storeCodeCallback: (code, selectedItem) => { if (itemData) { itemData.paisDestino = code || null; } } }); } return container; }
function renderNotasFiscaisTable() { const RNF_LOG = '[RenderNFTable]'; console.log(`${RNF_LOG} Executando.`); const tbody = document.querySelector('#notasFiscaisTable tbody'); const theadRow = document.querySelector('#notasFiscaisTable thead tr'); const batchBtn = document.getElementById('batchEditButton'); if (!tbody || !theadRow) { console.error(`${RNF_LOG} Tabela ou thead não encontrada.`); return; } tbody.innerHTML = ''; let statusHdr = theadRow.querySelector('.status-header'); if (!statusHdr) { statusHdr = document.createElement('th'); statusHdr.textContent = 'Status DUE'; statusHdr.classList.add('status-header', 'text-center'); statusHdr.style.width = '80px'; theadRow.appendChild(statusHdr); } else if (statusHdr !== theadRow.lastElementChild) { theadRow.appendChild(statusHdr); } const colCount = theadRow.cells.length; let hasItems = false; const nfs = window.processedNFData || []; if (!Array.isArray(nfs) || nfs.length === 0) { tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center text-muted fst-italic">Carregue XMLs...</td></tr>`; if (batchBtn) batchBtn.disabled = true; return; } nfs.forEach((nfEntry, nfIdx) => { const nf = nfEntry.nf || {}; const items = nfEntry.items || []; if (!items.length) { console.warn(`${RNF_LOG} NF ${nfIdx} sem itens.`); return; } const chaveShort = getSafe(nf, 'chaveAcesso', 'N/A').slice(-9); const nomeDest = getSafe(nf, 'destinatario.nome', 'Desconhecido'); items.forEach((item, itemIdx) => { if (!item || typeof item !== 'object') { console.warn(`${RNF_LOG} Item inválido ${itemIdx} NF ${nfIdx}`); return; } hasItems = true; let paisDestDisp = 'N/A'; if (item.paisDestino && moduleState.dataSources.paises.cache) { const pFound = moduleState.dataSources.paises.cache.find(p => String(p.CODIGO_NUMERICO) === String(item.paisDestino)); paisDestDisp = pFound ? pFound.NOME : `Cód: ${item.paisDestino}`; } const row = document.createElement('tr'); row.classList.add('item-row'); row.dataset.nfIndex = nfIdx; row.dataset.itemIndex = itemIdx; row.innerHTML = `<td>...${htmlspecialchars(chaveShort)}</td><td class="text-center">${htmlspecialchars(getSafe(item, 'nItem', itemIdx + 1))}</td><td>${htmlspecialchars(getSafe(item, 'ncm', 'N/A'))}</td><td>${htmlspecialchars(getSafe(item, 'xProd', 'N/A'))}</td><td>${htmlspecialchars(nomeDest)}</td><td>${htmlspecialchars(paisDestDisp)}</td><td class="text-center"><button type="button" class="btn btn-sm btn-outline-primary toggle-details" title="Detalhes Item ${htmlspecialchars(getSafe(item, 'nItem', itemIdx + 1))}" data-nf-index="${nfIdx}" data-item-index="${itemIdx}"><i class="bi bi-pencil-fill"></i></button></td>`; const statusCell = document.createElement('td'); const completo = isItemDueComplete(item); statusCell.style.textAlign = 'center'; statusCell.style.verticalAlign = 'middle'; statusCell.innerHTML = completo ? '<span class="text-success" title="Completo">✅</span>' : '<span class="text-danger" title="Incompleto">❌</span>'; row.appendChild(statusCell); tbody.appendChild(row); }); }); if (!hasItems) { tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center text-muted fst-italic">Nenhum item válido.</td></tr>`; if(batchBtn) batchBtn.disabled = true; } else { if(batchBtn) batchBtn.disabled = false; } }

document.addEventListener('DOMContentLoaded', () => {
    const DCL_LOG = '[DOMContentLoaded]'; console.log(`${DCL_LOG} Iniciando...`);
    moduleState.globalItemDetailsModalElement = document.getElementById('itemDetailsModal');
    moduleState.globalBatchEditModalElement = document.getElementById('batchEditModal');
    const inputXML = document.getElementById('xml-files'), uploadStatus = document.getElementById('uploadStatus'),
          spinner = document.getElementById('spinner'), notasTableQuery = '#notasFiscaisTable',
          batchEditBtn = document.getElementById('batchEditButton'), mainForm = document.getElementById('dueForm'),
          salvarDueBtn = document.getElementById('salvarDUE'), enviarDueBtn = document.getElementById('enviarDUE');

    if (!mainForm) { console.error(`${DCL_LOG} ERRO FATAL: Formulário #dueForm NÃO encontrado.`); return; }
    logTodosOsElementosNoFormularioPrincipal();

    if (moduleState.globalItemDetailsModalElement) moduleState.itemDetailsModalInstance = new bootstrap.Modal(moduleState.globalItemDetailsModalElement);
    if (moduleState.globalBatchEditModalElement) moduleState.batchEditModalInstance = new bootstrap.Modal(moduleState.globalBatchEditModalElement);

    if (!Array.isArray(window.processedNFData)) { window.processedNFData = []; }
    renderNotasFiscaisTable();

    populateMainForm();
    
    fetchPaisesDataIfNeeded();

    if (inputXML) { inputXML.addEventListener('change', async (event) => { const files = event.target.files; if (!files.length) return; uploadStatus.innerHTML = `<div class="d-flex align-items-center"><div class="spinner-border spinner-border-sm me-2 text-primary"></div>Processando...</div>`; inputXML.disabled = true; let tempProcData = []; for (const file of files) { if (file.name.toLowerCase().endsWith('.xml')) { try { const xml = await file.text(); const data = parseNFeXML(xml, file.name); if (data?.items?.length > 0) { tempProcData.push({ nf: data, items: data.items }); } } catch (e) { console.error(`Erro ao processar ${file.name}`, e); } } } inputXML.disabled = false; inputXML.value = null; if (tempProcData.length > 0) { window.processedNFData = tempProcData; renderNotasFiscaisTable(); populateMainForm(); } else { showToast("Nenhum item válido encontrado nos XMLs.", "warning"); } }); }
    const notasTableElement = document.querySelector(notasTableQuery);
    if (notasTableElement && moduleState.itemDetailsModalInstance) { notasTableElement.addEventListener('click', async (e) => { const btn = e.target.closest('button.toggle-details'); if (!btn) return; const nfIdx = parseInt(btn.dataset.nfIndex, 10); const itemIdx = parseInt(btn.dataset.itemIndex, 10); if (isNaN(nfIdx) || isNaN(itemIdx) || !window.processedNFData?.[nfIdx]?.items?.[itemIdx]) return; const modalBody = moduleState.globalItemDetailsModalElement.querySelector('.modal-body'); const modalTitle = moduleState.globalItemDetailsModalElement.querySelector('.modal-title'); const saveBtn = moduleState.globalItemDetailsModalElement.querySelector('#saveItemDetails'); if (!modalBody || !modalTitle || !saveBtn) return; const nfData = window.processedNFData[nfIdx].nf || {}; const itemData = window.processedNFData[nfIdx].items[itemIdx]; modalTitle.textContent = `Detalhes Item ${htmlspecialchars(getSafe(itemData, 'nItem', itemIdx + 1))}`; modalBody.innerHTML = 'Carregando...'; saveBtn.dataset.nfIndex = nfIdx; saveBtn.dataset.itemIndex = itemIdx; try { const formContainer = await createItemDetailsFields(itemData, nfData, nfIdx, itemIdx); modalBody.innerHTML = ''; modalBody.appendChild(formContainer); moduleState.itemDetailsModalInstance.show(); } catch (err) { modalBody.innerHTML = `<div class="alert alert-danger">Erro ao carregar detalhes.</div>`; } }); }
    const saveItemBtn = document.getElementById('saveItemDetails');
    if (saveItemBtn) { saveItemBtn.addEventListener('click', () => { const nfIdx = parseInt(saveItemBtn.dataset.nfIndex, 10); const itemIdx = parseInt(saveItemBtn.dataset.itemIndex, 10); if (isNaN(nfIdx) || isNaN(itemIdx)) return; const item = window.processedNFData[nfIdx].items[itemIdx]; const formCont = moduleState.globalItemDetailsModalElement.querySelector('.item-details-form-container'); if (!formCont) return; const fields = ['ncm', 'descricaoNcm', 'atributosNcm', 'infAdProd', 'descricaoDetalhadaDue', 'unidadeEstatistica', 'quantidadeEstatistica', 'pesoLiquidoItem', 'condicaoVenda', 'vmle', 'vmcv', 'enquadramento1', 'enquadramento2', 'enquadramento3', 'enquadramento4']; fields.forEach(fName => { const input = formCont.querySelector(`[name="${fName}"]`); if (input) { item[fName] = (input.type === 'number' ? parseFloat(input.value) || null : input.value.trim()); } }); // Pais Destino é tratado pelo autocomplete
    const ccptRadio = formCont.querySelector('input[name="ccptCcrom"]:checked'); if (ccptRadio) item.ccptCcrom = ccptRadio.value; showToast("Dados do item salvos.", "success"); moduleState.itemDetailsModalInstance.hide(); renderNotasFiscaisTable(); }); }
});

function logTodosOsElementosNoFormularioPrincipal() { const form = document.getElementById('dueForm'); const LOG_FORM_IDS = '[LogFormIDs]'; if (form) { console.warn(`${LOG_FORM_IDS} --- IDs no Formulário Principal '#dueForm' ---`); const elementsWithId = form.querySelectorAll('[id]'); if (elementsWithId.length === 0) { console.warn(`${LOG_FORM_IDS} Nenhum elemento com 'id' encontrado em #dueForm.`); } else { elementsWithId.forEach(el => { console.log(`${LOG_FORM_IDS}   TAG: ${el.tagName}, ID: '${el.id}' (Name: ${el.name || 'N/A'})`); }); } console.warn(`${LOG_FORM_IDS} --- Fim da Lista de IDs ---`); } else { console.error(`${LOG_FORM_IDS} ERRO CRÍTICO: Formulário #dueForm NÃO encontrado.`); } }
console.log('[main.mjs] Script finalizou execução global.');