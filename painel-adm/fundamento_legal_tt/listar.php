<?php 
require_once("../../conexao.php");
require_once("campos.php");

echo <<<HTML
<table id="example" class="table table-striped table-light table-hover my-4">
<thead>
<tr>
<th>ID</th>
<th>CÓD.</th>
<th>Descrição</th>
<th>Mnemonico Sistema Controle</th>
<th>Benefício Fiscal SISEN</th>
<th>Permitir DUIMP de Terceiros</th>
<th>Permitir Registro Pessoa Física</th>
<th>Data Início</th>
<th>Data Fim</th>
<th>Versão</th>
<th>Ações</th>
</tr>
</thead>
<tbody>
HTML;

$query = $pdo->query("SELECT * from $pagina order by ID desc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
for($i=0; $i < @count($res); $i++){
    $id = $res[$i]['ID'];
    $cp1 = $res[$i]['CODIGO'];
    $cp2 = $res[$i]['DESCRICAO'];
    $cp3 = $res[$i]['MNEMONICO_SISTEMA_CONTROLE'];
    $cp4 = $res[$i]['CODIGO_BENEFICIO_FISCAL_SISEN'];
    $cp5 = $res[$i]['IN_PERMITIR_DUIMP_VIN_TERCEIROS'];
    $cp6 = $res[$i]['IN_PERMITE_REGISTRO_PESSOA_FISICA'];
    $cp7 = $res[$i]['DATA_INICIO'];
    $cp8 = $res[$i]['DATA_FIM'];
    $cp9 = $res[$i]['INTERNO_VERSAO'];

    echo <<<HTML
    <tr>
    <td>$id</td>       
    <td>$cp1</td>       
    <td>$cp2</td>
    <td>$cp3</td>       
    <td>$cp4</td>
    <td>$cp5</td>       
    <td>$cp6</td>       
    <td>$cp7</td>
    <td>$cp8</td>
    <td>$cp9</td>
    <td>

    <a href="#" onclick="editar('{$id}', '{$cp1}', '{$cp2}', '{$cp3}', '{$cp4}', '{$cp5}', '{$cp6}', '{$cp7}', '{$cp8}', '{$cp9}')" title="Editar Registro"><i class="bi bi-pencil-square text-primary"></i> </a>

    <a href="#" onclick="excluir('{$id}', '{$cp2}')" data-toggle="modal"
        data-target="#modalExcluir" data-id="{$id}" data-name="{$cp1}">
        <i class="bi bi-trash text-danger"></i></a>

    <a class="mx-1" href="#" onclick="mostrarDados('{$id}', '{$cp1}', '{$cp2}', '{$cp3}', '{$cp4}', '{$cp5}', '{$cp6}', '{$cp7}', '{$cp8}', '{$cp9}')" data-target="#modalDados" data-id="{$id}" 
        title="Ver Dados do Fundamento Legal">
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

    function editar(id, cp1, cp2, cp3, cp4, cp5, cp6, cp7, cp8, cp9) {
    $('#id').val(id);
    $('#CODIGO').val(cp1);
    $('#DESCRICAO').val(cp2);
    $('#MNEMONICO_SISTEMA_CONTROLE').val(cp3);
    $('#CODIGO_BENEFICIO_FISCAL_SISEN').val(cp4);
    $('#IN_PERMITIR_DUIMP_VIN_TERCEIROS').val(cp5);
    $('#IN_PERMITE_REGISTRO_PESSOA_FISICA').val(cp6);
    $('#DATA_INICIO').val(cp7);
    $('#DATA_FIM').val(cp8);
    $('#INTERNO_VERSAO').val(cp9);

    $('#tituloModal').text('Editar Registro');
    var myModal = new bootstrap.Modal(document.getElementById('modalForm'), { });
    myModal.show();
    $('#mensagem').text('');
}

function mostrarDados(id, cp1, cp2, cp3, cp4, cp5, cp6, cp7, cp8, cp9) {
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

    var myModal = new bootstrap.Modal(document.getElementById('modalDados'), { });
    myModal.show();
}

</script>
