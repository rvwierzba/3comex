// ./due/js/due-upload.mjs (CORRIGIDO)

class NFeProcessor {
    constructor() {
        this.notasFiscais = []; // Armazena os dados processados [{chaveAcesso, nomeImportador, pais, itens: [...]}, ...]
        console.log("NFeProcessor Instanciado (v.Corrigida)");
    }

    /** Processa uma lista de arquivos XML */
    async processFiles(files) {
        this.notasFiscais = []; // Limpa dados anteriores
        if (!files || files.length === 0) {
            console.warn('processFiles: Nenhum arquivo selecionado.');
            return this.notasFiscais; // Retorna array vazio
        }
        console.log(`processFiles: Iniciando processamento de ${files.length} arquivo(s)...`);
        for (const file of files) {
            try {
                await this.processSingleFile(file);
            } catch (error) {
                console.error(`Erro ao processar o arquivo ${file.name}:`, error);
                // Decide se quer parar tudo ou continuar com os próximos arquivos
                // throw error; // Descomente para parar no primeiro erro
            }
        }
        console.log("processFiles: Processamento concluído. Dados finais:", this.notasFiscais);
        return this.notasFiscais; // Retorna os dados processados
    }

    /** Processa um único arquivo XML */
    async processSingleFile(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();

            reader.onload = (e) => {
                try {
                    const xmlText = e.target.result;
                    const parser = new DOMParser();
                    const xmlDoc = parser.parseFromString(xmlText, "text/xml");

                    // Verifica erro de parse
                    const parserError = xmlDoc.querySelector("parsererror");
                    if (parserError) {
                        throw new Error(`Erro de parse no XML: ${parserError.textContent}`);
                    }

                    const infNFe = xmlDoc.querySelector('infNFe');
                    if (!infNFe) throw new Error('Elemento <infNFe> não encontrado no XML.');

                    // Pega a chave de acesso (Prioriza do protocolo, se existir)
                    const chNFeProt = xmlDoc.querySelector('protNFe infProt chNFe')?.textContent;
                    const chNFeId = infNFe.getAttribute('Id')?.replace('NFe', '');
                    const chaveAcesso = chNFeProt || chNFeId || `ERRO_CHAVE_${Date.now()}`;
                    if (!chNFeProt && !chNFeId) console.warn("Não foi possível extrair chave da NF-e (chNFe/Id)");

                    // Extrai dados da NF-e
                    const notaFiscal = {
                        chaveAcesso: chaveAcesso,
                        nomeImportador: this.getNomeImportador(xmlDoc), // CORRIGIDO
                        pais: this.getPais(xmlDoc),                   // CORRIGIDO (Só nome)
                        // Adicione outros dados da NF PAI que você precisar aqui (Ex: Emitente)
                        emitente: {
                            nome: xmlDoc.querySelector('emit xNome')?.textContent || '',
                            cnpj: xmlDoc.querySelector('emit CNPJ')?.textContent || xmlDoc.querySelector('emit CPF')?.textContent || ''
                        },
                        enderecoDestinatario: this.getEnderecoDestinatario(xmlDoc), // Pega objeto endereço
                        itens: this.extractItems(xmlDoc)             // Extrai itens
                    };

                    // Verifica se a NF já existe (pela chave) antes de adicionar
                    if (!this.notasFiscais.some(nf => nf.chaveAcesso === notaFiscal.chaveAcesso)) {
                        this.notasFiscais.push(notaFiscal);
                        console.log(`NF ${chaveAcesso} processada e adicionada.`);
                    } else {
                         console.warn(`NF ${chaveAcesso} já foi processada anteriormente. Pulando.`);
                    }
                    resolve(); // Resolve a promise para este arquivo

                } catch (error) {
                    console.error(`Erro no reader.onload para ${file.name}:`, error);
                    reject(error); // Rejeita a promise com o erro
                }
            };

            reader.onerror = () => {
                console.error(`Erro no FileReader ao ler ${file.name}`);
                reject(new Error(`Erro ao ler o arquivo ${file.name}.`));
            };

            reader.readAsText(file);
        });
    }

    // --- Funções Auxiliares de Extração (Corrigidas/Ajustadas) ---

    /** Pega o Nome do Destinatário/Importador */
    getNomeImportador(xmlDoc) {
        // CORRIGIDO: Busca dentro de <dest>
        return xmlDoc.querySelector('dest xNome')?.textContent || '';
    }

    /** Pega o Nome do País de Destino */
    getPais(xmlDoc) {
        // CORRIGIDO: Retorna apenas o nome do país
        return xmlDoc.querySelector('dest enderDest xPais')?.textContent || '';
    }

     /** Pega o objeto de endereço do destinatário */
    getEnderecoDestinatario(xmlDoc) {
        const enderDestNode = xmlDoc.querySelector('dest enderDest');
        if (!enderDestNode) return {}; // Retorna objeto vazio se não encontrar
        return {
            logradouro: enderDestNode.querySelector('xLgr')?.textContent || '',
            numero: enderDestNode.querySelector('nro')?.textContent || '',
            complemento: enderDestNode.querySelector('xCpl')?.textContent || '',
            bairro: enderDestNode.querySelector('xBairro')?.textContent || '',
            municipio: enderDestNode.querySelector('xMun')?.textContent || '',
            uf: enderDestNode.querySelector('UF')?.textContent || '',
            cep: enderDestNode.querySelector('CEP')?.textContent || '', // CEP não estava no XML exemplo
            codPais: enderDestNode.querySelector('cPais')?.textContent || '',
            pais: enderDestNode.querySelector('xPais')?.textContent || ''
        };
    }


    /** Extrai os itens da NF-e */
    extractItems(xmlDoc) {
        const itens = [];
        const detNodes = xmlDoc.querySelectorAll('infNFe det'); // Seletor mais específico

        detNodes.forEach((detNode) => {
            const prodNode = detNode.querySelector('prod');
            if (!prodNode) {
                 console.warn(`Item nItem="${detNode.getAttribute('nItem')}" pulado: tag <prod> não encontrada.`);
                 return; // Pula este item se não achar <prod>
            }

            // Adiciona campos existentes no XML e placeholders para os faltantes
            itens.push({
                // Do XML
                nItem: detNode.getAttribute('nItem') || '???',
                xProd: prodNode.querySelector('xProd')?.textContent || '',
                ncm: prodNode.querySelector('NCM')?.textContent || '',
                cfop: prodNode.querySelector('CFOP')?.textContent || '', // Adicionado CFOP
                qCom: prodNode.querySelector('qCom')?.textContent || '',
                uCom: prodNode.querySelector('uCom')?.textContent || '',
                vUnCom: prodNode.querySelector('vUnCom')?.textContent || '',
                vProd: prodNode.querySelector('vProd')?.textContent || '', // Adicionado vProd
                qTrib: prodNode.querySelector('qTrib')?.textContent || prodNode.querySelector('qCom')?.textContent || '', // Usa qTrib ou fallback qCom
                uTrib: prodNode.querySelector('uTrib')?.textContent || prodNode.querySelector('uCom')?.textContent || '', // Usa uTrib ou fallback uCom
                infAdProd: detNode.querySelector('infAdProd')?.textContent || '',

                // === CAMPOS NÃO PRESENTES NO XML Exemplo (Inicializados como null/vazio) ===
                // Estes precisarão ser preenchidos/editados na interface
                pesoLiquidoItem: null, // Não existe por item no XML exemplo
                condicaoVenda: null,
                vmcvMoeda: null,
                vmleMoeda: null,
                primeiroEnquadramento: null,
                segundoEnquadramento: null,
                terceiroEnquadramento: null,
                quartoEnquadramento: null,
                listaLpco: [], // Começa vazio
                tratamentoTributario: null, // Pode ser derivado do CST? Por ora null.
                ccptcCcromStatus: null // Estado inicial do rádio
            });
        });
        console.log(`extractItems: Extraídos ${itens.length} itens.`);
        return itens;
    }

    // REMOVIDO: populateFields(xmlDoc) - Não deve manipular DOM externo
    // REMOVIDO: getCodigoMoeda(tPag) - Lógica de moeda deve ficar na interface principal
    // REMOVIDO: renderizarNotas() - Não deve manipular DOM externo
    // REMOVIDO: toggleDetails(button) - Lógica de UI deve ficar fora
    // REMOVIDO: removeNota(button) - Lógica de UI deve ficar fora
}

export default NFeProcessor;