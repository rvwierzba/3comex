<?php 
require_once('../../conexao.php');
require_once("campos.php");

echo <<<HTML
<table id="example" class="table table-striped table-light table-hover my-4">
<thead>
<tr>
<th>Sigla ISO2</th>
<th>Sigla ISO3</th>
<th>Nome</th>
<th>Nome em Inglês</th>
<th>Código Numérico</th>
<th>Ações</th>
</tr>
</thead>
<tbody>
HTML;

$query = $pdo->query("SELECT * from $pagina order by CODIGO_NUMERICO desc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
for($i=0; $i < @count($res); $i++){
	foreach ($res[$i] as $key => $value){

		$cp1 = $res[$i]['SIGLA_ISO2'];
		$cp2 = $res[$i]['SIGLA_IS03'];
		$cp3 = $res[$i]['NOME'];
		$cp4 = $res[$i]['NOME_INGLES'];
		$cp5 = $res[$i]['CODIGO_NUMERICO'];
	} 

echo <<<HTML
	<tr>
	<td>$cp1</td>		
	<td>$cp2</td>
	<td>$cp3</td>		
	<td>$cp4</td>
	<td>$cp5</td>
	<td>
		<a href="#" onclick="editar('{$cp1}', '{$cp2}', '{$cp3}', '{$cp4}', '{$cp5}', '{$res[$i]['DATA_INICIO']}', '{$res[$i]['DATA_FIM']}', '{$res[$i]['INTERNO_VERSAO']}')" title="Editar Registro"><i class="bi bi-pencil-square text-primary"></i> </a>

		<a href="#" onclick="excluir('{$cp5}', '{$cp3}')" data-toggle="modal"
	  data-target="#modalExcluir" data-id="<?=$cp5?>" data-name="<?=$cp3?>">
		<i class="bi bi-trash text-danger"></i></a>

		<a class="mx-1" href="#" onclick="mostrarDados('{$cp1}', '{$cp2}', '{$cp3}', '{$cp4}', '{$cp5}', '{$res[$i]['DATA_INICIO']}', '{$res[$i]['DATA_FIM']}', '{$res[$i]['INTERNO_VERSAO']}')" data-target="#modalDados" data-id="<?=$cp1?>" 
			title="Ver Dados do País">
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
	});

	function editar(cp1, cp2, cp3, cp4, cp5, cp6, cp7, cp8){
		$('#<?=$campo1?>').val(cp1);
		$('#<?=$campo2?>').val(cp2);
		$('#<?=$campo3?>').val(cp3);
		$('#<?=$campo4?>').val(cp4);
		$('#<?=$campo5?>').val(cp5);
		$('#<?=$campo6?>').val(cp6);
		$('#<?=$campo7?>').val(cp7);
		$('#<?=$campo8?>').val(cp8);
		$('#tituloModal').text('Editar Registro');
		var myModal = new bootstrap.Modal(document.getElementById('modalForm'), {});
		myModal.show();
		$('#mensagem').text('');
	}

	function excluir(cp5, cp3){
		$('#codNum-excluir').val(cp5);
		$('#nome-excluido').text(cp3);
		
		var myModal = new bootstrap.Modal(document.getElementById('modalExcluir'), {});
		myModal.show();
	}

	function mostrarDados(cp1, cp2, cp3, cp4, cp5, cp6, cp7, cp8){
		$('#campo1').text(cp1);
		$('#campo2').text(cp2);
		$('#campo3').text(cp3);
		$('#campo4').text(cp4);
		$('#campo5').text(cp5);
		$('#campo6').text(cp6);
		$('#campo7').text(cp7);
		$('#campo8').text(cp8);
		var myModal = new bootstrap.Modal(document.getElementById('modalDados'), {});
		myModal.show();
	}
</script>