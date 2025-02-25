// due-upload.mjs

class NFeProcessor {
    constructor() {
        this.notasFiscais = [];
        this.totalNFs = 0;
        this.processando = false;
        this.nfeNS = 'http://www.portalfiscal.inf.br/nfe';
    }

    async processFiles(files) {
        if (this.processando) return;
        this.processando = true;
        this.notasFiscais = [];
        this.totalNFs = 0;
        this.clearTable();
        const promises = [];

        for (const file of files) {
            promises.push(this.processSingleFile(file));
        }

        try {
            await Promise.all(promises);
            this.updateTable();
        } catch (error) {
            console.error("Erro ao processar arquivos:", error);
            this.updateUploadStatus(`Erro ao processar: ${error.message}`, 'error');
        } finally {
            this.processando = false;
        }
    }

    async processSingleFile(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = (event) => {
                try {
                    const xmlString = event.target.result;
                    const parser = new DOMParser();
                    const xmlDoc = parser.parseFromString(xmlString, "text/xml");

                    const nfeProc = xmlDoc.getElementsByTagNameNS(this.nfeNS, 'nfeProc')[0];
                    const nfe = xmlDoc.getElementsByTagNameNS(this.nfeNS, 'NFe')[0];

                    if (!nfeProc && !nfe) {
                        reject(new Error(`Arquivo ${file.name} não é uma NF-e válida.`));
                        return;
                    }

                    const nfData = this.extractData(xmlDoc);
                    this.notasFiscais.push(nfData);
                    this.totalNFs++;
                    this.updateUploadStatus(`Arquivo ${file.name} processado com sucesso!`, 'success');
                    resolve();
                } catch (error) {
                    reject(new Error(`Erro ao analisar o arquivo ${file.name}: ${error.message}`));
                }
            };
            reader.onerror = () => reject(new Error(`Erro ao ler o arquivo ${file.name}.`));
            reader.readAsText(file);
        });
    }

    getElementTextNS(parent, tagName) {
        if (!parent) return '';
        const elements = parent.getElementsByTagNameNS(this.nfeNS, tagName);
        return elements.length > 0 ? elements[0].textContent.trim() : '';
    }

    extractData(xmlDoc) {
        const infNFe = xmlDoc.getElementsByTagNameNS(this.nfeNS, 'infNFe')[0];
        const chaveAcesso = infNFe ? infNFe.getAttribute("Id").replace("NFe", "") : '';

        const ide = xmlDoc.getElementsByTagNameNS(this.nfeNS, 'ide')[0];
        const emit = xmlDoc.getElementsByTagNameNS(this.nfeNS, 'emit')[0];
        const dest = xmlDoc.getElementsByTagNameNS(this.nfeNS, 'dest')[0];
        const transp = xmlDoc.getElementsByTagNameNS(this.nfeNS, 'transp')[0];
        const exporta = xmlDoc.getElementsByTagNameNS(this.nfeNS, 'exporta')[0];
        const total = xmlDoc.getElementsByTagNameNS(this.nfeNS, 'total')[0];
        const ICMSTot = total ? total.getElementsByTagNameNS(this.nfeNS, 'ICMSTot')[0] : null;

        const enderEmit = emit ? emit.getElementsByTagNameNS(this.nfeNS, 'enderEmit')[0] : null;
        const enderDest = dest ? dest.getElementsByTagNameNS(this.nfeNS, 'enderDest')[0] : null;
        const vol = transp ? transp.getElementsByTagNameNS(this.nfeNS, 'vol')[0] : null;

        const nfeData = {
            chaveAcesso,
            nNF: this.getElementTextNS(ide, 'nNF'),
            serie: this.getElementTextNS(ide, 'serie'),
            dhEmi: this.formatDateTime(this.getElementTextNS(ide, 'dhEmi')),
            modFrete: this.getElementTextNS(transp, 'modFrete'),
            xNome: this.getElementTextNS(emit, 'xNome'),
            cnpjEmitente: this.getElementTextNS(emit, 'CNPJ'),
            xLgr: this.getElementTextNS(enderEmit, 'xLgr'),
            xNomeImportador: this.getElementTextNS(dest, 'xNome'),
            xLgrImportador: this.getElementTextNS(enderDest, 'xLgr'),
            xPaisImportador: this.getElementTextNS(enderDest, 'xPais'),
            cPaisImportador: this.getElementTextNS(enderDest, 'cPais'),
            UFSaidaPais: this.getElementTextNS(exporta, 'UFSaidaPais'),
            pesoL: this.getElementTextNS(vol, 'pesoL'),
            qVol: this.getElementTextNS(vol, 'qVol'),
            valorTotal: ICMSTot ? this.getElementTextNS(ICMSTot, 'vProd') : '0.00',
            itens: [],
        };

        const itens = xmlDoc.getElementsByTagNameNS(this.nfeNS, 'det');
        Array.from(itens).forEach((item) => {
            const prod = item.getElementsByTagNameNS(this.nfeNS, 'prod')[0];
            if (!prod) return;

            nfeData.itens.push({
                nItem: item.getAttribute("nItem"),
                cProd: this.getElementTextNS(prod, 'cProd'),
                xProd: this.getElementTextNS(prod, 'xProd'),
                NCM: this.getElementTextNS(prod, 'NCM'),
                CFOP: this.getElementTextNS(prod, 'CFOP'),
                uCom: this.getElementTextNS(prod, 'uCom'),
                qCom: this.getElementTextNS(prod, 'qCom'),
                vUnCom: this.getElementTextNS(prod, 'vUnCom'),
                vProd: this.getElementTextNS(prod, 'vProd'),
            });
        });

        return nfeData;
    }

    formatDateTime(dateTime) {
        if (!dateTime) return '';
        const [datePart, timePart] = dateTime.split('T');
        const [year, month, day] = datePart.split('-');
        const time = timePart.substring(0, 8);
        return `${day}/${month}/${year} ${time}`;
    }
    updateTable() {
        const tableBody = document.querySelector("#notasFiscaisTable tbody");
        if (!tableBody) return;

        tableBody.innerHTML = '';
        this.notasFiscais.forEach((nf) => {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td>${nf.chaveAcesso}</td>
                <td>${nf.xNomeImportador}</td>
                <td>${nf.xPaisImportador}</td>
                <td class="meus-botoes">
                    <button class="btn btn-dark btn-sm toggle-details" data-chave="${nf.chaveAcesso}" type="button">+</button>
                    <button class="btn btn-danger btn-sm remove-nf" data-chave="${nf.chaveAcesso}" type="button">Remover</button>
                </td>
            `;
            tableBody.appendChild(row);

            const detailsRow = document.createElement("tr");
            detailsRow.classList.add("details-row");
            detailsRow.style.display = "none";
            //  Usamos diretamente o this.generateDetailsHtml(nf)
            detailsRow.innerHTML = `<td colspan="4"><div class="detalhes-nfe">${this.generateDetailsHtml(nf)}</div></td>`;
            tableBody.appendChild(detailsRow);
        });

        this.addDelegatedEventListeners();
        this.preencherCampos();
    }
    generateDetailsHtml(nf) {

        let html = `<div class="card mb-3">
                        <div class="card-header">
                            <h5>Detalhes da NF-e: ${nf.chaveAcesso}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Número:</strong> ${nf.nNF}</p>
                                    <p><strong>Série:</strong> ${nf.serie}</p>
                                    <p><strong>Data Emissão:</strong> ${nf.dhEmi}</p>
                                    <p><strong>Modalidade Frete:</strong> ${nf.modFrete}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Exportador:</strong> ${nf.xNome}</p>
                                    <p><strong>CNPJ Exportador:</strong> ${nf.cnpjEmitente}</p>
                                    <p><strong>Endereço Exportador:</strong> ${nf.xLgr}</p>
                                </div>
                            </div>
                            <hr>
                            <h6>Itens da NF-e</h6>`;  // Fim do cabeçalho, início dos itens

        nf.itens.forEach(item => {
            html += `
                <div class="card mb-2">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 text-muted">Item ${item.nItem}: ${item.xProd}</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <p><strong>Item da Nota Fiscal:</strong> <input type="text" class="form-control form-control-sm" value="${item.nItem}" readonly></p>
                                <p><strong>NCM:</strong> <input type="text" class="form-control form-control-sm" value="${item.NCM}" readonly></p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Nota Fiscal:</strong> <input type="text" class="form-control form-control-sm" value="${nf.nNF}" readonly></p>
                                 <p><strong>Quantidade Estatística:</strong> <input type="text" class="form-control form-control-sm" value="${item.qCom}" readonly></p>
                            </div>

                            <div class="col-md-4">
                                <p><strong>Unidade Estatística:</strong> <input type="text" class="form-control form-control-sm" value="${item.uCom}" readonly></p>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        html += `</div></div>`; // Fecha card-body e card principal
        return html;
    }


    addDelegatedEventListeners() {
        const tableBody = document.querySelector("#notasFiscaisTable tbody");
        if (!tableBody) return;

        tableBody.addEventListener("click", (event) => {
            if (event.target.classList.contains("toggle-details")) {
                const button = event.target;
                const detailsRow = button.closest("tr").nextElementSibling;
                if (detailsRow) {
                    detailsRow.style.display = detailsRow.style.display === "none" ? "" : "none";
                    button.textContent = detailsRow.style.display === "none" ? "+" : "-";
                }
            }

            if (event.target.classList.contains("remove-nf")) {
                const chave = event.target.dataset.chave;
                this.removeNotaFiscal(chave);
                this.updateTable();
            }
        });
    }

    preencherCampos() {
        const safeSet = (id, value, type = 'value') => {
            const element = document.getElementById(id);
            if (element) element[type] = value || '';
        };

        if (this.notasFiscais.length === 0) {
            ['cnpj-cpf-select', 'nomeCliente', 'pes-liq-ttl', 'ruc',
                'und-estatis', 'qdt-estatis', 'und-comerc', 'qdt-comerc', 'val'].forEach(id => {
                    safeSet(id, '');
                });
            ['xNome', 'xLgr', 'xPaisImp'].forEach(id => {
                safeSet(id, '', 'textContent');
            });
            return;
        }

        const primeiraNF = this.notasFiscais[0];
        const totais = this.calcularTotaisGerais();

        // Aba: Importação de NFs
        safeSet('cnpj-cpf-select', primeiraNF.cnpjEmitente);
        safeSet('nomeCliente', primeiraNF.xNome);

        // Aba: Dados da Declaração
        safeSet('xNome', primeiraNF.xNomeImportador, 'textContent');
        safeSet('xLgr', primeiraNF.xLgrImportador, 'textContent');
        safeSet('xPaisImp', primeiraNF.xPaisImportador, 'textContent');

        safeSet('pes-liq-ttl', totais.pesoLiquido.toFixed(5));
        safeSet('und-estatis', 'KG');
        safeSet('qdt-estatis', totais.quantidadeEstatistica.toFixed(5));
        safeSet('und-comerc', 'KG');
        safeSet('qdt-comerc', totais.quantidadeComercial.toFixed(5));
        safeSet('val', totais.valorTotal.toFixed(2));
        safeSet('ruc', primeiraNF.chaveAcesso);
        safeSet('xPais', primeiraNF.xPaisImportador);
    }

    calcularTotaisGerais() {
        return this.notasFiscais.reduce((acc, nf) => {
            acc.pesoLiquido += parseFloat(nf.pesoL) || 0;
            acc.valorTotal += parseFloat(nf.valorTotal) || 0;

            nf.itens.forEach(item => {
                acc.quantidadeEstatistica += parseFloat(item.qCom) || 0;
                acc.quantidadeComercial += parseFloat(item.qCom) || 0;
            });

            return acc;
        }, {
            pesoLiquido: 0,
            quantidadeEstatistica: 0,
            quantidadeComercial: 0,
            valorTotal: 0
        });
    }

    removeNotaFiscal(chave) {
        this.notasFiscais = this.notasFiscais.filter(nf => nf.chaveAcesso !== chave);
    }

    clearTable() {
        const tableBody = document.querySelector("#notasFiscaisTable tbody");
        if (tableBody) tableBody.innerHTML = "";
    }

    updateUploadStatus(message, type = 'info') {
        const statusDiv = document.getElementById('uploadStatus');
        if (!statusDiv) return;

        const alertClass = type === 'error' ? 'alert-danger' : 'alert-success';
        statusDiv.innerHTML += `<div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                                    ${message}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>`;
    }
}

// Inicialização
const nfeProcessor = new NFeProcessor();

document.getElementById('xml-files').addEventListener('change', async (event) => {
    const files = event.target.files;
    if (files.length > 0) {
        await nfeProcessor.processFiles(files);
    }
});

export { NFeProcessor };