<?php 
require_once("../../conexao.php");
require_once("campos.php");

echo <<<HTML
<table id="example" class="table table-striped table-light table-hover my-4">
<thead>
<tr>
<th>Código</th>
<th>Cia Aérea</th>
<th>Código Voo</th>	
<th>Telefone</th>									
<th>Ações</th>
</tr>
</thead>
<tbody>
HTML;


$query = $pdo->query("SELECT * from $pagina order by id desc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
for($i=0; $i < @count($res); $i++){
	foreach ($res[$i] as $key => $value){

		$id =$res[$i]['id'];
		$cp1 = $res[$i]['codigo'];
		$cp2 = $res[$i]['nomecia'];
		$cp3 = $res[$i]['nome'];
		$cp4 = $res[$i]['codvoo'];
		$cp5 = $res[$i]['endereco'];
		$cp6=  $res[$i]['cidade'];
		$cp7 = $res[$i]['estado'];
		$cp8 = $res[$i]['cep'];
		$cp9 = $res[$i]['telefone'];
		$cp10 = $res[$i]['whatsapp'];
		$cp11 = $res[$i]['email'];
		$cp12 = $res[$i]['bandeira'];
		$cp13 = $res[$i]['nif'];
		$cp14 = $res[$i]['motivo'];
		$cp15 = $res[$i]['cnpj'];
		$cp16 = $res[$i]['account'];
		$cp17 = $res[$i]['rps'];
		$cp18 = $res[$i]['codcontabil'];
		$cp19 = $res[$i]['datareg'];
		$cp20 = $res[$i]['usuario'];
		$cp21 = $res[$i]['codbandeira'];

	} 

echo <<<HTML
	<tr>
	<td>{$cp1}</td>		
	<td>{$cp2}</td>	
	<td>{$cp4}</td>	
	<td>{$cp9}</td>									
	<td>
	<a href="#" onclick="editar('{$id}', '{$cp1}', '{$cp2}', '{$cp3}', '{$cp4}', '{$cp5}', '{$cp6}', '{$cp7}', '{$cp8}', '{$cp9}', '{$cp10}', '{$cp11}', '{$cp12}', '{$cp13}', '{$cp14}', '{$cp15}', '{$cp16}', '{$cp17}', '{$cp18}', '{$cp19}', '{$cp20}', '{$cp21}')" title="Editar Registro"> 
		<i class="bi bi-pencil-square text-primary"></i></a>


		<a href="#" onclick="excluir('{$id}', '{$cp2}')" data-toggle="modal"
	  data-target="#modalExcluir" data-id="<?=$id?>" data-name="<?=$cp1?>">
		<i class="bi bi-trash text-danger"></i></a>


		<a class="mx-1" href="#" onclick="mostrarDados('{$id}', '{$cp1}', '{$cp2}', '{$cp3}', '{$cp4}', '{$cp5}', '{$cp6}', '{$cp7}',
			'{$cp8}','{$cp9}','{$cp10}','{$cp11}','{$cp12}','{$cp13}','{$cp14}','{$cp15}','{$cp16}','{$cp17}','{$cp18}',
			'{$cp19}','{$cp20}', '{$cp21}')" data-target="#modalDados" data-id="<?=$id?>" 
			title="Ver Dados da Cia Aérea">
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

	


	function editar(id, cp1, cp2, cp3, cp4, cp5, cp6, cp7, cp8,
	 cp9, cp10, cp11, cp12, cp13, cp14, cp15, cp16, cp17, cp18, cp19, cp20, cp21){
		$('#id').val(id);
		$('#<?=$campo1?>').val(cp1);
		$('#<?=$campo2?>').val(cp2);
		$('#<?=$campo3?>').val(cp3);
		$('#<?=$campo4?>').val(cp4);
		$('#<?=$campo5?>').val(cp5);
		$('#<?=$campo6?>').val(cp6);
		$('#<?=$campo7?>').val(cp7);
		$('#<?=$campo8?>').val(cp8);
		$('#<?=$campo9?>').val(cp9);
		$('#<?=$campo10?>').val(cp10);
		$('#<?=$campo11?>').val(cp11);
		$('#<?=$campo12?>').val(cp12);
		$('#<?=$campo13?>').val(cp13);
		$('#<?=$campo14?>').val(cp14);
		$('#<?=$campo15?>').val(cp15);
		$('#<?=$campo16?>').val(cp16);
		$('#<?=$campo17?>').val(cp17);
		$('#<?=$campo18?>').val(cp18);
		$('#<?=$campo19?>').val(cp19);
		$('#<?=$campo20?>').val(cp20);
		$('#<?=$campo21?>').val(cp21);

		
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
		
		$('#mensagem').text('');

	}

	function mostrarDadosTeste(id){
		var myModal = new bootstrap.Modal(document.getElementById('modalDados'), { })
    myModal.show();
	}
	
	function mostrarDados(id, cp1, cp2, cp3, cp4, cp5, cp6, cp7, cp8, cp9, cp10, cp11, cp12, cp13, cp14, cp15,
	 cp16, cp17, cp18, cp19, cp20, cp21){
		$('#id').val(id);
		
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

		
		var myModal = new bootstrap.Modal(document.getElementById('modalDados'), { })
    myModal.show();
		
		
		
	} 
	

</script>