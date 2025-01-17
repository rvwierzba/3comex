<?php 
require_once("../../conexao.php");
require_once("campos.php");

echo <<<HTML
<table id="example" class="table table-striped table-light table-hover my-4">
<thead>
<tr>
<th>Nome Reduzido</th>
<th>{$campo3}</th>
<th>{$campo4}</th>									
<th>Ações</th>
</tr>
</thead>
<tbody>
HTML;


$query = $pdo->query("SELECT * from $pagina order by Codigo desc ");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
for($i=0; $i < @count($res); $i++){
	foreach ($res[$i] as $key => $value){

		$id = $res[$i]['Codigo'];
		$cp1 = $res[$i]['NomeRes'];
		$cp2 = $res[$i]['Nome'];
		$cp3 = $res[$i]['CNPJ'];
		$cp4 = $res[$i]['CPF'];
		$cp5 = $res[$i]['Endereco'];
		$cp6 = $res[$i]['Complemento'];
		$cp7 = $res[$i]['Bairro'];
		$cp8 = $res[$i]['Cidade'];
		$cp9 = $res[$i]['Estado'];
		$cp10 = $res[$i]['Cep'];
		$cp11 = $res[$i]['Telefone'];
		$cp12 = $res[$i]['Celular'];
		$cp13 = $res[$i]['InscMun'];
		$cp14 = $res[$i]['InscEst'];
		$cp15 = $res[$i]['Site'];
		$cp16 = $res[$i]['Email'];
		$cp17 = $res[$i]['Vendedor'];
		$cp18 = $res[$i]['ComVend'];
		$cp19 = $res[$i]['Ptax'];
		$cp20 = $res[$i]['Obs'];
		$cp21 = $res[$i]['CustService'];
		$cp22 = $res[$i]['EmailNfe'];
		$cp23 = $res[$i]['LocalRps'];
		$cp24 = $res[$i]['Grupo'];
		$cp25 = $res[$i]['DiasVenc'];
		$cp26 = $res[$i]['VencRadar'];
		$cp27 = $res[$i]['VencProcuracao'];
		$cp28 = $res[$i]['VencMercante'];
		$cp29 = $res[$i]['VencAnvisa'];
		$cp30 = $res[$i]['IrDia'];
		$cp31 = $res[$i]['IN381'];
		$cp32 = $res[$i]['Simples'];
		$cp33 = $res[$i]['IOF'];
		$cp34 = $res[$i]['ImpEsc'];
		$cp35 = $res[$i]['NumPad'];
		$cp36 = $res[$i]['SubsTrib'];
		$cp37 = $res[$i]['ISS'];
		$cp38 = $res[$i]['Suframa'];
		$cp39 = $res[$i]['CodInt'];
		$cp40 = $res[$i]['CodContabil'];
		$cp41 = $res[$i]['FDA'];
		$cp42 = $res[$i]['CtaDesp'];
		$cp43 = $res[$i]['DataCad'];
		$cp44 = $res[$i]['UsuResp'];
  
	 
		
	}
		

echo <<<HTML
	<tr>
	<td>{$cp1}</td>	
	<td>{$cp3}</td>	
	<td>{$cp4}</td>	
	<td>

	<a href="#" onclick="editar('{$id}', '{$cp1}', '{$cp2}', '{$cp3}', '{$cp4}', '{$cp5}', '{$cp6}', '{$cp7}', '{$cp8}', '{$cp9}', '{$cp10}', '{$cp11}', '{$cp12}', '{$cp13}', '{$cp14}', '{$cp15}', '{$cp16}', '{$cp17}', '{$cp18}', '{$cp19}', '{$cp20}', '{$cp21}', '{$cp22}', '{$cp23}','{$cp24}','{$cp25}', '{$cp26}', '{$cp27}', '{$cp28}', '{$cp29}', '{$cp30}', '{$cp31}', '{$cp32}', '{$cp33}', '{$cp34}', 
	'{$cp35}', '{$cp36}', '{$cp37}', '{$cp38}', '{$cp39}', '{$cp40}', '{$cp41}', '{$cp42}', '{$cp42}', '{$cp43}', '{$cp44}')" title="Editar Registro">	<i class="bi bi-pencil-square text-primary"></i> </a>
	<a href="#" onclick="excluir('{$id}' , '{$cp1}')" title="Excluir Registro">	<i class="bi bi-trash text-danger"></i> </a>
	

	<a class="mx-1" href="#" onclick="mostrarDados('{$id}', '{$cp1}', '{$cp2}', '{$cp3}', '{$cp4}', '{$cp5}', '{$cp6}', '{$cp7}', '{$cp8}', '{$cp9}', '{$cp10}', '{$cp11}', '{$cp12}', '{$cp13}', '{$cp14}', '{$cp15}', '{$cp16}', '{$cp17}', '{$cp18}', '{$cp19}', '{$cp20}', '{$cp21}', '{$cp22}', '{$cp23}','{$cp24}','{$cp25}', '{$cp26}', '{$cp27}', '{$cp28}', '{$cp29}', '{$cp30}', '{$cp31}', '{$cp32}', '{$cp33}', '{$cp34}', 
	'{$cp35}', '{$cp36}', '{$cp37}', '{$cp38}', '{$cp39}', '{$cp40}', '{$cp41}', '{$cp42}', '{$cp42}', '{$cp43}', '{$cp44}')" title="Ver Dados do Cliente">
	<i class="bi bi-exclamation-square"></i></a>

	</td>
	</tr>
