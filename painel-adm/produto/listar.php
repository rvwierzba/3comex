<?php 
	require_once("../../conexao.php");
	require_once("campos.php");

echo <<<HTML
	<table id="example" class="table table-striped table-light table-hover my-4">
	<thead>
	<tr>
	<th>CÓD.</th>
	<th>NCM</th>
	<th>País de origem</th>
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

		$cp1 = $res[$i]['codigo'];
		$cp2 = $res[$i]['codigo_interno'];
		$cp3 = $res[$i]['descricao'];
		$cp4 = $res[$i]['denominacao'];
		$cp5 = $res[$i]['ncm'];
		$cp6 = $res[$i]['periodo_registro_inicio'];
		$cp7 = $res[$i]['periodo_registro_fim'];
    $cp8 = $res[$i]['situacao'];
    $cp9 = $res[$i]['ultima_alteracao_inicio'];
    $cp10 = $res[$i]['ultima_alteracao_fim'];
    $cp11 = $res[$i]['pais_origem'];
    $cp12 = $res[$i]['cpf_cnpj_fabricante'];
    $cp13 = $res[$i]['cpf_cnpj_raiz'];
    $cp14 = $res[$i]['modalidade'];
    $cp15 = $res[$i]['data_referencia'];
    $cp16 = $res[$i]['operador_estrangeiro_codigo'];
		
	}

echo <<<HTML
		<tr>
		<td>{$cp1}</td>		
		<td>{$cp5}</td>		
		<td>{$cp11}</td>		
		<td>
		<a href="#" onclick="editar('{$id}', '{$cp1}', '{$cp2}', '{$cp3}', '{$cp4}', '{$cp5}', '{$cp6}', '{$cp7}', '{$cp8}', '{$cp9}', '{$cp10}', '{$cp11}', '{$cp12}', '{$cp13}', '{$cp14}', '{$cp15}', '{$cp16}')" title="Editar Registro"> 
		<i class="bi bi-pencil-square text-primary"></i></a>
		<a href="#" onclick="excluir('{$id}', '{$cp1}')" title="Excluir Registro"> 
		<i class="bi bi-trash text-danger"></i></a>
		<a class="mx-1" href="#" onclick="mostrarDados('{$id}', '{$cp1}', '{$cp2}', '{$cp3}', '{$cp4}', '{$cp5}', '{$cp6}', '{$cp7}', '{$cp8}', '{$cp9}', '{$cp10}', '{$cp11}', '{$cp12}', '{$cp13}', '{$cp14}', '{$cp15}', '{$cp16}')" data-target="#modalDados" data-id="<?=$id?>" 
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

	function editar(id, cp1, cp2, cp3, cp4, cp5, cp6, cp7, cp8, cp9, cp10, cp11, cp12, cp13, cp14, cp15, cp16){
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
		
		$('#tituloModal').text('Editar Registro');
		var myModal = new bootstrap.Modal(document.getElementById('modalForm'), {		});
		myModal.show();
		$('#mensagem').text('');
	}

	function limparCampos(){
		$('#id').val('');
		$('#<?=$campo1?>').val('');


		$('#mensagem').text('');

	}

	function mostrarDados(id, cp1, cp2, cp3, cp4, cp5, cp6, cp7, cp8, cp9, cp10, cp11, cp12, cp13, cp14, cp15, cp16){
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
			
		var myModal = new bootstrap.Modal(document.getElementById('modalDados'), { })
    myModal.show();
	}

</script>