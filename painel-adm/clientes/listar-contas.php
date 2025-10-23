<?php 
// Inclui a conexão e verifica a sessão (mantido conforme seu código original)
require_once("../../conexao.php");
require_once("../verificar.php");

// 2. Verifica se o documento (doc) foi enviado via POST
if (!isset($_POST['doc']) || empty($_POST['doc'])) {
    // Retorna uma linha de erro se o documento não for fornecido
    echo '<tr><td colspan="7" class="text-center text-danger">Documento do cliente é obrigatório para a consulta.</td></tr>';
    exit();
}

// 3. Captura e limpa o documento (Para buscar na tabela bancarias)
$doc_cliente = preg_replace('/[^0-9]/', '', $_POST['doc']); 

// Se a limpeza não resultou em nada (ex: passou só pontuação)
if (empty($doc_cliente)) {
    echo '<tr><td colspan="7" class="text-center text-danger">Documento fornecido é inválido após a limpeza.</td></tr>';
    exit();
}

// 4. Prepara a consulta SQL
// A busca é feita limpando os pontos e traços do campo 'doc' na tabela bancarias
$query = "SELECT * FROM bancarias WHERE REPLACE(REPLACE(doc, '.', ''), '-', '') = :doc_cliente";

$stmt = $pdo->prepare($query);
$stmt->bindValue(':doc_cliente', $doc_cliente);
$stmt->execute();

// 5. Verifica se encontrou registros
$contas = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($contas)) {
    // *** CORREÇÃO CRÍTICA: RETORNA A MENSAGEM INFORMATIVA E PARA ***
    echo '<tr><td colspan="7" class="text-center text-info">Nenhuma conta bancária cadastrada para este documento.</td></tr>';
    exit(); // O SCRIPT PARA AQUI SE NÃO HOUVER CONTAS
}

// 6. Itera sobre os resultados e gera o HTML das linhas (SÓ EXECUTA SE HOUVER CONTAS)
foreach ($contas as $conta) {

        $id = htmlspecialchars(addslashes($conta['id']));
        $banco = htmlspecialchars(addslashes($conta['banco']));
        $agencia = htmlspecialchars(addslashes($conta['agencia']));
        $conta_numero = htmlspecialchars(addslashes($conta['conta'])); // Mapeia para ContaNumero no JS
        $tipo = htmlspecialchars(addslashes($conta['tipo']));
        $pessoa = htmlspecialchars(addslashes($conta['pessoa']));
        $doc_conta = htmlspecialchars(addslashes($conta['doc'])); // Mapeia para DocConta no JS
        
        // Para a exclusão, usamos o nome do banco no modal
        $banco_nome_exclusao = htmlspecialchars(addslashes($conta['banco']));

        // Formata o código PHP como string para ser injetado no HTML
       $btn_editar = "editarConta(event, '{$id}', '{$banco}', '{$agencia}', '{$conta_numero}', '{$tipo}', '{$pessoa}', '{$doc_conta}')";
       $btn_excluir = "excluirConta(event, '{$id}', '{$banco_nome_exclusao}')";

    ?>
    <tr>
        <td><?php echo htmlspecialchars($conta['banco']); ?></td>
        <td><?php echo htmlspecialchars($conta['agencia']); ?></td>
        <td><?php echo htmlspecialchars($conta['conta']); ?></td>
        <td><?php echo htmlspecialchars($conta['tipo']); ?></td> 
        <td><?php echo htmlspecialchars($conta['pessoa']); ?></td>
        <td><?php echo htmlspecialchars($conta['doc']); ?></td>
       <td class="d-flex gap-1">
            <button type="button" class="btn btn-sm btn-primary" onclick="<?php echo $btn_editar; ?>">✎</button>
            <button type="button" class="btn btn-sm btn-danger" onclick="<?php echo $btn_excluir; ?>">🗑</button>
       </td>
    </tr>
    <?php
}
?>