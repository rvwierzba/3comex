// due-upload.mjs

class NFeProcessor {
    constructor() {
        this.notasFiscais = [];
        this.nfeNS = 'http://www.portalfiscal.inf.br/nfe';
        this.visibleDetails = new Set(); // Armazena chaves de acesso visíveis
        this.initEventHandlers();
    }

    // ========== [PROCESSAMENTO DE ARQUIVOS] ==========
    async processFiles(files) {
        this.notasFiscais = [];
        for (const file of files) {
            await this.processFile(file);
        }
        this.updateUI();
    }

    async processFile(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                try {
                    const xmlDoc = new DOMParser().parseFromString(e.target.result, "text/xml");
                    const nfData = this.extractCompleteData(xmlDoc);
                    this.notasFiscais.push(nfData);
                    resolve();
                } catch (error) {
                    reject(`Erro no arquivo ${file.name}: ${error}`);
                }
            };
            reader.onerror = () => reject(`Erro ao ler ${file.name}`);
            reader.readAsText(file);
        });
    }

    // ========== [EXTRAÇÃO DE DADOS] ==========
    extractCompleteData(xmlDoc) {
        const getVal = (tag, parent = xmlDoc) => 
            parent.getElementsByTagNameNS(this.nfeNS, tag)[0]?.textContent?.trim() || '';

        const ide = xmlDoc.getElementsByTagNameNS(this.nfeNS, 'ide')[0];
        const emit = xmlDoc.getElementsByTagNameNS(this.nfeNS, 'emit')[0];
        const dest = xmlDoc.getElementsByTagNameNS(this.nfeNS, 'dest')[0] || {};
        
        return {
            chaveAcesso: xmlDoc.getElementsByTagNameNS(this.nfeNS, 'infNFe')[0]?.getAttribute('Id')?.replace('NFe', '') || '',
            dadosGerais: {
                nNF: getVal('nNF', ide),
                serie: getVal('serie', ide),
                dhEmi: getVal('dhEmi', ide)
            },
            emitente: {
                xNome: getVal('xNome', emit),
                CNPJ: getVal('CNPJ', emit)
            },
            destinatario: {
                xNome: getVal('xNome', dest),
                enderDest: {
                    xPais: getVal('xPais', dest.getElementsByTagNameNS(this.nfeNS, 'enderDest')[0])
                }
            },
            itens: Array.from(xmlDoc.getElementsByTagNameNS(this.nfeNS, 'det')).map(det => ({
                nItem: det.getAttribute('nItem'),
                xProd: getVal('xProd', det)
            }))
        };
    }

    // ========== [ATUALIZAÇÃO DA UI] ==========
    updateUI() {
        this.updateTable();
        this.fillFormFields();
    }

    updateTable() {
        const table = document.querySelector("#notasFiscaisTable");
        if (!table) return;
    
        // Mantém o cabeçalho existente
        const thead = table.querySelector('thead') || document.createElement('thead');
        
        // Atualiza apenas o corpo da tabela
        const tbody = table.querySelector('tbody') || document.createElement('tbody');
        
        tbody.innerHTML = this.notasFiscais.map(nf => `
            <tr>
                <td>${nf.chaveAcesso}</td>
                <td>${nf.destinatario.xNome || 'N/A'}</td>
                <td>${nf.destinatario.enderDest.xPais || 'N/A'}</td>
                <td class="meus-botoes">
                    <button type="button" 
                            class="btn btn-dark btn-sm toggle-details" 
                            data-chave="${nf.chaveAcesso}"
                            aria-expanded="${this.visibleDetails.has(nf.chaveAcesso)}">
                        ${this.visibleDetails.has(nf.chaveAcesso) ? '-' : '+'}
                    </button>
                    <button type="button" 
                            class="btn btn-danger btn-sm remove-nf" 
                            data-chave="${nf.chaveAcesso}">
                        ×
                    </button>
                </td>
            </tr>
            <tr class="details-row" 
                data-chave="${nf.chaveAcesso}" 
                style="display: ${this.visibleDetails.has(nf.chaveAcesso) ? 'table-row' : 'none'}">
                <td colspan="4">
                    <div class="p-3 bg-light">
                        ${this.generateDetailsHtml(nf)}
                    </div>
                </td>
            </tr>
        `).join('');
    
        // Garante a estrutura correta da tabela
        if (!table.querySelector('thead')) {
            table.prepend(thead);
        }
        if (!table.querySelector('tbody')) {
            table.appendChild(tbody);
        }
    }

    generateDetailsHtml(nf) {
        return `
            <div class="row">
                <div class="col-md-6">
                    <h6>Informações Básicas</h6>
                    <p><strong>Número:</strong> ${nf.dadosGerais.nNF}</p>
                    <p><strong>Série:</strong> ${nf.dadosGerais.serie}</p>
                    <p><strong>Data Emissão:</strong> ${this.formatDate(nf.dadosGerais.dhEmi)}</p>
                </div>
                <div class="col-md-6">
                    <h6>Emitente/Destinatário</h6>
                    <p><strong>CNPJ Emitente:</strong> ${nf.emitente.CNPJ}</p>
                    <p><strong>País Destino:</strong> ${nf.destinatario.enderDest.xPais}</p>
                </div>
            </div>
            <div class="mt-3">
                <h6>Itens (${nf.itens.length})</h6>
                <ul class="list-group">
                    ${nf.itens.map(item => `
                        <li class="list-group-item">
                            ${item.nItem} - ${item.xProd}
                        </li>
                    `).join('')}
                </ul>
            </div>
        `;
    }

    // ========== [MANIPULAÇÃO DE EVENTOS] ==========
    initEventHandlers() {
        const tabela = document.querySelector('#notasFiscaisTable');
        
        tabela.addEventListener('click', (e) => {
            e.preventDefault(); // Impede o comportamento padrão
            e.stopPropagation();
            
            const target = e.target;
            
            if (target.classList.contains('toggle-details')) {
                this.toggleDetails(target);
            }
            
            if (target.classList.contains('remove-nf')) {
                this.removeNota(target);
            }
        });
    }

    toggleDetails(button) {
        const chave = button.dataset.chave;
        const detailsRow = document.querySelector(`.details-row[data-chave="${chave}"]`);
        
        if (!detailsRow) return;

        const isVisible = detailsRow.style.display === 'none';
        
        // Atualiza o estado visual com transição
        detailsRow.style.display = isVisible ? 'table-row' : 'none';
        button.textContent = isVisible ? '-' : '+';
        button.setAttribute('aria-expanded', isVisible);
        
        // Atualiza o controle de estado
        if (isVisible) {
            this.visibleDetails.add(chave);
        } else {
            this.visibleDetails.delete(chave);
        }
    }

    removeNota(button) {
        const chave = button.dataset.chave;
        this.notasFiscais = this.notasFiscais.filter(nf => nf.chaveAcesso !== chave);
        this.visibleDetails.delete(chave);
        this.updateUI();
    }

    // ========== [PREENCHIMENTO DE CAMPOS] ==========
    fillFormFields() {
        const safeFill = (selector, value, prop = 'value') => {
            const el = document.querySelector(selector);
            if (el) el[prop] = value || '';
        };

        if (this.notasFiscais.length > 0) {
            const nf = this.notasFiscais[0];
            safeFill('#cnpj-cpf-select', nf.emitente.CNPJ);
            safeFill('#nomeCliente', nf.emitente.xNome);
            safeFill('#xNome', nf.destinatario.xNome, 'textContent');
            safeFill('#xPais', nf.destinatario.enderDest.xPais);
        }
    }

    // ========== [UTILITÁRIOS] ==========
    formatDate(dateTime) {
        try {
            return new Date(dateTime).toLocaleString('pt-BR');
        } catch {
            return 'Data inválida';
        }
    }
}

export default NFeProcessor;