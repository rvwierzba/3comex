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


$query = $pdo->query("SELECT * from $pagina order by id desc ");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
for($i=0; $i < @count($res); $i++){
	foreach ($res[$i] as $key => $value){

		$id = $res[$i]['Id'];
		$cp1 = $res[$i]['NomeRes'];
		$cp2 = $res[$i]['Nome'];
		$cp3 = $res[$i]['Tipo'];
		$cp4 = $res[$i]['CNPJ'];
		$cp5 = $res[$i]['Endereco'];
		$cp6 = $res[$i]['Bairro'];
		$cp7 = $res[$i]['Cidade'];
		$cp8 = $res[$i]['UF'];
		$cp9 = $res[$i]['Pais'];
		$cp10 = $res[$i]['Iata'];
		$cp11 = $res[$i]['Cod_Dac'];
		$cp12 = $res[$i]['Telefone'];
		$cp13 = $res[$i]['Whatsapp'];
		$cp14 = $res[$i]['Contato1'];
		$cp15 = $res[$i]['email1'];
		$cp16 = $res[$i]['Telefone1'];
		$cp17 = $res[$i]['Cargo1'];
		$cp18 = $res[$i]['Obs1'];
		$cp19 = $res[$i]['Contato2'];
		$cp20 = $res[$i]['Email2'];
		$cp21 = $res[$i]['Telefone2'];
		$cp22 = $res[$i]['Cargo2'];
		$cp23 = $res[$i]['Obs2'];
		$cp24 = $res[$i]['DataReg'];
		$cp25 = $res[$i]['Usuario'];
		$cp26 = $res[$i]['CEP'];
		$cp27 = $res[$i]['InscMun'];
		$cp28 = $res[$i]['InscEst'];
		$cp29 = $res[$i]['Site'];
		$cp30 = $res[$i]['Complemento'];
		
	}
		

echo <<<HTML
	<tr>
	<td>{$cp1}</td>	
	<td>{$cp3}</td>	
	<td>{$cp4}</td>	
	<td>

	<a href="#" onclick="editar('{$id}', '{$cp1}', '{$cp2}', '{$cp3}', '{$cp4}', '{$cp5}', '{$cp6}', '{$cp7}', '{$cp8}', '{$cp9}', '{$cp10}', '{$cp11}', '{$cp12}', '{$cp13}', '{$cp14}', '{$cp15}', '{$cp16}', '{$cp17}', '{$cp18}', '{$cp19}', '{$cp20}', '{$cp21}', '{$cp22}', '{$cp23}','{$cp24}','{$cp25}', '{$cp26}', '{$cp27}', '{$cp28}', '{$cp29}', '{$cp30}')" title="Editar Registro">	<i class="bi bi-pencil-square text-primary"></i> </a>
	<a href="#" onclick="excluir('{$id}' , '{$cp1}')" title="Excluir Registro">	<i class="bi bi-trash text-danger"></i> </a>
	

	<a class="mx-1" href="#" onclick="mostrarDados('{$id}', '{$cp1}', '{$cp2}', '{$cp3}', '{$cp4}', '{$cp5}', '{$cp6}', '{$cp7}', '{$cp8}', '{$cp9}', '{$cp10}', '{$cp11}', '{$cp12}', '{$cp13}', '{$cp14}', '{$cp15}', '{$cp16}', '{$cp17}', '{$cp18}', '{$cp19}', '{$cp20}', '{$cp21}', '{$cp22}', '{$cp23}','{$cp24}','{$cp25}')" title="Ver Dados do Agente">
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


function editar(id, cp1, cp2, cp3, cp4, cp5, cp6, cp7, cp8, cp9, cp10, cp11, cp12, cp13, cp14, cp15, cp16, cp17, cp18, cp19, cp20, cp21, cp22, cp23 ,cp24, cp25, cp26, cp27, cp28, cp29){
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
		

	$('#tituloModal').text('Editar Registro');
	var myModal = new bootstrap.Modal(document.getElementById('modalForm'), {		});
	myModal.show();
	$('#mensagem').text('');
}



function limparCampos(){
	$('#id').val('');
	$('#<?=$campo1?>').val('');
	$('#tipo-shw').val('');
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
	
	$('#mensagem').text('');
	
}



function mostrarDados(id, cp1, cp2, cp3, cp4, cp5, cp6, cp7, cp8, cp9, cp10, cp11, cp12, cp13, cp14, cp15, cp16, cp17, cp18, cp19, cp20, cp21, cp22, cp23 ,cp24, cp25){
	
	$('#campo1').text(cp1);
	$('#campo2').text(cp2);
	$('#tipo-shw').text(cp3);
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

	
	var myModal = new bootstrap.Modal(document.getElementById('modalDados'), {		});
	myModal.show();
	
}

</script>




