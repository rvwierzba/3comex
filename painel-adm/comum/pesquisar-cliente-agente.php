<?php
require_once(dirname(__DIR__) . '../../conexao.php');

if (isset($_POST['query'])) {
    // Converter a consulta para minúsculas
    $query = strtolower($_POST['query']);

    // Pesquisar clientes com comparação case-insensitive
    $stmtClientes = $pdo->prepare("SELECT nome, cnpj FROM clientes WHERE LOWER(nome) LIKE ? OR cnpj LIKE ?");
    $stmtClientes->execute(["%$query%", "%$query%"]);
    $clientes = $stmtClientes->fetchAll(PDO::FETCH_ASSOC);

    // Pesquisar agentes com comparação case-insensitive
    $stmtAgentes = $pdo->prepare("SELECT nome, cnpj FROM agentes WHERE LOWER(nome) LIKE ? OR cnpj LIKE ?");
    $stmtAgentes->execute(["%$query%", "%$query%"]);
    $agentes = $stmtAgentes->fetchAll(PDO::FETCH_ASSOC);

    // Combinar resultados
    $results = array_merge($clientes, $agentes);

    // Exibir resultados
    if (count($results) > 0) {
        foreach ($results as $row) {
            // Remover a formatação do CNPJ/CPF
            $cnpjCpfSemFormatacao = preg_replace('/[^0-9]/', '', $row['cnpj']);
            echo "<a href='#' class='list-group-item list-group-item-action' data-cnpj='{$cnpjCpfSemFormatacao}' data-nome='{$row['nome']}'>{$row['cnpj']} - {$row['nome']}</a>";
        }
    } else {
        echo '<p class="list-group-item">Nenhum resultado encontrado</p>';
    }
}
?>
