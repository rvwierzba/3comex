<?php
// painel-adm/despesas/listar.php (AJUSTADO PARA O PADRÃO CORRETO)

require_once("../../conexao.php");
require_once("campos.php");

// Formata os nomes das colunas para usar como rótulos na tabela
$label1 = ucfirst(str_replace('_', ' ', $campo1));
$label2 = ucfirst(str_replace('_', ' ', $campo2));

echo <<<HTML
<table id="example" class="table table-striped table-light table-hover my-4">
<thead>
    <tr>
        <th>{$label1}</th>
        <th>{$label2}</th>
        <th>Ações</th>
    </tr>
</thead>
<tbody>
HTML;

$query = $pdo->query("SELECT d.id, d.nome, d.cat_despesa, c.nome as nome_categoria 
                     FROM despesas as d 
                     LEFT JOIN cat_despesas as c ON d.cat_despesa = c.id 
                     ORDER BY d.nome asc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
for ($i = 0; $i < @count($res); $i++) {
    $id = $res[$i]['id'];
    $valorCampo1 = $res[$i]['nome'];
    $valorCampo2 = $res[$i]['cat_despesa']; // Passa o ID da categoria para o JS
    $nome_categoria = $res[$i]['nome_categoria'];

    echo <<<HTML
    <tr>
        <td>{$valorCampo1}</td>
        <td>{$nome_categoria}</td>
        <td>
            <a href="#" onclick="editar('{$id}', '{$valorCampo1}', '{$valorCampo2}')" title="Editar Registro">
                <i class="bi bi-pencil-square text-primary"></i>
            </a>
            <a href="#" onclick="excluir('{$id}', '{$valorCampo1}')" title="Excluir Registro">
                <i class="bi bi-trash text-danger"></i>
            </a>
        </td>
    </tr>
    HTML;
}

echo <<<HTML
</tbody>
</table>
<script>
    $(document).ready(function() { $('#example').DataTable({ "ordering": false }); });
</script>
HTML;
?>