HTML;
} 
echo <<<HTML
</tbody>
</table>
HTML;

?>

<script>
$(document).ready(function() {    
	$('#example').DataTable({
		"ordering": false
	});

} );


function editar(id, cp1, cp2, cp3, cp4, cp5, cp6, cp7, cp8, cp9, cp10, cp11, cp12, cp13, cp14, cp15, cp16, cp17, cp18, cp19, cp20, cp21, cp22, cp23 ,cp24, cp25, cp26, cp27, cp28, cp29, cp30, cp31, cp32, cp33, cp34, cp35, cp36, cp37, cp38, cp39, cp40, cp41, cp42, cp43, cp44){
	$('#id').val(id);
	$('#<?=$campo1?>').val(cp1);
	$('#<?=$campo2?>').val(cp2);
	$('#tipo-shw').val(cp3);
	$('#<?=$campo4?>').val(cp4);
	$('#<?=$campo5?>').val(cp5);
	$('#<?=$campo6?>').val(cp6);
	$('#<?=$campo7?>').val(cp7);
	$('#<?=$campo9?>').val(cp9);
	$('#<?=$campo10?>').val(cp10);
	$('#<?=$campo11?>').val(cp11);
	$('#<?=$campo12?>').val(cp12);
	$('#<?=$campo13?>').val(cp13);
	$('#<?=$campo14?>').val(cp14);
	$('#<?=$campo15?>').val(cp15);
	$('#<?=$campo16?>').val(cp16);
	$('#<?=$campo17?>').val(cp18);
	$('#<?=$campo18?>').val(cp18);
	$('#<?=$campo19?>').val(cp19);
	$('#<?=$campo20?>').val(cp20);
	$('#<?=$campo21?>').val(cp21);
	$('#<?=$campo22?>').val(cp22);
	$('#<?=$campo23?>').val(cp23);
	$('#<?=$campo24?>').val(cp24);
	$('#<?=$campo25?>').val(cp25);
	$('#<?=$campo26?>').val(cp26);
	$('#<?=$campo27?>').val(cp27);
	$('#<?=$campo28?>').val(cp28);
	$('#<?=$campo29?>').val(cp29);
	$('#<?=$campo30?>').val(cp30);
	$('#<?=$campo31?>').val(cp31);
	$('#<?=$campo32?>').val(cp32);
	$('#<?=$campo33?>').val(cp33);
	$('#<?=$campo34?>').val(cp34);
	$('#<?=$campo35?>').val(cp35);
	$('#<?=$campo36?>').val(cp36);
	$('#<?=$campo37?>').val(cp37);
	$('#<?=$campo38?>').val(cp38);
	$('#<?=$campo39?>').val(cp39);
	$('#<?=$campo40?>').val(cp40);
	$('#<?=$campo41?>').val(cp41);
	$('#<?=$campo42?>').val(cp42);
	$('#<?=$campo43?>').val(cp43);
	$('#<?=$campo44?>').val(cp44);
		

	$('#tituloModal').text('Editar Registro');
	var myModal = new bootstrap.Modal(document.getElementById('modalForm'), {		});
	myModal.show();
	$('#mensagem').text('');
}



