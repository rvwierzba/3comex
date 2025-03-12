<?php 
    @session_start();
    require_once("../conexao.php");
    require_once("verificar.php");
    $id_usuario = $_SESSION['id_usuario'];
    //RECUPERAR DADOS DO USUÁRIO
    $query = $pdo->query("SELECT * FROM usuarios WHERE id = '$id_usuario'");
    $res = $query->fetchAll(PDO::FETCH_ASSOC);
    $nome_usuario = $res[0]['nome'];
    $email_usuario = $res[0]['email'];
    $senha_usuario = $res[0]['senha'];
    $nivel_usuario = $res[0]['nivel'];

    //MENUS DO PAINEL
    $menu1 = 'home';
    $menu2 = 'clientes';
    $menu3 = 'niveis';
    $menu4 = 'usuarios';
    $menu5 = 'bancos';
    $menu6 = 'bancarias';
    $menu7 = 'cat_despesas';
    $menu8 = 'despesas';
    $menu9 = 'frequencias';
    $menu10 = 'formas_pgtos';
    $menu11 = 'produtos';
    $menu12 = 'cat_produtos';
    $menu13 = 'fornecedores';
    $menu14 = 'estoques';
    $menu15 = 'agentes';
    $menu16 = 'paises';
    $menu17 = 'portos';
    $menu18 = 'cias';
    $menu19 = 'classif';
    $menu20 = 'documentos';
    $menu21 = 'lcpo';
    $menu22 = 'unidades_rfb';
    $menu23 = 'recinto_aduaneiro';
    $menu24 = 'tipo_conteiner';
    $menu25 = 'moeda';
    $menu26 = 'org_anuente';
    $menu27 = 'tp_conhec';
    $menu28 = 'tp_declara';
    $menu29 = 'tp_area';
    $menu30 = 'solicitante';
    $menu31 = 'op_estrangeiro';
    $menu32 = 'taxas';
    $menu33 = 'enquadramento';
    $menu34 = 'fundamento_legal_tt';
    $menuAgentes = 'agentes';
    $menuDUE = 'due';
    $menuLPCOE = 'LpcoE';
    $menuLPCOC = 'LpcoConsulta';
    $menuDUIMP = 'duimp';
    $menuIct = 'Ict';
    $menuIanx = 'Ianx';
    $menuIlpco = 'Ilpco';
    $menuIpgto = 'Ipgto';
    $menuItab = 'Itab';
    $menuItrat = 'Itrat';
    $menuProd = 'produto';
    $menuPope = 'Pope';
    $menuSolitNum = 'solicitacao_de_numerarios';
    

    if(@$_GET['pag'] == ""){
        $pag = $menu1;
    }else{
        $pag = $_GET['pag'];
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="description" content="">
    <link href="../img/logo.png" rel="shortcut icon" type="image/x-icon">

    <!--BOOTSTRAP V5.0-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js" integrity="sha384-JEW9xMcG8R+pH31jmWH6WWP0WintQrMb4s7ZOdauHnUtxwoG2vI5DkLtS3qm9Ekf" crossorigin="anonymous"></script>

    <!--JQUERY -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">

    <!-- Mascaras JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.11/jquery.mask.min.js"></script>

    <!--LOCAL CSS FILES-->
    <link rel="stylesheet" type="text/css" href="../DataTables/DataTables-1.10.23/datatable.css"/>
    <link rel="stylesheet" type="text/css" href="../css/style.css"/>

    <!--FONT AWESOME-->
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <script src="../fontawesome/js/all.min.js"></script>

    <!--FLAGS-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.0.0/css/flag-icons.min.css"/>

    <!--LOCAL JS FILES-->
    <script type="text/javascript" src="../DataTables/datatables.min.js"></script>

    <title>3comex</title>
  
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><img src="../img/logo.png" width="30px"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="index.php?pag=<?php echo $menu1 ?>">Home</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Cadastros
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="index.php?pag=<?php echo $menu15 ?>">Ag. Brasil / Sub-Agente ; Coloader</a></li>
                            <li><a class="dropdown-item" href="index.php?pag=<?php echo $menuAgentes ?>">Agentes</a></li>
                            <li><a class="dropdown-item" href="index.php?pag=<?php echo $menu2 ?>">Clientes</a></li>
                            <li><a class="dropdown-item" href="index.php?pag=<?php echo $menu18 ?>">Cia Aérea</a></li>
                            <li><a class="dropdown-item" href="index.php?pag=<?php echo $menu19 ?>">Classificações</a></li>
                            <li><a class="dropdown-item" href="index.php?pag=<?php echo $menu7 ?>">Categoria Títulos</a></li>
                            <li><a class="dropdown-item" href="index.php?pag=<?php echo $menu5 ?>">Bancos</a></li>
                            <li><a class="dropdown-item" href="index.php?pag=<?php echo $menu6 ?>">Contas Bancárias</a></li>
                            <li><a class="dropdown-item" href="index.php?pag=<?php echo $menu20 ?>">Documentos</a></li>
                            <li><a class="dropdown-item" href="index.php?pag=<?php echo $menu13 ?>">Fornecedores</a></li>
                            <li><a class="dropdown-item" href="index.php?pag=<?php echo $menu21 ?>">Modelo LCPO</a></li>
                            <li><a class="dropdown-item" href="index.php?pag=<?php echo $menu3 ?>">Níveis de Usuários</a></li>
                            <li><a class="dropdown-item" href="index.php?pag=<?php echo $menu8 ?>">Títulos</a></li>
                            <li><a class="dropdown-item" href="index.php?pag=<?php echo $menu4 ?>">Usuários</a></li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">      
                        Tabelas SISCOMEX   
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="index.php?pag=<?php echo $menu33 ?>">Enquadramento</a></li>
                            <li><a class="dropdown-item" href="index.php?pag=<?php echo $menu34 ?>">Fundamento Leg. Trat. Trib.</a></li>
                            <li><a class="dropdown-item" href="index.php?pag=<?php echo $menu16 ?>">Países</a></li>
                            <li><a class="dropdown-item" href="index.php?pag=<?php echo $menu17 ?>">Portos</a></li>
                            <li><a class="dropdown-item" href="index.php?pag=<?php echo $menu23 ?>">Recinto Aduaneiro</a></li>
                            <li><a class="dropdown-item" href="index.php?pag=<?php echo $menu30 ?>">Solicitante</a></li>
                            <li><a class="dropdown-item" href="index.php?pag=<?php echo $menu24 ?>">Tipo Conteiner</a></li>
                            <li><a class="dropdown-item" href="index.php?pag=<?php echo $menu27 ?>">Tipo de Conhecimento</a></li>
                            <li><a class="dropdown-item" href="index.php?pag=<?php echo $menu28 ?>">Tipo Declaração Aduaneira</a></li>
                            <li><a class="dropdown-item" href="index.php?pag=<?php echo $menu29 ?>">Tipo Área Equipamento</a></li>
                            <li><a class="dropdown-item" href="index.php?pag=<?php echo $menu25 ?>">Tipo Moedas</a></li>
                            <li><a class="dropdown-item" href="index.php?pag=<?php echo $menu26 ?>">Órgão Anuente</a></li>
                            <li><a class="dropdown-item" href="index.php?pag=<?php echo $menu22 ?>">Unidades RFB</a></li>
                        </ul>    
                    </li>    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Exportação
                        </a> 
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="index.php?pag=<?php echo $menu4 ?>">Carga e Trânsito</a></li>
                            <li><a class="dropdown-item" href="index.php?pag=<?php echo $menuLPCOC ?>">Consulta LPCO</a></li>
                            <li><a class="dropdown-item" href="index.php?pag=<?php echo $menu6 ?>">Documentos</a></li>
                            <li><a class="dropdown-item" href="index.php?pag=<?php echo $menuDUE ?>">DU-E</a></li>
                            <li><a class="dropdown-item" href="index.php?pag=<?php echo $menuLPCOE ?>">LPCO</a></li>
                            <li><a class="dropdown-item" href="index.php?pag=<?php echo $menu8 ?>">Tabelas</a></li>
                            <li><a class="dropdown-item" href="index.php?pag=<?php echo $menu7 ?>">Tratamento Tributário</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Importação
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="index.php?pag=<?php echo $menuIct ?>">Carga e Trânsito</a></li>
                            <li><a class="dropdown-item" href="index.php?pag=<?php echo $menuIclas ?>">Consultar Classificação</a></li>
                            <li><a class="dropdown-item" href="index.php?pag=<?php echo $menuIanx ?>">Documentos</a></li>
                            <li><a class="dropdown-item" href="index.php?pag=<?php echo $menuDUIMP ?>">DUIMP</a></li>
                            <li><a class="dropdown-item" href="index.php?pag=<?php echo $menuIlpco ?>">LPCO</a></li>
                            <li><a class="dropdown-item" href="index.php?pag=<?php echo $menuIpgto ?>">Pagamentos</a></li>
                            <li><a class="dropdown-item" href="index.php?pag=<?php echo $menuItab ?>">Tabelas</a></li>
                            <li><a class="dropdown-item" href="index.php?pag=<?php echo $menuItrat ?>">Tratamento Tributário</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Produtos
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="index.php?pag=<?php echo $menuProd ?>">Cadastrar Produto</a></li>
                            <li><a class="dropdown-item" href="index.php?pag=<?php echo $menu31 ?>">Operador Estrangeiro</a></li>
                            <li><a class="dropdown-item" href="index.php?pag=<?php echo $menu6 ?>">Consultar Classificação</a></li>
                            <li><a class="dropdown-item" href="index.php?pag=<?php echo $menuPsim ?>">Simuladores</a></li>
                       </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Financeiro
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="index.php?pag=<?php echo $menuSolitNum ?>">Solicitações de Numerário</a></li>
                        </ul>
                    </li>
                </ul>
                <div class="d-flex mr-4">
                    <img class="img-profile rounded-circle" src="../img/user.jpg" width="40px" height="40px">
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php echo @$nome_usuario; ?>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalPerfil">Editar Dados</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="../logout.php">Sair</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid mb-4 mx-4">
        <?php 
            require_once($pag.'.php');
        ?>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalPerfil" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Editar Dados</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form-perfil" method="post">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nome-usuario" class="form-label">Nome</label>
                            <input type="text" class="form-control" name="nome-usuario" placeholder="Nome" value="<?php echo $nome_usuario ?>">
                        </div>
                        <div class="mb-3">
                            <label for="email-usuario" class="form-label">Email</label>
                            <input type="email" class="form-control" name="email-usuario" placeholder="Email" value="<?php echo $email_usuario ?>">
                        </div>
                        <div class="mb-3">
                            <label for="senha-usuario" class="form-label">Senha</label>
                            <input type="text" class="form-control" name="senha-usuario" placeholder="Senha" value="<?php echo $senha_usuario ?>">
                        </div>
                        <small><div id="mensagem-perfil" class="text-center"></div></small>
                        <input type="hidden" class="form-control" name="id-usuario"  value="<?php echo $id_usuario ?>">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btn-fechar-perfil">Fechar</button>
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Ajax para inserir ou editar dados -->
    <script type="text/javascript">
        $("#form-perfil").submit(function () {
            event.preventDefault();
            var formData = new FormData(this);

            $.ajax({
                url: "editar-perfil.php",
                type: 'POST',
                data: formData,
                success: function (mensagem) {
                    $('#mensagem-perfil').removeClass()
                    if (mensagem.trim() == "Salvo com Sucesso") {
                        $('#btn-fechar-perfil').click();
                        window.location = "index.php";
                    } else {
                        $('#mensagem-perfil').addClass('text-danger')
                    }
                    $('#mensagem-perfil').text(mensagem)
                },
                cache: false,
                contentType: false,
                processData: false,
            });
        });
    </script>
</body>
</html>
