// --- Arquivo: due/js/main.mjs ---

console.log('[main.mjs INÍCIO GLOBAL] Script main.mjs carregado e a iniciar execução global.');
// Com a abordagem AJAX, não esperamos que window.paisesData seja preenchido aqui.
// Estes logs confirmarão isso.
if (typeof window.paisesData !== 'undefined' && window.paisesData !== null && Array.isArray(window.paisesData)) {
    console.log('[main.mjs INÍCIO GLOBAL] Verificação de window.paisesData (legado/injeção direta): ENCONTRADO e é um array. Número de países:', window.paisesData.length);
    if (window.paisesData.length === 0) {
        console.warn('[main.mjs INÍCIO GLOBAL] window.paisesData (legado/injeção direta) é um array VAZIO.');
    }
} else {
    console.log('[main.mjs INÍCIO GLOBAL] window.paisesData (legado/injeção direta) NÃO ESTÁ DEFINIDO ou não é um array, como esperado com a abordagem AJAX pura.');
}

// Variável para cache dos dados dos países carregados via AJAX
let _paisesDataCache = null;
let _paisesDataFetchPromise = null;

// --- Funções Auxiliares ---
const getSafe = (obj, path, defaultValue = '') => { try { const value = path.split('.').reduce((o, k) => (o || {})[k], obj); return value ?? defaultValue; } catch { return defaultValue; } };
const getXmlValue = (el, tag) => el?.getElementsByTagName(tag)?.[0]?.textContent?.trim() ?? '';
const getXmlAttr = (el, attr) => el?.getAttribute(attr) ?? '';
const htmlspecialchars = (str) => { if (typeof str !== 'string') return String(str ?? ''); return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;'); };

// --- Variáveis Globais ---
let itemDetailsModalInstance = null; // Será instanciado no DOMContentLoaded
let batchEditModalInstance = null; // Será instanciado no DOMContentLoaded

const requiredDueFields = [ 'ncm', 'descricaoDetalhadaDue', 'unidadeEstatistica', 'quantidadeEstatistica', 'pesoLiquidoItem', 'condicaoVenda', 'vmcv', 'paisDestino', 'enquadramento1' ];

function isItemDueComplete(item) {
    if (!item) return false;
    return requiredDueFields.every(fieldName => {
        const value = item[fieldName]; let isFilled;
        if (Array.isArray(value)) { isFilled = value !== null && value !== undefined && value.length > 0; }
        else if (typeof value === 'number') { isFilled = value !== null && value !== undefined && !isNaN(value); }
        else { isFilled = value !== null && value !== undefined && String(value).trim() !== ''; }
        return isFilled;
    });
}

const parseNFeXML = (xmlString, fileName = 'arquivo') => {
    console.log(`[main.mjs parseNFeXML] Iniciando para ${fileName}`);
    try {
        const parser = new DOMParser(); const xmlDoc = parser.parseFromString(xmlString, "text/xml"); const parserError = xmlDoc.getElementsByTagName("parsererror"); if (parserError.length > 0) { throw new Error(`Erro parse XML: ${parserError[0].textContent}`); } const infNFe = xmlDoc.getElementsByTagName("infNFe")[0]; if (!infNFe) { throw new Error(`<infNFe> não encontrada`); } const chave = getXmlAttr(infNFe, 'Id').replace('NFe', ''); const emit = infNFe.getElementsByTagName("emit")[0]; const dest = infNFe.getElementsByTagName("dest")[0]; const enderDest = dest?.getElementsByTagName("enderDest")[0]; const exporta = infNFe.getElementsByTagName("exporta")[0]; const infAdic = infNFe.getElementsByTagName("infAdic")[0]; const detElements = infNFe.getElementsByTagName("det"); const nfeData = { chaveAcesso: chave, emitente: { cnpj: getXmlValue(emit, "CNPJ"), nome: getXmlValue(emit, "xNome") }, destinatario: { nome: getXmlValue(dest, "xNome"), idEstrangeiro: getXmlValue(dest, "idEstrangeiro"), endereco: { logradouro: getXmlValue(enderDest, "xLgr"), numero: getXmlValue(enderDest, "nro"), bairro: getXmlValue(enderDest, "xBairro"), municipio: getXmlValue(enderDest, "xMun"), uf: getXmlValue(enderDest, "UF"), paisNome: getXmlValue(enderDest, "xPais"), paisCodigo: getXmlValue(enderDest, "cPais") } }, exportacao: { ufSaidaPais: getXmlValue(exporta, "UFSaidaPais"), localExportacao: getXmlValue(exporta, "xLocExporta") }, infAdicional: { infCpl: getXmlValue(infAdic, "infCpl"), infAdFisco: getXmlValue(infAdic, "infAdFisco") }, items: [] }; for (let i = 0; i < detElements.length; i++) { const det = detElements[i]; const prod = det.getElementsByTagName("prod")[0]; if (!prod) { console.warn(`[main.mjs parseNFeXML - Parse Item ${i+1}] <prod> não encontrada.`); continue; } const nItem = getXmlAttr(det, 'nItem') || (i + 1).toString(); const xProdValue = getXmlValue(prod, "xProd"); const qCom = parseFloat(getXmlValue(prod, "qCom")) || 0; const vUnCom = parseFloat(getXmlValue(prod, "vUnCom")) || 0; const vProd = parseFloat(getXmlValue(prod, "vProd")) || 0; const qTrib = parseFloat(getXmlValue(prod, "qTrib")) || null; const pesoLiquidoXml = getXmlValue(prod, "pesoL") || getXmlValue(prod, "PESOL") || getXmlValue(prod, "PesoLiquido"); const pesoL = pesoLiquidoXml ? parseFloat(pesoLiquidoXml.replace(',', '.')) : null; const pesoLiquidoItem = isNaN(pesoL) ? null : pesoL; const paisDestinoInicialXML = getSafe(nfeData, 'destinatario.endereco.paisCodigo', null); nfeData.items.push({ nItem: nItem, cProd: getXmlValue(prod, "cProd"), xProd: xProdValue, ncm: getXmlValue(prod, "NCM"), cfop: getXmlValue(prod, "CFOP"), uCom: getXmlValue(prod, "uCom"), qCom: qCom, vUnCom: vUnCom, vProd: vProd, uTrib: getXmlValue(prod, "uTrib"), qTrib: qTrib, infAdProd: getXmlValue(det, "infAdProd"), descricaoNcm: "", atributosNcm: "", unidadeEstatistica: getXmlValue(prod, "uTrib"), quantidadeEstatistica: qTrib, pesoLiquidoItem: pesoLiquidoItem, condicaoVenda: "", vmcv: null, vmle: vProd, paisDestino: paisDestinoInicialXML, descricaoDetalhadaDue: xProdValue, enquadramento1: "", enquadramento2: "", enquadramento3: "", enquadramento4: "", lpcos: [], nfsRefEletronicas: [], nfsRefFormulario: [], nfsComplementares: [], ccptCcrom: "" }); } console.log(`[main.mjs parseNFeXML] Parse XML OK: ${fileName} - ${nfeData.items.length} itens.`); return nfeData;
    } catch (error) { console.error(`[main.mjs parseNFeXML] Erro GERAL Parse XML ${fileName}:`, error); const uploadStatusEl = document.getElementById('uploadStatus'); if(uploadStatusEl) uploadStatusEl.innerHTML += `<div class="text-danger small"><i class="bi bi-x-octagon-fill me-1"></i>Falha processar ${htmlspecialchars(fileName)}: ${htmlspecialchars(error.message)}</div>`; return null; }
};

async function fetchPaisesDataIfNeeded() {
    if (_paisesDataCache) {
        console.log('[main.mjs fetchPaisesDataIfNeeded] Usando dados de países do cache.');
        return _paisesDataCache;
    }
    if (_paisesDataFetchPromise) {
        console.log('[main.mjs fetchPaisesDataIfNeeded] Aguardando promise de busca de países existente.');
        return _paisesDataFetchPromise;
    }

    const ajaxUrl = '/3comex/painel-adm/due/ajax_buscar_paises.php'; // Mantenha o caminho absoluto que funcionou
    console.log('[main.mjs fetchPaisesDataIfNeeded] Buscando dados de países via AJAX para URL:', ajaxUrl);
    
    _paisesDataFetchPromise = fetch(ajaxUrl)
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    throw new Error(`Erro HTTP ${response.status} (${response.statusText}) ao buscar países. Resposta: ${text}`);
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.sucesso && data.paises && Array.isArray(data.paises)) {
                _paisesDataCache = data.paises;
                console.log(`[main.mjs fetchPaisesDataIfNeeded] Países carregados via AJAX com sucesso: ${_paisesDataCache.length} países.`);
                return _paisesDataCache;
            } else {
                console.error('[main.mjs fetchPaisesDataIfNeeded] Resposta AJAX para países não foi bem-sucedida ou dados inválidos:', data.mensagem, data);
                _paisesDataCache = []; 
                return []; 
            }
        })
        .catch(error => {
            console.error('[main.mjs fetchPaisesDataIfNeeded] Erro CRÍTICO na requisição AJAX para buscar países:', error);
            showToast('Erro ao carregar lista de países. A busca pode não funcionar.', 'error');
            _paisesDataCache = []; 
            _paisesDataFetchPromise = null; 
            return []; 
        });
    return _paisesDataFetchPromise;
}

