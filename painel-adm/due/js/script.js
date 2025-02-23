$(document).ready(function() {
    function findLocalName(context, localName) {
        return $(context).find('*').filter(function() {
            return this.localName === localName;
        });
    }

    let table;
    if ($.fn.DataTable.isDataTable('#tabela-nfe')) {
        table = $('#tabela-nfe').DataTable();
    } else {
        table = $('#tabela-nfe').DataTable({
            data: [],
            columns: [
                { data: 'nNF', title: 'Número NF-e' },
                { data: 'serie', title: 'Série' },
                { data: 'emitente', title: 'Emitente' },
                { data: 'nItem', title: 'Número Item' },
                {
                    data: null,
                    title: 'Ações',
                    render: function(data, type, row) {
                        if (!row || !row.nNF) {
                            console.error("Dados inválidos:", row);
                            return "Erro nos dados";
                        }
                        return `
                            <div class="d-flex justify-content-around">
                                <button class="btn btn-info btn-sm expand-btn" data-nnf="${row.nNF}">+</button>
                                <button class="btn btn-danger btn-sm remover-nf-btn" data-nnf="${row.nNF}">Remover</button>
                            </div>
                        `;
                    },
                    orderable: false
                },
                { data: 'xml', visible: false }
            ],
            deferRender: true,
            dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json'
            }
        });
    }

    function adicionarNFe(xmlDoc) {
        try {
            if (!$.fn.DataTable.isDataTable('#tabela-nfe')) {
                console.error("A tabela ainda não foi inicializada!");
                return;
            }

            const ide = findLocalName(xmlDoc, 'ide');
            const emit = findLocalName(xmlDoc, 'emit');
            if (ide.length === 0 || emit.length === 0) {
                throw new Error("Elementos 'ide' ou 'emit' não encontrados no XML.");
            }

            const nNF = findLocalName(ide, 'nNF').text();
            const serie = findLocalName(ide, 'serie').text();
            const emitente = findLocalName(emit, 'xNome').text();

            if (!nNF || !serie || !emitente) {
                console.error("Dados incompletos da NF-e:", { nNF, serie, emitente });
                return;
            }

            if (table.rows().data().toArray().some(row => row.nNF === nNF)) {
                alert(`NF-e ${nNF} já está na tabela!`);
                return;
            }

            const itens = findLocalName(xmlDoc, 'det').map(function() {
                return $(this).attr('nItem');
            }).get();

            const xmlString = new XMLSerializer().serializeToString(xmlDoc);

            table.row.add({
                nNF: nNF,
                serie: serie,
                emitente: emitente,
                nItem: itens.join(', '),
                xml: xmlString
            }).draw();

        } catch (error) {
            console.error('Erro ao processar XML:', error);
            alert('Formato XML inválido ou incompleto!');
        }
    }

    $('#tabela-nfe tbody').on('click', '.expand-btn', function() {
        const tr = $(this).closest('tr');
        const row = table.row(tr);

        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass('shown');
            $(this).text('+');
        } else {
            row.child(criarTabelaDetalhes(row.data().xml)).show();
            tr.addClass('shown');
            $(this).text('-');
        }
    });

    $('#tabela-nfe tbody').on('click', '.remover-nf-btn', function() {
        if (confirm('Confirmar remoção?')) {
            table.row($(this).closest('tr')).remove().draw();
        }
    });

    $('#xml-files').on('change', function(e) {
        const files = e.target.files;
        Array.from(files).forEach(file => {
            const reader = new FileReader();
            reader.onload = (e) => {
                try {
                    const parser = new DOMParser();
                    const xmlDoc = parser.parseFromString(e.target.result, "text/xml");

                    if (findLocalName(xmlDoc, 'parsererror').length > 0) {
                        throw new Error('XML malformado');
                    }

                    adicionarNFe(xmlDoc);
                } catch (error) {
                    alert(`Erro no arquivo ${file.name}: ${error.message}`);
                }
            };
            reader.readAsText(file);
        });
    });
});