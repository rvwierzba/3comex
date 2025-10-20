<?php 
// 1. Inclui o arquivo de conexão PDO
require_once("../../conexao.php");
// Pode ser útil incluir verificação de login/sessão aqui
// require_once("../../verificar.php"); 

// 2. Verifica se o documento (doc) foi enviado via POST
if (!isset($_POST['doc']) || empty($_POST['doc'])) {
    // Retorna uma linha de erro se o documento não for fornecido
    echo '<tr><td colspan="7" class="text-center text-danger">Documento do cliente é obrigatório para a consulta.</td></tr>';
    exit();
}

// 3. Captura e limpa o documento
// A função de limpeza depende de como você armazena no banco, 
// mas é bom remover caracteres não numéricos.
$doc_cliente = preg_replace('/[^0-9]/', '', $_POST['doc']); 

// 4. Prepara a consulta SQL
// Usamos prepared statements para segurança (evitar SQL Injection)
$query = "SELECT * FROM bancarias WHERE doc = :doc_cliente";
$stmt = $pdo->prepare($query);
$stmt->bindValue(':doc_cliente', $doc_cliente);
$stmt->execute();

// 5. Verifica se encontrou registros
$contas = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($contas)) {
    echo '<tr><td colspan="7" class="text-center text-info">Nenhuma conta bancária cadastrada para este documento.</td></tr>';
    exit();
}

// 6. Itera sobre os resultados e gera o HTML das linhas
foreach ($contas as $conta) {
    ?>
    <tr>
        <td><?php echo htmlspecialchars($conta['banco']); ?></td>
        <td><?php echo htmlspecialchars($conta['agencia']); ?></td>
        <td><?php echo htmlspecialchars($conta['conta']); ?></td>
        <td><?php echo htmlspecialchars($conta['tipo']); ?></td> 
        <td><?php echo htmlspecialchars($conta['pessoa']); ?></td>
        <td><?php echo htmlspecialchars($conta['doc']); ?></td>
        <td>
            <button class="btn btn-sm btn-primary" onclick="editarConta(<?php echo $conta['id']; ?>)">Editar</button>
            <button class="btn btn-sm btn-danger" onclick="excluirConta(<?php echo $conta['id']; ?>)">Excluir</button>
        </td>
    </tr>
    <?php
}
?>