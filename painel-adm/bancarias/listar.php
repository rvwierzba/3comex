<?php 
require_once("../../conexao.php");
require_once("campos.php");

// Assumindo que $campo1 a $campo6 estão definidos em campos.php
// $campo1 = Banco, $campo2 = Agência, $campo3 = Conta, $campo4 = Tipo, $campo5 = Pessoa, $campo6 = Doc (CPF/CNPJ)

echo <<<HTML
<table id="example" class="table table-striped table-hover my-4">
<thead>
<tr>
<th>{$campo1}</th>
<th>{$campo2}</th>
<th>{$campo3}</th>  
<th>{$campo4}</th>  
<th>{$campo5}</th>  
<th>CPF / CNPJ</th>       
<th>Ações</th>
</tr>
</thead>
<tbody>
HTML;


$query = $pdo->query("SELECT * from $pagina order by id desc ");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
for($i=0; $i < @count($res); $i++){
    // O loop foreach interno é desnecessário aqui, pois você está acessando diretamente o array $res[$i]
    
    $id = $res[$i]['id'];
    $cp1 = $res[$i]['banco'];
    $cp2 = $res[$i]['agencia'];
    $cp3 = $res[$i]['conta'];
    $cp4 = $res[$i]['tipo'];
    $cp5 = $res[$i]['pessoa'];
    $cp6 = $res[$i]['doc']; // CPF ou CNPJ

    echo <<<HTML
    <tr>
    <td>{$cp1}</td>      
    <td>{$cp2}</td> 
    <td>{$cp3}</td> 
    <td>{$cp4}</td> 
    <td>{$cp5}</td> 
    <td>{$cp6}</td>            
    <td>
    <a href="#" onclick="editar('{$id}', '{$cp1}', '{$cp2}', '{$cp3}', '{$cp4}', '{$cp5}', '{$cp6}')" title="Editar Registro">  <i class="bi bi-pencil-square text-primary"></i> </a>
    <a href="#" onclick="excluir('{$id}' , '{$cp1}')" title="Excluir Registro"> <i class="bi bi-trash text-danger"></i> </a>
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
    // Inicializa o DataTables
    $('#example').DataTable({
        "ordering": false,
        "language": {
            "url": "../../js/ajax.js" // Adicionando tradução
        }
    });
});

/**
 * Função para preencher os campos do modal de edição (que é o modalForm)
 * Os campos do modal devem usar os nomes de ID definidos em bancarias.php (que incluem $campo1 a $campo6)
 */
function editar(id, cp1, cp2, cp3, cp4, cp5, cp6){
    // Limpa as mensagens de feedback
    $('#mensagem').text('');

    // 1. Preenche o ID oculto
    $('#id').val(id);
    
    // 2. Preenche os campos de texto
    $('#<?=$campo2?>').val(cp2); // Agência
    $('#<?=$campo3?>').val(cp3); // Conta
    $('#<?=$campo6?>').val(cp6); // CPF / CNPJ

    // 3. Preenche os campos SELECT (Banco, Tipo, Pessoa)
    // Usamos .val() seguido de .trigger('change') para garantir que o Bootstrap/select2 (se houver) atualize a seleção
    $('#<?=$campo1?>').val(cp1).trigger('change'); // Banco
    $('#<?=$campo4?>').val(cp4).trigger('change'); // Tipo (Corrente/Poupança)
    
    // CRÍTICO: Definir o tipo de pessoa ($cp5) aciona a MÁSCARA correta no evento .change() de bancarias.php
    $('#<?=$campo5?>').val(cp5).trigger('change'); 

    // A atribuição do campo CP6 deve ser feita DEPOIS que a máscara correta for aplicada pelo trigger do cp5
    // Por segurança, garantimos que o valor seja atribuído após o trigger.
    setTimeout(function(){
        $('#<?=$campo6?>').val(cp6);
    }, 50);
    
    
    $('#tituloModal').text('Editar Registro');
    var myModal = new bootstrap.Modal(document.getElementById('modalForm'), {    });
    myModal.show();
}


function excluir(id, nome){
    if(confirm('Tem certeza que deseja excluir a conta do banco: ' + nome + ' (ID: ' + id + ')?')){
        $.ajax({
            url: "bancarias/excluir.php",
            type: "POST",
            data: { 'id-excluir': id },
            success: function(retorno){
                if(retorno.trim() == 'Excluído com Sucesso'){
                    alert('Registro excluído com sucesso!');
                    // Recarrega a lista principal (que é este próprio arquivo)
                    window.location.reload(); 
                } else {
                    alert('Erro ao excluir: ' + retorno);
                }
            },
            error: function(){
                alert('Erro de comunicação com o servidor.');
            }
        });
    }
}


function limparCampos(){
    // Limpa os campos de edição/inserção no modalForm
    $('#id').val('');
    
    // Os campos SELECT precisam ser redefinidos ou o .trigger('change') após reset não funciona bem.
    $('#<?=$campo1?>').val(''); // Banco
    $('#<?=$campo4?>').val(''); // Tipo
    $('#<?=$campo5?>').val(''); // Pessoa
    
    $('#<?=$campo2?>').val(''); // Agência
    $('#<?=$campo3?>').val(''); // Conta
    $('#<?=$campo6?>').val(''); // CPF/CNPJ
    
    $('#mensagem').text('');
}

</script>