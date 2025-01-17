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

$cp1 = $_POST[$campo1];
$cp2 = $_POST[$campo2];
$cp3 = $_POST[$campo3];
$cp4 = $_POST[$campo4];
$cp5 = $_POST[$campo5];
$cp6 = $_POST[$campo6];
$cp7 = $_POST[$campo7];
$cp8 = $_POST[$campo8];
$cp9 = $_POST[$campo9];
$cp10 = $_POST[$campo10];  // Iata
$cp11 = $_POST[$campo11];  // Cod_Dac
$cp12 = $_POST[$campo12];  // Telefone
$cp13 = $_POST[$campo13];  // Whatsapp
$cp14 = $_POST[$campo14];  // Contato1
$cp15 = $_POST[$campo15];  // Email1
$cp16 = $_POST[$campo16];  // Telefone1
$cp17 = $_POST[$campo17];  // Cargo1
$cp18 = $_POST[$campo18];  // Obs1
$cp19 = $_POST[$campo19];  // Contato2
$cp20 = $_POST[$campo20];  // Email2
$cp21 = $_POST[$campo21];  // Telefone2
$cp22 = $_POST[$campo22];  // Cargo2
$cp23 = $_POST[$campo23];  // Obs2

//Inserir os dados na tabela
$query = $pdo->prepare("INSERT INTO agentes SET NomeRes = :cp1, Nome = :cp2, Tipo = :cp3, CNPJ = :cp4, Endereco = :cp5, Bairro = :cp6, Cidade = :cp7, UF = :cp8, Pais = :cp9, Iata = :cp10, Cod_Dac = :cp11, Telefone = :cp12, Whatsapp = :cp13, Contato1 = :cp14, Email1 = :cp15, Telefone1 = :cp16, Cargo1 = :cp17, Obs1 = :cp18, Contato2 = :cp19, Email2 = :cp20, Telefone2 = :cp21, Cargo2 = :cp22, Obs2 = :cp23");
$query->execute(array(':cp1'=>$cp1, ':cp2'=>$cp2, ':cp3'=>$cp3, ':cp4'=>$cp4, ':cp5'=>$cp5, ':cp6'=>$cp6, ':cp7'=>$cp7, ':cp8'=>$cp8, ':cp9'=>$cp9, ':cp10'=>$cp10, ':cp11'=>$cp11, ':cp12'=>$cp12, ':cp13'=>$cp13, ':cp14'=>$cp14, ':cp15'=>$cp15, ':cp16'=>$cp16, ':cp17'=>$cp17, ':cp18'=>$cp18, ':cp19'=>$cp19, ':cp20'=>$cp20, ':cp21'=>$cp21, ':cp22'=>$cp22, ':cp23'=>$cp23));
?>
