<?php
    require_once(dirname(__DIR__) . '../../conexao.php');


    if (isset($_POST['query'])) {
        $query = $_POST['query'];

        $stmt = $pdo->prepare("SELECT documento FROM documentos WHERE documento LIKE ?");
        $stmt->execute(["%$query%"]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($results as $row) {
            echo "<a href='#' class='list-group-item list-group-item-action'>{$row['documento']}</a>";
        }
}
?>