<?php
// painel-adm/fornecedores/listar.php

require_once("../../conexao.php");
$pagina = 'fornecedores';

echo <<<HTML
<table id="example" class="table table-striped table-light table-hover my-4">
<thead>
    <tr>
        <th>Nome</th>
        <th>Documento</th>
        <th>Telefone</th>
        <th>Ativo</th>
        <th>Ações</th>
    </tr>
</thead>
<tbody>
HTML;

$query = $pdo->query("SELECT * from $pagina order by id desc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
for ($i = 0; $i < @count($res); $i++) {
    foreach ($res[$i] as $key => $value) {}
    $id = $res[$i]['id'];
    $nome = $res[$i]['nome'];
    $pessoa = $res[$i]['pessoa'];
    $doc = $res[$i]['doc'];
    $telefone = $res[$i]['telefone'];
    $endereco = $res[$i]['endereco'];
    $ativo = $res[$i]['ativo'];
    $obs = $res[$i]['obs'];
    $banco = $res[$i]['banco'];
    $agencia = $res[$i]['agencia'];
    $email = $res[$i]['email'];

    echo <<<HTML
    <tr>
        <td>{$nome}</td>
        <td>{$doc}</td>
        <td>{$telefone}</td>
        <td>{$ativo}</td>
        <td>
            <a href="#" onclick="editar('{$id}', '{$nome}', '{$pessoa}', '{$doc}', '{$telefone}', '{$endereco}', '{$ativo}', '{$obs}', '{$banco}', '{$agencia}', '{$email}')" title="Editar Registro">
                <i class="bi bi-pencil-square text-primary"></i>
            </a>
            <a href="#" onclick="excluir('{$id}', '{$nome}')" title="Excluir Registro">
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