<?php 
require_once('../../conexao.php');
require_once("campos.php");

echo <<<HTML
<table id="example" class="table table-striped table-light table-hover my-4">
<thead>
<tr>
<th>Código</th>
<th>Descrição</th>
<th>Data Início</th>
<th>Data Fim</th>
<th>Ações</th>
</tr>
</thead>
<tbody>
HTML;

$query = $pdo->query("SELECT * from $pagina order by codigo desc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
for($i=0; $i < @count($res); $i++){
    $cp1 = $res[$i]['CODIGO'];
    $cp2 = $res[$i]['DESCRICAO'];
    $cp3 = $res[$i]['DATA_INICIO'];
    $cp4 = $res[$i]['DATA_FIM'];
    $cp5 = $res[$i]['INTERNO_VERSAO'];
    $cp6 = $res[$i]['INTERNO_HASH'];

echo <<<HTML
    <tr>
    <td>$cp1</td>        
    <td>$cp2</td>
    <td>$cp3</td>        
    <td>$cp4</td>
    <td>
        <a href="#" onclick="editar('$cp1', '$cp1', '$cp2', '$cp3', '$cp4', '$cp5', '$cp6')" title="Editar Registro"><i class="bi bi-pencil-square text-primary"></i> </a>
        <a href="#" onclick="excluir('$cp1', '$cp2')" data-toggle="modal" data-target="#modalExcluir"><i class="bi bi-trash text-danger"></i></a>
        <a class="mx-1" href="#" onclick="mostrarDados('$cp1', '$cp1', '$cp2', '$cp3', '$cp4', '$cp5', '$cp6')" title="Ver Dados do Porto"><i class="bi bi-exclamation-square"></i></a>
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

    function editar(codigo, cp1, cp2, cp3, cp4, cp5, cp6){
        $('#codigo-original').val(codigo);
        $('#<?=$campo1?>').val(cp1);
        $('#<?=$campo2?>').val(cp2);
        $('#<?=$campo3?>').val(cp3);
        $('#<?=$campo4?>').val(cp4);
        $('#<?=$campo5?>').val(cp5);
        $('#<?=$campo6?>').val(cp6);

        $('#tituloModal').text('Editar Registro');
        var myModal = new bootstrap.Modal(document.getElementById('modalForm'), {});
        myModal.show();
        $('#mensagem').text('');
    }

    function excluir(codigo, descricao){
        $('#codigo-excluir').val(codigo);
        $('#nome-excluido').text(descricao);

        var myModal = new bootstrap.Modal(document.getElementById('modalExcluir'), {});
        myModal.show();
    }

    function mostrarDados(codigo, cp1, cp2, cp3, cp4, cp5, cp6){
        $('#campo1').text(cp1);
        $('#campo2').text(cp2);
        $('#campo3').text(cp3);
        $('#campo4').text(cp4);
        $('#campo5').text(cp5);
        $('#campo6').text(cp6);

        var myModal = new bootstrap.Modal(document.getElementById('modalDados'), {});
        myModal.show();
    }
</script>