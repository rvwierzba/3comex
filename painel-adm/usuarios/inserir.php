<?php
// painel-adm/usuarios/inserir.php
require_once("../../conexao.php");
require_once("campos.php"); // $pagina, $campo1, $campo2, etc. são definidos aqui

// 1. Obter dados do POST com tratamento correto para $id e sanitização/validação
$id_post = isset($_POST['id']) ? $_POST['id'] : '';
$id = ($id_post !== '' && $id_post !== null) ? $id_post : null; // $id será null se 'id' do POST for vazio ou não existir

// Usar as variáveis $campoX de campos.php para pegar os dados corretos do $_POST
$nome = isset($_POST[$campo1]) ? trim(htmlspecialchars($_POST[$campo1])) : '';         // ex: $_POST['nome']
$email_input = isset($_POST[$campo2]) ? trim($_POST[$campo2]) : '';                   // ex: $_POST['email']
$email = filter_var($email_input, FILTER_VALIDATE_EMAIL); // Valida o email aqui
$senha_fornecida = isset($_POST[$campo3]) ? $_POST[$campo3] : '';                    // ex: $_POST['senha']
$nivel = isset($_POST[$campo4]) ? trim(htmlspecialchars($_POST[$campo4])) : '';       // ex: $_POST['nivel']

// 2. Validações básicas melhoradas
if (empty($nome)) {
    echo 'O campo nome é obrigatório!';
    exit();
}
if ($email === false) { // Agora $email é o resultado de filter_var
    echo 'Formato de e-mail inválido!';
    exit();
}
// Para um novo usuário ($id é null), a senha é obrigatória
if ($id === null && empty($senha_fornecida)) {
    echo 'A senha é obrigatória para novos registros!';
    exit();
}
if (empty($nivel)) {
    echo 'O campo nível é obrigatório!';
    exit();
}

try {
    // 3. Configurar PDO para lançar exceções (idealmente feito em conexao.php, mas bom garantir)
    if (isset($pdo)) {
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } else {
        echo "ERRO CRÍTICO: Conexão PDO (\$pdo) não está disponível! Verifique conexao.php.";
        exit();
    }

    // 4. VALIDAR EMAIL DUPLICADO
    $query_check_email = $pdo->prepare("SELECT id FROM `$pagina` WHERE `email` = :email_check"); // Usar crases para nomes de tabela/coluna
    $query_check_email->bindParam(':email_check', $email); // Usa o email validado
    $query_check_email->execute();
    $res_email = $query_check_email->fetch(PDO::FETCH_ASSOC);

    if ($res_email) {
        // Se encontrou um email, verifica se é de um registro diferente do que estamos editando
        if ($id === null || ($id !== null && $res_email['id'] != $id)) {
            echo 'Este e-mail já está cadastrado para outro usuário!';
            exit();
        }
    }

    // 5. Lógica de INSERT ou UPDATE
    if ($id === null) { // Novo registro (INSERT)
        // A senha já foi verificada como não vazia acima para $id === null
        $senha_hash = password_hash($senha_fornecida, PASSWORD_DEFAULT);

        // Usar crases para nomes de tabelas e colunas é uma boa prática
        $sql = "INSERT INTO `$pagina` (`nome`, `email`, `senha`, `nivel`) VALUES (:nome_val, :email_val, :senha_val, :nivel_val)";
        $query_exec = $pdo->prepare($sql);
        $query_exec->bindParam(':senha_val', $senha_hash);

    } else { // Atualizar registro (UPDATE)
        if (!empty($senha_fornecida)) {
            $senha_hash = password_hash($senha_fornecida, PASSWORD_DEFAULT);
            $sql = "UPDATE `$pagina` SET `nome` = :nome_val, `email` = :email_val, `senha` = :senha_val, `nivel` = :nivel_val WHERE `id` = :id_val";
            $query_exec = $pdo->prepare($sql);
            $query_exec->bindParam(':senha_val', $senha_hash);
        } else { // Não atualizar a senha se não foi fornecida
            $sql = "UPDATE `$pagina` SET `nome` = :nome_val, `email` = :email_val, `nivel` = :nivel_val WHERE `id` = :id_val";
            $query_exec = $pdo->prepare($sql);
        }
        $query_exec->bindParam(':id_val', $id, PDO::PARAM_INT); // Especificar o tipo para ID é bom
    }

    // Bind dos parâmetros comuns a INSERT e UPDATE
    $query_exec->bindParam(':nome_val', $nome);
    $query_exec->bindParam(':email_val', $email); // Usa o email já validado
    $query_exec->bindParam(':nivel_val', $nivel);

    if ($query_exec->execute()) {
        // Para INSERT, verificar rowCount para ter mais certeza.
        // Para UPDATE, rowCount pode ser 0 se os dados forem os mesmos, mas a operação foi um sucesso.
        if ($id === null) { // Se foi um INSERT
            if ($query_exec->rowCount() > 0) {
                echo 'Salvo com Sucesso';
            } else {
                echo 'Salvo com Sucesso (Alerta: INSERT não afetou linhas!)'; // Investigar se isso acontecer
            }
        } else { // Se foi um UPDATE
             echo 'Salvo com Sucesso';
        }
    } else {
        // Esta parte é menos provável de ser alcançada se exceções PDO estiverem ativas,
        // pois um erro de execução lançaria uma PDOException.
        echo 'Ocorreu um erro ao salvar os dados (execute() retornou false).';
        // $errorInfo = $query_exec->errorInfo(); // Para depurar se necessário
        // echo " Erro DB: " . (isset($errorInfo[2]) ? $errorInfo[2] : 'N/A');
    }

} catch (PDOException $e) {
    // Logar o erro real em um arquivo de log em ambiente de produção
    error_log("Erro no banco de dados (inserir.php): " . $e->getMessage() . " na query: " . (isset($sql) ? $sql : "N/A"));
    // Para depuração, mostrar a mensagem de erro. Em produção, uma mensagem genérica.
    echo "Erro no processamento do BD: " . $e->getMessage();
    // echo "Ocorreu um erro no processamento. Por favor, tente novamente mais tarde."; // Mensagem para produção
    exit();
}
?>