// add-lpcos.js

document.addEventListener('DOMContentLoaded', () => {

    //Usa o event delegation
    document.body.addEventListener('click', function(event){

       //Botão de adicionar
       if(event.target.classList.contains('add-lpco-btn')){
           const container = event.target.closest('.lpco-container'); //Acha o container do lpco
           if(!container) {
             console.error("lpco-container not found in add-lpcos.js"); // LOG
             return;
           }

           const select = container.querySelector('.lpco-select'); //Acha o select dentro do container.
           const lista = container.querySelector('.lista-lpcos'); //Acha a lista dentro do container

           // Adicione logs AQUI para verificar os seletores:
           console.log("container:", container);
           console.log("select:", select);
           console.log("lista:", lista);

           if(!select || !lista) {
              console.error("select or lista not found in add-lpcos.js"); // LOG
              return;
           }
               // ... (resto do seu código add-lpcos.js) ...
           const descricaoLpco = select.value;
           const textoLpco = select.options[select.selectedIndex].text;
           const codigoLpco = textoLpco.split(' - ')[0];

           //Verifica se o LPCO já foi adicionado *nesta lista especifica*
           const jaAdicionado = Array.from(lista.children).some(span => span.dataset.codigo === codigoLpco);

           if (descricaoLpco && !jaAdicionado) {
               const span = document.createElement('span');
               span.classList.add('badge', 'bg-primary', 'me-1', 'mb-1');
               span.textContent = textoLpco;
               span.dataset.codigo = codigoLpco;

               const removeButton = document.createElement('button');
               removeButton.type = 'button';
               removeButton.classList.add('btn-close');
               removeButton.setAttribute('aria-label', 'Remover LPCO');

               removeButton.addEventListener('click', function() {
                   span.remove();
                   //atualizarLpcosHidden(); // Se você precisar atualizar um campo hidden, faça isso aqui.
               });

               span.appendChild(removeButton);
               lista.appendChild(span);
               //atualizarLpcosHidden(); // Se precisar, atualize aqui
               select.value = ""; // Reset do select
           }
       }
    });
});