<?php 
	require_once("../../conexao.php");
	require_once("campos.php");

	echo <<<HTML
	<table id="example" class="table table-striped table-light table-hover my-4">
	<thead>
	<tr>
	<th>CPF/CNPJ Raíz</th>
	<th>Tin</th>
	<th>Nome</th>
	<th>Ações</th>
	</tr>
	</thead>
	<tbody>
	HTML;

	$query = $pdo->query("SELECT * from $pagina order by id desc ");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	for($i=0; $i < @count($res); $i++){
		foreach ($res[$i] as $key => $value){ 

		$id = $res[$i]['id'];

		$cp1 = $res[$i]['cpf_cnpj_raiz'];
		$cp2 = $res[$i]['tin'];
		$cp3 = $res[$i]['codigo'];
		$cp4 = $res[$i]['codigo_interno'];
		$cp5 = $res[$i]['nome'];
		$cp6 = $res[$i]['pais_origem'];
	}

	echo <<<HTML
		<tr>
		<td>{$cp1}</td>	
		<td>{$cp2}</td>	
		<td>{$cp5}</td>		
		<td>
		<a href="#" onclick="editar('{$id}', '{$cp1}', '{$cp2}', '{$cp3}', '{$cp4}', '{$cp5}', '{$cp6}')" title="Editar Registro"> 
		<i class="bi bi-pencil-square text-primary"></i></a>
		<a href="#" onclick="excluir('{$id}', '{$cp1}', '{$cp2}', '{$cp3}', '{$cp4}', '{$cp5}', '{$cp6}')" title="Excluir Registro"> 
		<i class="bi bi-trash text-danger"></i></a>
		<a class="mx-1" href="#" onclick="mostrarDados('{$id}', '{$cp1}', '{$cp2}', '{$cp3}', '{$cp4}', '{$cp5}', '{$cp6}')" data-target="#modalDados" data-id="<?=$id?>" 
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

	function editar(id, cp1, cp2, cp3, cp4, cp5, cp6){
		$('#id').val(id);
		$('#<?=$campo1?>').val(cp1);
		$('#<?=$campo2?>').val(cp2);
		$('#<?=$campo3?>').val(cp3);
		$('#<?=$campo4?>').val(cp4);
		$('#<?=$campo5?>').val(cp5);
		$('#<?=$campo6?>').val(cp6);
		
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

		$('#mensagem').text('');

	}

	function mostrarDados(id, cp1, cp2, cp3, cp4, cp5, cp6){
		$('#id').val(id);
		
		$('#campo1').text(cp1);
		$('#campo2').text(cp2);
		$('#campo3').text(cp3);
		$('#campo4').text(cp4);
		$('#campo5').text(cp5);
		$('#campo6').text(cp6);
					
		var myModal = new bootstrap.Modal(document.getElementById('modalDados'), { })
    myModal.show();
	}

</script>