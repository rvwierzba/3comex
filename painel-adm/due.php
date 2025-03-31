<?php
    // Conexão com o banco (coloque *antes* de qualquer HTML, mas dentro do <html> se já existir)
    include_once 'C:\\xampp\\htdocs\\3comex\\conexao.php';  // Ajuste o caminho se necessário!

    // --- PRE-FETCH DATA FOR MODALS (Error handling basic) ---
    $incoterms = [];


try {
    // AJUSTE a query se os nomes da tabela/colunas forem diferentes
    $stmt = $pdo->query('SELECT Sigla, Descricao FROM incoterms ORDER BY Sigla');
    if ($stmt) {
        $incoterms = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        error_log("Erro ao buscar Incoterms: PDO query falhou.");
    }
} catch (PDOException $e) {
    error_log("Erro PDO ao buscar Incoterms: " . $e->getMessage());
    // Considerar exibir uma mensagem de erro para o usuário ou fallback
}

$enquadramentos = [];
try {
    // AJUSTE a query se os nomes da tabela/colunas forem diferentes
    $stmt = $pdo->query('SELECT CODIGO, DESCRICAO FROM enquadramento ORDER BY CODIGO'); // Assumindo tabela 'enquadramento'
     if ($stmt) {
        $enquadramentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
     } else {
         error_log("Erro ao buscar Enquadramentos: PDO query falhou.");
     }
} catch (PDOException $e) {
    error_log("Erro PDO ao buscar Enquadramentos: " . $e->getMessage());
     // Considerar exibir uma mensagem de erro para o usuário ou fallback
}

$paises = [];
try {
    // AJUSTE a query se os nomes da tabela/colunas forem diferentes
    // Usando CodigoBACEN como value e Nome como texto (Ajuste se necessário)
    $stmt = $pdo->query('SELECT Sigla_is03, Nome FROM paises ORDER BY Nome'); // Assumindo tabela 'paises' com Sigla_is03
     if ($stmt) {
        $paises = $stmt->fetchAll(PDO::FETCH_ASSOC);
     } else {
         error_log("Erro ao buscar Países: PDO query falhou.");
     }
} catch (PDOException $e) {
    error_log("Erro PDO ao buscar Países: " . $e->getMessage());
     // Considerar exibir uma mensagem de erro para o usuário ou fallback
}


