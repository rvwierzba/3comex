<?php
require_once(dirname(__DIR__) . '../../conexao.php'); // Inclui o arquivo de conexão com o banco de dados

if (isset($_POST['query'])) {
    $query = $_POST['query'];
    $query = preg_replace("/[^a-zA-Z0-9\s]/", "", $query); // Remove caracteres especiais

    // Prepara a consulta SQL usando PDO
    $sql = "SELECT Codigo, Descricao FROM ncm WHERE Codigo LIKE :query OR Descricao LIKE :query LIMIT 10";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['query' => "%$query%"]);

    // Verifica se há resultados
    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $codigo_limpo = str_replace('.', '', $row['Codigo']); // Remove os pontos do código
            echo "<a href='#' class='list-group-item list-group-item-action' data-codigo='$codigo_limpo' data-descricao='{$row['Descricao']}'>".$row['Codigo']." - ".$row['Descricao']."</a>";
        }
    } else {
        echo "<p class='list-group-item list-group-item-action'>Nenhum resultado encontrado</p>";
    }
}
?>
