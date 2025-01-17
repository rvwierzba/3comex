<?php
require_once(dirname(__DIR__) . "../../conexao.php");

if (isset($_POST['query']) && isset($_POST['tipoPesquisa'])) {
    $query = $_POST['query'];
    $tipo = $_POST['tipoPesquisa'];

    // Converte o tipo para plural
    $tipoTabela = $tipo === 'agente' ? 'agentes' : 'clientes';

    try {
        // Consulta pelo CNPJ ou nome no banco de dados
        $sql = "SELECT nome, cnpj, endereco FROM $tipoTabela WHERE cnpj LIKE :query OR nome LIKE :query";
        $stmt = $pdo->prepare($sql);
        $likeQuery = "%" . $query . "%";
        $stmt->bindParam(':query', $likeQuery, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $output = '<div class="list-group">';
        if ($stmt->rowCount() > 0) {
            foreach ($result as $row) {
                $output .= '<a href="#" class="list-group-item list-group-item-action" data-cnpj="' . htmlspecialchars($row['cnpj']) . '" data-endereco="' . htmlspecialchars($row['endereco']) . '">' . htmlspecialchars($row['nome']) . ' (' . htmlspecialchars($row['cnpj']) . ')</a>';
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