?>

    <style>
        /* SEU CSS EXISTENTE AQUI */
        /* CAMPOS ITENS NFs */
        .form-group { margin-bottom: 1rem; }
        .form-check-inline { margin-right: 1rem; }
        .form-check-input { margin-top: 0.3rem; }
        #notasFiscaisTable { width: 100%; border-collapse: separate; border-spacing: 0; }
        #notasFiscaisTable td, #notasFiscaisTable th { vertical-align: middle; padding: 8px 12px; border: 1px solid #dee2e6; font-size: 0.9rem; /* Ajuste se necessário */ }
        #notasFiscaisTable thead { position: sticky; top: 0; background: white; z-index: 100; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); }
        .thead-light th { background-color: #f8f9fa; border-bottom: 2px solid #dee2e6; font-weight: 600; }
        /* Mantém estilo base para botões */
        button[type="button"], button.toggle-details { -webkit-appearance: none; -moz-appearance: none; appearance: none; cursor: pointer; background: none; border: none; padding: 0; font: inherit; color: inherit; }
        /* Estilo específico para o botão '+' */
        .btn.toggle-details {
            min-width: 32px; /* Largura mínima */
            line-height: 1; /* Alinha o '+' verticalmente */
            font-size: 1.2rem; /* Tamanho do '+' */
            font-weight: bold;
            color: #17a2b8; /* Cor ciano (info) */
            background-color: transparent;
            border: 1px solid transparent; /* Borda transparente para manter tamanho */
            padding: 0.25rem 0.5rem; /* Espaçamento interno */
            border-radius: 0.25rem; /* Borda arredondada */
            transition: transform 0.2s, background-color 0.2s;
        }
        .btn.toggle-details:hover {
            transform: scale(1.1);
            background-color: #e2f L9fa; /* Fundo leve no hover */
            color: #107180; /* Cor mais escura no hover */
            border-color: #17a2b8;
        }
        /* Estilo MUITO IMPORTANTE para as linhas de detalhes - MANTIDO */
        .details-row { display: none; background-color: #f8f9fa; }
        .inner-table { width: 100%; border-collapse: collapse; }
        .inner-table th, .inner-table td { padding: 8px; border: 1px solid #dee2e6; text-align: left; }
        /* Outros estilos mantidos */
        #tabela-nfe tr.details-row .save-nf-btn { background-color: #28a745; border-color: #28a745; color: #fff; padding: 8px 16px; border-radius: 4px; cursor: pointer; }
        #tabela-nfe tr.details-row .save-nf-btn:hover { background-color: #218838; border-color: #218838; }
        .meus-botoes > button { margin-right: 5px; }
        #tabela-nfe td .btn { margin: 2px; display: inline-block; }
        #tabela-nfe td .btn-danger { color: #fff; background-color: #dc3545; border-color: #dc3545; }
        .item-details-table { width: 100%; border-collapse: collapse; }
        .item-details-table th, .item-details-table td { padding: 8px; border: 1px solid #dee2e6; text-align: left; }
        .item-details-table input[type="text"], .item-details-table select { width: 100%; padding: 6px; margin-bottom: 4px; border: 1px solid #ced4da; border-radius: 4px; box-sizing: border-box;}
        .lpco-container { margin-top: 10px; }
        .lista-lpcos { display: flex; flex-wrap: wrap; gap: 5px; }
        .lpco-item { background-color: #e9ecef; padding: 2px 6px; border-radius: 4px; }
        .tab-content > .tab-pane:not(.active) { display: none !important; opacity: 0; }
        .tab-content > .tab-pane.active { display: block !important; opacity: 1; transition: opacity 0.3s ease; }
        .nav-tabs .nav-link.active { border-bottom: 3px solid #0d6efd !important; background: #fff; }
        .tab-content { border: 1px solid #dee2e6; border-radius: 0 0 0.5rem 0.5rem; padding: 20px; }
        .details-content { padding: 15px; }
        .meus-botoes { display: flex; justify-content: center; }
        .meus-botoes > button { margin-right: 5px; }
        .item-row td:last-child { text-align: center; }
        .min-h-40px { min-height: 40px; }
        #spinner { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 1060; }
        .sr-only, .visually-hidden { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0; }
    </style>
</head>
<body>

<div class="container mt-4">
    <h2 class="mb-4 text-center">Gerar Declaração Única de Exportação (DU-E)</h2>

    <ul class="nav nav-tabs" id="dueTabs">
        <li class="nav-item">
            <a class="nav-link active" id="tab-link-1" data-bs-toggle="tab" data-bs-target="#aba1" href="#aba1">Dados gerais</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="tab-link-3" data-bs-toggle="tab" data-bs-target="#aba3" href="#aba3">NFE(s) inserida(s)</a>
        </li>
    </ul>

    <div class="tab-content" id="dueTabsContent">
        <div class="tab-pane fade show active" id="aba1" role="tabpanel" aria-labelledby="tab-link-1">
            <form id="dueForm" enctype="multipart/form-data">
                <div class="card mb-4">
                    <div class="card-header">Upload de NF-e (XML)</div>
                    <div class="card-body">
                        <div class="form-group mb-0"> <label for="xml-files" class="form-label">Selecionar Arquivos XML</label>
                            <input type="file" id="xml-files" class="form-control" accept=".xml" multiple>
                        </div>
                        <div id="uploadStatus" class="mt-2 text-muted small"></div> </div>
                </div>

                <div class="card mb-4">
                     <div class="card-header">Informações Gerais da DU-E</div>
                     <div class="card-body">
                         <div class="row g-3">
                            <div class="col-md-6 form-group">
                                <label for="text-cnpj-cpf-select" class="form-label">CNPJ/CPF Exportador:</label>
                                <input class="form-control" type="text" id="text-cnpj-cpf-select" name="cnpj-cpf" list="cnpj-cpf-list" readonly>
                                <datalist id="cnpj-cpf-list"></datalist> </div>
                            <div class="col-md-6 form-group">
                                <label for="nomeCliente" class="form-label">Nome Exportador:</label>
                                <input type="text" id="nomeCliente" class="form-control" readonly>
                            </div>
                         </div>

                         <div class="row g-3 mt-3">
                             <div class="col-md-6 form-group">
                                 <h6>Forma de exportação</h6>
                                 <div class="form-check">
                                     <input class="form-check-input" type="radio" id="por-conta-propria" name="forma-export" value="Por conta própria" checked>
                                     <label class="form-check-label" for="por-conta-propria">Por conta própria</label>
                                 </div>
                                 <div class="form-check">
                                     <input class="form-check-input" type="radio" id="p-conta-ordem-terceiros" name="forma-export" value="Por conta ou ordem de terceiros">
                                     <label class="form-check-label" for="p-conta-ordem-terceiros">Por conta ou ordem de terceiros</label>
                                 </div>
                                  <div class="form-check">
                                      <input class="form-check-input" type="radio" id="p-op-rm-post-ou-remss" name="forma-export" value="Por operador de remessa postal ou expressa">
                                      <label class="form-check-label" for="p-op-rm-post-ou-remss">Por operador de remessa postal ou expressa</label>
                                  </div>
                             </div>
                              <div class="col-md-6 form-group">
                                  <h6>Tipo de documento fiscal</h6>
                                  <div class="form-check">
                                      <input class="form-check-input" type="radio" id="nfe" name="tp-doc-amp-merc-export" value="Nota fiscal eletronica" checked>
                                      <label class="form-check-label" for="nfe">Nota Fiscal Eletrônica (NF-e)</label>
                                  </div>
                                   <div class="form-check">
                                       <input class="form-check-input" type="radio" id="nf-form" name="tp-doc-amp-merc-export" value="Nota fiscal formulario">
                                       <label class="form-check-label" for="nf-form">Nota Fiscal Formulário</label>
                                   </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" id="s-nf" name="tp-doc-amp-merc-export" value="Sem nota fiscal">
                                        <label class="form-check-label" for="s-nf">Sem nota fiscal</label>
                                    </div>
                              </div>
                         </div>

                         <div class="row g-3 mt-3">
                             <div class="col-md-6 form-group">
                                 <label for="text-moeda" class="form-label">Moeda de Negociação:</label>
                                 <input id="text-moeda" type="text" class="form-control" list="moeda-list" name="moeda">
                                 <datalist id="moeda-list">
                                     <?php
                                         if ($pdo) {
                                             foreach($pdo->query('SELECT Codigo, Nome, Simbolo FROM moeda ORDER BY Nome') as $row){
                                                 echo '<option value="'. htmlspecialchars($row['Codigo']) .'" data-simbolo="'. htmlspecialchars($row['Simbolo']) .'">'. htmlspecialchars($row['Nome']) .'</option>';
                                             }
                                         } else {
                                             echo '<option value="">Erro ao carregar moedas</option>';
                                         }
                                     ?>
                                 </datalist>
                             </div>
                              <div class="col-md-6 form-group">
                                  <label for="ruc" class="form-label">Referência Única de Carga (RUC):</label>
                                  <input type="text" class="form-control" id="ruc" name="ruc" placeholder="Opcional">
                              </div>
                         </div>

                         <div class="row g-3 mt-3">
                             <div class="col-md-6 form-group">
                                 <label for="situacao-espec-despacho" class="form-label">Situação especial de despacho:</label>
                                 <select name="situacao-espec-despacho" class="form-select" id="situacao-espec-despacho">
                                     <option selected value="">Nenhuma</option>
                                     <option value="DU-E a posteriori">DU-E a posteriori</option>
                                     <option value="Embarque antecipado">Embarque antecipado</option>
                                     <option value="Exportação sem saída da mercadoria do país">Exportação sem saída da mercadoria do país</option>
                                 </select>
                             </div>
                             <div class="col-md-6 form-group d-flex align-items-center pt-3">
                                 <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" id="export-cons" name="export-cons" value="Exportação Consosorciada">
                                    <label class="form-check-label" for="export-cons">Exportação Consorciada</label>
                                 </div>
                             </div>
                         </div>

                        <hr class="my-4">

                        <div>
                            <h5 id="lbl-local-despacho">Local de Despacho</h5>
                             <div class="row g-3">
                                <div class="col-md-6 form-group">
                                     <label for="text-campo-de-pesquisa-unidades-rfb-d" class="form-label">Unidade da RFB (Despacho):</label>
                                     <input id="text-campo-de-pesquisa-unidades-rfb-d" type="text" class="form-control" list="unidades-rfb-d-list" name="unidade_rfb_despacho">
                                     <datalist id="unidades-rfb-d-list">
                                         <?php
                                             if($pdo) {
                                                 foreach($pdo->query('SELECT Codigo, Nome FROM unidades_rfb ORDER BY Nome') as $row){
                                                     echo '<option value="'. htmlspecialchars($row['Codigo']) .'">'. htmlspecialchars($row['Nome']) .'</option>';
                                                 }
                                             }
                                         ?>
                                     </datalist>
                                </div>
                                <div class="col-md-6 form-group">
                                     <label for="text-campo-de-pesquisa-recinto-alfandegado-d" class="form-label">Recinto Aduaneiro (Despacho):</label>
                                      <input id="text-campo-de-pesquisa-recinto-alfandegado-d" type="text" class="form-control" list="recinto-aduaneiro-d-list" name="recinto_aduaneiro_despacho">
                                      <datalist id="recinto-aduaneiro-d-list">
                                          <?php
                                              if($pdo) {
                                                  foreach($pdo->query('SELECT codigo, Nome FROM recinto_aduaneiro ORDER BY Nome') as $row){
                                                      echo '<option value="'. htmlspecialchars($row['codigo']) .'">'. htmlspecialchars($row['Nome']) .'</option>';
                                                  }
                                              }
                                          ?>
                                      </datalist>
                                      <div class="form-text">Informe se o local de despacho for um Recinto Alfandegado.</div>
                                 </div>
                             </div>
                        </div>

                        <hr class="my-4">

                        <div>
                             <h5 id="lbl-local-embarque">Local de Embarque / Transposição de Fronteira</h5>
                              <div class="row g-3">
                                 <div class="col-md-6 form-group">
                                     <label for="text-campo-de-pesquisa-unidades-rfb-e" class="form-label">Unidade da RFB (Embarque):</label>
                                     <input id="text-campo-de-pesquisa-unidades-rfb-e" type="text" class="form-control" list="unidades-rfb-e-list" name="unidade_rfb_embarque">
                                     <datalist id="unidades-rfb-e-list">
                                          <?php
                                              if($pdo) {
                                                  foreach($pdo->query('SELECT codigo, Nome FROM unidades_rfb ORDER BY Nome') as $row){
                                                      echo '<option value="'. htmlspecialchars($row['codigo']) .'">'. htmlspecialchars($row['Nome']) .'</option>';
                                                  }
                                              }
                                          ?>
                                     </datalist>
                                 </div>
                                 <div class="col-md-6 form-group">
                                     <label for="text-campo-de-pesquisa-recinto-alfandegado-e" class="form-label">Recinto Aduaneiro (Embarque):</label>
                                      <input id="text-campo-de-pesquisa-recinto-alfandegado-e" type="text" class="form-control" list="recinto-aduaneiro-e-list" name="recinto_aduaneiro_embarque">
                                      <datalist id="recinto-aduaneiro-e-list">
                                           <?php
                                               if($pdo) {
                                                   foreach($pdo->query('SELECT codigo, Nome FROM recinto_aduaneiro ORDER BY Nome') as $row){
                                                       echo '<option value="'. htmlspecialchars($row['codigo']) .'">'. htmlspecialchars($row['Nome']) .'</option>';
                                                   }
                                               }
                                           ?>
                                      </datalist>
                                       <div class="form-text">Informe se o local de embarque for um Recinto Alfandegado.</div>
                                 </div>
                              </div>
                        </div>

                         <hr class="my-4">

                        <div id="complementos">
                            <h5 id="lbl-complementos">Complementos</h5>
                            <div class="row g-3">
                                <div class="col-md-6 form-group">
                                     <label for="via-especial-transport" class="form-label">Via especial de transporte:</label>
                                     <select class="form-select" id="via-especial-transport" name="via-especial-transport">
                                         <option selected value="">Nenhuma</option>
                                         <option value="MEIOS PRÓPRIOS">MEIOS PRÓPRIOS</option>
                                         <option value="DUTOS">DUTOS</option>
                                         <option value="LINHAS DE TRANSMISSÃO">LINHAS DE TRANSMISSÃO</option>
                                         <option value="EM MÃO">EM MÃOS</option>
                                         <option value="POR REBOQUE">POR REBOQUE</option>
                                         <option value="TRANSPORTE VICINAL FRONTEIRIÇO">TRANSPORTE VICINAL FRONTEIRIÇO</option>
                                     </select>
                                </div>
                                <div class="col-md-6 form-group">
                                     <label for="info-compl" class="form-label">Informações complementares:</label>
                                     <textarea class="form-control" id="info-compl" name="info-compl" rows="3" placeholder="Informações adicionais relevantes"></textarea>
                                </div>
                             </div>
                         </div>
                     </div> </div> </form>
        </div> <div class="tab-pane fade" id="aba3" role="tabpanel" aria-labelledby="tab-link-3">
             <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Lista de Itens das Notas Fiscais</span>
                    <button type="button" class="btn btn-secondary btn-sm" id="batchEditButton" data-bs-toggle="modal" data-bs-target="#batchEditModal">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square me-1" viewBox="0 0 16 16">
                          <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                          <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                        </svg>
                        Preenchimento em lote
                    </button>
                 </div>
                <div class="card-body">
                     <div id="tabelaContainer" class="table-responsive">
                         <table class="table table-bordered table-hover table-sm" id="notasFiscaisTable"> <thead class="thead-light">
                                 <tr>
                                     <th>Chave NF-e</th>
                                     <th>Item</th>
                                     <th>NCM</th>
                                     <th>Descrição Mercadoria</th>
                                     <th>Importador</th>
                                     <th>País Importador</th>
                                     <th>Ações</th>
                                 </tr>
                             </thead>
                             <tbody>
                                 <tr><td colspan="7" class="text-center text-muted fst-italic">Carregue arquivos XML para visualizar os itens.</td></tr>
                             </tbody>
                         </table>
                     </div>
                 </div>
             </div>
        </div> </div> <div class="form-group mt-4 text-center"> <button type="button" id="gerarDUE" class="btn btn-primary w-50">Salvar e Gerar DU-E</button> </div>

    <div id="spinner" class="spinner-border text-primary" role="status" style="display: none;">
        <span class="visually-hidden">Carregando...</span> </div>
</div>

<div class="modal fade" id="itemDetailsModal" tabindex="-1" aria-labelledby="itemDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl"> <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="itemDetailsModalLabel">Detalhes do Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center p-5"><div class="spinner-border text-secondary" role="status"><span class="visually-hidden">Carregando detalhes...</span></div></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" id="saveItemDetails">Salvar Alterações do Item</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="batchEditModal" tabindex="-1" aria-labelledby="batchEditModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="batchEditModalLabel">Preenchimento em Lote para Todos os Itens</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small">Os valores selecionados aqui serão aplicados a <strong>todos os itens</strong> carregados das NF-es. Deixe um campo em "Selecione..." para não alterar o valor existente daquele campo nos itens.</p>
                <form id="batchEditForm">
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label for="batchIncotermSelect" class="form-label">Incoterm (Condição de Venda):</label>
                            <select id="batchIncotermSelect" class="form-select">
                                <option value="" selected>Selecione para alterar...</option>
                                <?php
                                if (!empty($incoterms)) {
                                    foreach ($incoterms as $incoterm) {
                                        echo '<option value="' . htmlspecialchars($incoterm['Sigla']) . '">' . htmlspecialchars($incoterm['Sigla'] . ' - ' . $incoterm['Descricao']) . '</option>';
                                    }
                                } else {
                                     echo '<option value="" disabled>Erro ao carregar Incoterms</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                             <label for="batchPaisDestinoInput" class="form-label">País de Destino Final:</label>
                             <input type="text" id="batchPaisDestinoInput" class="form-control" list="paisesDestinoList" placeholder="Digite ou selecione...">
                             <datalist id="paisesDestinoList">
                                <option value="">Selecione para alterar...</option>
                                <?php
                                if (!empty($paises)) {
                                    foreach ($paises as $pais) {
                                        echo '<option value="' . htmlspecialchars($pais['Nome']) . '" data-codigo="' . htmlspecialchars($pais['CodigoBACEN']) .'">' . htmlspecialchars($pais['Nome']) . '</option>';
                                    }
                                } else {
                                     echo '<option value="" disabled>Erro ao carregar Países</option>';
                                }
                                ?>
                             </datalist>
                        </div>

                        <?php for ($i = 1; $i <= 4; $i++): ?>
                        <div class="col-md-6 mb-3">
                            <label for="batchEnquadramento<?= $i ?>Select" class="form-label"><?= $i ?>º Enquadramento:</label>
                            <select id="batchEnquadramento<?= $i ?>Select" class="form-select">
                                <option value="" selected>Selecione para alterar...</option>
                                <?php
                                if (!empty($enquadramentos)) {
                                    foreach ($enquadramentos as $enq) {
                                        echo '<option value="' . htmlspecialchars($enq['CODIGO']) . '">' . htmlspecialchars($enq['CODIGO'] . ' - ' . $enq['DESCRICAO']) . '</option>';
                                    }
                                } else {
                                      echo '<option value="" disabled>Erro ao carregar Enquadramentos</option>';
                                }
                                ?>
                                <option value="99999">99999 - OPERACAO SEM ENQUADRAMENTO</option> </select>
                        </div>
                        <?php endfor; ?>

                        <div class="col-12 mb-3">
                             <label class="form-label d-block">Acordo Mercosul (CCPT/CCROM):</label>
                             <div class="form-check form-check-inline">
                                 <input class="form-check-input" type="radio" name="batchCcptCcrom" id="batchCcptCcromAlterar" value="" checked>
                                 <label class="form-check-label" for="batchCcptCcromAlterar">Não alterar</label>
                             </div>
                             <div class="form-check form-check-inline">
                                 <input class="form-check-input" type="radio" name="batchCcptCcrom" id="batchCcptCcromNone" value="NA"> <label class="form-check-label" for="batchCcptCcromNone">N/A (Nenhum)</label>
                             </div>
                             <div class="form-check form-check-inline">
                                 <input class="form-check-input" type="radio" name="batchCcptCcrom" id="batchCcpt" value="CCPT">
                                 <label class="form-check-label" for="batchCcpt">CCPT</label>
                             </div>
                             <div class="form-check form-check-inline">
                                 <input class="form-check-input" type="radio" name="batchCcptCcrom" id="batchCcrom" value="CCROM">
                                 <label class="form-check-label" for="batchCcrom">CCROM</label>
                             </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="saveBatchEdit">Aplicar a Todos os Itens</button>
            </div>
        </div>
    </div>
</div>

<script>
    console.log("PHP->JS: Preparando dados auxiliares...");
    try {
        // Atribui diretamente o resultado do json_encode (sem aspas em volta do PHP!)
        // As flags JSON_HEX_* garantem que caracteres HTML não quebrem o JS
        window.incotermsData = <?php echo json_encode($incoterms ?: [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
        window.enquadramentosData = <?php echo json_encode($enquadramentos ?: [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
        window.paisesData = <?php echo json_encode($paises ?: [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
        console.log("PHP->JS: Dados carregados diretamente nas variáveis window.");
    } catch(e) {
        // Este catch pegaria um erro de sintaxe JS se o JSON do PHP fosse MUITO inválido
        console.error("PHP->JS: Erro CRÍTICO na atribuição direta dos dados:", e);
        window.incotermsData = [];
        window.enquadramentosData = [];
        window.paisesData = [];
        alert("Falha crítica ao carregar dados essenciais da página. Verifique a fonte da página e o log de erros do PHP.");
    }
</script>


<script src="./due/js/main.mjs" type="module"></script>
<script src="./due/js/due-checkFields.js"></script>