function limparCampos(){
	$('#id').val('');
	$('#<?=$campo1?>').val('');
	$('#<?=$campo2?>').val('');
	$('#<?=$campo3?>').val('');
	$('#<?=$campo4?>').val('');
	$('#<?=$campo5?>').val('');
	$('#<?=$campo6?>').val('');
	$('#<?=$campo7?>').val('');
	$('#<?=$campo8?>').val('');
	$('#<?=$campo9?>').val('');
	$('#<?=$campo10?>').val('');
	$('#<?=$campo11?>').val('');
	$('#<?=$campo12?>').val('');
	$('#<?=$campo13?>').val('');
	$('#<?=$campo14?>').val('');
	$('#<?=$campo15?>').val('');
	$('#<?=$campo16?>').val('');
	$('#<?=$campo17?>').val('');
	$('#<?=$campo18?>').val('');
	$('#<?=$campo19?>').val('');
	$('#<?=$campo20?>').val('');
	$('#<?=$campo21?>').val('');
	$('#<?=$campo22?>').val('');
	$('#<?=$campo23?>').val('');
	$('#<?=$campo24?>').val('');
	$('#<?=$campo25?>').val('');
	$('#<?=$campo26?>').val('');
	$('#<?=$campo27?>').val('');
	$('#<?=$campo28?>').val('');
	$('#<?=$campo29?>').val('');
	$('#<?=$campo30?>').val('');
	$('#<?=$campo31?>').val('');
	$('#<?=$campo32?>').val('');
	$('#<?=$campo33?>').val('');
	$('#<?=$campo34?>').val('');
	$('#<?=$campo35?>').val('');
	$('#<?=$campo36?>').val('');
	$('#<?=$campo37?>').val('');
	$('#<?=$campo38?>').val('');
	$('#<?=$campo39?>').val('');
	$('#<?=$campo40?>').val('');
	$('#<?=$campo41?>').val('');
	$('#<?=$campo42?>').val('');
	$('#<?=$campo43?>').val('');
	$('#<?=$campo44?>').val('');
	
	$('#mensagem').text('');
	
}



function mostrarDados(id, cp1, cp2, cp3, cp4, cp5, cp6, cp7, cp8, cp9, cp10, cp11, cp12, cp13, cp14, cp15, cp16, cp17, cp18, cp19, cp20, cp21, cp22, cp23 ,cp24, cp25, cp26, cp27, cp28, cp29, cp30, cp31, cp32, cp33, cp34, cp35, cp36, cp37, cp38, cp39, cp40, cp41, cp42, cp43, cp44){
	
	$('#campo1').text(cp1);
	$('#campo2').text(cp2);
	$('#campo3').text(cp3);
	$('#campo4').text(cp4);
	$('#campo5').text(cp5);
	$('#campo6').text(cp6);
	$('#campo7').text(cp7);
	$('#campo8').text(cp8);
	$('#campo9').text(cp9);
	$('#campo10').text(cp10);
	$('#campo11').text(cp11);
	$('#campo12').text(cp12);
	$('#campo13').text(cp13);
	$('#campo14').text(cp14);
	$('#campo15').text(cp15);
	$('#campo16').text(cp16);
	$('#campo17').text(cp17);
	$('#campo18').text(cp18);
	$('#campo19').text(cp19);
	$('#campo20').text(cp20);
	$('#campo21').text(cp21);
	$('#campo22').text(cp22);
	$('#campo23').text(cp23);
	$('#campo24').text(cp24);
	$('#campo25').text(cp25);
	$('#campo26').text(cp26);
	$('#campo27').text(cp27);
	$('#campo28').text(cp28);
	$('#campo29').text(cp29);
	$('#campo30').text(cp30);
	$('#campo31').text(cp31);
	$('#campo32').text(cp32);
	$('#campo33').text(cp33);
	$('#campo34').text(cp34);
	$('#campo35').text(cp35);
	$('#campo36').text(cp36);
	$('#campo37').text(cp37);
	$('#campo38').text(cp38);
	$('#campo39').text(cp39);
	$('#campo40').text(cp40);
	$('#campo41').text(cp41);
	$('#campo42').text(cp42);
	$('#campo43').text(cp43);
	$('#campo44').text(cp44);
	
	var myModal = new bootstrap.Modal(document.getElementById('modalDados'), {		});
	myModal.show();
	
}

</script>




