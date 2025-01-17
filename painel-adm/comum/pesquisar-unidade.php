<?php
require_once(dirname(__DIR__) . '../../conexao.php');

if (isset($_POST['query'])) {
    // Obter e sanitizar a consulta de forma segura
    $query = strtolower(trim($_POST['query']));

    // Pesquisar unidades com comparação case-insensitive
    $stmt = $pdo->prepare("SELECT nome, codigo, regiao_fiscal FROM unidades_rfb WHERE LOWER(nome) LIKE ? OR codigo LIKE ? ORDER BY nome");
    $stmt->execute(["%$query%", "%$query%"]);
    $unidades = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($unidades) > 0) {
        foreach ($unidades as $row) {
            echo "<a href='#' class='list-group-item list-group-item-action' data-regiao='" . htmlspecialchars($row['regiao_fiscal'], ENT_QUOTES, 'UTF-8') . "' data-nome='" . htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8') . "'>" . htmlspecialchars($row['codigo'], ENT_QUOTES, 'UTF-8') . " - " . htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8') . "</a>";
        }
    } else {
        echo '<p class="list-group-item">Nenhum resultado encontrado</p>';
    }
}





?>
