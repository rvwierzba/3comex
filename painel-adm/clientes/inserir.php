<?php
// Arquivo: clientes/salvar.php
// Processa o formulário de inserção e atualização de clientes.

// Certifique-se que a conexão com o banco de dados está disponível
require_once("../../conexao.php"); // Caminho a partir de clientes/salvar.php para conexao.php
require_once("campos.php");        // Caminho a partir de clientes/salvar.php para clientes/campos.php

// Defina o nome da tabela a partir de campos.php
// É crucial que $pagina esteja definida em campos.php e corresponda ao nome da tabela
if (!isset($pdo) || !isset($pagina)) {
    echo "Erro: Dependências (conexao.php ou campos.php) não carregadas corretamente.";
    error_log("[salvar.php] ERRO: \$pdo ou \$pagina não definidos.");
    exit();
}

header('Content-Type: text/plain; charset=utf-8'); // Resposta em texto plano

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Recebe os dados do formulário via POST
    // Use ?? '' para definir um valor padrão (string vazia) se a chave não existir
    // Isso ajuda a evitar warnings de "Undefined index" e lida com campos opcionais vazios
    $id           = $_POST['id']           ?? '';
    $NomeRes      = $_POST['NomeRes']      ?? '';
    $Nome         = $_POST['Nome']         ?? '';
    $CNPJ         = $_POST['CNPJ']         ?? '';
    $CPF          = $_POST['CPF']          ?? '';
    $Endereco     = $_POST['Endereco']     ?? '';
    $Complemento  = $_POST['Complemento']  ?? '';
    $Bairro       = $_POST['Bairro']       ?? '';
    $Cidade       = $_POST['Cidade']       ?? '';
    $Estado       = $_POST['Estado']       ?? '';
    $Cep          = $_POST['Cep']          ?? '';
    $Telefone     = $_POST['Telefone']     ?? '';
    $Celular      = $_POST['Celular']      ?? '';
    $InscMun      = $_POST['InscMun']      ?? '';
    $InscEst      = $_POST['InscEst']      ?? '';
    $Site         = $_POST['Site']         ?? '';
    $Email        = $_POST['Email']        ?? '';
    $Vendedor     = $_POST['Vendedor']     ?? ''; // Certifique-se que este campo existe no form se for obrigatório
    $ComVend      = $_POST['ComVend']      ?? ''; // Certifique-se que este campo existe no form
    $Ptax         = $_POST['Ptax']         ?? ''; // Certifique-se que este campo existe no form
    $Obs          = $_POST['Obs']          ?? '';
    $CustService  = $_POST['CustService']  ?? ''; // Certifique-se que este campo existe no form
    $EmailNfe     = $_POST['EmailNfe']     ?? ''; // Certifique-se que este campo existe no form
    $LocalRps     = $_POST['LocalRps']     ?? ''; // Certifique-se que este campo existe no form
    $Grupo        = $_POST['Grupo']        ?? ''; // Certifique-se que este campo existe no form
    $DiasVenc     = $_POST['DiasVenc']     ?? ''; // Certifique-se que este campo existe no form
    $VencRadar    = $_POST['VencRadar']    ?? ''; // Certifique-se que este campo existe no form
    $VencProcuracao = $_POST['VencProcuracao'] ?? ''; // Certifique-se que este campo existe no form
    $VencMercante = $_POST['VencMercante'] ?? ''; // Certifique-se que este campo existe no form
    $VencAnvisa   = $_POST['VencAnvisa']   ?? ''; // Certifique-se que este campo existe no form
    $IrDia        = $_POST['IrDia']        ?? ''; // Certifique-se que este campo existe no form
    $IN381        = $_POST['IN381']        ?? ''; // Certifique-se que este campo existe no form
    $Simples      = $_POST['Simples']      ?? ''; // Certifique-se que este campo existe no form
    $IOF          = $_POST['IOF']          ?? ''; // Certifique-se que este campo existe no form
    $ImpEsc       = $_POST['ImpEsc']       ?? ''; // Certifique-se que este campo existe no form
    $NumPad       = $_POST['NumPad']       ?? ''; // Certifique-se que este campo existe no form
    $SubsTrib     = $_POST['SubsTrib']     ?? ''; // Certifique-se que este campo existe no form
    $ISS          = $_POST['ISS']          ?? ''; // Certifique-se que este campo existe no form
    $Suframa      = $_POST['Suframa']      ?? ''; // Certifique-se que este campo existe no form
    $CodInt       = $_POST['CodInt']       ?? ''; // Certifique-se que este campo existe no form
    $CodContabil  = $_POST['CodContabil']  ?? ''; // Certifique-se que este campo existe no form
    // DataCad e UsuResp geralmente são preenchidos pelo sistema, não pelo formulário
    // $DataCad      = $_POST['DataCad']      ?? ''; // Removido - Data de Cadastro
    // $UsuResp      = $_POST['UsuResp']      ?? ''; // Removido - Usuário Responsável


    // --- Validações (Opcional, mas recomendado) ---
    // Exemplo: verificar se NomeRes ou Nome não estão vazios
    if (empty($NomeRes) && empty($Nome)) {
       echo "O campo Nome Reduzido ou Nome Completo deve ser preenchido!";
       exit(); // Para a execução se a validação falhar
    }
    // Adicione mais validações conforme necessário (formato de email, CNPJ/CPF válidos, etc.)


    // --- Preparar a Query ---
    if ($id == "") {
        // Query para INSERÇÃO (ID vazio)
        $query = $pdo->prepare("INSERT INTO `{$pagina}` (NomeRes, Nome, CNPJ, CPF, Endereco, Complemento, Bairro, Cidade, Estado, Cep, Telefone, Celular, InscMun, InscEst, Site, Email, Vendedor, ComVend, Ptax, Obs, CustService, EmailNfe, LocalRps, Grupo, DiasVenc, VencRadar, VencProcuracao, VencMercante, VencAnvisa, IrDia, IN381, Simples, IOF, ImpEsc, NumPad, SubsTrib, ISS, Suframa, CodInt, CodContabil, DataCad, UsuResp) VALUES (:NomeRes, :Nome, :CNPJ, :CPF, :Endereco, :Complemento, :Bairro, :Cidade, :Estado, :Cep, :Telefone, :Celular, :InscMun, :InscEst, :Site, :Email, :Vendedor, :ComVend, :Ptax, :Obs, :CustService, :EmailNfe, :LocalRps, :Grupo, :DiasVenc, :VencRadar, :VencProcuracao, :VencMercante, :VencAnvisa, :IrDia, :IN381, :Simples, :IOF, :ImpEsc, :NumPad, :SubsTrib, :ISS, :Suframa, :CodInt, :CodContabil, CURDATE(), :UsuResp)");

        // Assumindo que você tem a variável de sessão para o usuário logado
        // Substitua $_SESSION['id_usuario'] pelo nome correto da sua variável de sessão
        // OU se UsuResp for um campo que você digita, use $_POST['UsuResp']
        $usuario_logado = $_SESSION['nome_usuario'] ?? 'Sistema'; // Exemplo: use o nome do usuário logado ou 'Sistema'

    } else {
        // Query para ATUALIZAÇÃO (ID não vazio)
        $query = $pdo->prepare("UPDATE `{$pagina}` SET NomeRes = :NomeRes, Nome = :Nome, CNPJ = :CNPJ, CPF = :CPF, Endereco = :Endereco, Complemento = :Complemento, Bairro = :Bairro, Cidade = :Cidade, Estado = :Estado, Cep = :Cep, Telefone = :Telefone, Celular = :Celular, InscMun = :InscMun, InscEst = :InscEst, Site = :Site, Email = :Email, Vendedor = :Vendedor, ComVend = :ComVend, Ptax = :Ptax, Obs = :Obs, CustService = :CustService, EmailNfe = :EmailNfe, LocalRps = :LocalRps, Grupo = :Grupo, DiasVenc = :DiasVenc, VencRadar = :VencRadar, VencProcuracao = :VencProcuracao, VencMercante = :VencMercante, VencAnvisa = :VencAnvisa, IrDia = :IrDia, IN381 = :IN381, Simples = :Simples, IOF = :IOF, ImpEsc = :ImpEsc, NumPad = :NumPad, SubsTrib = :SubsTrib, ISS = :ISS, Suframa = :Suframa, CodInt = :CodInt, CodContabil = :CodContabil WHERE Codigo = :id");

        // Para atualização, você precisa ligar o ID também
        $query->bindParam(':id', $id, PDO::PARAM_INT);
    }

    // --- Ligar os Parâmetros (bind parameters) ---
    // Use bindParam ou bindValue para segurança e performance
    // Ajuste o PDO::PARAM_STR ou PDO::PARAM_INT conforme o tipo de dado no seu BD
    $query->bindParam(':NomeRes', $NomeRes, PDO::PARAM_STR);
    $query->bindParam(':Nome', $Nome, PDO::PARAM_STR);
    $query->bindParam(':CNPJ', $CNPJ, PDO::PARAM_STR);
    $query->bindParam(':CPF', $CPF, PDO::PARAM_STR);
    $query->bindParam(':Endereco', $Endereco, PDO::PARAM_STR);
    $query->bindParam(':Complemento', $Complemento, PDO::PARAM_STR);
    $query->bindParam(':Bairro', $Bairro, PDO::PARAM_STR);
    $query->bindParam(':Cidade', $Cidade, PDO::PARAM_STR);
    $query->bindParam(':Estado', $Estado, PDO::PARAM_STR);
    $query->bindParam(':Cep', $Cep, PDO::PARAM_STR);
    $query->bindParam(':Telefone', $Telefone, PDO::PARAM_STR);
    $query->bindParam(':Celular', $Celular, PDO::PARAM_STR);
    $query->bindParam(':InscMun', $InscMun, PDO::PARAM_STR);
    $query->bindParam(':InscEst', $InscEst, PDO::PARAM_STR);
    $query->bindParam(':Site', $Site, PDO::PARAM_STR);
    $query->bindParam(':Email', $Email, PDO::PARAM_STR);
    $query->bindParam(':Vendedor', $Vendedor, PDO::PARAM_STR);
    $query->bindParam(':ComVend', $ComVend, PDO::PARAM_STR);
    $query->bindParam(':Ptax', $Ptax, PDO::PARAM_STR); // Ajuste o tipo se for numérico
    $query->bindParam(':Obs', $Obs, PDO::PARAM_STR);
    $query->bindParam(':CustService', $CustService, PDO::PARAM_STR);
    $query->bindParam(':EmailNfe', $EmailNfe, PDO::PARAM_STR);
    $query->bindParam(':LocalRps', $LocalRps, PDO::PARAM_STR);
    $query->bindParam(':Grupo', $Grupo, PDO::PARAM_STR);
    $query->bindParam(':DiasVenc', $DiasVenc, PDO::PARAM_INT); // Assumindo INT
    $query->bindParam(':VencRadar', $VencRadar, PDO::PARAM_STR); // Ajuste o tipo se for data ou outro
    $query->bindParam(':VencProcuracao', $VencProcuracao, PDO::PARAM_STR); // Ajuste o tipo
    $query->bindParam(':VencMercante', $VencMercante, PDO::PARAM_STR); // Ajuste o tipo
    $query->bindParam(':VencAnvisa', $VencAnvisa, PDO::PARAM_STR); // Ajuste o tipo
    $query->bindParam(':IrDia', $IrDia, PDO::PARAM_STR); // Ajuste o tipo
    $query->bindParam(':IN381', $IN381, PDO::PARAM_STR); // Ajuste o tipo
    $query->bindParam(':Simples', $Simples, PDO::PARAM_STR); // Ajuste o tipo
    $query->bindParam(':IOF', $IOF, PDO::PARAM_STR); // Ajuste o tipo
    $query->bindParam(':ImpEsc', $ImpEsc, PDO::PARAM_STR); // Ajuste o tipo
    $query->bindParam(':NumPad', $NumPad, PDO::PARAM_STR); // Ajuste o tipo
    $query->bindParam(':SubsTrib', $SubsTrib, PDO::PARAM_STR); // Ajuste o tipo
    $query->bindParam(':ISS', $ISS, PDO::PARAM_STR); // Ajuste o tipo
    $query->bindParam(':Suframa', $Suframa, PDO::PARAM_STR); // Ajuste o tipo
    $query->bindParam(':CodInt', $CodInt, PDO::PARAM_STR); // Ajuste o tipo
    $query->bindParam(':CodContabil', $CodContabil, PDO::PARAM_STR); // Ajuste o tipo

    // Ligue o parâmetro UsuResp apenas na inserção (ou se for atualizado no form)
    if ($id == "") {
       $query->bindParam(':UsuResp', $usuario_logado, PDO::PARAM_STR);
    }
    // Se UsuResp for atualizado via POST no form de edição, adicione bindParam para update também.
    // $query->bindParam(':UsuResp', $UsuResp, PDO::PARAM_STR); // Se vier do POST

    // --- Executar a Query ---
    $query->execute();

    // --- Sucesso ---
    echo "Salvo com Sucesso";

} catch (PDOException $e) {
    // --- Erro ---
    $error_message = "Erro ao salvar cliente: " . $e->getMessage();
    echo $error_message; // Retorna o erro para o AJAX
    error_log("[salvar.php] PDOException: " . $e->getMessage() . " - Query: " . $query_str_if_available ?? 'N/A' . " - POST: " . print_r($_POST, true));
} catch (Exception $e) {
    // --- Outros Erros ---
     $error_message = "Erro geral ao salvar cliente: " . $e->getMessage();
     echo $error_message; // Retorna o erro para o AJAX
     error_log("[salvar.php] Exception: " . $e->getMessage() . " - POST: " . print_r($_POST, true));
}

exit(); // Finaliza o script PHP
?>