async function createItemDetailsFields(itemData, nfData, nfIndex, itemIndex) {
    console.log('[main.mjs createItemDetailsFields] Função chamada.');
    const paisesParaEsteModal = await fetchPaisesDataIfNeeded();

    if (!paisesParaEsteModal || paisesParaEsteModal.length === 0) {
        console.warn('[main.mjs createItemDetailsFields] ALERTA: Lista de países está vazia ou não pôde ser carregada (AJAX). A busca de país pode não funcionar corretamente.');
    } else {
        console.log('[main.mjs createItemDetailsFields] Países disponíveis para o modal (via AJAX/cache):', paisesParaEsteModal.length);
    }

    const container = document.createElement('div');
    container.classList.add('item-details-form-container');
    const idPrefix = `modal-item-${nfIndex}-${itemIndex}-`;

    const val = (key, defaultValue = '') => getSafe(itemData, key, defaultValue);
    const isSelected = (v, t) => (v !== null && t !== null && String(v) == String(t)) ? 'selected' : '';
    const isChecked = (v, t) => v === t ? 'checked' : '';

    const createOptions = (data, valueKey, textKey, selectedValue, includeEmpty = true) => {
        let optionsHtml = includeEmpty ? '<option value="">Selecione...</option>' : '';
        if (data?.length) {
            optionsHtml += data.map(item => `
                <option value="${htmlspecialchars(getSafe(item, valueKey))}" 
                    ${isSelected(selectedValue, getSafe(item, valueKey))}>
                    ${htmlspecialchars(getSafe(item, textKey))}
                </option>`
            ).join('');
        }
        return optionsHtml;
    };

    let nomePaisInicialParaInput = '';
    if (itemData && typeof itemData.paisDestino !== 'undefined' && itemData.paisDestino !== null) {
        if (paisesParaEsteModal && paisesParaEsteModal.length > 0) {
            const paisEncontrado = paisesParaEsteModal.find(p => 
                p.CODIGO_NUMERICO != null && 
                String(p.CODIGO_NUMERICO) === String(itemData.paisDestino)
            );
            if (paisEncontrado) {
                nomePaisInicialParaInput = paisEncontrado.NOME;
            } else {
                // O log que você viu: "[main.mjs createItemDetailsFields] Código de país inicial '2755' do item não encontrado..."
                console.warn(`[main.mjs createItemDetailsFields] Código de país inicial '${itemData.paisDestino}' do item não encontrado na lista de ${paisesParaEsteModal.length} países carregada (AJAX).`);
            }
        } else {
            console.warn("[main.mjs createItemDetailsFields] Lista de países (AJAX) não disponível ou vazia para buscar nome do país inicial.");
        }
    }
    
    container.innerHTML = `
        <h5 class="mb-3 border-bottom pb-2">Item ${htmlspecialchars(val('nItem', itemIndex + 1))} (NF-e: ...${htmlspecialchars(getSafe(nfData, 'chaveAcesso', 'N/A').slice(-6))})</h5>
        <h6>Dados Básicos e NCM</h6>
        <div class="row g-3 mb-4"><div class="col-md-6"><label class="form-label">Exportador:</label><input type="text" class="form-control form-control-sm bg-light" value="${htmlspecialchars(getSafe(nfData, 'emitente.nome', 'N/A'))}" readonly></div><div class="col-md-6"><label for="${idPrefix}ncm" class="form-label">NCM:</label><input type="text" id="${idPrefix}ncm" name="ncm" class="form-control form-control-sm" value="${htmlspecialchars(val('ncm'))}" required></div><div class="col-md-6"><label for="${idPrefix}descricao_ncm" class="form-label">Descrição NCM:</label><input type="text" id="${idPrefix}descricao_ncm" name="descricaoNcm" class="form-control form-control-sm" value="${htmlspecialchars(val('descricaoNcm'))}" placeholder="Consultar"></div><div class="col-md-6"><label for="${idPrefix}atributos_ncm" class="form-label">Atributos NCM:</label><input type="text" id="${idPrefix}atributos_ncm" name="atributosNcm" class="form-control form-control-sm" value="${htmlspecialchars(val('atributosNcm'))}" placeholder="Consultar/definir"></div></div>
        <h6>Descrição Mercadoria</h6>
        <div class="mb-3"><label for="${idPrefix}descricao_mercadoria" class="form-label">Descrição NF-e:</label><textarea id="${idPrefix}descricao_mercadoria" class="form-control form-control-sm bg-light" rows="2" readonly>${htmlspecialchars(val('xProd'))}</textarea></div><div class="mb-3"><label for="${idPrefix}descricao_complementar" class="form-label">Descrição Complementar:</label><textarea id="${idPrefix}descricao_complementar" name="infAdProd" class="form-control form-control-sm" rows="2">${htmlspecialchars(val('infAdProd'))}</textarea></div><div class="mb-4"><label for="${idPrefix}descricao_detalhada_due" class="form-label">Descrição Detalhada DU-E:</label><textarea id="${idPrefix}descricao_detalhada_due" name="descricaoDetalhadaDue" class="form-control form-control-sm" rows="4" required>${htmlspecialchars(val('descricaoDetalhadaDue'))}</textarea></div>
        <h6>Quantidades e Valores</h6>
        <div class="row g-3 mb-4"><div class="col-md-4"><label for="${idPrefix}unidade_estatistica" class="form-label">Unid. Estatística:</label><input type="text" id="${idPrefix}unidade_estatistica" name="unidadeEstatistica" class="form-control form-control-sm" value="${htmlspecialchars(val('unidadeEstatistica'))}" required></div><div class="col-md-4"><label for="${idPrefix}quantidade_estatistica" class="form-label">Qtd. Estatística:</label><input type="number" step="any" id="${idPrefix}quantidade_estatistica" name="quantidadeEstatistica" class="form-control form-control-sm" value="${htmlspecialchars(val('quantidadeEstatistica', ''))}" required></div><div class="col-md-4"><label for="${idPrefix}peso_liquido" class="form-label">Peso Líquido (KG):</label><input type="number" step="any" id="${idPrefix}peso_liquido" name="pesoLiquidoItem" class="form-control form-control-sm" value="${htmlspecialchars(val('pesoLiquidoItem', ''))}" required></div><div class="col-md-3"><label class="form-label">Unid. Comercial:</label><input type="text" class="form-control form-control-sm bg-light" value="${htmlspecialchars(val('uCom'))}" readonly></div><div class="col-md-3"><label class="form-label">Qtd. Comercial:</label><input type="number" step="any" class="form-control form-control-sm bg-light" value="${htmlspecialchars(val('qCom'))}" readonly></div><div class="col-md-3"><label class="form-label">Vlr Unit. Com.:</label><input type="number" step="any" class="form-control form-control-sm bg-light" value="${htmlspecialchars(val('vUnCom'))}" readonly></div><div class="col-md-3"><label class="form-label">Vlr Total:</label><input type="number" step="any" class="form-control form-control-sm bg-light" value="${htmlspecialchars(val('vProd'))}" readonly></div><div class="col-md-4"><label for="${idPrefix}condicao_venda" class="form-label">Condição Venda:</label><select id="${idPrefix}condicao_venda" name="condicaoVenda" class="form-select form-select-sm" required>${createOptions((window.incotermsData || []).map(i => ({...i, DisplayText: `${i.Sigla} - ${i.Descricao}`})), 'Sigla', 'DisplayText', val('condicaoVenda'))}</select></div><div class="col-md-4"><label for="${idPrefix}vmle" class="form-label">VMLE (R$):</label><input type="number" step="any" id="${idPrefix}vmle" name="vmle" class="form-control form-control-sm" value="${htmlspecialchars(val('vmle', ''))}" required></div><div class="col-md-4"><label for="${idPrefix}vmcv" class="form-label">VMCV (Moeda):</label><input type="number" step="any" id="${idPrefix}vmcv" name="vmcv" class="form-control form-control-sm" value="${htmlspecialchars(val('vmcv', ''))}" required></div></div>
        <h6>Importador e Destino</h6>
        <div class="row g-3 mb-4"><div class="col-md-6"><label class="form-label">Nome Importador:</label><input type="text" class="form-control form-control-sm bg-light" value="${htmlspecialchars(getSafe(nfData, 'destinatario.nome', 'N/A'))}" readonly></div><div class="col-md-6"><label class="form-label">País Importador:</label><input type="text" class="form-control form-control-sm bg-light" value="${htmlspecialchars(getSafe(nfData, 'destinatario.endereco.paisNome', 'N/A'))} (${htmlspecialchars(getSafe(nfData, 'destinatario.endereco.paisCodigo', 'N/A'))})" readonly></div><div class="col-12"><label class="form-label">Endereço:</label><input type="text" class="form-control form-control-sm bg-light" value="${htmlspecialchars([getSafe(nfData, 'destinatario.endereco.logradouro'), getSafe(nfData, 'destinatario.endereco.numero'), getSafe(nfData, 'destinatario.endereco.bairro'), getSafe(nfData, 'destinatario.endereco.municipio'), getSafe(nfData, 'destinatario.endereco.uf')].filter(Boolean).join(', ') || 'Não informado')}" readonly></div>
        <div class="col-md-6">
            <label for="${idPrefix}pais_destino" class="form-label">País Destino Final:</label>
            <div class="position-relative">
                <input type="text" id="${idPrefix}pais_destino" class="form-control form-control-sm" value="${htmlspecialchars(nomePaisInicialParaInput)}" placeholder="Digite para buscar..." autocomplete="off" required>
                <div id="${idPrefix}paises_list" class="d-none position-absolute w-100 bg-white border rounded mt-1" style="max-height:200px; overflow-y:auto; z-index:1000;"></div>
            </div>
        </div></div>
        <h6>Enquadramentos</h6>
        <div class="row g-3 mb-4">${[1,2,3,4].map(num => `<div class="col-md-6"><label for="${idPrefix}enquadramento${num}" class="form-label">${num}º Enquadramento:</label><select id="${idPrefix}enquadramento${num}" name="enquadramento${num}" class="form-select form-select-sm"><option value="">Selecione...</option>${(window.enquadramentosData || []).map(enq => `<option value="${htmlspecialchars(enq.CODIGO)}" ${isSelected(val(`enquadramento${num}`), enq.CODIGO)}>${htmlspecialchars(enq.CODIGO)} - ${htmlspecialchars(enq.DESCRICAO)}</option>`).join('')}<option value="99999" ${isSelected(val(`enquadramento${num}`), '99999')}>99999 - SEM ENQUADRAMENTO</option></select></div>`).join('')}</div>
        <h6>Acordos Comerciais</h6>
        <div class="row g-3"><div class="col-md-5"><div class="border p-3 rounded"><div class="form-check"><input class="form-check-input" type="radio" name="ccptCcrom" id="${idPrefix}ccpt_ccrom_none" value="" ${isChecked(val('ccptCcrom'), '')}><label class="form-check-label" for="${idPrefix}ccpt_ccrom_none">Nenhum</label></div><div class="form-check"><input class="form-check-input" type="radio" name="ccptCcrom" id="${idPrefix}ccpt" value="CCPT" ${isChecked(val('ccptCcrom'), 'CCPT')}><label class="form-check-label" for="${idPrefix}ccpt">CCPT</label></div><div class="form-check"><input class="form-check-input" type="radio" name="ccptCcrom" id="${idPrefix}ccrom" value="CCROM" ${isChecked(val('ccptCcrom'), 'CCROM')}><label class="form-check-label" for="${idPrefix}ccrom">CCROM</label></div></div></div></div>
    `;

    const setupPaisBusca = () => {
        console.log('[main.mjs setupPaisBusca] Iniciando configuração da busca de país para o modal.');
        const input = container.querySelector(`#${idPrefix}pais_destino`);
        const lista = container.querySelector(`#${idPrefix}paises_list`);

        if (!input || !lista) {
            console.error('[main.mjs setupPaisBusca] Elementos input/lista do país NÃO encontrados no DOM do modal. idPrefix:', idPrefix);
            return;
        }

        const paisesParaEstaBusca = _paisesDataCache || []; 

        if (paisesParaEstaBusca.length === 0) {
            console.warn('[main.mjs setupPaisBusca] ALERTA: A lista de países (_paisesDataCache) está VAZIA. A busca não funcionará. Verifique o endpoint AJAX e os logs do PHP.');
            lista.innerHTML = '<div class="p-2 text-danger small">Erro: Lista de países indisponível ou vazia.</div>';
            lista.classList.remove('d-none');
            return;
        } else {
            console.log('[main.mjs setupPaisBusca] Países disponíveis para busca (do cache/AJAX):', paisesParaEstaBusca.length);
        }

        const atualizarLista = (termo = '') => {
            lista.innerHTML = '';
            if (paisesParaEstaBusca.length === 0) { lista.classList.add('d-none'); return; }
            
            const termoLower = termo.toLowerCase();
            const paisesFiltrados = paisesParaEstaBusca.filter(p =>
                p.NOME && typeof p.NOME === 'string' && p.NOME.toLowerCase().includes(termoLower)
            );

            if (paisesFiltrados.length > 0) {
                paisesFiltrados.forEach(pais => {
                    const itemDiv = document.createElement('div');
                    itemDiv.className = 'list-group-item list-group-item-action py-1 px-2';
                    itemDiv.style.cursor = 'pointer';
                    itemDiv.textContent = pais.NOME;
                    itemDiv.addEventListener('click', () => {
                        input.value = pais.NOME;
                        lista.classList.add('d-none');
                        if (itemData) { 
                            itemData.paisDestino = pais.CODIGO_NUMERICO;
                            console.log('[main.mjs setupPaisBusca] País selecionado:', pais.NOME, '- Código:', itemData.paisDestino);
                        }
                    });
                    lista.appendChild(itemDiv);
                });
                lista.classList.remove('d-none');
            } else {
                if (termo.length > 0) {
                    lista.innerHTML = '<div class="p-2 text-muted small">Nenhum país encontrado.</div>';
                    lista.classList.remove('d-none');
                } else {
                    lista.classList.add('d-none'); 
                }
            }
        };

        input.addEventListener('input', (e) => atualizarLista(e.target.value));
        input.addEventListener('focus', () => atualizarLista(input.value)); 
        
        // CORREÇÃO DO ERRO: Obter a referência ao elemento do modal aqui
        const esteModalElement = document.getElementById('itemDetailsModal'); // ID do seu modal de detalhes do item

        const clickForaHandlerModal = (e) => {
            if (lista && !lista.classList.contains('d-none') && !lista.contains(e.target) && e.target !== input) {
                lista.classList.add('d-none');
            }
        };
        document.addEventListener('click', clickForaHandlerModal, true);

        // Adicionar listener para 'hidden.bs.modal' para remover o clickForaHandlerModal
        if (esteModalElement) { // Verifica se o elemento do modal foi encontrado
            const onModalHidden = () => {
                console.log("[main.mjs setupPaisBusca] Modal de detalhes fechado (hidden.bs.modal). Removendo listener de clique fora.");
                document.removeEventListener('click', clickForaHandlerModal, true);
                esteModalElement.removeEventListener('hidden.bs.modal', onModalHidden); // Auto-remove este listener
            };
            // Remove qualquer listener anterior para evitar duplicatas se setupPaisBusca for chamado múltiplas vezes
            // para o mesmo modal (embora o conteúdo do modal seja recriado, o elemento do modal é o mesmo)
            esteModalElement.removeEventListener('hidden.bs.modal', onModalHidden); // Tenta remover antes de adicionar
            esteModalElement.addEventListener('hidden.bs.modal', onModalHidden);
        } else {
            console.warn("[main.mjs setupPaisBusca] Não foi possível encontrar o elemento do modal #itemDetailsModal para adicionar o listener 'hidden.bs.modal'. O listener de clique fora pode não ser removido.");
        }
    };
    
    setupPaisBusca(); // Chamado após o HTML do container ser definido e os dados dos países (paisesParaEsteModal) estarem disponíveis

    return container;
}

