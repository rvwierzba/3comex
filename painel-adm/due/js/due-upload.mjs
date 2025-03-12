import { preencherCamposItem } from './campos-itens-nfe.mjs';

class NFeProcessor {
    constructor() {
        this.notasFiscais = [];
    }

    async processFiles(files) {
        if (!files || files.length === 0) {
            throw new Error('Nenhum arquivo selecionado.');
        }

        for (const file of files) {
            await this.processSingleFile(file);
        }
    }

    async processSingleFile(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();

            reader.onload = async (e) => {
                try {
                    const xmlText = e.target.result;
                    const parser = new DOMParser();
                    const xmlDoc = parser.parseFromString(xmlText, "text/xml");

                    const infNFe = xmlDoc.querySelector('infNFe');
                    if (!infNFe) {
                        throw new Error('Elemento infNFe não encontrado no XML.');
                    }
                    const chaveAcesso = infNFe.getAttribute('Id').replace('NFe', '');

                    this.populateFields(xmlDoc);

                    const notaFiscal = {
                        chaveAcesso: chaveAcesso,
                        nomeImportador: this.getNomeImportador(xmlDoc),
                        pais: this.getPais(xmlDoc),
                        itens: this.extractItems(xmlDoc)
                    };

                    this.notasFiscais.push(notaFiscal);
                    this.renderizarNotas();
                    resolve();

                } catch (error) {
                    console.error('Erro ao processar arquivo:', error);
                    reject(error);
                }
            };

            reader.onerror = () => {
                console.error('Erro ao ler o arquivo:');
                reject(new Error('Erro ao ler o arquivo.'));
            };

            reader.readAsText(file);
        });
    }

    populateFields(xmlDoc) {
        // Preenche os campos da aba 1 (DADOS GERAIS)
        const emit = xmlDoc.querySelector('emit');
        if (emit) {
            const cnpj = emit.querySelector('CNPJ')?.textContent;
            const cpf = emit.querySelector('CPF')?.textContent;
            const xNome = emit.querySelector('xNome')?.textContent;

            if (cnpj) {
                document.getElementById('text-cnpj-cpf-select').value = cnpj;
                 //Preenche o datalist com os dados do cliente.
                const datalist = document.getElementById('cnpj-cpf-list');
                datalist.innerHTML = ''; //Limpa o datalist

                const option = document.createElement('option');
                option.value = cnpj;
                datalist.appendChild(option);

            } else if (cpf) {
                document.getElementById('text-cnpj-cpf-select').value = cpf;
                 //Preenche o datalist com os dados do cliente.
                const datalist = document.getElementById('cnpj-cpf-list');
                datalist.innerHTML = ''; //Limpa o datalist
                const option = document.createElement('option');
                option.value = cpf;
                datalist.appendChild(option);
            }

            document.getElementById('nomeCliente').value = xNome || '';


        }

        // Preenche a moeda:
        const infNFe = xmlDoc.querySelector("infNFe");
        const pag = infNFe.querySelector("pag");

        if (pag) {
            const detPag = pag.querySelector("detPag");
            if (detPag) {
                const tPag = detPag.querySelector("tPag");
                if (tPag) {
                    const codMoeda = this.getCodigoMoeda(tPag.textContent);
                    document.getElementById("text-moeda").value = codMoeda;

                    const datalistMoeda = document.getElementById('moeda');
                    datalistMoeda.innerHTML = '';
                    const optionMoeda = document.createElement('option');
                    optionMoeda.value = codMoeda;
                    datalistMoeda.appendChild(optionMoeda);
                }
            }
        }

        //Outros campos aba1
    }

    getCodigoMoeda(tPag) {
        switch (tPag) {
            case '01': return '970-DOLAR DOS ESTADOS UNIDOS';
            // ... outros casos ...
            default: return '';
        }
    }

    getNomeImportador(xmlDoc) {
        const emit = xmlDoc.querySelector('emit');
        return emit?.querySelector('xNome')?.textContent || '';
    }

    getPais(xmlDoc) {
        const dest = xmlDoc.querySelector("dest");
        const enderDest = dest?.querySelector("enderDest");
        const cPais = enderDest?.querySelector("cPais")?.textContent;
        const xPais = enderDest?.querySelector("xPais")?.textContent;
        return (cPais && xPais) ? `${cPais}-${xPais}` : "";
    }
    extractItems(xmlDoc) {
        const itens = [];
        const detNodes = xmlDoc.querySelectorAll('det');

        detNodes.forEach((detNode) => {
            const prodNode = detNode.querySelector('prod');
            itens.push({
                nItem: detNode.getAttribute('nItem'),
                xProd: prodNode?.querySelector('xProd')?.textContent || '',
                ncm: prodNode?.querySelector('NCM')?.textContent || '',
                cfop: prodNode?.querySelector('CFOP')?.textContent || '',
                qCom: prodNode?.querySelector('qCom')?.textContent || '',
                uCom: prodNode?.querySelector('uCom')?.textContent || '',
                vUnCom: prodNode?.querySelector('vUnCom')?.textContent || '',
                vProd: prodNode?.querySelector('vProd')?.textContent || '',
                infAdProd: detNode?.querySelector('infAdProd')?.textContent || ''
            });
        });
        return itens;
    }
    renderizarNotas() {
        const tbody = document.querySelector('#notasFiscaisTable tbody');
        if (!tbody) {
            console.error("Elemento tbody não encontrado.");
            return;
        }
        tbody.innerHTML = '';

        this.notasFiscais.forEach(nota => {
            const row = document.createElement('tr');
            row.dataset.chave = nota.chaveAcesso;
            row.innerHTML = `
                <td>${nota.chaveAcesso}</td>
                <td>${nota.nomeImportador}</td>
                <td>${nota.pais}</td>
                <td>
                    <button type="button" class="btn btn-info btn-sm toggle-details" data-chave="${nota.chaveAcesso}">+</button>
                    <button type="button" class="btn btn-danger btn-sm remove-nf" data-chave="${nota.chaveAcesso}">Remover</button>
                </td>
            `;
            tbody.appendChild(row);

            const detailsRow = document.createElement('tr');
            detailsRow.classList.add('details-row');
            detailsRow.dataset.chave = nota.chaveAcesso;
            detailsRow.style.display = 'none';

            // --- Construção da tabela interna (agora com a coluna LPCO) ---
            let innerTableHTML = `
                <td colspan="4">
                    <div class="details-content">
                        <table class="inner-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Produto</th>
                                    <th>NCM</th>
                                    <th>CFOP</th>
                                    <th>Qtd.</th>
                                    <th>Un.</th>
                                    <th>Valor Unit.</th>
                                    <th>Valor Total</th>
                                    <th>Inf. Ad.</th>
                                    <th>LPCO</th>  <!--  Coluna para LPCO -->
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
            `;
            nota.itens.forEach(item => {
                innerTableHTML += `
                    <tr>
                        <td>${item.nItem}</td>
                        <td><input type="text" class="campo-xProd" value="${item.xProd || ''}"></td>
                        <td><input type="text" class="campo-ncm" value="${item.ncm || ''}"></td>
                        <td><input type="text" class="campo-cfop" value="${item.cfop || ''}"></td>
                        <td><input type="text" class="campo-qCom" value="${item.qCom || ''}"></td>
                        <td><input type="text" class="campo-uCom" value="${item.uCom || ''}"></td>
                        <td><input type="text" class="campo-vUnCom" value="${item.vUnCom || ''}"></td>
                        <td><input type="text" class="campo-vProd" value="${item.vProd || ''}"></td>
                        <td><input type="text" class="campo-infAdProd" value="${item.infAdProd || ''}"></td>
                        <td class="lpco-container">  <!--  Container para os elementos de LPCO -->

                        </td>
                        <td><button class='btn btn-success'>Salvar</button></td>
                    </tr>
                `;
            });

            innerTableHTML += `
                            </tbody>
                        </table>
                    </div>
                </td>
            `;
            detailsRow.innerHTML = innerTableHTML;


            // --- Chamada para preencherCamposItem (AGORA ANTES de adicionar ao DOM) ---
            nota.itens.forEach(item => {
                preencherCamposItem(item, detailsRow); //  <---  IMPORTANTE!
            });
            tbody.appendChild(detailsRow);  // Agora sim, adiciona ao DOM
        });
        if (typeof addSaveButtonListeners === 'function') { //Verifica se a função existe
              addSaveButtonListeners();
         }

    }
    toggleDetails(button) {
        const chave = button.dataset.chave;
        const detailsRow = document.querySelector(`tr.details-row[data-chave="${chave}"]`);
        if (detailsRow) {
            detailsRow.style.display = detailsRow.style.display === 'none' ? 'table-row' : 'none';
            button.textContent = button.textContent === '+' ? '-' : '+';
        }
    }

    removeNota(button){
        const chave = button.dataset.chave;
        this.notasFiscais = this.notasFiscais.filter(nf => nf.chaveAcesso !== chave);
        //Usa o querySelectorAll, pois agora tem a linha principal e de detalhe.
        document.querySelectorAll(`tr[data-chave="${chave}"]`).forEach(el => el.remove());
    }
}
export default NFeProcessor;