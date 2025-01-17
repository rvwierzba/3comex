<?php
// painel-adm/comum/pesquisar-pais.php

require_once(dirname(__DIR__) . '../../conexao.php'); // Ajuste o caminho conforme necessário

if (isset($_POST['query'])) {
    // Obter e sanitizar a consulta de forma segura
    $query = strtolower(trim($_POST['query']));

    try {
        // Prepara a consulta com proteção contra injeção SQL
        $stmt = $pdo->prepare("SELECT NOME FROM paises WHERE NOME LIKE ? OR SIGLA_ISO2 LIKE ? OR SIGLA_IS03 LIKE ? ORDER BY NOME LIMIT 10");
        $likeQuery = '%' . $query . '%';
        $stmt->execute([$likeQuery, $likeQuery, $likeQuery]);
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($resultados) {
            foreach ($resultados as $row) {
                echo '<a href="#" class="list-group-item list-group-item-action">' . 
                    htmlspecialchars($row['NOME'], ENT_QUOTES, 'UTF-8') . 
                    '</a>';
            }
        } else {
            echo '<p class="list-group-item">Nenhum resultado encontrado</p>';
        }
    } catch (PDOException $e) {
        echo '<p class="list-group-item">Erro no servidor</p>';
    }
}
?>