// --- Renderização da Tabela de Itens ---
function renderNotasFiscaisTable() {
    // ... (Seu código renderNotasFiscaisTable existente, adaptado para usar _paisesDataCache para nomes de países se necessário) ...
    console.log('[main.mjs renderNotasFiscaisTable] EXECUTANDO.');
    const tbody = document.querySelector('#notasFiscaisTable tbody');
    const theadRow = document.querySelector('#notasFiscaisTable thead tr');
    const batchButton = document.getElementById('batchEditButton');
    if (!tbody || !theadRow) { console.error("[main.mjs renderNotasFiscaisTable] Tabela #notasFiscaisTable ou thead não encontrada."); return; }
    tbody.innerHTML = '';

    let statusHeader = theadRow.querySelector('.status-header');
    if (!statusHeader) { statusHeader = document.createElement('th'); statusHeader.textContent = 'Status DUE'; statusHeader.classList.add('status-header'); statusHeader.style.width = '80px'; statusHeader.style.textAlign = 'center'; theadRow.appendChild(statusHeader); }
    else if (statusHeader !== theadRow.lastElementChild) { theadRow.appendChild(statusHeader); }

    const colCount = theadRow.cells.length;
    let hasItems = false;
    const currentNFData = window.processedNFData || [];

    if (!Array.isArray(currentNFData) || currentNFData.length === 0) {
        tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center text-muted fst-italic">Carregue arquivos XML ou dados existentes...</td></tr>`;
        if (batchButton) batchButton.disabled = true;
        return;
    }

    currentNFData.forEach((nfEntry, nfIndex) => {
        let nf = nfEntry.nf || {};
        let items = nfEntry.items || (Array.isArray(nfEntry) ? nfEntry : (nfEntry.nItem ? [nfEntry] : []));

        if (!Array.isArray(items) || items.length === 0) {
            console.warn(`[main.mjs renderNotasFiscaisTable] Entrada ${nfIndex} sem array de 'items' válido.`);
            return;
        }

        const chaveNFeShort = getSafe(nf, 'chaveAcesso', 'N/A').slice(-9);
        const nomeDest = getSafe(nf, 'destinatario.nome', 'Desconhecido');
        
        items.forEach((item, itemIndex) => {
            if (!item || typeof item !== 'object') {
                console.warn(`[main.mjs renderNotasFiscaisTable] Item inválido no índice ${itemIndex} da NF ${nfIndex}`);
                return;
            }
            hasItems = true;

            let paisDestNomeParaTabela = getSafe(nf, 'destinatario.endereco.paisNome', '');
            if (item.paisDestino) {
                const paisesDisponiveisParaTabela = _paisesDataCache && Array.isArray(_paisesDataCache) && _paisesDataCache.length > 0;
                if (paisesDisponiveisParaTabela) {
                    const paisItemEncontrado = _paisesDataCache.find(p => String(p.CODIGO_NUMERICO) === String(item.paisDestino));
                    if (paisItemEncontrado) {
                        paisDestNomeParaTabela = paisItemEncontrado.NOME;
                    } else {
                        if (!paisDestNomeParaTabela) paisDestNomeParaTabela = `Cód: ${item.paisDestino}`;
                    }
                } else {
                     if (!paisDestNomeParaTabela) paisDestNomeParaTabela = `Cód: ${item.paisDestino}`;
                }
            }
            if (!paisDestNomeParaTabela) paisDestNomeParaTabela = 'N/A';

            const row = document.createElement('tr');
            row.classList.add('item-row');
            row.dataset.nfIndex = nfIndex;
            row.dataset.itemIndex = itemIndex;

            row.innerHTML = `
                <td>...${htmlspecialchars(chaveNFeShort)}</td>
                <td class="text-center">${htmlspecialchars(getSafe(item, 'nItem', itemIndex + 1))}</td>
                <td>${htmlspecialchars(getSafe(item, 'ncm', 'N/A'))}</td>
                <td>${htmlspecialchars(getSafe(item, 'xProd', 'N/A'))}</td>
                <td>${htmlspecialchars(nomeDest)}</td>
                <td>${htmlspecialchars(paisDestNomeParaTabela)}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-primary toggle-details" title="Detalhes Item ${htmlspecialchars(getSafe(item, 'nItem', itemIndex + 1))}" data-nf-index="${nfIndex}" data-item-index="${itemIndex}">
                        <i class="bi bi-pencil-fill"></i>
                    </button>
                </td>
            `;

            const statusCell = document.createElement('td');
            const completo = isItemDueComplete(item);
            statusCell.style.textAlign = 'center'; statusCell.style.verticalAlign = 'middle';
            statusCell.innerHTML = completo ? '<span class="text-success" title="Completo">&#x2705;</span>' : '<span class="text-danger" title="Incompleto">&#x274C;</span>';
            row.appendChild(statusCell);
            tbody.appendChild(row);
        });
    });

    if (!hasItems && currentNFData.length > 0) {
        tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center text-warning fst-italic">Nenhum item válido encontrado.</td></tr>`;
        if (batchButton) batchButton.disabled = true;
    } else if (hasItems) {
        if (batchButton) batchButton.disabled = false;
    } else {
        tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center text-muted fst-italic">Carregue XMLs...</td></tr>`;
        if (batchButton) batchButton.disabled = true;
    }
}

