<?php 
require_once("../../conexao.php");
require_once("campos.php");

echo <<<HTML
<table id="example" class="table table-striped table-light table-hover my-4">
<thead>
<tr>
<th>CÓD.</th>
<th>Descrição</th>
<th>CÓD. Grupo Tipo Container</th>
<th>Versão (interno)</th>
</tr>
</thead>
<tbody>
HTML;

$query = $pdo->query("SELECT * from $pagina order by id desc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
for($i=0; $i < @count($res); $i++){
	foreach ($res[$i] as $key => $value){

		$id = $res[$i]['id'];

    $cp1 = $res[$i]['codigo'];
    $cp2 = $res[$i]['descricao']; 
    $cp3 = $res[$i]['comprimento']; 
    $cp4 = $res[$i][ 'dimensoes']; 
    $cp5 = $res[$i]['codigo_grupo_tipo_conteiner']; 
    $cp6 = $res[$i]['data_inicio'];
    $cp7 = $res[$i]['data_fim'];
    $cp8 = $res[$i]['interno_versao']; 
    /* $res[$i][''interno_hash']; */
	} 

echo <<<HTML
	<tr>
	<td>$cp1</td>		
	<td>$cp2</td>
	<td>$cp5</td>		
	<td>$cp8</td>
	<td>
		<a href="#" onclick="editar('{$id}', '{$cp1}', '{$cp2}','{$cp3}', '{$cp4}', '{$cp5}', '{$cp6}', '{$cp7}', '{$cp8}')" title="Editar Registro"><i class="bi bi-pencil-square text-primary"></i> </a>


		<a href="#" onclick="excluir('{$id}', '{$cp2}')" data-toggle="modal"
	  data-target="#modalExcluir" data-id="<?=$id?>" data-name="<?=$cp1?>">
		<i class="bi bi-trash text-danger"></i></a>


		<a class="mx-1" href="#" onclick="mostrarDados('{$id}', '{$cp1}', '{$cp2}','{$cp3}', '{$cp4}', '{$cp5}', '{$cp6}', '{$cp7}', '{$cp8}')" data-target="#modalDados" data-id="<?=$id?>" 
			title="Ver Dados da Moeda">
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

	


	function editar(id, cp1, cp2, cp3, cp4, cp5, cp6, cp7, cp8){
		$('#id').val(id);
		$('#<?=$campo1?>').val(cp1);
		$('#<?=$campo2?>').val(cp2);
    $('#<?=$campo3?>').val(cp3);
		$('#<?=$campo4?>').val(cp4);
    $('#<?=$campo5?>').val(cp5);
		$('#<?=$campo6?>').val(cp6);
    $('#<?=$campo7?>').val(cp7);
    $('#<?=$campo8?>').val(cp8);
					
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
    				
		$('#mensagem').text('');

	}

	function mostrarDadosTeste(id){
		var myModal = new bootstrap.Modal(document.getElementById('modalDados'), { })
    myModal.show();
	}
	
	function mostrarDados(id, cp1, cp2, cp3, cp4, cp5, cp6, cp7, cp8){
		$('#id').val(id);
		
		$('#campo1').text(cp1);
		$('#campo2').text(cp2);
    $('#campo3').text(cp3);
		$('#campo4').text(cp4);
    $('#campo5').text(cp5);
		$('#campo6').text(cp6);
    $('#campo7').text(cp7);
    $('#campo7').text(cp8);
				
		var myModal = new bootstrap.Modal(document.getElementById('modalDados'), { })
    myModal.show();
		
		
		
	} 
	

</script>