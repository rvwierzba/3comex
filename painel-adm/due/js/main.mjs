// Arquivo: due/js/main.mjs

// --- Funções Auxiliares ---
const getSafe = (obj, path, defaultValue = '') => {
    try {
        const value = path.split('.').reduce((o, k) => (o || {})[k], obj);
        return value ?? defaultValue;
    } catch {
        return defaultValue;
    }
};
const getXmlValue = (el, tag) => el?.getElementsByTagName(tag)?.[0]?.textContent?.trim() ?? '';
const getXmlAttr = (el, attr) => el?.getAttribute(attr) ?? '';
const htmlspecialchars = (str) => {
    if (typeof str !== 'string') return str;
    return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
};

// --- Variáveis Globais ---
let processedNFData = []; // Armazena os dados das NFs processadas [{ nf: {...}, items: [{...}, ...] }, ...]
let itemDetailsModalInstance = null;
let batchEditModalInstance = null;

// --- Definição de Campos Obrigatórios para Status DUE ---
const requiredDueFields = [
    'ncm', 'descricaoDetalhadaDue', 'unidadeEstatistica', 'quantidadeEstatistica',
    'pesoLiquidoItem', 'condicaoVenda', 'vmcv', 'vmle', 'paisDestino', 'enquadramento1',
];

/**
 * Verifica se um item específico está com os campos mínimos para a DUE preenchidos.
 * @param {object} item - O objeto do item.
 * @returns {boolean} - True se completo, False caso contrário.
 */
function isItemDueComplete(item) {
    if (!item) return false;
    return requiredDueFields.every(fieldName => {
        const value = item[fieldName];
        let isFilled;
        if (Array.isArray(value)) {
            isFilled = value !== null && value !== undefined; // Array pode ser vazio intencionalmente (e.g., lpcos)
        } else if (typeof value === 'number') {
            isFilled = value !== null && value !== undefined && !isNaN(value); // Números não podem ser NaN
        } else {
            isFilled = value !== null && value !== undefined && value.toString().trim() !== ''; // Strings não podem ser vazias/null/undefined
        }
        return isFilled;
    });
}


// --- Parser XML ---
const parseNFeXML = (xmlString, fileName = 'arquivo') => {
    console.log(`[Parse XML] Iniciando para ${fileName}`);
    try {
        const parser = new DOMParser();
        const xmlDoc = parser.parseFromString(xmlString, "text/xml");
        const parserError = xmlDoc.getElementsByTagName("parsererror");
        if (parserError.length > 0) { throw new Error(`Erro de parse no XML: ${parserError[0].textContent}`); }

        const infNFe = xmlDoc.getElementsByTagName("infNFe")[0];
        if (!infNFe) { throw new Error(`Tag <infNFe> não encontrada`); }

        const chave = getXmlAttr(infNFe, 'Id').replace('NFe', '');
        const emit = infNFe.getElementsByTagName("emit")[0];
        const dest = infNFe.getElementsByTagName("dest")[0];
        const enderDest = dest?.getElementsByTagName("enderDest")[0];
        const exporta = infNFe.getElementsByTagName("exporta")[0];
        const infAdic = infNFe.getElementsByTagName("infAdic")[0];
        const detElements = infNFe.getElementsByTagName("det");

        const nfeData = {
            chaveAcesso: chave,
            emitente: {
                cnpj: getXmlValue(emit, "CNPJ"),
                nome: getXmlValue(emit, "xNome")
            },
            destinatario: {
                nome: getXmlValue(dest, "xNome"),
                idEstrangeiro: getXmlValue(dest, "idEstrangeiro"),
                endereco: {
                    logradouro: getXmlValue(enderDest, "xLgr"),
                    numero: getXmlValue(enderDest, "nro"),
                    bairro: getXmlValue(enderDest, "xBairro"),
                    municipio: getXmlValue(enderDest, "xMun"),
                    uf: getXmlValue(enderDest, "UF"),
                    paisNome: getXmlValue(enderDest, "xPais"),
                    paisCodigo: getXmlValue(enderDest, "cPais") // cPais (Código BACEN)
                }
            },
            exportacao: {
                ufSaidaPais: getXmlValue(exporta, "UFSaidaPais"),
                localExportacao: getXmlValue(exporta, "xLocExporta")
            },
            infAdicional: {
                infCpl: getXmlValue(infAdic, "infCpl"),
                infAdFisco: getXmlValue(infAdic, "infAdFisco")
            },
            items: []
        };

        console.log(`[Parse XML] Dados Cabeçalho OK para ${fileName}.`);

        for (let i = 0; i < detElements.length; i++) {
            const det = detElements[i];
            const prod = det.getElementsByTagName("prod")[0];
            if (!prod) { console.warn(`[Parse Item ${i+1}] Tag <prod> não encontrada.`); continue; }

            const nItem = getXmlAttr(det, 'nItem') || (i + 1).toString();
            const xProdValue = getXmlValue(prod, "xProd");
            const qComValue = getXmlValue(prod, "qCom");
            const vUnComValue = getXmlValue(prod, "vUnCom");
            const vProdValue = getXmlValue(prod, "vProd");
            const qTribValue = getXmlValue(prod, "qTrib");
            let pesoLiquidoXml = getXmlValue(prod, "pesoL") || getXmlValue(prod, "PESOL") || getXmlValue(prod, "PesoLiquido");

            // --- Conversões e Valores Padrão ---
            const qCom = parseFloat(qComValue) || 0;
            const vUnCom = parseFloat(vUnComValue) || 0;
            const vProd = parseFloat(vProdValue) || 0;
            const qTrib = parseFloat(qTribValue) || null; // Pode ser null se não informado
            // Limpa e converte peso líquido
            const pesoL = pesoLiquidoXml ? parseFloat(pesoLiquidoXml.replace(',', '.')) : null;
            const pesoLiquidoItem = isNaN(pesoL) ? null : pesoL;

             // Usa CodigoBACEN (cPais) do XML se existir
             const paisDestinoInicial = getSafe(nfeData, 'destinatario.endereco.paisCodigo', null);

            nfeData.items.push({
                // Dados do XML
                nItem: nItem,
                cProd: getXmlValue(prod, "cProd"),
                xProd: xProdValue,
                ncm: getXmlValue(prod, "NCM"),
                cfop: getXmlValue(prod, "CFOP"),
                uCom: getXmlValue(prod, "uCom"),
                qCom: qCom,
                vUnCom: vUnCom,
                vProd: vProd,
                uTrib: getXmlValue(prod, "uTrib"),
                qTrib: qTrib,
                infAdProd: getXmlValue(det, "infAdProd"), // Info adicional do item
                // Campos específicos da DU-E (inicializados)
                descricaoNcm: "",
                atributosNcm: "",
                unidadeEstatistica: getXmlValue(prod, "uTrib"), // Inicializa com uTrib (ajustar se necessário)
                quantidadeEstatistica: qTrib, // Usa qTrib convertido
                pesoLiquidoItem: pesoLiquidoItem, // Usa peso convertido
                condicaoVenda: "", // Incoterm
                vmcv: null, // Valor Moeda Cond Venda
                vmle: vProd, // VMLE (R$) - Inicializa com vProd
                paisDestino: paisDestinoInicial, // Código BACEN do país destino (do XML ou null)
                descricaoDetalhadaDue: xProdValue, // Inicializa com xProd
                enquadramento1: "", enquadramento2: "", enquadramento3: "", enquadramento4: "",
                lpcos: [], nfsRefEletronicas: [], nfsRefFormulario: [], nfsComplementares: [],
                ccptCcrom: ""
            });
        }
        console.log(`[Parse XML] ${fileName} OK - ${nfeData.items.length} itens processados.`);
        return nfeData;
    } catch (error) {
        console.error(`Erro GERAL no Parse XML de ${fileName}:`, error);
        const uploadStatusEl = document.getElementById('uploadStatus');
        if (uploadStatusEl) {
             uploadStatusEl.innerHTML += `<div class="text-danger small"><i class="bi bi-x-octagon-fill me-1"></i>Falha ao processar ${htmlspecialchars(fileName)}: ${htmlspecialchars(error.message)}</div>`;
        }
        return null;
    }
};