// --- Preencher Campos da Aba 1 ---
const populateMainForm = (nfData) => {
    // ... (Seu código populateMainForm existente, sem alterações) ...
    console.log('[main.mjs populateMainForm] EXECUTANDO. Dados recebidos (nfData):', nfData);
    const formElements = { cnpjCpf: document.getElementById('text-cnpj-cpf-select'), nomeCliente: document.getElementById('nomeCliente'), infoCompl: document.getElementById('info-compl') };
    const editId = document.getElementById('due_id_hidden')?.value;
    if (nfData && Object.keys(nfData).length > 0) { console.log('[main.mjs populateMainForm] Preenchendo com dados (XML ou edição).'); if (formElements.cnpjCpf) formElements.cnpjCpf.value = getSafe(nfData, 'emitente.cnpj', ''); if (formElements.nomeCliente) formElements.nomeCliente.value = getSafe(nfData, 'emitente.nome', ''); if (formElements.infoCompl && (!formElements.infoCompl.value || !formElements.infoCompl.value.trim())) { formElements.infoCompl.value = getSafe(nfData, 'infAdicional.infCpl', ''); }
    } else if (editId && window.dueDataPrincipalPHP) { console.log('[main.mjs populateMainForm] Preenchendo com dados de edição (window.dueDataPrincipalPHP).'); if (formElements.cnpjCpf) formElements.cnpjCpf.value = getSafe(window.dueDataPrincipalPHP, 'cnpj_exportador', ''); if (formElements.nomeCliente) formElements.nomeCliente.value = getSafe(window.dueDataPrincipalPHP, 'nome_exportador', ''); if (formElements.infoCompl) formElements.infoCompl.value = getSafe(window.dueDataPrincipalPHP, 'info_complementar_geral', '');
    } else { console.warn('[main.mjs populateMainForm] Recebeu nfData vazio ou nulo, e não é edição com dueDataPrincipalPHP.'); if (editId) { console.log('[main.mjs populateMainForm] Modo Edição (ID existe), mas sem dados para popular formulário principal via JS.'); } else { console.log('[main.mjs populateMainForm] Nova DU-E. Resetando rádios padrão (se existirem).'); const radioPropria = document.getElementById('por-conta-propria'); const radioNfe = document.getElementById('nfe'); if(radioPropria && !radioPropria.checked && !document.querySelector('input[name="tipo_operacao_due"]:checked')) radioPropria.checked = true; if(radioNfe && !radioNfe.checked && !document.querySelector('input[name="tipo_documento_base"]:checked')) radioNfe.checked = true; } }
};

