<?php
require_once("../../conexao.php");
require_once("campos.php"); // Se este arquivo contém o mapeamento, mantenha.
@session_start();
require_once("../verificar.php");

$id_usuario = $_SESSION['id_usuario'];

// RECUPERAR DADOS DO USUÁRIO (mantido)
$query = $pdo->query("SELECT * from usuarios where id = '$id_usuario'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$nome_usuario = $res[0]['nome'];

// COLETA DE DADOS (Baseado no mapeamento do formulário cliente.php)
// Se 'campos.php' contém a definição das variáveis, ajuste este bloco
// Caso contrário, use os nomes diretos dos campos POST:
$cp1 = $_POST['NomeRes'];
$cp2 = $_POST['Nome'];
$cp3 = $_POST['CNPJ']; // <-- CNPJ é o campo-chave
$cp4 = $_POST['CPF'];
$cp5 = $_POST['Endereco'];
$cp6 = $_POST['Bairro'];
$cp7 = $_POST['Cidade'];
$cp8 = $_POST['Estado'];
$cp9 = $_POST['Cep'];
$cp10 = $_POST['Telefone'];
$cp11 = $_POST['Celular'];
$cp12 = $_POST['InscMun'];
$cp13 = $_POST['InscEst'];
$cp14 = $_POST['Site'];
$cp15 = $_POST['Email'];
$cp16 = $_POST['Obs'];

$CNPJ_a_verificar = $cp3; 

// ******************************************************
// 1. VERIFICAÇÃO INICIAL (Para dar feedback rápido se o CNPJ JÁ EXISTE)
// ******************************************************
$query_verificar = $pdo->prepare("SELECT id FROM clientes WHERE CNPJ = :cnpj");
$query_verificar->execute(array(':cnpj' => $CNPJ_a_verificar));
$total_registros = $query_verificar->rowCount();

if ($total_registros > 0) {
    // CNPJ já existe, exibe mensagem e termina.
    echo 'CNPJ já cadastrado!';
    exit(); 
} else {
    // ******************************************************
    // 2. TENTATIVA DE INSERÇÃO
    // ******************************************************
    $query = $pdo->prepare("INSERT INTO clientes SET 
        NomeRes = :cp1, 
        Nome = :cp2, 
        CNPJ = :cp3, 
        CPF = :cp4, 
        Endereco = :cp5, 
        Bairro = :cp6, 
        Cidade = :cp7, 
        Estado = :cp8, 
        Cep = :cp9, 
        Telefone = :cp10, 
        Celular = :cp11, 
        InscMun = :cp12, 
        InscEst = :cp13, 
        Site = :cp14, 
        Email = :cp15, 
        Obs = :cp16"
    );

    $sucesso = $query->execute(array(
        ':cp1'=>$cp1, ':cp2'=>$cp2, ':cp3'=>$cp3, ':cp4'=>$cp4, ':cp5'=>$cp5, 
        ':cp6'=>$cp6, ':cp7'=>$cp7, ':cp8'=>$cp8, ':cp9'=>$cp9, ':cp10'=>$cp10, 
        ':cp11'=>$cp11, ':cp12'=>$cp12, ':cp13'=>$cp13, ':cp14'=>$cp14, 
        ':cp15'=>$cp15, ':cp16'=>$cp16
    ));
    
    if ($sucesso) {
        
        // ******************************************************
        // 3. LIMPEZA DE DUPLICATAS (SOLUÇÃO FORÇADA DE ÚLTIMO RECURSO)
        // ******************************************************
        
        // Acha o ID mais novo (o de maior valor) para o CNPJ recém-inserido/duplicado
        $query_max_id = $pdo->prepare("SELECT MAX(id) AS max_id FROM clientes WHERE CNPJ = :cnpj");
        $query_max_id->execute(array(':cnpj' => $CNPJ_a_verificar));
        $res_max_id = $query_max_id->fetch(PDO::FETCH_ASSOC);
        $id_para_manter = $res_max_id['max_id'];
        
        if ($id_para_manter) {
            // Exclui TODOS os registros com este CNPJ cujo ID seja DIFERENTE do ID mais novo.
            $query_excluir = $pdo->prepare("DELETE FROM clientes WHERE CNPJ = :cnpj AND id != :id_manter");
            $query_excluir->execute(array(
                ':cnpj' => $CNPJ_a_verificar,
                ':id_manter' => $id_para_manter
            ));
            
            // Retorna o texto exato que o AJAX espera para fechar o modal.
            echo 'Salvo com Sucesso'; 
        } else {
             // Caso a inserção tenha falhado silenciosamente
             echo 'Erro: Registro inserido, mas falha na validação pós-inserção.';
        }
    } else {
        echo 'Erro ao Salvar! Por favor, tente novamente.';
    }
}
?>