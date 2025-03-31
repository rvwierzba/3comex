// Lista de nomes das propriedades consideradas obrigatórias DENTRO de um item
const requiredFields = [
    'xProd',                // Descrição da mercadoria
    'ncm',                  // NCM
    'uComercializada',      // Unidade comercializada
    'qComercializada',      // Quantidade comercializada
    'vUnCom',               // Valor unitário comercializado (ou o valor total)
    'pesoLiquido',          // Peso líquido
    'condicaoVenda',        // Condição de venda
    'vmcvMoeda',            // VMCV
    'vmleMoeda',            // VMLE
    'nomeImportador',
    'enderecoImportador',
    'paisImportador',
    'paisDestino',
    'primeiroEnquadramento', // Adicione ou remova conforme necessário
    // 'segundoEnquadramento', // Exemplo: descomente se for obrigatório
    // 'terceiroEnquadramento',
    // 'quartoEnquadramento',
    // 'tratamentoTributario',
];

/**
 * Verifica se todos os campos obrigatórios de um item estão preenchidos.
 * @param {object} item O objeto do item da NF-e.
 * @returns {boolean} True se completo, False se incompleto.
 */
function isItemComplete(item) {
    if (!item) return false; // Segurança extra

    return requiredFields.every(fieldName => {
        const value = item[fieldName];
        // Considera preenchido se existir e não for null/undefined/string vazia (após trim)
        return value !== null && value !== undefined && value.toString().trim() !== '';
    });
}

// Função para renderizar a tabela principal.
function renderNotasFiscaisTable() {
    const tbody = document.querySelector('#notasFiscaisTable tbody');
    const theadRow = document.querySelector('#notasFiscaisTable thead tr'); // Seleciona a linha do cabeçalho
    tbody.innerHTML = ''; // Limpa o corpo da tabela.

    // Adiciona o cabeçalho da coluna de Status (se ainda não existir)
    if (!theadRow.querySelector('.status-header')) {
         const statusHeader = document.createElement('th');
         statusHeader.textContent = 'Status';
         statusHeader.classList.add('status-header'); // Classe para evitar duplicatas
         theadRow.appendChild(statusHeader);
    }


    if (processor && Array.isArray(processor.notasFiscais)) {
        processor.notasFiscais.forEach((nf, nfIndex) => {
            if (Array.isArray(nf.itens)) {
                nf.itens.forEach((item, itemIndex) => {
                    const itemRow = document.createElement('tr');
                    itemRow.classList.add('item-row');

                    // --- Células existentes ---
                    const chaveCell = document.createElement('td');
                    chaveCell.textContent = nf.chave;
                    itemRow.appendChild(chaveCell);

                    const nomeImportadorCell = document.createElement('td');
                    nomeImportadorCell.textContent = item.nomeImportador;
                    itemRow.appendChild(nomeImportadorCell);

                    const paisImportadorCell = document.createElement('td');
                    paisImportadorCell.textContent = item.paisImportador;
                    itemRow.appendChild(paisImportadorCell);

                    // --- Célula de Ações ---
                    const actionsCell = document.createElement('td');
                    const toggleBtn = document.createElement('button');
                    toggleBtn.type = 'button';
                    toggleBtn.classList.add('btn', 'btn-info', 'btn-sm', 'toggle-details');
                    toggleBtn.innerHTML = '+';
                    toggleBtn.dataset.nfIndex = nfIndex;
                    toggleBtn.dataset.itemIndex = itemIndex;
                    actionsCell.appendChild(toggleBtn);
                    itemRow.appendChild(actionsCell); // Adiciona ações ANTES do status

                    // --- NOVA CÉLULA DE STATUS ---
                    const statusCell = document.createElement('td');
                    const completo = isItemComplete(item); // Verifica o status
                    if (completo) {
                        statusCell.innerHTML = '<span style="color: green; font-weight: bold;">&#x2705;</span>'; // Check verde (✅)
                        statusCell.title = 'Completo'; // Tooltip
                    } else {
                        statusCell.innerHTML = '<span style="color: red; font-weight: bold;">&#x274C;</span>'; // X vermelho (❌)
                        statusCell.title = 'Incompleto'; // Tooltip
                    }
                    statusCell.style.textAlign = 'center'; // Centraliza o ícone
                    itemRow.appendChild(statusCell); // Adiciona a célula de status no final

                    tbody.appendChild(itemRow);
                });
            } else {
                console.error('nf.itens não é um array:', nf.itens);
            }
        });
    } else {
        console.error('processor.notasFiscais não está definido ou não é um array:', processor.notasFiscais);
    }
}