// --- Função para Criar os Campos do Modal Detalhado ---
function createItemDetailsFields(itemData, nfData, nfIndex, itemIndex) {
    // console.log(`[createItemDetailsFields] Iniciando para NF ${nfIndex}, Item ${itemIndex}`); // Log menos verboso
    const container = document.createElement('div');
    container.classList.add('item-details-form-container');

    const idPrefix = `modal-item-${nfIndex}-${itemIndex}-`;
    const val = (key, defaultValue = '') => getSafe(itemData, key, defaultValue);
    // Usar == em isSelected para comparar string do value com número/string do itemData.paisDestino
    const isSelected = (value, targetValue) => (value !== null && targetValue !== null && value == targetValue) ? 'selected' : '';
    const isChecked = (value, targetValue) => value === targetValue ? 'checked' : '';

    const createOptions = (data, valueKey, textKey, selectedValue, includeEmpty = true, dataAttrKey = null, dataAttrValueKey = null) => {
        let optionsHtml = includeEmpty ? '<option value="">Selecione...</option>' : '';
        if (data && Array.isArray(data) && data.length > 0) {
            optionsHtml += data.map(item => {
                const itemValue = getSafe(item, valueKey);
                const itemText = getSafe(item, textKey);
                let dataAttrHtml = '';
                if (dataAttrKey && dataAttrValueKey && item[dataAttrValueKey]) {
                    dataAttrHtml = ` data-${htmlspecialchars(dataAttrKey)}="${htmlspecialchars(getSafe(item, dataAttrValueKey))}"`;
                }
                const selectedAttr = isSelected(selectedValue, itemValue); // Usa comparação flexível
                return `<option value="${htmlspecialchars(itemValue)}" ${selectedAttr}${dataAttrHtml}>${htmlspecialchars(itemText)}</option>`;
            }).join('');
        }
        return optionsHtml;
    };

    // Opções para Enquadramento
    const enqOptions = (num) => {
        let html = '<option value="">Selecione...</option>';
        const enqData = window.enquadramentosData || [];
        const currentVal = val(`enquadramento${num}`);
        if (Array.isArray(enqData)) {
            html += enqData.map(enq =>
                 `<option value="${htmlspecialchars(enq.CODIGO)}" ${isSelected(currentVal, enq.CODIGO)}>${htmlspecialchars(enq.CODIGO)} - ${htmlspecialchars(enq.DESCRICAO)}</option>`
            ).join('');
        }
        html += `<option value="99999" ${isSelected(currentVal, '99999')}>99999 - OPERACAO SEM ENQUADRAMENTO</option>`;
        return html;
    };
    const enqSelectHTML = (num) => `<select id="${idPrefix}enquadramento${num}" name="enquadramento${num}" class="form-select form-select-sm">${enqOptions(num)}</select>`;

    // Opções para Incoterm
    const incotermTextMap = (window.incotermsData || []).map(i => ({ ...i, DisplayText: `${getSafe(i, 'Sigla')} - ${getSafe(i, 'Descricao')}` }));
    const incotermOptionsHTML = createOptions(incotermTextMap, 'Sigla', 'DisplayText', val('condicaoVenda'));

    // Opções para País Destino (value = CodigoBACEN, text = Nome)
    const paisOptionsHTML = createOptions(window.paisesData || [], 'CodigoBACEN', 'Nome', val('paisDestino'));

    // --- HTML do Modal ---
    container.innerHTML = `
    <h5 class="mb-3 border-bottom pb-2">Item ${htmlspecialchars(val('nItem', itemIndex + 1))} (NF-e: ...${htmlspecialchars(getSafe(nfData, 'chaveAcesso', 'N/A').slice(-6))})</h5>
    <h6>Dados Básicos e NCM</h6> <div class="row g-3 mb-4"> <div class="col-md-6"> <label class="form-label">Exportador:</label> <input type="text" class="form-control form-control-sm bg-light" value="${htmlspecialchars(getSafe(nfData, 'emitente.nome', 'N/A'))}" readonly> </div> <div class="col-md-6"> <label for="${idPrefix}ncm" class="form-label">NCM:</label> <input type="text" id="${idPrefix}ncm" name="ncm" class="form-control form-control-sm" value="${htmlspecialchars(val('ncm'))}" required> </div> <div class="col-md-6"> <label for="${idPrefix}descricao_ncm" class="form-label">Descrição NCM:</label> <input type="text" id="${idPrefix}descricao_ncm" name="descricaoNcm" class="form-control form-control-sm" value="${htmlspecialchars(val('descricaoNcm'))}" placeholder="Consultar externamente se necessário"> </div> <div class="col-md-6"> <label for="${idPrefix}atributos_ncm" class="form-label">Atributos NCM:</label> <input type="text" id="${idPrefix}atributos_ncm" name="atributosNcm" class="form-control form-control-sm" value="${htmlspecialchars(val('atributosNcm'))}" placeholder="Consultar/definir atributos"> </div> </div>
    <h6>Descrição da Mercadoria</h6> <div class="mb-3"> <label for="${idPrefix}descricao_mercadoria" class="form-label">Descrição Conforme NF-e:</label> <textarea id="${idPrefix}descricao_mercadoria" class="form-control form-control-sm bg-light" rows="2" readonly>${htmlspecialchars(val('xProd'))}</textarea> </div> <div class="mb-3"> <label for="${idPrefix}descricao_complementar" class="form-label">Descrição Complementar (NF-e - infAdProd):</label> <textarea id="${idPrefix}descricao_complementar" name="infAdProd" class="form-control form-control-sm" rows="2">${htmlspecialchars(val('infAdProd'))}</textarea> </div> <div class="mb-4"> <label for="${idPrefix}descricao_detalhada_due" class="form-label">Descrição Detalhada para DU-E:</label> <textarea id="${idPrefix}descricao_detalhada_due" name="descricaoDetalhadaDue" class="form-control form-control-sm" rows="4" placeholder="Descrição completa e detalhada exigida para a DU-E" required>${htmlspecialchars(val('descricaoDetalhadaDue'))}</textarea> </div>
    <h6>Quantidades e Valores</h6> <div class="row g-3 mb-4"> <div class="col-md-4"> <label for="${idPrefix}unidade_estatistica" class="form-label">Unid. Estatística (NCM):</label> <input type="text" id="${idPrefix}unidade_estatistica" name="unidadeEstatistica" class="form-control form-control-sm" value="${htmlspecialchars(val('unidadeEstatistica'))}" placeholder="Unid. conforme NCM" required> </div> <div class="col-md-4"> <label for="${idPrefix}quantidade_estatistica" class="form-label">Qtd. Estatística:</label> <input type="number" step="any" id="${idPrefix}quantidade_estatistica" name="quantidadeEstatistica" class="form-control form-control-sm" value="${htmlspecialchars(val('quantidadeEstatistica', ''))}" required> </div> <div class="col-md-4"> <label for="${idPrefix}peso_liquido" class="form-label">Peso Líquido Total Item (KG):</label> <input type="number" step="any" id="${idPrefix}peso_liquido" name="pesoLiquidoItem" class="form-control form-control-sm" value="${htmlspecialchars(val('pesoLiquidoItem', ''))}" required> </div> <div class="col-md-3"> <label for="${idPrefix}unidade_comercializada" class="form-label">Unid. Comercial.:</label> <input type="text" id="${idPrefix}unidade_comercializada" class="form-control form-control-sm bg-light" value="${htmlspecialchars(val('uCom'))}" readonly> </div> <div class="col-md-3"> <label for="${idPrefix}quantidade_comercializada" class="form-label">Qtd. Comercial.:</label> <input type="number" step="any" id="${idPrefix}quantidade_comercializada" class="form-control form-control-sm bg-light" value="${htmlspecialchars(val('qCom'))}" readonly> </div> <div class="col-md-3"> <label for="${idPrefix}valor_unit_comercial" class="form-label">Vlr Unit. Com. (R$):</label> <input type="number" step="any" id="${idPrefix}valor_unit_comercial" class="form-control form-control-sm bg-light" value="${htmlspecialchars(val('vUnCom'))}" readonly> </div> <div class="col-md-3"> <label class="form-label">Vlr Total Item (R$):</label> <input type="number" step="any" class="form-control form-control-sm bg-light" value="${htmlspecialchars(val('vProd'))}" readonly> </div> <div class="col-md-4"> <label for="${idPrefix}condicao_venda" class="form-label">Condição Venda (Incoterm):</label> <select id="${idPrefix}condicao_venda" name="condicaoVenda" class="form-select form-select-sm" required> ${incotermOptionsHTML} </select> </div> <div class="col-md-4"> <label for="${idPrefix}vmle" class="form-label">VMLE (R$):</label> <input type="number" step="any" id="${idPrefix}vmle" name="vmle" class="form-control form-control-sm" value="${htmlspecialchars(val('vmle', ''))}" title="Valor da Mercadoria no Local de Embarque" required> </div> <div class="col-md-4"> <label for="${idPrefix}vmcv" class="form-label">VMCV (Moeda Negoc.):</label> <input type="number" step="any" id="${idPrefix}vmcv" name="vmcv" class="form-control form-control-sm" value="${htmlspecialchars(val('vmcv', ''))}" title="Valor da Mercadoria na Condição de Venda (na moeda de negociação)" required> </div> </div>
    <h6>Importador e Destino</h6> <div class="row g-3 mb-4"> <div class="col-md-6"> <label class="form-label">Nome Importador (NF-e):</label> <input type="text" class="form-control form-control-sm bg-light" value="${htmlspecialchars(getSafe(nfData, 'destinatario.nome', 'N/A'))}" readonly> </div> <div class="col-md-6"> <label class="form-label">País Importador (NF-e):</label> <input type="text" class="form-control form-control-sm bg-light" value="${htmlspecialchars(getSafe(nfData, 'destinatario.endereco.paisNome', 'N/A'))} (${htmlspecialchars(getSafe(nfData, 'destinatario.endereco.paisCodigo', 'N/A'))})" readonly> </div> <div class="col-12"> <label class="form-label">Endereço Importador (NF-e):</label> <input type="text" class="form-control form-control-sm bg-light" value="${htmlspecialchars([getSafe(nfData, 'destinatario.endereco.logradouro'), getSafe(nfData, 'destinatario.endereco.numero'), getSafe(nfData, 'destinatario.endereco.bairro'), getSafe(nfData, 'destinatario.endereco.municipio'), getSafe(nfData, 'destinatario.endereco.uf')].filter(Boolean).join(', ') || '(Não informado na NF-e)')}" readonly> </div> <div class="col-md-6"> <label for="${idPrefix}pais_destino" class="form-label">País Destino Final (DU-E):</label> <select id="${idPrefix}pais_destino" name="paisDestino" class="form-select form-select-sm" required> ${paisOptionsHTML} </select> </div> </div>
    <h6>Enquadramentos da Operação</h6> <div class="row g-3 mb-4"> ${[1, 2, 3, 4].map(num => `<div class="col-md-6"> <label for="${idPrefix}enquadramento${num}" class="form-label">${num}º Enquadramento:</label> ${enqSelectHTML(num)} </div>`).join('')} <small class="text-muted">O 1º enquadramento é obrigatório.</small> </div>
    <h6>LPCO (Licenças, Permissões, Certificados e Outros)</h6> <div class="lpco-container mb-4 list-manager-section" id="${idPrefix}lpco-section"> <div class="input-group input-group-sm"> <input type="text" class="form-control list-item-input" placeholder="Digite o número do LPCO"> <button type="button" class="btn btn-success add-list-item-btn">Adicionar</button> </div> <div class="mt-2"> <label class="form-label small text-muted">LPCOs Adicionados:</label> <div class="border p-2 rounded bg-light list-display min-h-40px"> ${(val('lpcos', []) || []).map(lpco => `<span class="badge bg-secondary me-1 mb-1 list-item" data-value="${htmlspecialchars(lpco)}">${htmlspecialchars(lpco)} <button type="button" class="btn-close btn-close-white btn-sm remove-list-item" aria-label="Remover"></button></span>`).join('')} </div> <input type="hidden" name="lpcos" value="${htmlspecialchars((val('lpcos', []) || []).join(','))}"> </div> </div>
    <h6>Referências e Tratamento Tributário</h6> <div class="row g-3"> <div class="col-md-7"> <div class="border p-3 rounded mb-3 list-manager-section" id="${idPrefix}nfe-ref-section"> <label class="form-label fw-bold small mb-1">NF-e Referenciada</label> <div class="input-group input-group-sm mb-2"> <input type="text" class="form-control list-item-input" placeholder="Chave de Acesso (44 dígitos)"> <button class="btn btn-outline-secondary add-list-item-btn" type="button">Add</button> </div> <ul class="list-group list-group-flush list-display small ps-1"> ${(val('nfsRefEletronicas', []) || []).map(k => `<li class="list-group-item py-1 px-0 d-flex justify-content-between align-items-center list-item" data-value="${htmlspecialchars(k)}">${htmlspecialchars(k)}<button type="button" class="btn-close btn-sm remove-list-item" aria-label="Remover"></button></li>`).join('')} </ul> <input type="hidden" name="nfsRefEletronicas" value="${htmlspecialchars((val('nfsRefEletronicas', []) || []).join(','))}"> </div> <div class="border p-3 rounded mb-3 list-manager-section" id="${idPrefix}nf_form-ref-section"> <label class="form-label fw-bold small mb-1">NF Formulário Referenciada</label> <div class="input-group input-group-sm mb-2"> <input type="text" class="form-control list-item-input" placeholder="Série, Número, Modelo, etc."> <button class="btn btn-outline-secondary add-list-item-btn" type="button">Add</button> </div> <ul class="list-group list-group-flush list-display small ps-1"> ${(val('nfsRefFormulario', []) || []).map(d => `<li class="list-group-item py-1 px-0 d-flex justify-content-between align-items-center list-item" data-value="${htmlspecialchars(d)}">${htmlspecialchars(d)}<button type="button" class="btn-close btn-sm remove-list-item" aria-label="Remover"></button></li>`).join('')} </ul> <input type="hidden" name="nfsRefFormulario" value="${htmlspecialchars((val('nfsRefFormulario', []) || []).join(','))}"> </div> <div class="border p-3 rounded mb-3 mb-md-0 list-manager-section" id="${idPrefix}nfc-ref-section"> <label class="form-label fw-bold small mb-1">NF Complementar</label> <div class="input-group input-group-sm mb-2"> <input type="text" class="form-control list-item-input" placeholder="Chave de Acesso (44 dígitos)"> <button class="btn btn-outline-secondary add-list-item-btn" type="button">Add</button> </div> <ul class="list-group list-group-flush list-display small ps-1"> ${(val('nfsComplementares', []) || []).map(k => `<li class="list-group-item py-1 px-0 d-flex justify-content-between align-items-center list-item" data-value="${htmlspecialchars(k)}">${htmlspecialchars(k)}<button type="button" class="btn-close btn-sm remove-list-item" aria-label="Remover"></button></li>`).join('')} </ul> <input type="hidden" name="nfsComplementares" value="${htmlspecialchars((val('nfsComplementares', []) || []).join(','))}"> </div> </div> <div class="col-md-5"> <div class="border p-3 rounded h-100"> <h6 class="mb-3">Acordo Mercosul</h6> <div class="form-check mb-2"> <input class="form-check-input" type="radio" name="ccptCcrom" id="${idPrefix}ccpt_ccrom_none" value="" ${isChecked(val('ccptCcrom'), '')}> <label class="form-check-label small" for="${idPrefix}ccpt_ccrom_none">N/A (Não se aplica)</label> </div> <div class="form-check mb-2"> <input class="form-check-input" type="radio" name="ccptCcrom" id="${idPrefix}ccpt" value="CCPT" ${isChecked(val('ccptCcrom'), 'CCPT')}> <label class="form-check-label small" for="${idPrefix}ccpt">CCPT</label> </div> <div class="form-check"> <input class="form-check-input" type="radio" name="ccptCcrom" id="${idPrefix}ccrom" value="CCROM" ${isChecked(val('ccptCcrom'), 'CCROM')}> <label class="form-check-label small" for="${idPrefix}ccrom">CCROM</label> </div> </div> </div> </div>
    `;


    // --- Função Genérica para Gerenciar Listas (LPCO, Refs) ---
    const setupListManager = (sectionElement) => {
        const addButton = sectionElement.querySelector('.add-list-item-btn');
        const inputField = sectionElement.querySelector('.list-item-input');
        const displayArea = sectionElement.querySelector('.list-display');
        const hiddenInput = sectionElement.querySelector('input[type="hidden"]');

        // Função interna para atualizar o hidden input
        const updateHiddenInput = () => {
            if (!displayArea || !hiddenInput) return;
            const items = Array.from(displayArea.querySelectorAll('.list-item'))
                             .map(item => item.dataset.value);
            hiddenInput.value = items.join(',');
            // console.log(`Hidden input ${hiddenInput.name} atualizado: ${hiddenInput.value}`);
        };

        // Função para adicionar listener de remoção a um botão
        const addRemoveListener = (button) => {
            button.addEventListener('click', (e) => {
                 e.target.closest('.list-item').remove();
                 updateHiddenInput();
             });
        };

        // Adicionar item
        if (addButton && inputField && displayArea && hiddenInput) {
            addButton.addEventListener('click', () => {
                const value = inputField.value.trim();
                if (value) {
                    const exists = Array.from(displayArea.querySelectorAll('.list-item')).some(item => item.dataset.value === value);
                    if (exists) {
                        alert('Este item já foi adicionado.');
                        return;
                    }

                    let newItemElement;
                    if (displayArea.tagName === 'UL') { // Se for lista UL/LI
                        newItemElement = document.createElement('li');
                        newItemElement.className = 'list-group-item py-1 px-0 d-flex justify-content-between align-items-center list-item';
                        newItemElement.innerHTML = `${htmlspecialchars(value)}<button type="button" class="btn-close btn-sm remove-list-item" aria-label="Remover"></button>`;
                    } else { // Se for div com badges (LPCO)
                        newItemElement = document.createElement('span');
                        newItemElement.className = 'badge bg-secondary me-1 mb-1 list-item';
                        newItemElement.innerHTML = `${htmlspecialchars(value)} <button type="button" class="btn-close btn-close-white btn-sm remove-list-item" aria-label="Remover"></button>`;
                    }
                    newItemElement.dataset.value = value;
                    addRemoveListener(newItemElement.querySelector('.remove-list-item')); // Adiciona listener ao novo botão

                    displayArea.appendChild(newItemElement);
                    inputField.value = '';
                    updateHiddenInput();
                } else {
                     alert("Por favor, digite um valor para adicionar.");
                }
            });
        }

        // Adiciona listeners aos botões de remoção já existentes
        displayArea.querySelectorAll('.remove-list-item').forEach(addRemoveListener);
    };

    // Aplica o gerenciador a cada seção relevante
    container.querySelectorAll('.list-manager-section, .lpco-container').forEach(setupListManager);

    return container;
} // --- Fim createItemDetailsFields ---


