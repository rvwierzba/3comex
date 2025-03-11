// due/js/add-lpcos.js

document.addEventListener('DOMContentLoaded', function() {

   //console.log('add-lpcos carregado com sucesso!'); - FOR TEST

    const selectLpco = document.getElementById('add-lpcos');
    const listaLpcos = document.getElementById('lista-lpcos');
    const lpcosHidden = document.getElementById('lpcos-hidden');
    const lpcosAdicionados = new Set();

    function atualizarLpcosHidden() {
        lpcosHidden.value = Array.from(lpcosAdicionados).join(',');
    }

    selectLpco.addEventListener('change', function() {
        const descricaoLpco = this.value; // Pega a DESCRIÇÃO do value
        const textoLpco = this.options[this.selectedIndex].text; // Pega o texto (CÓDIGO)
        const codigoLpco = textoLpco.split(' - ')[0]; // Extrai o CÓDIGO do texto.  MUITO IMPORTANTE!

        if (descricaoLpco && !lpcosAdicionados.has(codigoLpco)) { // Usa o CÓDIGO para verificar duplicatas
            lpcosAdicionados.add(codigoLpco); // Adiciona o CÓDIGO ao Set

            const span = document.createElement('span');
            span.classList.add('badge', 'bg-primary', 'me-1', 'mb-1');
            span.textContent = textoLpco; //Exibe o texto
            span.dataset.codigo = codigoLpco; // Armazena o CÓDIGO (não a descrição)

            const removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.classList.add('btn-close');
            removeButton.setAttribute('aria-label', 'Remover LPCO');
            removeButton.addEventListener('click', function() {
                lpcosAdicionados.delete(codigoLpco); // Remove o CÓDIGO do Set
                span.remove();
                atualizarLpcosHidden();
            });

            span.appendChild(removeButton);
            listaLpcos.appendChild(span);
            atualizarLpcosHidden();
            this.value = ""; // Reset
        }
    });
});