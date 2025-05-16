<?php
    include_once 'C:\\xampp\\htdocs\\3comex\\conexao.php'; // Caminho absoluto!


    $searchTerm = $_GET['search'] ?? '';
    $datalistId = $_GET['datalist'] ?? '';

    $searchTerm = '%' . $searchTerm . '%'; // Prepara para a consulta LIKE

    $sql = '';  // Inicializa a query
    $options = []; // Inicializa o array de resultados


    switch ($datalistId) {
        case 'cnpj-cpf-list':
            $sql = 'SELECT DISTINCT CONCAT(CNPJ, " - ", Nome) AS text FROM importador WHERE CNPJ LIKE :searchTerm OR Nome LIKE :searchTerm';
            break;

        case 'moeda':
            $sql = 'SELECT CONCAT(Codigo, " - ", Nome) AS text FROM moeda WHERE Codigo LIKE :searchTerm OR Nome LIKE :searchTerm';
            break;

       
        // Adicione outros cases aqui, se você adicionar mais datalists no futuro.

        default:
            // É importante ter um caso default para lidar com IDs desconhecidos
            echo json_encode(['error' => 'ID de datalist desconhecido: ' . $datalistId]);
            exit;
    }

    // Só executa a consulta se um SQL válido foi definido
    if ($sql) {
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':searchTerm' => $searchTerm]);

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $options[] = ['text' => $row['text']];
            }

            echo json_encode($options); // Retorna o array como JSON

        } catch (PDOException $e) {
            echo json_encode(['error' => 'Erro na consulta: ' . $e->getMessage()]);
        }
    }

?>