// --- Renderização da Tabela de Itens ---
function renderNotasFiscaisTable() {
    // console.log("[Render Tabela] Iniciando..."); // Log menos verboso
    const tbody = document.querySelector('#notasFiscaisTable tbody');
    const theadRow = document.querySelector('#notasFiscaisTable thead tr');
    const batchButton = document.getElementById('batchEditButton');

    if (!tbody || !theadRow) {
        console.error("Elementos tbody ou thead>tr #notasFiscaisTable não encontrados.");
        return;
    }
    tbody.innerHTML = ''; // Limpa antes de renderizar

    // Garante header de Status no final
    let statusHeader = theadRow.querySelector('.status-header');
    if (!statusHeader) {
        statusHeader = document.createElement('th');
        statusHeader.textContent = 'Status DUE';
        statusHeader.classList.add('status-header');
        statusHeader.style.width = '80px'; statusHeader.style.textAlign = 'center';
        theadRow.appendChild(statusHeader);
    } else if (statusHeader !== theadRow.lastElementChild) {
        theadRow.appendChild(statusHeader);
    }
    const colCount = theadRow.cells.length;

    let hasItems = false;
    let totalItemsRendered = 0;

    if (processedNFData.length === 0) {
        tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center text-muted fst-italic">Carregue arquivos XML...</td></tr>`;
        if (batchButton) batchButton.disabled = true;
        // console.log("[Render Tabela] Nenhuma NF processada.");
        return;
    }

    processedNFData.forEach((nfEntry, nfIndex) => {
        if (!nfEntry || !nfEntry.nf || !Array.isArray(nfEntry.items)) return; // Pula inválidos

        const nf = nfEntry.nf;
        const items = nfEntry.items;
        const chaveNFeShort = getSafe(nf, 'chaveAcesso', 'CHAVE_INVÁLIDA').slice(-9);
        const nomeDest = getSafe(nf, 'destinatario.nome', 'N/A');
        const paisDestCodXml = getSafe(nf, 'destinatario.endereco.paisCodigo', null);
        let paisDestNome = getSafe(nf, 'destinatario.endereco.paisNome', 'N/A');
        // Tenta achar nome do país pelo código se não veio nome no XML
         if ((!paisDestNome || paisDestNome === 'N/A') && paisDestCodXml && window.paisesData) {
             const paisEncontrado = window.paisesData.find(p => p.CodigoBACEN == paisDestCodXml);
             if (paisEncontrado) paisDestNome = paisEncontrado.Nome;
             else if (paisDestCodXml) paisDestNome = `Código ${paisDestCodXml}`;
         }

        if (items.length === 0) return; // Pula NF sem itens

        items.forEach((item, itemIndex) => {
            if (!item) return; // Pula item inválido
            hasItems = true; totalItemsRendered++;

            const row = document.createElement('tr');
            row.classList.add('item-row');
            row.dataset.nfIndex = nfIndex; row.dataset.itemIndex = itemIndex;

            // Células básicas
            row.innerHTML = `
                <td>...${htmlspecialchars(chaveNFeShort)}</td>
                <td class="text-center">${htmlspecialchars(getSafe(item, 'nItem', itemIndex + 1))}</td>
                <td>${htmlspecialchars(getSafe(item, 'ncm', 'N/A'))}</td>
                <td>${htmlspecialchars(getSafe(item, 'xProd', 'N/A'))}</td>
                <td>${htmlspecialchars(nomeDest)}</td>
                <td>${htmlspecialchars(paisDestNome)}</td>`;

            // Célula de Ações (+)
            const actionsCell = document.createElement('td');
            actionsCell.classList.add('text-center');
            const toggleBtn = document.createElement('button');
            toggleBtn.type = 'button'; toggleBtn.classList.add('btn', 'toggle-details');
            toggleBtn.title = `Abrir Detalhes do Item ${htmlspecialchars(getSafe(item, 'nItem', itemIndex + 1))}`;
            toggleBtn.dataset.nfIndex = nfIndex; toggleBtn.dataset.itemIndex = itemIndex;
            toggleBtn.innerHTML = '+';
            actionsCell.appendChild(toggleBtn);
            row.appendChild(actionsCell);

            // Célula de Status (ícone)
            const statusCell = document.createElement('td');
            const completo = isItemDueComplete(item);
            statusCell.style.textAlign = 'center'; statusCell.style.verticalAlign = 'middle';
            statusCell.innerHTML = completo
                ? '<span style="color: green; font-size: 1.2em; font-weight: bold;" title="Completo para DU-E">&#x2705;</span>'
                : '<span style="color: red; font-size: 1.2em; font-weight: bold;" title="Incompleto para DU-E">&#x274C;</span>';
            row.appendChild(statusCell);

            tbody.appendChild(row);
        });
    });

    // Mensagens finais
    if (!hasItems && processedNFData.length > 0) {
        tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center text-warning fst-italic">Nenhum item válido encontrado.</td></tr>`;
        if (batchButton) batchButton.disabled = true;
    } else if (hasItems) {
        if (batchButton) batchButton.disabled = false;
        console.log(`[Render Tabela] Concluído. ${totalItemsRendered} itens exibidos.`);
    } else {
         tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center text-muted fst-italic">Carregue arquivos XML...</td></tr>`;
         if (batchButton) batchButton.disabled = true;
    }
}


