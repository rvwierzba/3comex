<?php
    require_once(dirname(__DIR__) . "../../siscomex/handshake.php");
    require_once(dirname(__DIR__) . '../../conexao.php');

    // Configuração da URL
    $endpoint = '/api/ext/tabela';
    $nomeTabela = 'RECINTO_ADUANEIRO';
    $fullURL =  $baseURL . '/' . 'tabx' . '/' . $endpoint . '/' . $nomeTabela;

    // Tokens do HANDSHAKE: $setToken & $csrfToken no handshake.php
    $requestHeaders = array(
        "Authorization: $setToken",
        "X-Csrf-Token: $csrfToken"
    );

    // Inicializar cURL
    $ch = curl_init();

    // Configurar a requisição
    curl_setopt($ch, CURLOPT_URL, $fullURL);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");

    // Executar a solicitação
    $response = curl_exec($ch);

    // Decodifica o JSON para um array associativo
    $responseCleaned = trim($response);
    $responseArray = json_decode($responseCleaned, true);

    // Verificar erros
    if ($response === false) {
        echo "Erro na requisição cURL: " . curl_error($ch);
    } elseif (!is_array($responseArray) || !isset($responseArray['dados'])) {
        echo "Erro na resposta ou a resposta não é um array válido.";
    } else {
        // Sucesso na obtenção da resposta
        
        // Iterar sobre os itens na resposta
        foreach ($responseArray['dados'] as $item) {
            // Preparando as variáveis para evitar erros de variáveis indefinidas
            $codigo = $nome = $descricao =  $inicioVigencia = $fimVigencia =  '';

            // Iterar sobre os campos de cada item
            foreach ($item['campos'] as $campo) {
                switch ($campo['nome']) {
                    case 'CODIGO':
                        $codigo = $campo['valor'];
                        break;
                    case 'NOME': // Corrigindo a variável para $nome
                        $nome = $campo['valor'];
                        break;
                    case 'DATA_INICIO':
                        $inicioVigencia = $campo['valor'];
                        break;
                    case 'DATA_FIM':
                        $fimVigencia = $campo['valor'];
                        break;
                }
            }
            
            // Verifico se o registro a ser atualizado já existe através do CODIGO
            $querySelect = $pdo->query("SELECT codigo from recinto_aduaneiro WHERE codigo = '$codigo'");
            $res = $querySelect->fetchAll(PDO::FETCH_ASSOC);
            $total_reg = @count($res);
            
            if ($total_reg === 0) { // Se registro não existir, inserir novo registro
                $query = $pdo->prepare("INSERT INTO recinto_aduaneiro (codigo, nome, data_inicio, data_fim) 
                                        VALUES (:codigo, :nome, :dataInicio, :dataFim)");

                $query->bindValue(":codigo", $codigo);
                $query->bindValue(":nome", $nome); // Inserir o nome correto agora
                $query->bindValue(":dataInicio", $inicioVigencia);
                $query->bindValue(":dataFim", $fimVigencia);
                $query->execute();
            } else {
                // Aqui pode-se atualizar o registro caso já exista
                // $query = $pdo->prepare("UPDATE recinto_aduaneiro SET nome = :nome, ... WHERE codigo = :codigo");
            }
        }
    }

    curl_close($ch);

    // Mensagem de sucesso
    echo "Atualização da Tabela realizada com sucesso!";
    echo "<br>";
    echo "<a class='btn btn-primary' href='index.php?pag=recinto_aduaneiro'>Retornar a Lista de Recintos Aduaneiros</a>";
?>