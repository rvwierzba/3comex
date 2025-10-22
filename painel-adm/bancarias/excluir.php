<?php 
require_once("../../conexao.php");
// require_once("campos.php"); // Comente ou remova se não for necessário aqui
require_once("campos.php");

// 1. DEFINA O NOME CORRETO DA TABELA AQUI (Substitua 'sua_tabela_de_contas')
$nome_tabela = $pagina; 

// 2. Pegar o ID DE FORMA SEGURA
$id = isset($_POST['id-excluir']) ? $_POST['id-excluir'] : null;

if (!$id) {
    // Se o ID não veio, retorna erro (ajudará a debugar)
    http_response_code(400); // Bad Request
    echo "Erro: ID da conta não fornecido.";
    exit;
}

try {
    // 3. USAR PREPARED STATEMENT (Seguro)
    $sql = "DELETE FROM $nome_tabela WHERE id = :id";
    
    $stmt = $pdo->prepare($sql);
    
    // Liga o parâmetro de forma segura
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
    $stmt->execute();
    
    // 4. VERIFICAR SE ALGO FOI AFETADO (Se count > 0, a exclusão funcionou)
    if ($stmt->rowCount() > 0) {
        echo 'Excluído com Sucesso';
    } else {
        // Excluiu de fato, mas 0 linhas foram afetadas (o ID não existia)
        http_response_code(200); // Mantém o 200 OK, mas avisa que nada foi feito
        echo "Aviso: Nenhum registro encontrado com o ID: " . htmlspecialchars($id);
    }

} catch (PDOException $e) {
    // Se houver erro no SQL (ex: tabela não existe, erro de sintaxe SQL)
    http_response_code(500); // Internal Server Error
    echo "Erro no Banco de Dados: " . $e->getMessage();
}

?>