// --- Preencher Campos da Aba 1 ---
const populateMainForm = (nfData) => {
    // Elementos do formulário
    const formElements = {
        cnpjCpf: document.getElementById('text-cnpj-cpf-select'),
        nomeCliente: document.getElementById('nomeCliente'),
        infoCompl: document.getElementById('info-compl'),
        moeda: document.getElementById('text-moeda'),
        ruc: document.getElementById('ruc'),
        situacaoEspec: document.getElementById('situacao-espec-despacho'),
        unidadeRfbD: document.getElementById('text-campo-de-pesquisa-unidades-rfb-d'),
        recintoD: document.getElementById('text-campo-de-pesquisa-recinto-alfandegado-d'),
        unidadeRfbE: document.getElementById('text-campo-de-pesquisa-unidades-rfb-e'),
        recintoE: document.getElementById('text-campo-de-pesquisa-recinto-alfandegado-e'),
        viaEspecial: document.getElementById('via-especial-transport'),
        exportCons: document.getElementById('export-cons'),
        radioContaPropria: document.getElementById('por-conta-propria'),
        radioNfe: document.getElementById('nfe')
        // Adicione outros elementos se necessário
    };

    if (nfData) {
        // Preenche com dados da primeira NF válida
        if (formElements.cnpjCpf) formElements.cnpjCpf.value = getSafe(nfData, 'emitente.cnpj', '');
        if (formElements.nomeCliente) formElements.nomeCliente.value = getSafe(nfData, 'emitente.nome', '');
        // Preenche info complementar apenas se vazio
        if (formElements.infoCompl && !formElements.infoCompl.value.trim()) {
            formElements.infoCompl.value = getSafe(nfData, 'infAdicional.infCpl', '');
        }
        console.log(`[Aba 1] Dados do emitente preenchidos a partir da NF.`);
    } else {
        // Limpa todos os campos do formulário principal
        Object.values(formElements).forEach(el => {
            if (el) {
                 if (el.type === 'checkbox' || el.type === 'radio') {
                     // Para radios/checkboxes, desmarcar (exceto os padrão)
                     if (el.id !== 'por-conta-propria' && el.id !== 'nfe') {
                        el.checked = false;
                     }
                 } else if (el.tagName === 'SELECT') {
                     el.value = ''; // Reseta select
                 } else {
                     el.value = ''; // Limpa input/textarea
                 }
            }
        });
         // Garante que os radios padrão fiquem marcados
         if(formElements.radioContaPropria) formElements.radioContaPropria.checked = true;
         if(formElements.radioNfe) formElements.radioNfe.checked = true;

        console.log(`[Aba 1] Formulário principal limpo.`);
    }
};


