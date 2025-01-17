<?php 
require_once("../../conexao.php");
require_once("campos.php");
@session_start();
require_once("../verificar.php");

$id_usuario = $_SESSION['id_usuario'];

//RECUPERAR DADOS DO USUÁRIO
$query = $pdo->query("SELECT * from usuarios where id = '$id_usuario'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$nome_usuario = $res[0]['nome'];

$cp1 = $_POST[$campo1];  // NomeRes
$cp2 = $_POST[$campo2];  // Nome
$cp3 = $_POST[$campo3];  // CNPJ
$cp4 = $_POST[$campo4];  // CPF
$cp5 = $_POST[$campo5];  // Endereço
$cp6 = $_POST[$campo6];  // Complemento
$cp7 = $_POST[$campo7];  // Bairro
$cp8 = $_POST[$campo8];  // Cidade
$cp9 = $_POST[$campo9];  // Estado
$cp10 = $_POST[$campo10];  // Cep
$cp11 = $_POST[$campo11];  // Telefone
$cp12 = $_POST[$campo12];  // Celular
$cp13 = $_POST[$campo13];  // InscMun
$cp14 = $_POST[$campo14];  // InscEst
$cp15 = $_POST[$campo15];  // Site
$cp16 = $_POST[$campo16];  // Email
$cp17 = $_POST[$campo17];  // Vendedor
$cp18 = $_POST[$campo18];  // ComVend
$cp19 = $_POST[$campo19];  // Ptax
$cp20 = $_POST[$campo20];  // Obs
$cp21 = $_POST[$campo21];  // CustService
$cp22 = $_POST[$campo22];  // EmailNfe
$cp23 = $_POST[$campo23];  // LocalRps
$cp24 = $_POST[$campo24];  // Grupo
$cp25 = $_POST[$campo25];  // DiasVenc
$cp26 = $_POST[$campo26];  // VencRadar
$cp27 = $_POST[$campo27];  // VencProcuracao
$cp28 = $_POST[$campo28];  // VencMercante
$cp29 = $_POST[$campo29];  // VencAnvisa
$cp30 = $_POST[$campo30];  // IrDia
$cp31 = $_POST[$campo31];  // IN381
$cp32 = $_POST[$campo32];  // Simples
$cp33 = $_POST[$campo33];  // IOF
$cp34 = $_POST[$campo34];  // ImpEsc
$cp35 = $_POST[$campo35];  // NumPad
$cp36 = $_POST[$campo36];  // SubsTrib
$cp37 = $_POST[$campo37];  // ISS
$cp38 = $_POST[$campo38];  // Suframa
$cp39 = $_POST[$campo39];  // CodInt
$cp40 = $_POST[$campo40];  // CodContabil
$cp41 = $_POST[$campo41];  // FDA
$cp42 = $_POST[$campo42];  // CtaDesp

//Inserir os dados na tabela
$query = $pdo->prepare("INSERT INTO clientes SET NomeRes = :cp1, Nome = :cp2, CNPJ = :cp3, CPF = :cp4, Endereco = :cp5, Complemento = :cp6, Bairro = :cp7, Cidade = :cp8, Estado = :cp9, Cep = :cp10, Telefone = :cp11, Celular = :cp12, InscMun = :cp13, InscEst = :cp14, Site = :cp15, Email = :cp16, Vendedor = :cp17, Ptax = :cp19, Obs = :cp20, DiasVenc = :cp25, VencRadar = :cp26, VencProcuracao = :cp27, VencMercante = :cp28, VencAnvisa = :cp29, Simples = :cp32, IOF = :cp33, FDA = :cp41, CtaDesp = :cp42, Grupo = :cp24");
$query->execute(array(':cp1'=>$cp1, ':cp2'=>$cp2, ':cp3'=>$cp3, ':cp4'=>$cp4, ':cp5'=>$cp5, ':cp6'=>$cp6, ':cp7'=>$cp7, ':cp8'=>$cp8, ':cp9'=>$cp9, ':cp10'=>$cp10, ':cp11'=>$cp11, ':cp12'=>$cp12, ':cp13'=>$cp13, ':cp14'=>$cp14, ':cp15'=>$cp15, ':cp16'=>$cp16, ':cp17'=>$cp17, ':cp19'=>$cp19, ':cp20'=>$cp20, ':cp25'=>$cp25, ':cp26'=>$cp26, ':cp27'=>$cp27, ':cp28'=>$cp28, ':cp29'=>$cp29, ':cp32'=>$cp32, ':cp33'=>$cp33, ':cp41'=>$cp41, ':cp42'=>$cp42, ':cp24'=>$cp24));
?>
