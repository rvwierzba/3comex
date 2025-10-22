<?php 
    require_once("../../conexao.php");
    require_once("campos.php");

    echo <<<HTML
    <table id="example" class="table table-striped table-light table-hover my-4">
    <thead>
    <tr>
    <th>Nome</th>
    <th>CPF / CNPJ</th>
    <th>Telefone</th>
    <th>Email</th>
    <th>Ações</th>
    </tr>
    </thead>
    <tbody>
    HTML;

    $query = $pdo->query("SELECT * from $pagina order by Codigo desc ");
    $res = $query->fetchAll(PDO::FETCH_ASSOC);
    for($i=0; $i < @count($res); $i++){
        
        // --- CORREÇÃO DE SINTAXE: ESCAPAR ASPAS (addslashes) ---
        $id = $res[$i]['Codigo'];
        $cp1 = addslashes($res[$i]['NomeRes']);
        $cp2 = addslashes($res[$i]['Nome']);
        $cp3 = addslashes($res[$i]['CNPJ']);
        $cp4 = addslashes($res[$i]['CPF']);
        $cp5 = addslashes($res[$i]['Endereco']);
        $cp6 = addslashes($res[$i]['Complemento']);
        $cp7 = addslashes($res[$i]['Bairro']);
        $cp8 = addslashes($res[$i]['Cidade']);
        $cp9 = addslashes($res[$i]['Estado']);
        $cp10 = addslashes($res[$i]['Cep']);
        $cp11 = addslashes($res[$i]['Telefone']);
        $cp12 = addslashes($res[$i]['Celular']);
        $cp13 = addslashes($res[$i]['InscMun']);
        $cp14 = addslashes($res[$i]['InscEst']);
        $cp15 = addslashes($res[$i]['Site']);
        $cp16 = addslashes($res[$i]['Email']);
        $cp17 = addslashes($res[$i]['Vendedor']);
        $cp18 = addslashes($res[$i]['ComVend']);
        $cp19 = addslashes($res[$i]['Ptax']);
        $cp20 = addslashes($res[$i]['Obs']);
        $cp21 = addslashes($res[$i]['CustService']);
        $cp22 = addslashes($res[$i]['EmailNfe']);
        $cp23 = addslashes($res[$i]['LocalRps']);
        $cp24 = addslashes($res[$i]['Grupo']);
        $cp25 = addslashes($res[$i]['DiasVenc']);
        $cp26 = addslashes($res[$i]['VencRadar']);
        $cp27 = addslashes($res[$i]['VencProcuracao']);
        $cp28 = addslashes($res[$i]['VencMercante']);
        $cp29 = addslashes($res[$i]['VencAnvisa']);
        $cp30 = addslashes($res[$i]['IrDia']);
        $cp31 = addslashes($res[$i]['IN381']);
        $cp32 = addslashes($res[$i]['Simples']);
        $cp33 = addslashes($res[$i]['IOF']);
        $cp34 = addslashes($res[$i]['ImpEsc']);
        $cp35 = addslashes($res[$i]['NumPad']);
        $cp36 = addslashes($res[$i]['SubsTrib']);
        $cp37 = addslashes($res[$i]['ISS']);
        $cp38 = addslashes($res[$i]['Suframa']);
        $cp39 = addslashes($res[$i]['CodInt']);
        $cp40 = addslashes($res[$i]['CodContabil']);
        $cp41 = addslashes($res[$i]['FDA']);
        $cp42 = addslashes($res[$i]['CtaDesp']);
        $cp43 = addslashes($res[$i]['DataCad']);
        $cp44 = addslashes($res[$i]['UsuResp']);

        // Define qual documento mostrar (CNPJ ou CPF)
        $documento = $cp3 ? $cp3 : $cp4;

    echo <<<HTML
        <tr>
        <td>{$cp2}</td>
        <td>{$documento}</td>
        <td>{$cp11}</td>
        <td>{$cp16}</td>
        <td>
            <a href="#" onclick="editar('{$id}', '{$cp1}', '{$cp2}', '{$cp3}', '{$cp4}', '{$cp5}', '{$cp6}', '{$cp7}', '{$cp8}', '{$cp9}', '{$cp10}', '{$cp11}', '{$cp12}', '{$cp13}', '{$cp14}', '{$cp15}', '{$cp16}', '{$cp17}', '{$cp18}', '{$cp19}', '{$cp20}', '{$cp21}', '{$cp22}', '{$cp23}', '{$cp24}', '{$cp25}', '{$cp26}', '{$cp27}', '{$cp28}', '{$cp29}', '{$cp30}', '{$cp31}', '{$cp32}', '{$cp33}', '{$cp34}', '{$cp35}', '{$cp36}', '{$cp37}', '{$cp38}', '{$cp39}', '{$cp40}', '{$cp41}', '{$cp42}', '{$cp43}', '{$cp44}')" title="Editar Registro">
                <i class="bi bi-pencil-square text-primary"></i>
            </a>
            <a href="#" onclick="excluir('{$id}', '{$cp2}')" title="Excluir Registro">
                <i class="bi bi-trash text-danger"></i>
            </a>
            <a href="#" onclick="abrirModalContas('{$documento}')" title="Ver Contas">
                 <i class="bi bi-bank2 text-dark"></i>
            </a>
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
        // ESSENCIAL: Destrói o DataTables anterior antes de criar o novo
        $('#example').DataTable({
            "ordering": false,
            "destroy": true, 
            "language": {
                "url": "../js/pt-BR.json"
            }
        });
    });

    // A função editar COMPLETA
    function editar(id, p1, p2, p3, p4, p5, p6, p7, p8, p9, p10, p11, p12, p13, p14, p15, p16, p17, p18, p19, p20, p21, p22, p23, p24, p25, p26, p27, p28, p29, p30, p31, p32, p33, p34, p35, p36, p37, p38, p39, p40, p41, p42, p43, p44) {
        
        // Pega o documento (CNPJ ou CPF) do cliente que está sendo editado
        const doc_cliente_pai = p3 || p4;
        
        $('#id').val(id);
        $('#doc_cliente_pai').val(doc_cliente_pai); // Define o documento aqui
        $('#NomeRes').val(p1);
        $('#Nome').val(p2);
        $('#CNPJ').val(p3);
        $('#CPF').val(p4);
        $('#Endereco').val(p5);
        $('#Complemento').val(p6);
        $('#Bairro').val(p7);
        $('#Cidade').val(p8);
        $('#Estado').val(p9);
        $('#Cep').val(p10);
        $('#Telefone').val(p11);
        $('#Celular').val(p12);
        $('#InscMun').val(p13);
        $('#InscEst').val(p14);
        $('#Site').val(p15);
        $('#Email').val(p16);
        $('#Vendedor').val(p17);
        $('#ComVend').val(p18);
        $('#Ptax').val(p19);
        $('#Obs').val(p20);
        $('#CustService').val(p21);
        $('#EmailNfe').val(p22);
        $('#LocalRps').val(p23);
        $('#Grupo').val(p24);
        $('#DiasVenc').val(p25);
        $('#VencRadar').val(p26);
        $('#VencProcuracao').val(p27);
        $('#VencMercante').val(p28);
        $('#VencAnvisa').val(p29);
        $('#IrDia').val(p30);
        $('#IN381').val(p31);
        $('#Simples').val(p32);
        $('#IOF').val(p33);
        $('#ImpEsc').val(p34);
        $('#NumPad').val(p35);
        $('#SubsTrib').val(p36);
        $('#ISS').val(p37);
        $('#Suframa').val(p38);
        $('#CodInt').val(p39);
        $('#CodContabil').val(p40);
        $('#FDA').val(p41);
        $('#CtaDesp').val(p42);
        $('#DataCad').val(p43);
        $('#UsuResp').val(p44);
        
        $('#tituloModal').text('Editar Registro');
        var myModal = new bootstrap.Modal(document.getElementById('modalForm'), {});
        myModal.show();
        $('#mensagem').text('');
        
        // --- CHAMA A LISTAGEM DE CONTAS AO ABRIR O MODAL DE EDIÇÃO ---
        abrirModalContas(doc_cliente_pai); 
    }

    function limparCampos() {
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
        $('#<?=$campo22?>').val('');
        $('#<?=$campo23?>').val('');
        $('#<?=$campo24?>').val('');
        $('#<?=$campo25?>').val('');
        $('#<?=$campo26?>').val('');
        $('#<?=$campo27?>').val('');
        $('#<?=$campo28?>').val('');
        $('#<?=$campo29?>').val('');
        $('#<?=$campo30?>').val('');
        $('#<?=$campo31?>').val('');
        $('#<?=$campo32?>').val('');
        $('#<?=$campo33?>').val('');
        $('#<?=$campo34?>').val('');
        $('#<?=$campo35?>').val('');
        $('#<?=$campo36?>').val('');
        $('#<?=$campo37?>').val('');
        $('#<?=$campo38?>').val('');
        $('#<?=$campo39?>').val('');
        $('#<?=$campo40?>').val('');
        $('#<?=$campo41?>').val('');
        $('#<?=$campo42?>').val('');
        $('#<?=$campo43?>').val('');
        $('#<?=$campo44?>').val('');

        $('#mensagem').text('');
    }
    
    // Assumindo que a função excluir é simples (você não a forneceu completa)
    function excluir(id, nome) {
        $('#nome-excluido').text(nome);
        $('#id-excluir').val(id);
        var myModal = new bootstrap.Modal(document.getElementById('modalExcluir'), {});
        myModal.show();
        $('#mensagem-excluir').text('');
    }
</script>