// --- Código Principal (Inicialização e Listeners) ---
document.addEventListener('DOMContentLoaded', () => {
    console.log("DOM Carregado. Iniciando script main.mjs.");

    // --- Verificação Dados PHP ---
    console.log("JS: Verificando dados pré-carregados...");
    // (Verificações já existentes são mantidas)
    if (typeof window.incotermsData === 'undefined' || !Array.isArray(window.incotermsData)) { console.warn("window.incotermsData ausente/inválido."); window.incotermsData = []; }
    if (typeof window.enquadramentosData === 'undefined' || !Array.isArray(window.enquadramentosData)) { console.warn("window.enquadramentosData ausente/inválido."); window.enquadramentosData = []; }
    if (typeof window.paisesData === 'undefined' || !Array.isArray(window.paisesData)) { console.warn("window.paisesData ausente/inválido."); window.paisesData = []; }
    console.log("JS: Dados auxiliares OK.");

    // --- Referências UI ---
    const inputXML = document.getElementById('xml-files');
    const uploadStatus = document.getElementById('uploadStatus');
    const spinner = document.getElementById('spinner');
    const notasTable = document.querySelector('#notasFiscaisTable');
    const itemDetailsModalElement = document.getElementById('itemDetailsModal');
    const saveItemButtonModal = document.getElementById('saveItemDetails');
    const batchEditButton = document.getElementById('batchEditButton');
    const batchEditModalElement = document.getElementById('batchEditModal');
    const saveBatchButton = document.getElementById('saveBatchEdit');
    const mainForm = document.getElementById('dueForm');
    const salvarDueButton = document.getElementById('salvarDUE');
    const enviarDueButton = document.getElementById('enviarDUE');
    const dueIdHiddenInput = document.getElementById('due_id_hidden');

    // --- Verificação Elementos Essenciais ---
    const essentialElements = { inputXML, uploadStatus, spinner, notasTable, itemDetailsModalElement, saveItemButtonModal, batchEditButton, batchEditModalElement, saveBatchButton, mainForm, salvarDueButton, enviarDueButton, dueIdHiddenInput };
    let missingElement = false;
    for (const key in essentialElements) {
        if (!essentialElements[key]) {
            console.error(`ERRO FATAL: Elemento UI essencial não encontrado: ${key}`);
            missingElement = true;
        }
    }
    if (missingElement) {
        alert("Erro crítico na inicialização da página. Alguns componentes essenciais não foram encontrados. Verifique o console (F12).");
        if (salvarDueButton) salvarDueButton.disabled = true;
        if (enviarDueButton) enviarDueButton.disabled = true;
        return;
    }
    console.log("Elementos UI referenciados OK.");

    // --- Inicializar Modais Bootstrap ---
    try {
        if (window.bootstrap && bootstrap.Modal) {
            itemDetailsModalInstance = new bootstrap.Modal(itemDetailsModalElement);
            batchEditModalInstance = new bootstrap.Modal(batchEditModalElement);
            // Listeners para limpar modais ao fechar (código anterior mantido)
            itemDetailsModalElement.addEventListener('hidden.bs.modal', () => {
                if (saveItemButtonModal) { delete saveItemButtonModal.dataset.nfIndex; delete saveItemButtonModal.dataset.itemIndex; }
                const mb = itemDetailsModalElement.querySelector('.modal-body'); if (mb) mb.innerHTML = '<div class="text-center p-5">...</div>';
                // console.log("Modal item fechado.");
            });
            batchEditModalElement.addEventListener('hidden.bs.modal', () => {
                const bf = document.getElementById('batchEditForm');
                 if (bf) {
                    bf.reset();
                    const ra = bf.querySelector('#batchCcptCcromAlterar'); if(ra) ra.checked = true;
                 }
                // console.log("Modal lote fechado.");
            });
            console.log("Modais Bootstrap OK.");
        } else { throw new Error("Bootstrap Modal não encontrado."); }
    } catch (e) {
        console.error("Falha ao inicializar modais:", e);
        alert("Erro ao inicializar componentes da interface (Modais).");
        if (batchEditButton) batchEditButton.disabled = true;
    }

    // --- Renderizar Tabela Inicial Vazia ---
    renderNotasFiscaisTable();

    // --- Listener Input XML ---
    inputXML.addEventListener('change', async (event) => {
        console.log("[Input XML] Evento 'change'.");
        const files = event.target.files;
        if (!files || files.length === 0) { /* ... (lógica de limpeza como antes) ... */
            uploadStatus.textContent = 'Nenhum arquivo selecionado.'; processedNFData = []; renderNotasFiscaisTable(); populateMainForm(null); if (dueIdHiddenInput) dueIdHiddenInput.value = ''; if (enviarDueButton) enviarDueButton.disabled = true; if (salvarDueButton) salvarDueButton.disabled = true; if (batchEditButton) batchEditButton.disabled = true; console.log("[Input XML] Nenhum arquivo."); return;
        }

        uploadStatus.innerHTML = `<div class="d-flex align-items-center"><div class="spinner-grow spinner-grow-sm me-2" role="status"></div> <span>Processando ${files.length} arquivo(s)...</span></div>`;
        spinner.style.display = 'block'; inputXML.disabled = true; salvarDueButton.disabled = true; enviarDueButton.disabled = true; batchEditButton.disabled = true;

        processedNFData = []; // Limpa dados anteriores
        let promises = []; let errorCount = 0; let warningCount = 0; let statusMessagesHTML = '';
        console.log(`[Input XML] Lendo ${files.length} arquivo(s).`);

        for (const file of files) { /* ... (lógica de validação e parse como antes) ... */
             if (file.name.toLowerCase().endsWith('.xml') && (file.type === 'text/xml' || file.type === 'application/xml' || file.type === '')) {
                promises.push( file.text().then(xml => { const data = parseNFeXML(xml, file.name); if (data?.items?.length > 0) { processedNFData.push({ nf: data, items: data.items }); statusMessagesHTML += `<div class="text-success small"><i class="bi bi-check-circle-fill me-1"></i>${htmlspecialchars(file.name)}: OK (${data.items.length} item(ns))</div>`; } else if (data) { statusMessagesHTML += `<div class="text-warning small"><i class="bi bi-exclamation-triangle-fill me-1"></i>${htmlspecialchars(file.name)}: Sem itens válidos.</div>`; warningCount++; } else { errorCount++; /* Msg erro no parse */ } }).catch(err => { console.error(`Erro LER ${file.name}:`, err); statusMessagesHTML += `<div class="text-danger small"><i class="bi bi-x-octagon-fill me-1"></i>Falha LER ${htmlspecialchars(file.name)}.</div>`; errorCount++; }) );
             } else { statusMessagesHTML += `<div class="text-secondary small"><i class="bi bi-slash-circle-fill me-1"></i>${htmlspecialchars(file.name)}: Ignorado (não XML).</div>`; warningCount++; }
        }

        try { await Promise.all(promises); console.log("[Input XML] Promises OK."); }
        catch (err) { console.error("Erro GERAL async:", err); statusMessagesHTML += `<div class="text-danger">Erro inesperado.</div>`; errorCount++; }
        finally { /* ... (lógica de finalização como antes, incluindo limpar dueIdHiddenInput e gerenciar disabled dos botões) ... */
            spinner.style.display = 'none'; inputXML.disabled = false; event.target.value = null;
            if (dueIdHiddenInput) { dueIdHiddenInput.value = ''; console.log("[Input XML] ID oculto limpo."); } // Limpa ID
            const totalItems = processedNFData.reduce((sum, entry) => sum + (entry.items ? entry.items.length : 0), 0); const totalNFs = processedNFData.length; uploadStatus.innerHTML = statusMessagesHTML;
            if (totalItems > 0) { populateMainForm(processedNFData[0]?.nf); uploadStatus.insertAdjacentHTML('beforeend', `<hr class="my-1"><div class="text-primary fw-bold small">Total: ${totalItems} item(ns) em ${totalNFs} NF-e(s).</div>`); if (errorCount > 0) uploadStatus.insertAdjacentHTML('beforeend', `<div class="text-danger small">(${errorCount} erro(s))</div>`); salvarDueButton.disabled = false; enviarDueButton.disabled = true; /* Habilita Salvar, desabilita Enviar */ batchEditButton.disabled = false; }
            else { populateMainForm(null); if (errorCount > 0) { uploadStatus.insertAdjacentHTML('beforeend', `<hr class="my-1"><div class="text-danger fw-bold small">Falha. Nenhum item. Ver console.</div>`); } else { uploadStatus.insertAdjacentHTML('beforeend', `<hr class="my-1"><div class="text-warning small">Nenhum item válido.</div>`); } salvarDueButton.disabled = true; enviarDueButton.disabled = true; batchEditButton.disabled = true; }
            renderNotasFiscaisTable(); console.log("[Input XML] FIM.");
        }
    });


    // --- Listener Abrir Modal Item ---
    notasTable.addEventListener('click', (e) => { /* ... (código como na resposta anterior) ... */
         const detailsButton = e.target.closest('button.toggle-details'); if (!detailsButton) return;
         const nfIndex = parseInt(detailsButton.dataset.nfIndex, 10); const itemIndex = parseInt(detailsButton.dataset.itemIndex, 10);
         console.log(`[Abrir Modal Item] NF ${nfIndex}, Item ${itemIndex}`);
         if (isNaN(nfIndex) || isNaN(itemIndex) || !processedNFData[nfIndex]?.items?.[itemIndex]) { console.error("Índices/dados inválidos modal:", nfIndex, itemIndex, processedNFData); alert("Erro: Dados item não encontrados."); return; }
         try {
             const nfData = processedNFData[nfIndex].nf; const itemData = processedNFData[nfIndex].items[itemIndex];
             const modalBody = itemDetailsModalElement.querySelector('.modal-body'); const modalTitle = itemDetailsModalElement.querySelector('.modal-title');
             if (!modalBody || !modalTitle || !itemDetailsModalInstance || !saveItemButtonModal) { console.error("Modal elems?"); alert("Erro interno modal."); return; }
             modalTitle.textContent = `Detalhes Item ${htmlspecialchars(getSafe(itemData, 'nItem', itemIndex + 1))} (NF: ...${htmlspecialchars(getSafe(nfData, 'chaveAcesso', 'N/A').slice(-6))})`;
             modalBody.innerHTML = '<div class="text-center p-5"><div class="spinner-border text-primary"></div></div>'; // Spinner
             saveItemButtonModal.dataset.nfIndex = nfIndex; saveItemButtonModal.dataset.itemIndex = itemIndex;
             setTimeout(() => { // Gera conteúdo async
                 try { console.time("createItemDetailsFields"); modalBody.innerHTML = ''; modalBody.appendChild(createItemDetailsFields(itemData, nfData, nfIndex, itemIndex)); console.timeEnd("createItemDetailsFields"); itemDetailsModalInstance.show(); console.log(`[Abrir Modal Item] Exibido.`); }
                 catch (renderErr) { console.error("Erro renderizar modal:", renderErr); modalBody.innerHTML = `<div class="alert alert-danger">Erro carregar detalhes.</div>`; if (!itemDetailsModalInstance._isShown) itemDetailsModalInstance.show(); }
             }, 50);
         } catch (err) { console.error("Erro geral abrir modal:", err); alert(`Erro abrir detalhes: ${err.message}`); }
    });


    // --- Listener Salvar Modal Item ---
    saveItemButtonModal.addEventListener('click', () => { /* ... (código como na resposta anterior, com getModalValue, getModalRadioValue, getHiddenListValue, Object.assign, feedback visual e renderNotasFiscaisTable()) ... */
         const nfIndex = parseInt(saveItemButtonModal.dataset.nfIndex, 10); const itemIndex = parseInt(saveItemButtonModal.dataset.itemIndex, 10);
         console.log(`[Salvar Modal Item] Tentando salvar NF ${nfIndex}, Item ${itemIndex}`);
         if (isNaN(nfIndex) || isNaN(itemIndex) || !processedNFData[nfIndex]?.items?.[itemIndex]) { console.error("Ref. inválida salvar."); alert("Erro salvar item."); return; }

         const itemDataRef = processedNFData[nfIndex].items[itemIndex]; // Referência ao objeto original
         const idPrefix = `modal-item-${nfIndex}-${itemIndex}-`;
         const modalContent = itemDetailsModalElement.querySelector('.modal-body .item-details-form-container'); if (!modalContent) { console.error("Corpo modal salvar?"); return; }

         console.log("[Salvar Modal Item] --- Lendo valores do Modal ---");
         try {
             const getModalValue = (fieldIdSuffix, convertToNumber = false, isFloat = true) => {
                 const el = modalContent.querySelector(`#${idPrefix}${fieldIdSuffix}`); let value = el?.value ?? null;
                 if (value !== null) { value = value.trim(); if (convertToNumber) { if (value === '') { value = null; } else { const cleanValue = value.replace(',', '.'); const num = isFloat ? parseFloat(cleanValue) : parseInt(cleanValue, 10); value = isNaN(num) ? null : num; } } }
                 // console.log(`  - Campo ${fieldIdSuffix}: lido '${el?.value}', processado para:`, value); // Log verboso
                 return value;
             };
             const getModalRadioValue = (radioName) => { const el = modalContent.querySelector(`input[name="${radioName}"]:checked`); const value = el?.value ?? ""; /*console.log(`  - Radio ${radioName}: selecionado '${value}'`);*/ return value; };
             const getHiddenListValue = (hiddenInputName) => { const el = modalContent.querySelector(`input[name="${hiddenInputName}"]`); const value = (el?.value || '').split(',').map(item => item.trim()).filter(Boolean); /*console.log(`  - Lista ${hiddenInputName}: processado para:`, value);*/ return value; };

             const newData = {
                 ncm: getModalValue('ncm'), descricaoNcm: getModalValue('descricao_ncm'), atributosNcm: getModalValue('atributos_ncm'),
                 infAdProd: getModalValue('descricao_complementar'), descricaoDetalhadaDue: getModalValue('descricao_detalhada_due'),
                 unidadeEstatistica: getModalValue('unidade_estatistica'), quantidadeEstatistica: getModalValue('quantidade_estatistica', true, true),
                 pesoLiquidoItem: getModalValue('peso_liquido', true, true), condicaoVenda: getModalValue('condicao_venda'),
                 vmle: getModalValue('vmle', true, true), vmcv: getModalValue('vmcv', true, true), paisDestino: getModalValue('pais_destino'),
                 enquadramento1: getModalValue('enquadramento1'), enquadramento2: getModalValue('enquadramento2'),
                 enquadramento3: getModalValue('enquadramento3'), enquadramento4: getModalValue('enquadramento4'),
                 lpcos: getHiddenListValue('lpcos'), nfsRefEletronicas: getHiddenListValue('nfsRefEletronicas'),
                 nfsRefFormulario: getHiddenListValue('nfsRefFormulario'), nfsComplementares: getHiddenListValue('nfsComplementares'),
                 ccptCcrom: getModalRadioValue('ccptCcrom')
             };

             console.log("[Salvar Modal Item] --- Atualizando objeto itemDataRef ---");
             // console.log("  Objeto ANTES:", JSON.parse(JSON.stringify(itemDataRef))); // Log verboso
             Object.assign(itemDataRef, newData); // Mescla/sobrescreve dados no objeto original
             // console.log("  Objeto DEPOIS:", JSON.parse(JSON.stringify(itemDataRef))); // Log verboso

             // Feedback visual e fecha modal
             const originalButtonText = saveItemButtonModal.innerHTML; saveItemButtonModal.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Salvando...`; saveItemButtonModal.disabled = true;
             setTimeout(() => {
                 if (itemDetailsModalInstance) itemDetailsModalInstance.hide();
                 alert("Dados do item atualizados localmente!");
                 saveItemButtonModal.innerHTML = originalButtonText; saveItemButtonModal.disabled = false;
                 renderNotasFiscaisTable(); // Atualiza status na tabela principal
                 console.log("[Salvar Modal Item] Dados atualizados em memória e tabela re-renderizada.");
             }, 300);

         } catch (saveErr) { console.error("Erro durante atualização do item:", saveErr); alert(`Erro ao salvar dados do item: ${saveErr.message}.`); saveItemButtonModal.innerHTML = 'Salvar Alterações do Item'; saveItemButtonModal.disabled = false; }
    });


    // --- Listener Botão Lote (Abrir Modal) ---
    batchEditButton.addEventListener('click', () => { /* ... (código como na resposta anterior) ... */
         if (processedNFData.length === 0 || processedNFData.every(nf => !nf.items || nf.items.length === 0)) { alert("Não há itens carregados."); return; }
         if (batchEditModalInstance) { console.log("[Edição Lote] Abrindo modal..."); batchEditModalInstance.show(); } else { alert("Erro ao abrir modal de lote."); }
    });


    // --- Listener Salvar Modal Lote ---
    saveBatchButton.addEventListener('click', () => { /* ... (código como na resposta anterior, mas com busca de CodigoBACEN) ... */
         console.log("[Edição Lote] Aplicando...");
         const batchForm = document.getElementById('batchEditForm');
         if (!batchForm || (processedNFData.length === 0 || processedNFData.every(nf => !nf.items || nf.items.length === 0)) ) { alert("Formulário ou itens não encontrados."); if (batchEditModalInstance) batchEditModalInstance.hide(); return; }

         const incotermLote = batchForm.querySelector('#batchIncotermSelect').value;
         const paisNomeLoteInput = batchForm.querySelector('#batchPaisDestinoInput');
         const paisNomeLote = paisNomeLoteInput.value.trim(); // Pega o NOME digitado/selecionado
         let paisCodigoLote = null; // Precisamos do CÓDIGO

         if (paisNomeLote && window.paisesData) {
             // Tenta encontrar o país na lista de dados (window.paisesData) pelo NOME
             const paisEncontrado = window.paisesData.find(p => p.Nome.toLowerCase() === paisNomeLote.toLowerCase());
             if (paisEncontrado && paisEncontrado.CodigoBACEN) {
                 paisCodigoLote = paisEncontrado.CodigoBACEN; // Pega o código correspondente
                 console.log(`[Edição Lote] País encontrado: ${paisNomeLote} -> Código BACEN: ${paisCodigoLote}`);
             } else {
                 console.warn(`[Edição Lote] País "${paisNomeLote}" não encontrado nos dados carregados. Código não será aplicado.`);
                 alert(`Atenção: O país "${paisNomeLote}" não foi encontrado na lista de países válidos. Este campo não será alterado em lote.`);
             }
         }

         const enqsLote = [1, 2, 3, 4].map(i => batchForm.querySelector(`#batchEnquadramento${i}Select`).value);
         const ccptCcromLote = batchForm.querySelector('input[name="batchCcptCcrom"]:checked').value;

         console.log("[Edição Lote] Valores a aplicar:", { incotermLote, paisCodigoLote, enqsLote, ccptCcromLote });
         let itemsChangedCount = 0;

         processedNFData.forEach((nfEntry) => {
             if (nfEntry.items && Array.isArray(nfEntry.items)) {
                 nfEntry.items.forEach((item) => {
                     let changed = false;
                     if (incotermLote && item.condicaoVenda !== incotermLote) { item.condicaoVenda = incotermLote; changed = true; }
                     if (paisCodigoLote && item.paisDestino !== paisCodigoLote) { item.paisDestino = paisCodigoLote; changed = true; } // Aplica o CÓDIGO
                     enqsLote.forEach((enq, i) => { const key = `enquadramento${i+1}`; if (enq && item[key] !== enq) { item[key] = enq; changed = true; } });
                     if (ccptCcromLote !== "" && item.ccptCcrom !== ccptCcromLote) { item.ccptCcrom = ccptCcromLote; changed = true; }
                     if (changed) itemsChangedCount++;
                 });
             }
         });

         if (batchEditModalInstance) batchEditModalInstance.hide();
         renderNotasFiscaisTable(); // Atualiza a tabela
         alert(`${itemsChangedCount} item(ns) atualizados localmente.`);
         console.log(`[Edição Lote] Concluído. ${itemsChangedCount} itens modificados.`);
    });


    // --- Listener Abas ---
    document.querySelectorAll('#dueTabs .nav-link').forEach(link => { /* ... (código como antes) ... */
        link.addEventListener('shown.bs.tab', event => { console.log(`Aba ativada: ${event.target.id}`); });
    });


    // --- Listener Botão SALVAR DU-E (Chama Backend) ---
    salvarDueButton.addEventListener('click', async () => { /* ... (código como na resposta anterior, com fetch para salvar_due.php, tratamento de resposta JSON, atualização do dueIdHiddenInput e gerenciamento do disabled dos botões) ... */
         console.log("[Salvar DU-E] Clicado.");
         spinner.style.display = 'block'; salvarDueButton.disabled = true; enviarDueButton.disabled = true;

         const formDataObj = {};
         const formDataEntries = new FormData(mainForm);
         for (const [key, value] of formDataEntries.entries()) {
             const element = mainForm.elements[key];
              if (element?.type === 'checkbox') { formDataObj[key] = element.checked; } // Envia true/false
              else if (element?.type === 'radio') { const checkedRadio = mainForm.querySelector(`input[name="${key}"]:checked`); formDataObj[key] = checkedRadio ? checkedRadio.value : null; }
              else if(element?.tagName === 'SELECT' && value === '') { formDataObj[key] = null; }
              else { formDataObj[key] = value.trim() === '' ? null : value; }
         }
         formDataObj['due_id'] = dueIdHiddenInput.value || null; // Pega do oculto

         const itemsToSave = processedNFData;
         console.log("Dados Formulário:", formDataObj);
         // console.log("Dados Itens:", JSON.stringify(itemsToSave)); // Log muito longo

         if (itemsToSave.reduce((count, nf) => count + (nf.items?.length || 0), 0) === 0) {
             alert("Não é possível salvar sem itens."); spinner.style.display = 'none'; salvarDueButton.disabled = false; enviarDueButton.disabled = !dueIdHiddenInput.value; return;
         }

         const payload = { formData: formDataObj, itemsData: itemsToSave };

         try {
             console.log("Enviando para due/salvar_due.php...");
             const response = await fetch('due/salvar_due.php', { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' }, body: JSON.stringify(payload) });

              // Primeiro, verifica se a resposta é OK (status 2xx)
              if (!response.ok) {
                  // Se não for OK, tenta ler como texto para ver erro HTML/PHP
                  const errorText = await response.text();
                  console.error(`Erro HTTP ${response.status}: ${response.statusText}. Resposta do servidor:`, errorText);
                  alert(`Falha ao salvar a DU-E. Erro no servidor (HTTP ${response.status}). Verifique o console e os logs do PHP.`);
                  throw new Error(`HTTP error ${response.status}`); // Lança erro para cair no catch
              }

              // Se a resposta foi OK, TENTA parsear como JSON
              const result = await response.json(); // Pode falhar aqui se a resposta OK não for JSON válido

             // Processa resultado JSON
             if (result.success) {
                 console.log("Resposta OK:", result);
                 alert(result.message || 'DU-E salva!');
                 if (result.due_id) { dueIdHiddenInput.value = result.due_id; console.log(`ID oculto atualizado: ${result.due_id}`); document.title = `DU-E: ${result.due_id}`; }
                 enviarDueButton.disabled = false; // Habilita envio pós-salvamento
             } else {
                 console.error("Resposta c/ Erro Lógico:", result);
                 alert(`Falha ao salvar: ${result.message || 'Erro desconhecido retornado pelo servidor.'}`);
                 enviarDueButton.disabled = true; // Desabilita envio se falhou
             }

         } catch (error) {
              // Captura erros de rede, falha no response.ok, ou falha no response.json()
             console.error("Erro na comunicação fetch ou parse JSON:", error);
              // Verifica se a mensagem já foi mostrada (no caso de erro HTTP)
              if (!error.message.startsWith('HTTP error')) {
                   alert(`Erro de comunicação ao tentar salvar: ${error.message}. Verifique a conexão e o console.`);
              }
             enviarDueButton.disabled = true; // Desabilita envio em caso de erro
         } finally {
             spinner.style.display = 'none'; salvarDueButton.disabled = false; // Reabilita salvar
             // Reabilitar Enviar SÓ SE tiver ID
             if(enviarDueButton) enviarDueButton.disabled = !dueIdHiddenInput.value;
             console.log("[Salvar DU-E] Fim da tentativa.");
         }
    });


    // --- Listener Botão ENVIAR DU-E (Placeholder) ---
    enviarDueButton.addEventListener('click', () => { /* ... (código como na resposta anterior) ... */
         const currentDueId = dueIdHiddenInput.value;
         if (!currentDueId) { alert("Salve a DU-E primeiro."); return; }
         console.log(`[Enviar DU-E] Clicado para ${currentDueId}. Futuro.`);
         if (confirm(`Tem certeza que deseja enviar a DU-E ${currentDueId} para o Siscomex?`)) {
             alert(`FUNCIONALIDADE DE ENVIO (DU-E: ${currentDueId}) NÃO IMPLEMENTADA.`);
             console.log("=> Lógica API Siscomex aqui.");
         } else { console.log("[Enviar DU-E] Cancelado."); }
    });


    // --- Estado Inicial dos Botões ---
    salvarDueButton.disabled = true; // Começa desabilitado até carregar itens
    enviarDueButton.disabled = true; // Começa desabilitado até ter um ID salvo

    console.log("Script principal: Aplicação pronta.");

}); // --- FIM DOMContentLoaded ---