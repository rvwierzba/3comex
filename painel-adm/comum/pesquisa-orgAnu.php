<?php

  include_once(dirname(__DIR__) . '../../conexao.php');

  if (isset($_POST['query'])) {
    $query = $_POST['query'];

    try {
        // Consulta pelo código ou descrição no banco de dados
        $sql = "SELECT codigo, descricao FROM org_anuente WHERE codigo LIKE :query OR descricao LIKE :query";
        $stmt = $pdo->prepare($sql);
        $likeQuery = "%" . $query . "%";
        $stmt->bindParam(':query', $likeQuery, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $output = '<div class="list-group">';
        if ($stmt->rowCount() > 0) {
            foreach ($result as $row) {
                $output .= '<a href="#" class="list-group-item list-group-item-action" data-codigo="' . htmlspecialchars($row['codigo']) . '">' . htmlspecialchars($row['codigo']) . ' - ' . htmlspecialchars($row['descricao']) . '</a>';
            }
        } else {
            $output .= '<a href="#" class="list-group-item list-group-item-action disabled">Nenhum resultado encontrado</a>';
        }
        $output .= '</div>';

        echo $output;
    } catch (PDOException $e) {
        echo '<div class="list-group"><a href="#" class="list-group-item list-group-item-action disabled">Erro na consulta: ' . htmlspecialchars($e->getMessage()) . '</a></div>';
    }
}

?>
