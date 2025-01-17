<?php
require_once(dirname(__DIR__) . '../../conexao.php');

if (isset($_POST['query'])) {
    // Obter e sanitizar a consulta de forma segura
    $query = strtolower(trim($_POST['query']));
    
    // Receber regiao_fiscal se fornecido
    $regiao_fiscal = isset($_POST['regiao']) ? trim($_POST['regiao']) : null;

    // Definir o mapeamento entre regiao_fiscal e nomes de recintos aduaneiros
    // Este mapeamento é baseado nas informações fornecidas sobre URF de Despacho
    // Substitua os nomes conforme os registros reais da sua tabela recinto_aduaneiro

    $regiao_recintos = [
        'PELOTAS' => ['PELOTAS'],
        'PASSO FUNDO' => ['PASSO FUNDO'],
        'SANTA MARIA' => ['SANTA MARIA'],
        'PORTO DE RIO GRANDE' => ['PORTO DE RIO GRANDE'],
        'CAXIAS DO SUL' => ['CAXIAS DO SUL'],
        'NOVO HAMBURGO' => ['NOVO HAMBURGO'],
        'SANTANA DO LIVRAMENTO' => ['SANTANA DO LIVRAMENTO'],
        'QUARAI' => ['QUARAI', 'BARRA DO QUARAÍ'],
        'PORTO MAUA' => ['PORTO MAUA', 'PORTO XAVIER'],
        'SAO BORJA' => ['SAO BORJA'],
        'ITAQUI' => ['ITAQUI'],
        'PORTO ALEGRE' => ['PORTO ALEGRE', 'AEROPORTO SALGADO FILHO - PORTO ALEGRE'],
        'MACAPÁ' => ['MACAPÁ'],
        'PORTO VELHO' => ['PORTO VELHO'],
        'BOA VISTA' => ['BOA VISTA'],
        'PORTO DE BELEM' => ['PORTO DE BELEM', 'AEROPORTO INTERNACIONAL DE BELEM'],
        'PORTO DE MANAUS' => ['PORTO DE MANAUS', 'AEROPORTO EDUARDO GOMES'],
        'PORTO DE PECEM' => ['PORTO DE PECEM'],
        'PORTO DE FORTALEZA' => ['PORTO DE FORTALEZA', 'AEROPORTO INTERNACIONAL PINTO MARTINS'],
        'PORTO DE SAO LUIS' => ['PORTO DE SAO LUIS'],
        'PORTO DE SAO PAULO' => ['AEROPORTO INTERNACIONAL DE SAO PAULO/GUARULHOS', 'AEROPORTO INTERNACIONAL DE VIRACOPOS', 'PORTO DE SANTOS'],
        'ITAJAI' => ['ITAJAI'],
        'PORTO DE PARANAGUA' => ['PORTO DE PARANAGUA'],
        'LONDRINA' => ['LONDRINA'],
        'PONTA GROSSA' => ['PONTA GROSSA'],
        'MARINGA' => ['MARINGA'],
        'JOINVILLE' => ['JOINVILLE'],
        'FLORIANOPOLIS' => ['FLORIANOPOLIS'],
        'CURITIBA' => ['CURITIBA'],
        // Continue adicionando conforme necessário
    ];

    // Inicializar a consulta SQL
    if ($regiao_fiscal && isset($regiao_recintos[$regiao_fiscal])) {
        $recintos_nomes = $regiao_recintos[$regiao_fiscal];
        // Preparar placeholders para a cláusula IN
        $placeholders = implode(',', array_fill(0, count($recintos_nomes), '?'));

        if (is_numeric($query)) {
            // Pesquisa por código com filtro de recintos
            $sql = "SELECT nome, codigo FROM recinto_aduaneiro WHERE codigo LIKE ? AND nome IN ($placeholders) ORDER BY nome";
            $stmt = $pdo->prepare($sql);
            $params = array_merge(['%' . $query . '%'], $recintos_nomes);
        } else {
            // Pesquisa por nome com filtro de recintos
            $sql = "SELECT nome, codigo FROM recinto_aduaneiro WHERE LOWER(nome) LIKE ? AND nome IN ($placeholders) ORDER BY nome";
            $stmt = $pdo->prepare($sql);
            $params = array_merge(['%' . $query . '%'], $recintos_nomes);
        }
        $stmt->execute($params);
    } else {
        // Pesquisa sem filtro de recintos
        if (is_numeric($query)) {
            $stmt = $pdo->prepare("SELECT nome, codigo FROM recinto_aduaneiro WHERE codigo LIKE ? ORDER BY nome");
            $stmt->execute(['%' . $query . '%']);
        } else {
            $stmt = $pdo->prepare("SELECT nome, codigo FROM recinto_aduaneiro WHERE LOWER(nome) LIKE ? ORDER BY nome");
            $stmt->execute(['%' . $query . '%']);
        }
    }

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($results) > 0) {
        foreach ($results as $row) {
            echo '<a href="#" class="list-group-item list-group-item-action">' . 
                htmlspecialchars($row['codigo'], ENT_QUOTES, 'UTF-8') . 
                ' - ' . 
                htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8') . 
                '</a>';
        }
    } else {
        echo '<p class="list-group-item">Nenhum resultado encontrado</p>';
    }
}
?>
