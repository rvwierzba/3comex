// campos-itens-nfe.mjs

function preencherCamposItem(item, row) {
    if (!row) {
        console.error("preencherCamposItem: row is null");
        return;
    }

    // Preenche os campos de texto (como antes)
    const xProdInput = row.querySelector('.campo-xProd');
    if (xProdInput) xProdInput.value = item.xProd || '';
    else console.error("campo-xProd not found");

    const ncmInput = row.querySelector('.campo-ncm');
    if (ncmInput) ncmInput.value = item.ncm || '';
    else console.error("campo-ncm not found");

    const cfopInput = row.querySelector('.campo-cfop');
    if (cfopInput) cfopInput.value = item.cfop || '';
    else console.error("campo-cfop not found");

    const qComInput = row.querySelector('.campo-qCom');
    if (qComInput) qComInput.value = item.qCom || '';
    else console.error("campo-qCom not found");

    const uComInput = row.querySelector('.campo-uCom');
    if (uComInput) uComInput.value = item.uCom || '';
    else console.error("campo-uCom not found");

    const vUnComInput = row.querySelector('.campo-vUnCom');
    if (vUnComInput) vUnComInput.value = item.vUnCom || '';
    else console.error("campo-vUnCom not found");

    const vProdInput = row.querySelector('.campo-vProd');
    if (vProdInput) vProdInput.value = item.vProd || '';
    else console.error("campo-vProd not found");

    const infAdProdInput = row.querySelector('.campo-infAdProd');
    if (infAdProdInput) infAdProdInput.value = item.infAdProd || '';
    else console.error("campo-infAdProd not found");


    // --- Lógica para criar e preencher os elementos de LPCO ---

    const lpcoContainer = row.querySelector('.lpco-container');
    if (!lpcoContainer) {
        console.error("lpco-container not found");
        return;
    }
     //Adiciona o botão de +
    const addButton = document.createElement('button');
    addButton.type = 'button';
    addButton.classList.add('btn', 'btn-secondary', 'btn-sm', 'add-lpco-btn');
    addButton.textContent = '+';
    lpcoContainer.appendChild(addButton);

    // 1. Cria o <select>
    const selectLpco = document.createElement('select');
    selectLpco.classList.add('form-select', 'lpco-select'); // Adiciona classes (importante para o event delegation)
    // Adiciona um ID único (opcional, mas recomendado para evitar conflitos)
    selectLpco.id = `lpco-select-${item.nItem}-${Math.random().toString(36).substring(7)}`; // ID único *realmente*

    // 2. Preenche o <select> com as opções (usa o SEU array de opções)
    const defaultOption = document.createElement('option');
    defaultOption.value = "";
    defaultOption.textContent = "Selecione";
    selectLpco.appendChild(defaultOption);

    // Substitua este array com o SEU array de opções de LPCO
    const opcoesLpco = [
        { value: "E00095", text: "E00095 - EXPORTAÇÃO DE PRODUTOS SUJEITOS A CONTROLE DE COMERCIALIZAÇÃO - SIGVIG" },
        { value: "E00094", text: "E00094 - EXPORTAÇÃO DE PRODUTOS SUJEITOS À MANIFESTAÇÃO DO EXÉRCITO - EB" },
        { value: "E00087", text: "E00087 - EXPORTAÇÃO DE PRODUTOS DA FAUNA E FLORA - IBAMA/SISCOMEX" },

    ];

    opcoesLpco.forEach(opcao => {
        const option = document.createElement('option');
        option.value = opcao.value;
        option.textContent = opcao.text;
        selectLpco.appendChild(option);
    });

    // 3. Cria a <div> para exibir os LPCOs adicionados
    const listaLpcos = document.createElement('div');
    listaLpcos.classList.add('lista-lpcos'); // Adiciona a classe

    // 4. Adiciona os elementos ao container
    lpcoContainer.appendChild(selectLpco);
    lpcoContainer.appendChild(listaLpcos);

    console.log("preencherCamposItem: item:", item); // Log do item
    console.log("preencherCamposItem: row:", row);  // Log da row
    console.log("lpcoContainer:", lpcoContainer); // Log do container
    console.log("selectLpco (após criação):", selectLpco); // Log do select

}

// ---  EXPORT (Nível Superior do Módulo) ---
export { preencherCamposItem };

document.addEventListener('DOMContentLoaded', () => {
    if (typeof addSaveButtonListeners === 'function') {
        addSaveButtonListeners(); // Chama se a função existir
    }
});