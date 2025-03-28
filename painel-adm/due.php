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
    $stmt = $pdo->query('SELECT CodigoBACEN, Nome FROM paises ORDER BY Nome'); // Assumindo tabela 'paises' com CodigoBACEN
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


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script type="module">
    console.log("SCRIPT COM PARSER INICIADO");

    // --- Funções Auxiliares ---
    const getSafe = (obj, path, defaultValue = '') => { try { const value = path.split('.').reduce((o, k) => (o || {})[k], obj); return value ?? defaultValue; } catch { return defaultValue; } };
    const getXmlValue = (el, tag) => el?.getElementsByTagName(tag)?.[0]?.textContent?.trim() ?? '';
    const getXmlAttr = (el, attr) => el?.getAttribute(attr) ?? '';

    // --- Variáveis Globais ---
    let processedNFData = []; // Guarda NFs processadas [{nf: {...}, items: [...]}, ...]
    let itemDetailsModalInstance = null;
    let batchEditModalInstance = null; // Instância para o novo modal

    // --- Parser XML (Mantido igual) ---
    const parseNFeXML = (xmlString, fileName = 'arquivo') => {
        console.log(`[Parse XML] Iniciando para ${fileName}`);
        try {
            const parser = new DOMParser();
            const xmlDoc = parser.parseFromString(xmlString, "text/xml");
            const parserError = xmlDoc.getElementsByTagName("parsererror");
            if (parserError.length > 0) {
                console.error(`Erro PARSE XML ${fileName}:`, parserError[0].textContent);
                throw new Error(`Erro de parse no XML: ${fileName}`);
            }
            const infNFe = xmlDoc.getElementsByTagName("infNFe")[0];
            if (!infNFe) {
                console.error(`Tag <infNFe> não encontrada em ${fileName}`);
                 throw new Error(`Tag <infNFe> não encontrada: ${fileName}`);
            }
            const chave = getXmlAttr(infNFe, 'Id').replace('NFe', '');
            const emit = infNFe.getElementsByTagName("emit")[0];
            const dest = infNFe.getElementsByTagName("dest")[0];
            const enderDest = dest?.getElementsByTagName("enderDest")[0];
            const exporta = infNFe.getElementsByTagName("exporta")[0];
            const detElements = infNFe.getElementsByTagName("det");

            const nfeData = {
                chaveAcesso: chave,
                emitente: { cnpj: getXmlValue(emit, "CNPJ"), nome: getXmlValue(emit, "xNome") },
                destinatario: {
                    nome: getXmlValue(dest, "xNome"), idEstrangeiro: getXmlValue(dest, "idEstrangeiro"),
                    endereco: { logradouro: getXmlValue(enderDest, "xLgr"), numero: getXmlValue(enderDest, "nro"), bairro: getXmlValue(enderDest, "xBairro"), municipio: getXmlValue(enderDest, "xMun"), uf: getXmlValue(enderDest, "UF"), paisNome: getXmlValue(enderDest, "xPais"), paisCodigo: getXmlValue(enderDest, "cPais") }
                },
                exportacao: { ufSaidaPais: getXmlValue(exporta, "UFSaidaPais"), localExportacao: getXmlValue(exporta, "xLocExporta") },
                items: []
            };

            for (let i = 0; i < detElements.length; i++) {
                const det = detElements[i];
                const prod = det.getElementsByTagName("prod")[0]; if (!prod) continue;
                const uTrib = getXmlValue(prod, "uTrib"); const qTrib = getXmlValue(prod, "qTrib"); const vProd = getXmlValue(prod, "vProd");
                nfeData.items.push({
                    nItem: getXmlAttr(det, 'nItem'), cProd: getXmlValue(prod, "cProd"), xProd: getXmlValue(prod, "xProd"), ncm: getXmlValue(prod, "NCM"), cfop: getXmlValue(prod, "CFOP"), uCom: getXmlValue(prod, "uCom"), qCom: getXmlValue(prod, "qCom"), vUnCom: getXmlValue(prod, "vUnCom"), vProd: vProd, uTrib: uTrib, qTrib: qTrib, infAdProd: getXmlValue(det, "infAdProd"),
                    // --- Campos Editáveis (Valores Iniciais Padrão/XML) ---
                    descricaoNcm: "", atributosNcm: "", unidadeEstatistica: uTrib, quantidadeEstatistica: qTrib, pesoLiquidoItem: (uTrib.toUpperCase() === 'KG' || uTrib.toUpperCase() === 'QUILOGRAMA') ? qTrib : "", condicaoVenda: "", vmcv: "", vmle: vProd, paisDestino: getSafe(nfeData, 'destinatario.endereco.paisNome', ''), descricaoDetalhadaDue: getXmlValue(prod, "xProd"), // Default para xProd
                    enquadramento1: "", enquadramento2: "", enquadramento3: "", enquadramento4: "", lpcos: [], nfsRefEletronicas: [], nfsRefFormulario: [], nfsComplementares: [], ccptCcrom: ""
                });
            }
            console.log(`[Parse XML] ${fileName} OK - ${nfeData.items.length} itens encontrados.`);
            return nfeData;
        } catch (error) {
            console.error(`Erro GERAL no Parse XML de ${fileName}:`, error);
            const uploadStatusEl = document.getElementById('uploadStatus');
            if(uploadStatusEl) uploadStatusEl.innerHTML += `<div class="text-danger small">Falha ao processar ${fileName}: ${error.message}</div>`;
            return null;
        }
    };

    // --- Função para Criar os Campos do Modal Detalhado (SEM ACCORDION) ---
    function createItemDetailsFields(itemData, nfData, nfIndex, itemIndex) {
        const container = document.createElement('div');
        container.classList.add('item-details-form-container');
        const idPrefix = `modal-item-${nfIndex}-${itemIndex}-`;
        const val = (key, defaultValue = '') => getSafe(itemData, key, defaultValue);
        const isSelected = (value, targetValue) => value === targetValue ? 'selected' : '';
        const isChecked = (value, targetValue) => value === targetValue ? 'checked' : '';

        // Gera opções para selects (usando dados globais carregados do PHP)
        const createOptions = (data, valueKey, textKey, selectedValue, includeEmpty = true) => {
            let optionsHtml = includeEmpty ? '<option value="">Selecione...</option>' : '';
            if (data && data.length > 0) {
                optionsHtml += data.map(item =>
                    `<option value="${htmlspecialchars(item[valueKey])}" ${isSelected(selectedValue, item[valueKey])}>${htmlspecialchars(item[textKey])}</option>`
                ).join('');
            }
            return optionsHtml;
        };

        // Helper para escapar HTML (simples)
        const htmlspecialchars = (str) => {
             if (typeof str !== 'string') return str;
             return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
        }


        const enqOptions = (num) => createOptions(window.enquadramentosData || [], 'CODIGO', 'DESCRICAO', val(`enquadramento${num}`), true) + `<option value="99999" ${isSelected(val(`enquadramento${num}`), '99999')}>99999 - OPERACAO SEM ENQUADRAMENTO</option>`; // Adiciona 99999
        const enqSelectHTML = (num) => `<select id="${idPrefix}enquadramento${num}" name="enquadramento${num}" class="form-select form-select-sm">${enqOptions(num)}</select>`;

        // Constrói Incoterm Text: Sigla - Descricao
        const incotermTextMap = (window.incotermsData || []).map(i => ({...i, DisplayText: `${i.Sigla} - ${i.Descricao}`}));
        const incotermOptionsHTML = createOptions(incotermTextMap, 'Sigla', 'DisplayText', val('condicaoVenda'));

        const paisOptionsHTML = createOptions(window.paisesData || [], 'Nome', 'Nome', val('paisDestino')); // Usa Nome como valor e texto

        // *** HTML Completo do Modal (SEM ACCORDION) ***
        container.innerHTML = `
        <h5 class="mb-3 border-bottom pb-2">Item ${val('nItem', itemIndex + 1)} (NF-e: ...${getSafe(nfData, 'chaveAcesso', 'N/A').slice(-6)})</h5>

        <h6>Dados Básicos e NCM</h6>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Exportador:</label>
                <input type="text" class="form-control form-control-sm bg-light" value="${htmlspecialchars(getSafe(nfData, 'emitente.nome', 'N/A'))}" readonly>
            </div>
             <div class="col-md-6">
                 <label for="${idPrefix}ncm" class="form-label">NCM:</label>
                 <input type="text" id="${idPrefix}ncm" name="ncm" class="form-control form-control-sm" value="${htmlspecialchars(val('ncm'))}" required>
             </div>
             <div class="col-md-6">
                 <label for="${idPrefix}descricao_ncm" class="form-label">Descrição NCM:</label>
                 <input type="text" id="${idPrefix}descricao_ncm" name="descricao_ncm" class="form-control form-control-sm" value="${htmlspecialchars(val('descricaoNcm'))}" placeholder="Consultar externamente se necessário">
             </div>
              <div class="col-md-6">
                  <label for="${idPrefix}atributos_ncm" class="form-label">Atributos NCM:</label>
                  <input type="text" id="${idPrefix}atributos_ncm" name="atributos_ncm" class="form-control form-control-sm" value="${htmlspecialchars(val('atributosNcm'))}" placeholder="Consultar/definir atributos">
              </div>
         </div>

        <h6>Descrição da Mercadoria</h6>
        <div class="mb-3">
            <label for="${idPrefix}descricao_mercadoria" class="form-label">Descrição Conforme NF-e:</label>
            <textarea id="${idPrefix}descricao_mercadoria" name="descricao_mercadoria" class="form-control form-control-sm" rows="2" readonly>${htmlspecialchars(val('xProd'))}</textarea> </div>
        <div class="mb-3">
            <label for="${idPrefix}descricao_complementar" class="form-label">Descrição Complementar (NF-e - infAdProd):</label>
            <textarea id="${idPrefix}descricao_complementar" name="descricao_complementar" class="form-control form-control-sm" rows="2">${htmlspecialchars(val('infAdProd'))}</textarea>
        </div>
         <div class="mb-4">
             <label for="${idPrefix}descricao_detalhada_due" class="form-label">Descrição Detalhada para DU-E:</label>
             <textarea id="${idPrefix}descricao_detalhada_due" name="descricao_detalhada_due" class="form-control form-control-sm" rows="4" placeholder="Descrição completa e detalhada exigida para a DU-E" required>${htmlspecialchars(val('descricaoDetalhadaDue', val('xProd')))}</textarea> </div>

        <h6>Quantidades e Valores</h6>
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <label for="${idPrefix}unidade_estatistica" class="form-label">Unid. Estatística (NCM):</label>
                <input type="text" id="${idPrefix}unidade_estatistica" name="unidade_estatistica" class="form-control form-control-sm" value="${htmlspecialchars(val('unidadeEstatistica', val('uTrib')))}" placeholder="Unid. conforme NCM">
            </div>
            <div class="col-md-4">
                <label for="${idPrefix}quantidade_estatistica" class="form-label">Qtd. Estatística:</label>
                <input type="number" step="any" id="${idPrefix}quantidade_estatistica" name="quantidade_estatistica" class="form-control form-control-sm" value="${htmlspecialchars(val('quantidadeEstatistica', val('qTrib')))}">
            </div>
            <div class="col-md-4">
                <label for="${idPrefix}peso_liquido" class="form-label">Peso Líquido Total (KG):</label>
                <input type="number" step="any" id="${idPrefix}peso_liquido" name="peso_liquido" class="form-control form-control-sm" value="${htmlspecialchars(val('pesoLiquidoItem', (val('uTrib','').toUpperCase() === 'KG' ? val('qTrib') : '')))}" required>
            </div>

            <div class="col-md-3">
                 <label for="${idPrefix}unidade_comercializada" class="form-label">Unid. Comercial.:</label>
                 <input type="text" id="${idPrefix}unidade_comercializada" name="unidade_comercializada" class="form-control form-control-sm bg-light" value="${htmlspecialchars(val('uCom'))}" readonly>
             </div>
             <div class="col-md-3">
                 <label for="${idPrefix}quantidade_comercializada" class="form-label">Qtd. Comercial.:</label>
                 <input type="number" step="any" id="${idPrefix}quantidade_comercializada" name="quantidade_comercializada" class="form-control form-control-sm bg-light" value="${htmlspecialchars(val('qCom'))}" readonly>
             </div>
             <div class="col-md-3">
                 <label for="${idPrefix}valor_unit_comercial" class="form-label">Vlr Unit. Com. (R$):</label>
                 <input type="number" step="any" id="${idPrefix}valor_unit_comercial" name="valor_unit_comercial" class="form-control form-control-sm bg-light" value="${htmlspecialchars(val('vUnCom'))}" readonly>
             </div>
            <div class="col-md-3">
                <label class="form-label">Vlr Total Item (R$):</label>
                <input type="number" class="form-control form-control-sm bg-light" value="${htmlspecialchars(val('vProd'))}" readonly>
            </div>

            <div class="col-md-4">
                 <label for="${idPrefix}condicao_venda" class="form-label">Condição Venda (Incoterm):</label>
                 <select id="${idPrefix}condicao_venda" name="condicao_venda" class="form-select form-select-sm">
                     ${incotermOptionsHTML}
                 </select>
             </div>
            <div class="col-md-4">
                <label for="${idPrefix}vmle" class="form-label">VMLE (R$):</label>
                <input type="number" step="any" id="${idPrefix}vmle" name="vmle" class="form-control form-control-sm" value="${htmlspecialchars(val('vmle', val('vProd')))}" title="Valor da Mercadoria no Local de Embarque">
            </div>
            <div class="col-md-4">
                <label for="${idPrefix}vmcv" class="form-label">VMCV (Moeda Negoc.):</label>
                <input type="number" step="any" id="${idPrefix}vmcv" name="vmcv" class="form-control form-control-sm" value="${htmlspecialchars(val('vmcv'))}" title="Valor da Mercadoria na Condição de Venda (na moeda de negociação)">
            </div>
        </div>

        <h6>Importador e Destino</h6>
         <div class="row g-3 mb-4">
             <div class="col-md-6">
                 <label class="form-label">Nome Importador (NF-e):</label>
                 <input type="text" class="form-control form-control-sm bg-light" value="${htmlspecialchars(getSafe(nfData, 'destinatario.nome', 'N/A'))}" readonly>
             </div>
             <div class="col-md-6">
                 <label class="form-label">País Importador (NF-e):</label>
                 <input type="text" class="form-control form-control-sm bg-light" value="${htmlspecialchars(getSafe(nfData, 'destinatario.endereco.paisNome', 'N/A'))} (${htmlspecialchars(getSafe(nfData, 'destinatario.endereco.paisCodigo', 'N/A'))})" readonly>
             </div>
             <div class="col-12">
                 <label class="form-label">Endereço Importador (NF-e):</label>
                 <input type="text" class="form-control form-control-sm bg-light" value="${htmlspecialchars([getSafe(nfData, 'destinatario.endereco.logradouro'), getSafe(nfData, 'destinatario.endereco.numero'), getSafe(nfData, 'destinatario.endereco.bairro'), getSafe(nfData, 'destinatario.endereco.municipio'), getSafe(nfData, 'destinatario.endereco.uf')].filter(Boolean).join(', ') || '(Não informado na NF-e)')}" readonly>
             </div>
             <div class="col-md-6">
                  <label for="${idPrefix}pais_destino" class="form-label">País Destino Final (DU-E):</label>
                  <select id="${idPrefix}pais_destino" name="pais_destino" class="form-select form-select-sm">
                      ${paisOptionsHTML}
                  </select>
              </div>
         </div>

          <h6>Enquadramentos da Operação</h6>
          <div class="row g-3 mb-4">
              ${[1, 2, 3, 4].map(num => `
              <div class="col-md-6">
                  <label for="${idPrefix}enquadramento${num}" class="form-label">${num}º Enquadramento:</label>
                  ${enqSelectHTML(num)}
              </div>`).join('')}
          </div>

          <h6>LPCO (Licenças, Permissões, Certificados e Outros)</h6>
           <div class="lpco-container mb-4" id="${idPrefix}lpco-section">
                <div class="input-group input-group-sm">
                    <input type="text" id="${idPrefix}lpco-input" class="form-control" placeholder="Digite o número do LPCO">
                    <button type="button" class="btn btn-success add-lpco-btn">Adicionar</button>
                </div>
                <div class="mt-2">
                    <label class="form-label small text-muted">LPCOs Adicionados:</label>
                    <div class="border p-2 rounded bg-light lpco-list min-h-40px">
                        ${val('lpcos', []).map(lpco => `<span class="badge bg-secondary me-1 mb-1 lpco-item" data-value="${htmlspecialchars(lpco)}">${htmlspecialchars(lpco)} <button type="button" class="btn-close btn-close-white btn-sm remove-lpco" aria-label="Remover LPCO"></button></span>`).join('')}
                    </div>
                    <input type="hidden" class="lpcos-hidden" name="${idPrefix}lpcos_hidden" value="${htmlspecialchars(val('lpcos', []).join(','))}">
                </div>
           </div>

           <h6>Referências e Tratamento Tributário</h6>
            <div class="row g-3">
                <div class="col-md-7">
                      <div class="border p-3 rounded mb-3" id="${idPrefix}nfe-ref-section">
                          <label class="form-label fw-bold small mb-1">NF-e Referenciada</label>
                          <div class="input-group input-group-sm mb-2">
                              <input type="text" class="form-control nfe-ref-input" placeholder="Chave de Acesso (44 dígitos)">
                              <button class="btn btn-outline-secondary add-nfe-ref-btn" type="button">Add</button>
                          </div>
                          <ul class="list-group list-group-flush nfe-ref-list small ps-1">
                              ${val('nfsRefEletronicas', []).map(k => `<li class="list-group-item py-1 px-0 d-flex justify-content-between align-items-center" data-value="${htmlspecialchars(k)}">${htmlspecialchars(k)}<button type="button" class="btn-close btn-sm remove-ref" aria-label="Remover"></button></li>`).join('')}
                          </ul>
                          <input type="hidden" class="nfsRefEletronicas-hidden" name="${idPrefix}nfsRefEletronicas_hidden" value="${htmlspecialchars(val('nfsRefEletronicas', []).join(','))}">
                      </div>

                      <div class="border p-3 rounded mb-3" id="${idPrefix}nf_form-ref-section">
                           <label class="form-label fw-bold small mb-1">NF Formulário Referenciada</label>
                           <div class="input-group input-group-sm mb-2">
                               <input type="text" class="form-control nf_form-ref-input" placeholder="Série, Número, Modelo, etc.">
                               <button class="btn btn-outline-secondary add-nf_form-ref-btn" type="button">Add</button>
                           </div>
                           <ul class="list-group list-group-flush nf_form-ref-list small ps-1">
                               ${val('nfsRefFormulario', []).map(d => `<li class="list-group-item py-1 px-0 d-flex justify-content-between align-items-center" data-value="${htmlspecialchars(d)}">${htmlspecialchars(d)}<button type="button" class="btn-close btn-sm remove-ref" aria-label="Remover"></button></li>`).join('')}
                           </ul>
                           <input type="hidden" class="nfsRefFormulario-hidden" name="${idPrefix}nfsRefFormulario_hidden" value="${htmlspecialchars(val('nfsRefFormulario', []).join(','))}">
                       </div>

                        <div class="border p-3 rounded mb-3 mb-md-0" id="${idPrefix}nfc-ref-section">
                            <label class="form-label fw-bold small mb-1">NF Complementar</label>
                            <div class="input-group input-group-sm mb-2">
                                <input type="text" class="form-control nfc-ref-input" placeholder="Chave de Acesso (44 dígitos)">
                                <button class="btn btn-outline-secondary add-nfc-ref-btn" type="button">Add</button>
                            </div>
                            <ul class="list-group list-group-flush nfc-ref-list small ps-1">
                                ${val('nfsComplementares', []).map(k => `<li class="list-group-item py-1 px-0 d-flex justify-content-between align-items-center" data-value="${htmlspecialchars(k)}">${htmlspecialchars(k)}<button type="button" class="btn-close btn-sm remove-ref" aria-label="Remover"></button></li>`).join('')}
                            </ul>
                            <input type="hidden" class="nfsComplementares-hidden" name="${idPrefix}nfsComplementares_hidden" value="${htmlspecialchars(val('nfsComplementares', []).join(','))}">
                        </div>
                </div>
                <div class="col-md-5">
                     <div class="border p-3 rounded h-100"> <h6 class="mb-3">Acordo Mercosul</h6>
                         <div class="form-check mb-2">
                             <input class="form-check-input" type="radio" name="${idPrefix}ccpt_ccrom" id="${idPrefix}ccpt_ccrom_none" value="" ${isChecked(val('ccptCcrom'), '')}>
                             <label class="form-check-label small" for="${idPrefix}ccpt_ccrom_none">N/A (Não se aplica)</label>
                         </div>
                         <div class="form-check mb-2">
                             <input class="form-check-input" type="radio" name="${idPrefix}ccpt_ccrom" id="${idPrefix}ccpt" value="CCPT" ${isChecked(val('ccptCcrom'), 'CCPT')}>
                             <label class="form-check-label small" for="${idPrefix}ccpt">CCPT (Cert. Cumprimento Política Tarifária Comum)</label>
                         </div>
                         <div class="form-check">
                             <input class="form-check-input" type="radio" name="${idPrefix}ccpt_ccrom" id="${idPrefix}ccrom" value="CCROM" ${isChecked(val('ccptCcrom'), 'CCROM')}>
                             <label class="form-check-label small" for="${idPrefix}ccrom">CCROM (Cert. Cumprimento Requisitos Origem Mercosul)</label>
                         </div>
                      </div>
                </div>
            </div>
        `;

        // --- Listeners Dinâmicos para Adicionar/Remover Itens (LPCO, Refs) ---
        // (Mantidos iguais, usando delegação de eventos no 'container')
        container.addEventListener('click', (e) => {
            // Remover LPCO
            if (e.target.classList.contains('remove-lpco')) {
                 const badge = e.target.closest('.lpco-item');
                 const containerDiv = badge?.closest('.lpco-container');
                 const hiddenInput = containerDiv?.querySelector('.lpcos-hidden');
                 if (badge && hiddenInput) {
                     const valueToRemove = badge.dataset.value;
                     hiddenInput.value = (hiddenInput.value || '').split(',').filter(i => i && i !== valueToRemove).join(',');
                     badge.remove();
                 }
                 return;
            }
             // Remover Referência (NF-e, Formulário, Complementar)
             if (e.target.classList.contains('remove-ref')) {
                 const listItem = e.target.closest('li[data-value]');
                 const sectionDiv = listItem?.closest('.border');
                 const hiddenInput = sectionDiv?.querySelector('input[type="hidden"]');
                 if (listItem && hiddenInput) {
                     const valueToRemove = listItem.dataset.value;
                     hiddenInput.value = (hiddenInput.value || '').split(',').filter(i => i && i !== valueToRemove).join(',');
                     listItem.remove();
                 }
                 return;
             }
            // Adicionar LPCO
            if (e.target.classList.contains('add-lpco-btn')) {
                const containerDiv = e.target.closest('.lpco-container');
                const inputElement = containerDiv?.querySelector('input[type="text"]');
                const listDiv = containerDiv?.querySelector('.lpco-list');
                const hiddenInput = containerDiv?.querySelector('.lpcos-hidden');
                 if (inputElement && listDiv && hiddenInput) {
                    const valueToAdd = inputElement.value.trim().toUpperCase();
                     if (valueToAdd && !(hiddenInput.value || '').split(',').includes(valueToAdd)) {
                         listDiv.insertAdjacentHTML('beforeend', `<span class="badge bg-secondary me-1 mb-1 lpco-item" data-value="${htmlspecialchars(valueToAdd)}">${htmlspecialchars(valueToAdd)} <button type="button" class="btn-close btn-close-white btn-sm remove-lpco" aria-label="Remover LPCO"></button></span>`);
                         hiddenInput.value = [...(hiddenInput.value || '').split(','), valueToAdd].filter(Boolean).join(',');
                         inputElement.value = '';
                    } else if (valueToAdd) { alert('LPCO já adicionado.'); }
                      else { alert('Digite um número de LPCO válido.'); }
                 }
                return;
            }
             // Adicionar NF-e Referenciada
             if (e.target.classList.contains('add-nfe-ref-btn')) {
                 const sectionDiv = e.target.closest('.border');
                 const input = sectionDiv?.querySelector('.nfe-ref-input');
                 const list = sectionDiv?.querySelector('.nfe-ref-list');
                 const hidden = sectionDiv?.querySelector('.nfsRefEletronicas-hidden');
                 if (input && list && hidden) {
                     const key = input.value.trim().replace(/\D/g, '');
                     if (key.length === 44 && !(hidden.value || '').split(',').includes(key)) {
                         list.insertAdjacentHTML('beforeend', `<li class="list-group-item py-1 px-0 d-flex justify-content-between align-items-center" data-value="${key}">${key}<button type="button" class="btn-close btn-sm remove-ref" aria-label="Remover"></button></li>`);
                         hidden.value = [...(hidden.value || '').split(','), key].filter(Boolean).join(',');
                         input.value = '';
                     } else if (key.length !== 44) { alert('Chave NF-e inválida. Deve conter 44 dígitos numéricos.'); }
                       else { alert('Esta NF-e já foi adicionada como referência.'); }
                 }
                 return;
             }
             // Adicionar NF Formulário Referenciada
             if (e.target.classList.contains('add-nf_form-ref-btn')) {
                  const sectionDiv = e.target.closest('.border');
                  const input = sectionDiv?.querySelector('.nf_form-ref-input');
                  const list = sectionDiv?.querySelector('.nf_form-ref-list');
                  const hidden = sectionDiv?.querySelector('.nfsRefFormulario-hidden');
                  if (input && list && hidden) {
                      const details = input.value.trim();
                      if (details && !(hidden.value || '').split(',').includes(details)) {
                         list.insertAdjacentHTML('beforeend', `<li class="list-group-item py-1 px-0 d-flex justify-content-between align-items-center" data-value="${htmlspecialchars(details)}">${htmlspecialchars(details)}<button type="button" class="btn-close btn-sm remove-ref" aria-label="Remover"></button></li>`);
                          hidden.value = [...(hidden.value || '').split(','), details].filter(Boolean).join(',');
                          input.value = '';
                      } else if (details) { alert('Esta referência de NF Formulário já foi adicionada.'); }
                        else { alert('Insira os detalhes da NF Formulário (Série, Número, etc.).'); }
                  }
                  return;
             }
              // Adicionar NF Complementar
              if (e.target.classList.contains('add-nfc-ref-btn')) {
                  const sectionDiv = e.target.closest('.border');
                  const input = sectionDiv?.querySelector('.nfc-ref-input');
                  const list = sectionDiv?.querySelector('.nfc-ref-list');
                  const hidden = sectionDiv?.querySelector('.nfsComplementares-hidden');
                  if (input && list && hidden) {
                      const key = input.value.trim().replace(/\D/g, '');
                      if (key.length === 44 && !(hidden.value || '').split(',').includes(key)) {
                          list.insertAdjacentHTML('beforeend', `<li class="list-group-item py-1 px-0 d-flex justify-content-between align-items-center" data-value="${key}">${key}<button type="button" class="btn-close btn-sm remove-ref" aria-label="Remover"></button></li>`);
                          hidden.value = [...(hidden.value || '').split(','), key].filter(Boolean).join(',');
                          input.value = '';
                      } else if (key.length !== 44) { alert('Chave NF Complementar inválida. Deve conter 44 dígitos numéricos.'); }
                        else { alert('Esta NF Complementar já foi adicionada.'); }
                  }
                  return;
              }
        });

        return container;
    } // --- Fim createItemDetailsFields ---


    // --- Renderização da Tabela de Itens (COM BOTÃO '+' RESTAURADO) ---
    function renderNotasFiscaisTable() {
        console.log("[Render Tabela] Iniciando renderização da tabela de itens.");
        const tbody = document.querySelector('#notasFiscaisTable tbody');
        const batchButton = document.getElementById('batchEditButton');

        if (!tbody) { console.error("Elemento tbody #notasFiscaisTable não encontrado!"); return; }
        tbody.innerHTML = ''; // Limpa
        let hasItems = false;
        let totalItemsRendered = 0;

        if (processedNFData.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted fst-italic">Carregue arquivos XML para visualizar os itens.</td></tr>';
             if (batchButton) batchButton.disabled = true;
             console.log("[Render Tabela] Nenhuma NF processada.");
            return;
        }

        processedNFData.forEach((nfEntry, nfIndex) => {
            const nf = nfEntry.nf; const items = nfEntry.items;
            const chaveNFeShort = getSafe(nf, 'chaveAcesso', 'N/A').slice(-9);
            const nomeDest = getSafe(nf, 'destinatario.nome', 'N/A');
            const paisDest = getSafe(nf, 'destinatario.endereco.paisNome', 'N/A');

            if (!items || items.length === 0) {
                console.log(`[Render Tabela] NF ...${chaveNFeShort} não possui itens.`);
                return;
            }

            items.forEach((item, itemIndex) => {
                hasItems = true; totalItemsRendered++;
                const row = document.createElement('tr');
                row.classList.add('item-row');
                row.dataset.nfIndex = nfIndex; row.dataset.itemIndex = itemIndex;

                // Linha da tabela com botão '+' restaurado
                row.innerHTML = `
                    <td>...${chaveNFeShort}</td>
                    <td class="text-center">${getSafe(item, 'nItem', itemIndex + 1)}</td>
                    <td>${getSafe(item, 'ncm', 'N/A')}</td>
                    <td>${getSafe(item, 'xProd', 'N/A')}</td>
                    <td>${nomeDest}</td>
                    <td>${paisDest}</td>
                    <td class="text-center">
                        <button type="button" class="btn toggle-details" title="Detalhes Item ${getSafe(item, 'nItem', itemIndex + 1)}" data-nf-index="${nfIndex}" data-item-index="${itemIndex}">+</button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        });

        if (!hasItems && processedNFData.length > 0) {
             tbody.innerHTML = '<tr><td colspan="7" class="text-center text-warning fst-italic">Nenhum item válido encontrado nas NF-es carregadas.</td></tr>';
             if (batchButton) batchButton.disabled = true;
             console.log("[Render Tabela] NFs carregadas, mas sem itens válidos.");
        } else if (hasItems) {
             if (batchButton) batchButton.disabled = false;
             console.log(`[Render Tabela] FIM. ${totalItemsRendered} itens renderizados.`);
        } else {
              if (batchButton) batchButton.disabled = true;
        }
    }

    // --- Preencher Campos da Aba 1 (Mantido igual) ---
    const populateMainForm = (nfData) => {
        const cnpjValue = nfData ? getSafe(nfData, 'emitente.cnpj', '') : '';
        const nomeValue = nfData ? getSafe(nfData, 'emitente.nome', '') : '';
        const elCNPJ = document.getElementById('text-cnpj-cpf-select');
        const elNome = document.getElementById('nomeCliente');
        if (elCNPJ) elCNPJ.value = cnpjValue;
        if (elNome) elNome.value = nomeValue;
        console.log(nfData ? `[Aba 1] Emitente carregado: CNPJ ${cnpjValue}` : `[Aba 1] Limpando dados emitente.`);
    };

     // --- Passar dados PHP para JS (Mantido igual) ---
     try {
        window.incotermsData = <?php echo json_encode($incoterms ?: []); ?>;
        window.enquadramentosData = <?php echo json_encode($enquadramentos ?: []); ?>;
        window.paisesData = <?php echo json_encode($paises ?: []); ?>;
        console.log("Dados PHP (Incoterms, Enquadramentos, Países) carregados para JS.");
     } catch (e) {
        console.error("Erro ao carregar dados PHP para JS:", e);
        window.incotermsData = []; window.enquadramentosData = []; window.paisesData = [];
     }

    // --- Código Principal (Inicialização e Listeners - Mantido igual) ---
    document.addEventListener('DOMContentLoaded', () => {
        console.log("DOM Carregado. Iniciando script.");

        // --- Referências UI ---
        const inputXML = document.getElementById('xml-files');
        const uploadStatus = document.getElementById('uploadStatus');
        const spinner = document.getElementById('spinner');
        const notasTable = document.querySelector('#notasFiscaisTable');
        const itemDetailsModalElement = document.getElementById('itemDetailsModal');
        const saveItemButtonModal = document.getElementById('saveItemDetails');
        const batchEditButton = document.getElementById('batchEditButton');
        const batchEditModalElement = document.getElementById('batchEditModal');
        const saveBatchButton = document.getElementById('saveBatchEdit');
        const mainForm = document.getElementById('dueForm');
        const gerarDueButton = document.getElementById('gerarDUE');

        // --- Verificação Elementos Essenciais ---
        if (!inputXML || !uploadStatus || !spinner || !notasTable || !itemDetailsModalElement || !saveItemButtonModal || !batchEditButton || !batchEditModalElement || !saveBatchButton || !mainForm || !gerarDueButton) {
            console.error("ERRO FATAL: Elementos UI essenciais não encontrados!");
            alert("Erro crítico na inicialização da página. Recarregue ou contate suporte.");
            return;
        }
        console.log("Elementos UI referenciados OK.");

        // --- Inicializar Modais ---
        try {
            if (window.bootstrap && bootstrap.Modal) {
                 itemDetailsModalInstance = new bootstrap.Modal(itemDetailsModalElement);
                 batchEditModalInstance = new bootstrap.Modal(batchEditModalElement);
                 // Listeners hidden.bs.modal para limpar modais
                 itemDetailsModalElement.addEventListener('hidden.bs.modal', () => {
                     if (saveItemButtonModal) { delete saveItemButtonModal.dataset.nfIndex; delete saveItemButtonModal.dataset.itemIndex; }
                     const modalBody = itemDetailsModalElement.querySelector('.modal-body');
                     if (modalBody) modalBody.innerHTML = '<div class="text-center p-5"><div class="spinner-border text-secondary" role="status"><span class="visually-hidden">Carregando...</span></div></div>';
                     console.log("Modal item fechado e resetado.");
                 });
                  batchEditModalElement.addEventListener('hidden.bs.modal', () => {
                      const batchForm = document.getElementById('batchEditForm');
                      if (batchForm) {
                         batchForm.reset();
                         const radioNaoAlterar = batchForm.querySelector('#batchCcptCcromAlterar');
                         if(radioNaoAlterar) radioNaoAlterar.checked = true;
                      }
                      console.log("Modal lote fechado e resetado.");
                  });
                 console.log("Modais Bootstrap inicializados OK.");
            } else { throw new Error("Bootstrap Modal não encontrado."); }
        } catch (e) {
            console.error("Falha ao inicializar modais:", e);
            alert("Erro ao inicializar componentes (Modais).");
            batchEditButton.disabled = true; // Desabilita botão se modal falhar
            return;
        }

        // --- Renderizar Tabela Inicial ---
        renderNotasFiscaisTable();

        // --- Listener Input XML ---
        inputXML.addEventListener('change', async (event) => {
            console.log("[Input XML] 'change'"); const files = event.target.files;
            if (!files || files.length === 0) {
                uploadStatus.textContent = 'Nenhum arquivo.'; processedNFData = []; renderNotasFiscaisTable(); populateMainForm(null); return;
            }
            uploadStatus.innerHTML = `<div class="spinner-grow spinner-grow-sm text-primary" role="status"></div> Processando ${files.length}...`;
            spinner.style.display = 'block'; inputXML.disabled = true; gerarDueButton.disabled = true; batchEditButton.disabled = true;
            processedNFData = []; let promises = []; let errorCount = 0;
            console.log(`[Input XML] Lendo ${files.length} arquivos.`);
            for (const file of files) {
                if (file.name.toLowerCase().endsWith('.xml') && file.type === 'text/xml') {
                     promises.push( file.text().then(xml => {
                         const data = parseNFeXML(xml, file.name);
                         if (data && data.items && data.items.length > 0) { processedNFData.push({ nf: data, items: data.items }); console.log(`[Input XML] OK ${file.name}. ${data.items.length} itens.`); }
                         else if (data) { console.warn(`[Input XML] ${file.name} lido, mas sem itens.`); uploadStatus.innerHTML += `<div class="text-warning small">${file.name}: sem itens válidos.</div>`; }
                         else { errorCount++; } // Erro já logado no parse
                     }).catch(err => { console.error(`Erro LER ${file.name}:`, err); uploadStatus.innerHTML += `<div class="text-danger small">Falha ao ler ${file.name}.</div>`; errorCount++; }) );
                } else { console.warn(`Ignorado: ${file.name}`); uploadStatus.innerHTML += `<div class="text-secondary small">${file.name}: ignorado.</div>`; }
            }
            try { await Promise.all(promises); console.log("[Input XML] Promises concluídas."); }
            catch (err) { console.error("Erro GERAL async:", err); uploadStatus.innerHTML += `<div class="text-danger">Erro inesperado.</div>`; errorCount++; }
            finally {
                spinner.style.display = 'none'; inputXML.disabled = false; gerarDueButton.disabled = false; event.target.value = null;
                const totalItems = processedNFData.reduce((s, e) => s + e.items.length, 0); const totalNFs = processedNFData.length;
                if (totalItems > 0) { uploadStatus.textContent = `OK: ${totalItems} item(ns) em ${totalNFs} NF-e(s) válidas.`; if (errorCount > 0) uploadStatus.innerHTML += ` (${errorCount} erro(s)).`; populateMainForm(processedNFData[0]?.nf); }
                else if (totalNFs === 0 && errorCount === 0) { uploadStatus.textContent = "Nenhuma NF-e válida encontrada."; populateMainForm(null); }
                else if (errorCount > 0 && totalItems === 0) { uploadStatus.innerHTML = `Falha. Nenhum item válido. Verifique console (F12).`; populateMainForm(null); }
                else { uploadStatus.textContent = `OK. Nenhuma NF-e continha itens.`; if (errorCount > 0) uploadStatus.innerHTML += ` (${errorCount} erro(s)).`; populateMainForm(processedNFData[0]?.nf); }
                renderNotasFiscaisTable(); console.log("[Input XML] FIM.");
            }
        });

        // --- Listener Abrir Modal Item ---
        notasTable.addEventListener('click', (e) => {
            const detailsButton = e.target.closest('button.toggle-details'); if (!detailsButton) return;
            const nfIndex = parseInt(detailsButton.dataset.nfIndex, 10); const itemIndex = parseInt(detailsButton.dataset.itemIndex, 10);
            console.log(`[Abrir Modal Item] NF ${nfIndex}, Item ${itemIndex}`);
            if (isNaN(nfIndex) || isNaN(itemIndex) || !processedNFData[nfIndex]?.items?.[itemIndex]) { console.error("Índices/dados inválidos."); alert("Erro ao abrir detalhes."); return; }
            try {
                const nfData = processedNFData[nfIndex].nf; const itemData = processedNFData[nfIndex].items[itemIndex];
                const modalBody = itemDetailsModalElement.querySelector('.modal-body'); const modalTitle = itemDetailsModalElement.querySelector('.modal-title');
                if (!modalBody || !modalTitle || !itemDetailsModalInstance || !saveItemButtonModal) { console.error("Modal elems/instância?"); alert("Erro interno modal."); return; }
                modalTitle.textContent = `Detalhes Item ${getSafe(itemData, 'nItem', itemIndex + 1)} (NF: ...${getSafe(nfData, 'chaveAcesso', 'N/A').slice(-6)})`;
                modalBody.innerHTML = '<div class="text-center p-5"><div class="spinner-border text-secondary" role="status"><span class="visually-hidden">Carregando...</span></div></div>';
                saveItemButtonModal.dataset.nfIndex = nfIndex; saveItemButtonModal.dataset.itemIndex = itemIndex;
                setTimeout(() => { // Gera conteúdo após mostrar spinner
                    try {
                        console.time("createItemDetailsFields"); modalBody.innerHTML = '';
                        modalBody.appendChild(createItemDetailsFields(itemData, nfData, nfIndex, itemIndex));
                        console.timeEnd("createItemDetailsFields"); itemDetailsModalInstance.show(); console.log("[Abrir Modal Item] Exibido.");
                    } catch (renderErr) {
                        console.error("Erro criar/mostrar modal:", renderErr); modalBody.innerHTML = `<div class="alert alert-danger">Erro.</div>`; if (!itemDetailsModalInstance.isShown) itemDetailsModalInstance.show();
                    }
                }, 50);
            } catch (err) { console.error("Erro geral abrir modal:", err); alert(`Erro abrir detalhes: ${err.message}`); }
        });

        // --- Listener Salvar Modal Item ---
        saveItemButtonModal.addEventListener('click', () => {
            console.log("[Salvar Modal Item]"); const nfIndex = parseInt(saveItemButtonModal.dataset.nfIndex, 10); const itemIndex = parseInt(saveItemButtonModal.dataset.itemIndex, 10);
            if (isNaN(nfIndex) || isNaN(itemIndex) || !processedNFData[nfIndex]?.items?.[itemIndex]) { console.error("Ref. inválida."); alert("Erro salvar."); return; }
            const itemData = processedNFData[nfIndex].items[itemIndex]; const idPrefix = `modal-item-${nfIndex}-${itemIndex}-`;
            const modalContent = itemDetailsModalElement.querySelector('.modal-body .item-details-form-container'); if (!modalContent) { console.error("Corpo modal?"); return; }
            console.log(`[Salvar Modal Item] Atualizando NF ${nfIndex}, Item ${itemIndex}`);
            try {
                 const getModalValue = (fieldIdSuffix) => modalContent.querySelector(`#${idPrefix}${fieldIdSuffix}`)?.value?.trim() ?? null;
                 const getModalRadioValue = (radioNameSuffix) => modalContent.querySelector(`input[name="${idPrefix}${radioNameSuffix}"]:checked`)?.value ?? "";
                 const getHiddenListValue = (sectionIdSuffix, listClassSuffix) => (modalContent.querySelector(`#${idPrefix}${sectionIdSuffix} .${listClassSuffix}`)?.value || '').split(',').filter(Boolean);
                 // --- Atualiza o objeto itemData ---
                itemData.ncm = getModalValue('ncm'); itemData.descricaoNcm = getModalValue('descricao_ncm'); itemData.atributosNcm = getModalValue('atributos_ncm'); itemData.infAdProd = getModalValue('descricao_complementar'); itemData.descricaoDetalhadaDue = getModalValue('descricao_detalhada_due'); itemData.unidadeEstatistica = getModalValue('unidade_estatistica'); itemData.quantidadeEstatistica = getModalValue('quantidade_estatistica'); itemData.pesoLiquidoItem = getModalValue('peso_liquido'); itemData.condicaoVenda = getModalValue('condicao_venda'); itemData.vmle = getModalValue('vmle'); itemData.vmcv = getModalValue('vmcv'); itemData.paisDestino = getModalValue('pais_destino');
                for (let i = 1; i <= 4; i++) itemData[`enquadramento${i}`] = getModalValue(`enquadramento${i}`);
                itemData.lpcos = getHiddenListValue('lpco-section', 'lpcos-hidden'); itemData.nfsRefEletronicas = getHiddenListValue('nfe-ref-section', 'nfsRefEletronicas-hidden'); itemData.nfsRefFormulario = getHiddenListValue('nf_form-ref-section', 'nfsRefFormulario-hidden'); itemData.nfsComplementares = getHiddenListValue('nfc-ref-section', 'nfsComplementares-hidden'); itemData.ccptCcrom = getModalRadioValue('ccpt_ccrom');

                console.log("[Salvar Modal Item] OK:", JSON.parse(JSON.stringify(itemData))); alert("Dados do item atualizados."); if (itemDetailsModalInstance) itemDetailsModalInstance.hide();
                 // Opcional: re-renderizar tabela se necessário
                 // renderNotasFiscaisTable();
            } catch (saveErr) { console.error("Erro salvar item:", saveErr); alert(`Erro ao salvar: ${saveErr.message}.`); }
        });

        // --- Listener Botão Lote ---
        batchEditButton.addEventListener('click', () => {
            console.log("[Batch Edit] Botão lote clicado.");
             const totalItems = processedNFData.reduce((sum, entry) => sum + entry.items.length, 0);
             if (totalItems === 0) { alert("Não há itens carregados para preenchimento em lote."); console.warn("[Batch Edit] Sem itens."); return; }
            if (batchEditModalInstance) { console.log("[Batch Edit] Abrindo modal lote."); batchEditModalInstance.show(); }
            else { console.error("[Batch Edit] Instância modal lote não disponível."); alert("Erro ao abrir janela lote."); }
        });

        // --- Listener Salvar Modal Lote ---
        saveBatchButton.addEventListener('click', () => {
            console.log("[Batch Edit Save] Botão Aplicar lote clicado.");
             if (!processedNFData || processedNFData.length === 0 || processedNFData.every(nf => !nf.items || nf.items.length === 0)) { console.warn("[Batch Edit Save] Sem dados válidos."); alert("Não há itens válidos."); if (batchEditModalInstance) batchEditModalInstance.hide(); return; }
            const batchForm = document.getElementById('batchEditForm'); if (!batchForm) { console.error("[Batch Edit Save] Form lote não encontrado."); alert("Erro interno salvar lote."); return; }
            // --- Obter valores modal lote ---
            const batchIncoterm = batchForm.querySelector('#batchIncotermSelect')?.value; const batchPaisDestino = batchForm.querySelector('#batchPaisDestinoInput')?.value; const batchEnq = {}; for (let i = 1; i <= 4; i++) batchEnq[`enq${i}`] = batchForm.querySelector(`#batchEnquadramento${i}Select`)?.value; const batchCcptCcromRadio = batchForm.querySelector('input[name="batchCcptCcrom"]:checked'); const batchCcptCcrom = batchCcptCcromRadio ? batchCcptCcromRadio.value : null;
            console.log("[Batch Edit Save] Valores lote:", { incoterm: batchIncoterm, pais: batchPaisDestino, enq1: batchEnq.enq1, ccptCcrom: batchCcptCcrom });
            let itemsUpdatedCount = 0; console.log("[Batch Edit Save] Iniciando iteração...");
            // --- Iterar e atualizar ---
            processedNFData.forEach((nfEntry) => {
                if (nfEntry.items && nfEntry.items.length > 0) {
                    nfEntry.items.forEach((item) => { let itemChanged = false;
                        if (batchIncoterm !== "" && batchIncoterm !== null && item.condicaoVenda !== batchIncoterm) { item.condicaoVenda = batchIncoterm; itemChanged = true; }
                        if (batchPaisDestino !== "" && batchPaisDestino !== null && item.paisDestino !== batchPaisDestino) { item.paisDestino = batchPaisDestino; itemChanged = true; }
                        for (let i = 1; i <= 4; i++) { const batchValue = batchEnq[`enq${i}`]; const itemKey = `enquadramento${i}`; if (batchValue !== "" && batchValue !== null && item[itemKey] !== batchValue) { item[itemKey] = batchValue; itemChanged = true; } }
                        if (batchCcptCcrom !== "" && batchCcptCcrom !== null && item.ccptCcrom !== ((batchCcptCcrom === 'NA') ? '' : batchCcptCcrom)) { item.ccptCcrom = (batchCcptCcrom === 'NA') ? '' : batchCcptCcrom; itemChanged = true; }
                        if (itemChanged) itemsUpdatedCount++;
                    }); } });
            console.log(`[Batch Edit Save] FIM. ${itemsUpdatedCount} item(ns) atualizados.`); alert(`${itemsUpdatedCount} item(ns) atualizados em lote.`); if (batchEditModalInstance) batchEditModalInstance.hide();
             // Opcional: re-renderizar tabela
             // renderNotasFiscaisTable();
        });

         // --- Listener Abas ---
         const tabLinks = document.querySelectorAll('#dueTabs .nav-link');
         if (tabLinks.length > 0 && typeof bootstrap !== 'undefined' && bootstrap.Tab) {
             console.log("Configurando listeners abas.");
             tabLinks.forEach(link => { link.addEventListener('click', (e) => { e.preventDefault(); try { new bootstrap.Tab(link).show(); console.log(`[Tabs] Aba '${link.getAttribute('href')}' ativada.`); } catch (tabErr) { console.error(`Erro ativar aba '${link.getAttribute('href')}':`, tabErr); } }); });
             console.log("Listeners abas OK.");
         } else { console.warn("Navegação por abas não configurada."); }

        // --- Listener Botão Gerar DUE ---
        gerarDueButton.addEventListener('click', () => {
            console.log("[Gerar DUE] Clicado."); alert("Funcionalidade 'Gerar DU-E' não implementada.");
            // Aqui viria a lógica de coletar dados, validar, enviar ao backend, etc.
        });

        console.log("Script principal: Listeners configurados. Aplicação pronta.");

    }); // --- FIM DOMContentLoaded ---

</script>