// --- Código Principal (DOMContentLoaded) ---
document.addEventListener('DOMContentLoaded', () => {
    // ... (Seu código DOMContentLoaded existente, com a chamada a createItemDetailsFields agora sendo async)
    console.log('[main.mjs DOMContentLoaded] DOM Carregado. Iniciando script...');
    console.log('[main.mjs DOMContentLoaded] Verificando window.processedNFData inicial:', window.processedNFData);

    const inputXML = document.getElementById('xml-files');
    const uploadStatus = document.getElementById('uploadStatus');
    const spinner = document.getElementById('spinner');
    const notasTable = document.querySelector('#notasFiscaisTable');
    const itemDetailsModalElement = document.getElementById('itemDetailsModal'); // Definido aqui
    const batchEditButton = document.getElementById('batchEditButton');
    const batchEditModalElement = document.getElementById('batchEditModal'); // Definido aqui
    const mainForm = document.getElementById('dueForm');
    const salvarDueButton = document.getElementById('salvarDUE');
    const enviarDueButton = document.getElementById('enviarDUE');
    const dueIdHiddenInput = document.getElementById('due_id_hidden');

    if (!notasTable) console.error("[main.mjs DOMContentLoaded] ERRO FATAL: Tabela #notasFiscaisTable não encontrada.");
    if (!mainForm) console.error("[main.mjs DOMContentLoaded] ERRO FATAL: Formulário #dueForm não encontrado.");
    console.log("[main.mjs DOMContentLoaded] Elementos UI essenciais verificados.");

    try {
        if (window.bootstrap?.Modal && itemDetailsModalElement) {
            itemDetailsModalInstance = new bootstrap.Modal(itemDetailsModalElement);
            // O listener 'hidden.bs.modal' para limpar o conteúdo do modal e remover o 
            // 'clickForaHandlerModal' será adicionado DENTRO de setupPaisBusca,
            // pois o handler 'clickForaHandlerModal' é definido lá.
        }
        if (window.bootstrap?.Modal && batchEditModalElement) {
            batchEditModalInstance = new bootstrap.Modal(batchEditModalElement);
             batchEditModalElement.addEventListener('hidden.bs.modal', () => { 
                const bf = batchEditModalElement.querySelector('#batchEditForm');
                if(bf) { 
                    bf.reset(); 
                    const ra = bf.querySelector('input[name="batchCcptCcromModal"][value=""]'); 
                    if(ra) ra.checked = true;
                }
            });
        }
        console.log("[main.mjs DOMContentLoaded] Modais Bootstrap configurados.");
    } catch (e) { console.error("[main.mjs DOMContentLoaded] Falha inicializar Modais:", e); }

    console.log('[main.mjs DOMContentLoaded] Antes de chamar renderNotasFiscaisTable() inicial.');
    if (typeof renderNotasFiscaisTable === "function") renderNotasFiscaisTable();
    console.log('[main.mjs DOMContentLoaded] Depois de chamar renderNotasFiscaisTable() inicial.');
    
    if (window.processedNFData && window.processedNFData.length > 0 && window.processedNFData[0].nf) {
        populateMainForm(window.processedNFData[0].nf);
    } else if (window.dueDataPrincipalPHP) {
        populateMainForm(null);
    }

    if(inputXML) {
        inputXML.addEventListener('change', async (event) => {
            // ... (código do listener de input XML mantido como no seu original) ...
            console.log("[main.mjs inputXML 'change'] Evento de mudança de ficheiro XML.");
            const files = event.target.files;
            if (!files?.length) { if(uploadStatus) uploadStatus.innerHTML = 'Nenhum arquivo selecionado.'; return; }
            if(uploadStatus) uploadStatus.innerHTML = `<div class="d-flex align-items-center"><div class="spinner-border spinner-border-sm me-2 text-primary" role="status"></div> Processando ${files.length} arquivo(s)...</div>`;
            if(spinner) spinner.style.display = 'flex'; 
            inputXML.disabled = true;
            let tempProcessedData = []; let promises = []; let errorCount = 0; let warningCount = 0; let statusMessagesHTML = '';
            for (const file of files) {
                if (file.name.toLowerCase().endsWith('.xml') && (file.type === 'text/xml' || file.type === 'application/xml' || file.type === '')) {
                    promises.push( file.text().then(xml => {
                        const data = parseNFeXML(xml, file.name);
                        if (data?.items?.length > 0) { tempProcessedData.push({ nf: data, items: data.items }); statusMessagesHTML += `<div class="alert alert-success alert-sm py-1 px-2 mb-1 small"><i class="bi bi-check-circle-fill me-1"></i>${htmlspecialchars(file.name)}: OK (${data.items.length} itens)</div>`;
                        } else if (data) { statusMessagesHTML += `<div class="alert alert-warning alert-sm py-1 px-2 mb-1 small"><i class="bi bi-exclamation-triangle-fill me-1"></i>${htmlspecialchars(file.name)}: XML válido, mas sem itens.</div>`; warningCount++; 
                        } else { errorCount++; /* Erro já logado e adicionado ao status pelo parser */ }
                    }).catch(err => { console.error(`[main.mjs inputXML 'change'] Erro LER ${file.name}:`, err); statusMessagesHTML += `<div class="alert alert-danger alert-sm py-1 px-2 mb-1 small"><i class="bi bi-x-octagon-fill me-1"></i>Falha LER ${htmlspecialchars(file.name)}.</div>`; errorCount++; }) );
                } else { statusMessagesHTML += `<div class="alert alert-secondary alert-sm py-1 px-2 mb-1 small"><i class="bi bi-slash-circle-fill me-1"></i>${htmlspecialchars(file.name)}: Ignorado.</div>`; warningCount++; }
            }
            try { await Promise.all(promises); } 
            catch (err) { console.error("[main.mjs inputXML 'change'] Erro GERAL async XML:", err); statusMessagesHTML += `<div class="alert alert-danger mt-2">Erro inesperado.</div>`; errorCount++; }
            finally {
                if(spinner) spinner.style.display = 'none'; inputXML.disabled = false; if (event.target) event.target.value = null; 
                if(uploadStatus) uploadStatus.innerHTML = statusMessagesHTML;
                const totalItemsCarregados = tempProcessedData.reduce((sum, entry) => sum + (entry.items?.length || 0), 0);
                const totalNFsCarregadas = tempProcessedData.length;
                if (totalItemsCarregados > 0) {
                    console.log(`[main.mjs inputXML 'change'] Upload OK. ${totalItemsCarregados} itens em ${totalNFsCarregadas} NF(s). SUBSTITUINDO dados.`);
                    window.processedNFData = tempProcessedData; 
                    if (window.processedNFData[0]?.nf) populateMainForm(window.processedNFData[0].nf); else populateMainForm(null);
                    if(uploadStatus) uploadStatus.insertAdjacentHTML('beforeend', `<hr class="my-1"><div class="alert alert-primary alert-sm py-1 px-2 small fw-bold">Total: ${totalItemsCarregados} item(ns) em ${totalNFsCarregadas} NF(s) carregadas.</div>`);
                } else {
                    console.warn("[main.mjs inputXML 'change'] Upload concluído, sem itens válidos.");
                    if (!errorCount && uploadStatus) uploadStatus.insertAdjacentHTML('beforeend', `<hr class="my-1"><div class="alert alert-warning alert-sm py-1 px-2 small">Nenhum item válido encontrado.</div>`);
                    else if (errorCount > 0 && uploadStatus) uploadStatus.insertAdjacentHTML('beforeend', `<hr class="my-1"><div class="alert alert-danger alert-sm py-1 px-2 small fw-bold">Houve ${errorCount} erro(s).</div>`);
                }
                renderNotasFiscaisTable();
            }
        });
    }

    if(notasTable && itemDetailsModalInstance) { // Verifica itemDetailsModalInstance aqui
        notasTable.addEventListener('click', async (e) => {
            const detailsButton = e.target.closest('button.toggle-details'); 
            if (!detailsButton) return;
            
            console.log("[main.mjs Abrir Modal] Botão de detalhes clicado.");
            const nfIndex = parseInt(detailsButton.dataset.nfIndex, 10); 
            const itemIndex = parseInt(detailsButton.dataset.itemIndex, 10);
            
            if (isNaN(nfIndex) || isNaN(itemIndex) || !window.processedNFData?.[nfIndex]?.items?.[itemIndex]) { 
                console.error("[main.mjs Abrir Modal] Índices/dados inválidos para abrir modal:", { nfIndex, itemIndex }); 
                alert("Erro: Dados do item não encontrados para o modal."); return; 
            }
            
            // itemDetailsModalElement é definido no escopo do DOMContentLoaded
            const modalBody = itemDetailsModalElement.querySelector('.modal-body');
            const modalTitle = itemDetailsModalElement.querySelector('.modal-title');
            const saveBtn = itemDetailsModalElement.querySelector('#saveItemDetails');

            if (!modalBody || !modalTitle || !saveBtn) { 
                console.error("[main.mjs Abrir Modal] Elementos internos do modal de detalhes não encontrados."); 
                alert("Erro interno ao preparar para abrir detalhes do item."); return; 
            }
            
            const nfData = window.processedNFData[nfIndex].nf || {}; 
            const itemData = window.processedNFData[nfIndex].items[itemIndex];
            
            modalTitle.textContent = `Detalhes Item ${htmlspecialchars(getSafe(itemData, 'nItem', itemIndex + 1))} (NF: ...${htmlspecialchars(getSafe(nfData, 'chaveAcesso', 'N/A').slice(-6))})`;
            modalBody.innerHTML = '<div class="d-flex justify-content-center p-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">A carregar dados e países...</span></div></div>';
            
            saveBtn.dataset.nfIndex = nfIndex; 
            saveBtn.dataset.itemIndex = itemIndex; 
            
            try {
                const itemFormFieldsContainer = await createItemDetailsFields(itemData, nfData, nfIndex, itemIndex);
                modalBody.innerHTML = ''; 
                modalBody.appendChild(itemFormFieldsContainer);
                itemDetailsModalInstance.show(); 
                console.log("[main.mjs Abrir Modal] Modal de detalhes do item mostrado.");

            } catch (err) { 
                console.error("[main.mjs Abrir Modal] Erro geral ao tentar abrir modal de detalhes (catch no listener):", err); 
                if(modalBody) modalBody.innerHTML = `<div class="alert alert-danger p-3">Erro ao carregar os detalhes do item: ${htmlspecialchars(err.message || 'Erro desconhecido')}. Tente novamente.</div>`;
                if (itemDetailsModalInstance && !itemDetailsModalInstance._isShown) itemDetailsModalInstance.show();
            }
        });
    }

    // ... (Resto dos seus listeners: saveItemBtnGlobalRef, batchEditButton, saveBatchBtnGlobalRef, salvarDueButton, enviarDueButton)
    const saveItemBtnGlobalRef = document.getElementById('saveItemDetails');
    if (saveItemBtnGlobalRef) {
        saveItemBtnGlobalRef.addEventListener('click', () => {
            console.log("[main.mjs Salvar Modal Item] Botão 'Salvar' clicado.");
            const nfIndex = parseInt(saveItemBtnGlobalRef.dataset.nfIndex, 10); const itemIndex = parseInt(saveItemBtnGlobalRef.dataset.itemIndex, 10);
            if (isNaN(nfIndex) || isNaN(itemIndex) || !window.processedNFData?.[nfIndex]?.items?.[itemIndex]) { console.error("[main.mjs Salvar Modal Item] Ref inválida."); alert("Erro salvar."); return; }
            const itemDataRef = window.processedNFData[nfIndex].items[itemIndex];
            const idPrefix = `modal-item-${nfIndex}-${itemIndex}-`; 
            const modalContentContainer = itemDetailsModalElement?.querySelector('.modal-body .item-details-form-container');
            if (!modalContentContainer) { console.error("[main.mjs Salvar Modal Item] Conteúdo do modal não encontrado."); return; }
            try {
                const getModalValue = (idSuffix, num=false, flt=true) => { const el=modalContentContainer.querySelector(`#${idPrefix}${idSuffix}`); let v = el?.value ?? null; if(v!==null){ v=String(v).trim(); if(num){ if(v===''){v=null;}else{const c=v.replace(',','.'); const n=flt?parseFloat(c):parseInt(c,10); v=isNaN(n)?null:n;}}} return v;};
                const getModalRadio = (rName) => modalContentContainer.querySelector(`input[name="${rName}"]:checked`)?.value ?? "";
                const newData = { ncm: getModalValue('ncm'), descricaoNcm: getModalValue('descricao_ncm'), atributosNcm: getModalValue('atributos_ncm'), infAdProd: getModalValue('descricao_complementar'), descricaoDetalhadaDue: getModalValue('descricao_detalhada_due'), unidadeEstatistica: getModalValue('unidade_estatistica'), quantidadeEstatistica: getModalValue('quantidade_estatistica', true, true), pesoLiquidoItem: getModalValue('peso_liquido', true, true), condicaoVenda: getModalValue('condicao_venda'), vmle: getModalValue('vmle', true, true), vmcv: getModalValue('vmcv', true, true), /* paisDestino é atualizado pelo setupPaisBusca */ enquadramento1: getModalValue('enquadramento1'), enquadramento2: getModalValue('enquadramento2'), enquadramento3: getModalValue('enquadramento3'), enquadramento4: getModalValue('enquadramento4'), ccptCcrom: getModalRadio('ccptCcrom') };
                const paisNomeDigitado = getModalValue('pais_destino');
                if (paisNomeDigitado && _paisesDataCache) { const paisEncontradoNaLista = _paisesDataCache.find(p => p.NOME.toLowerCase() === paisNomeDigitado.toLowerCase()); if (paisEncontradoNaLista && String(itemDataRef.paisDestino) !== String(paisEncontradoNaLista.CODIGO_NUMERICO)) { itemDataRef.paisDestino = paisEncontradoNaLista.CODIGO_NUMERICO; } else if (!paisEncontradoNaLista && itemDataRef.paisDestino) { const nomeOriginalDoCodigo = (_paisesDataCache.find(p=>String(p.CODIGO_NUMERICO) === String(itemDataRef.paisDestino))?.NOME || ''); if (paisNomeDigitado !== nomeOriginalDoCodigo) { console.warn(`[main.mjs Salvar Modal Item] Nome de país '${paisNomeDigitado}' não encontrado. Código ${itemDataRef.paisDestino} (Nome original: ${nomeOriginalDoCodigo}) mantido.`); } } }
                Object.assign(itemDataRef, newData);
                const btnTxt = saveItemBtnGlobalRef.innerHTML; saveItemBtnGlobalRef.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Salvando...`; saveItemBtnGlobalRef.disabled = true;
                setTimeout(() => { itemDetailsModalInstance?.hide(); showToast("Item atualizado localmente!"); saveItemBtnGlobalRef.innerHTML = btnTxt; saveItemBtnGlobalRef.disabled = false; renderNotasFiscaisTable(); }, 300);
            } catch (saveErr) { console.error("[main.mjs Salvar Modal Item] Erro salvar:", saveErr); alert(`Erro: ${saveErr.message}.`); saveItemBtnGlobalRef.innerHTML = 'Salvar Alterações Item'; saveItemBtnGlobalRef.disabled = false; }
        });
    }

    if(batchEditButton && batchEditModalInstance) {
        batchEditButton.addEventListener('click', async () => { 
            if (!window.processedNFData?.length || window.processedNFData.every(nf => !nf.items?.length)) { alert("Não há itens para editar em lote."); return; } 
            console.log("[main.mjs Abrir Modal Lote] Botão clicado.");
            await fetchPaisesDataIfNeeded(); 
            batchEditModalInstance.show(); 
        });
    }

    const saveBatchBtnGlobalRef = document.getElementById('saveBatchEdit');
    if (saveBatchBtnGlobalRef && batchEditModalElement) {
        saveBatchBtnGlobalRef.addEventListener('click', () => {
            console.log("[main.mjs Salvar Modal Lote] Botão 'Aplicar' clicado.");
            const batchForm = batchEditModalElement.querySelector('#batchEditForm');
            if (!batchForm) { console.error("[main.mjs Salvar Modal Lote] Form #batchEditForm não encontrado."); return; }
            if (!window.processedNFData?.length || window.processedNFData.every(nf => !nf.items?.length)) { alert("Sem itens para lote."); batchEditModalInstance?.hide(); return; }
            const incotermLote = batchForm.querySelector('#batchIncotermSelectModal')?.value; 
            const paisNomeLote = batchForm.querySelector('#batchPaisDestinoInputModal')?.value.trim();
            let paisCodigoLote = null;
            if (paisNomeLote) { paisCodigoLote = getCountryCodeByName(paisNomeLote); if (!paisCodigoLote) { console.warn(`[main.mjs Salvar Modal Lote] País para lote "${paisNomeLote}" não encontrado.`); alert(`Atenção: País "${paisNomeLote}" não reconhecido.`); } }
            const enqsLote = [1,2,3,4].map(i => batchForm.querySelector(`#batchEnquadramento${i}SelectModal`)?.value); 
            const ccptCcromLote = batchForm.querySelector('input[name="batchCcptCcromModal"]:checked')?.value; 
            let itemsChangedCount = 0;
            window.processedNFData.forEach(nfEntry => { if (nfEntry.items?.length) { nfEntry.items.forEach(item => { let changed = false; if (incotermLote && item.condicaoVenda !== incotermLote) { item.condicaoVenda = incotermLote; changed = true; } if (paisCodigoLote && item.paisDestino !== paisCodigoLote) { item.paisDestino = paisCodigoLote; changed = true; } enqsLote.forEach((enq, i) => { const key = `enquadramento${i+1}`; if (enq && item[key] !== enq) { item[key] = enq; changed = true; } }); if (ccptCcromLote !== undefined && ccptCcromLote !== "") { if (ccptCcromLote === "NA" && item.ccptCcrom !== "") { item.ccptCcrom = ""; changed = true; } else if (ccptCcromLote !== "NA" && item.ccptCcrom !== ccptCcromLote) { item.ccptCcrom = ccptCcromLote; changed = true; } } if (changed) itemsChangedCount++; }); } });
            batchEditModalInstance?.hide(); renderNotasFiscaisTable(); showToast(`${itemsChangedCount} item(ns) atualizados em lote.`); console.log(`[main.mjs Salvar Modal Lote] ${itemsChangedCount} itens atualizados.`);
        });
    }
    
    const HTML_DUE_ID_HIDDEN_CONST = 'due_id_hidden'; 
    const HTML_DUE_NOME_CLIENTE_CONST = 'nomeCliente'; 

    if (salvarDueButton && mainForm && dueIdHiddenInput) {
        salvarDueButton.addEventListener('click', async () => { 
            console.log("[main.mjs Salvar DU-E] Botão Salvar DU-E clicado.");
            if(spinner) spinner.style.display = 'flex'; salvarDueButton.disabled = true; if(enviarDueButton) enviarDueButton.disabled = true;
            const formDataObj = {}; const formDataEntries = new FormData(mainForm);
            for (const [key, value] of formDataEntries.entries()) { const el=mainForm.elements[key];if(el?.type==='checkbox'){formDataObj[key]=el.checked;}else if(el?.type==='radio'){const chk=mainForm.querySelector(`input[name="${key}"]:checked`);formDataObj[key]=chk?chk.value:null;}else if(el?.tagName==='SELECT'&&value===''){formDataObj[key]=null;}else{formDataObj[key]=String(value).trim()===''?null:value;} }
            formDataObj[HTML_DUE_ID_HIDDEN_CONST] = dueIdHiddenInput.value || null;
            const itemsToSave = window.processedNFData || [];
            if (!Array.isArray(itemsToSave) || itemsToSave.reduce((count, nf) => count + (nf.items?.length || 0), 0) === 0) { alert("Não há itens válidos para salvar."); if(spinner) spinner.style.display = 'none'; salvarDueButton.disabled = false; if(enviarDueButton && dueIdHiddenInput) enviarDueButton.disabled = !dueIdHiddenInput.value; return; }
            const payload = { formData: formDataObj, itemsData: itemsToSave };
            console.log("[main.mjs Salvar DU-E] Enviando payload:", payload);
            console.log(`>>> [main.mjs Salvar DU-E] Valor nomeCliente ENVIADO: '${formDataObj[HTML_DUE_NOME_CLIENTE_CONST]}'`);
            try {
                const response = await fetch('due/salvar_due.php', { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' }, body: JSON.stringify(payload) });
                const responseText = await response.text(); console.log("[main.mjs Salvar DU-E] Resposta bruta:", responseText);
                if (!response.ok) { throw new Error(`HTTP ${response.status}: ${responseText}`); }
                const result = JSON.parse(responseText);
                if (result.success) { console.log("Salvo OK:", result); showToast(result.message || 'Salvo!'); if (result.due_id && dueIdHiddenInput) { dueIdHiddenInput.value = result.due_id; document.title = `DU-E: ${result.due_id}`; } if(enviarDueButton) enviarDueButton.disabled = !(dueIdHiddenInput && dueIdHiddenInput.value); }
                else { console.error("Erro lógico salvar:", result); alert(`Falha: ${result.message || 'Erro servidor.'}`); if(enviarDueButton && dueIdHiddenInput) enviarDueButton.disabled = !dueIdHiddenInput.value; }
            } catch (error) { console.error("[main.mjs Salvar DU-E] Erro fetch/salvar:", error); alert(`Erro comunicação: ${error.message}.`); if(enviarDueButton && dueIdHiddenInput) enviarDueButton.disabled = !dueIdHiddenInput.value; }
            finally { if(spinner) spinner.style.display = 'none'; salvarDueButton.disabled = false; if(enviarDueButton && dueIdHiddenInput) enviarDueButton.disabled = !dueIdHiddenInput.value; }
        });
        console.log("[main.mjs DOMContentLoaded] Listener Salvar Adicionado OK.");
    }

    if (enviarDueButton && dueIdHiddenInput) { 
        enviarDueButton.addEventListener('click', () => { const id=dueIdHiddenInput.value; if(!id){alert('Salve primeiro.');return;} if(confirm(`Enviar DU-E ${id}?`)){alert(`Envio NÃO IMPLEMENTADO (DU-E: ${id}).`);}}); 
        console.log("[main.mjs DOMContentLoaded] Listener Enviar Adicionado OK."); 
    }

    if(salvarDueButton) salvarDueButton.disabled = false; 
    if(enviarDueButton && dueIdHiddenInput) enviarDueButton.disabled = !dueIdHiddenInput.value; 
    if(batchEditButton) batchEditButton.disabled = !(window.processedNFData && window.processedNFData.length > 0 && window.processedNFData.some(nf => nf.items?.length > 0));

    console.log("[main.mjs DOMContentLoaded] Script principal: Aplicação pronta e listeners configurados.");
});

function showToast(message, type = 'info') {
    const toastContainer = document.getElementById('toast-container') || (() => {
        const tc = document.createElement('div');
        tc.id = 'toast-container';
        tc.style.position = 'fixed'; tc.style.top = '20px'; tc.style.right = '20px'; tc.style.zIndex = '1090';
        document.body.appendChild(tc);
        return tc;
    })();
    const toastEl = document.createElement('div');
    toastEl.className = `alert alert-${type === 'error' ? 'danger' : (type === 'success' ? 'success' : 'info')} alert-dismissible fade show m-1`;
    toastEl.setAttribute('role', 'alert');
    toastEl.innerHTML = `${htmlspecialchars(message)}<button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>`;
    toastContainer.appendChild(toastEl);
    if (bootstrap?.Alert) {
        const bsAlert = new bootstrap.Alert(toastEl);
        setTimeout(() => { bsAlert.close(); }, 5000);
    } else {
        setTimeout(() => { toastEl.classList.remove('show'); setTimeout(() => toastEl.remove(), 150);}, 5000);
    }
    console.log(`[Toast] Exibido: ${message}`);
}

function getCountryCodeByName(countryName) {
    if (!_paisesDataCache || !Array.isArray(_paisesDataCache)) {
        console.warn("[getCountryCodeByName] _paisesDataCache não disponível.");
        return null;
    }
    const countryNameToSearch = String(countryName)?.toLowerCase();
    const found = _paisesDataCache.find(p => p.NOME && p.NOME.toLowerCase() === countryNameToSearch);
    return found ? found.CODIGO_NUMERICO : null;
}
