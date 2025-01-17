<?php

   require_once('campos.php');
   require_once(dirname(__DIR__).'/../conexao.php');
  
    // Consulta Default
    $sql = "SELECT LEFT(Codigo, 2) AS Secao,
    id, Codigo, Descricao
    FROM $pagina";

    // Verifica se o formulário foi enviado
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data_pesquisa = $_POST["data_pesquisa"];
    $texto_pesquisa = $_POST["texto_pesquisa"];

   // Adiciona cláusulas WHERE conforme necessário
   $where_conditions = [];

   if (!empty($data_pesquisa)) {
       $where_conditions[] = "(STR_TO_DATE('$data_pesquisa', '%Y-%m-%d') BETWEEN Data_Inicio AND Data_Fim)";
   }

   if (!empty($texto_pesquisa)) {
       $where_conditions[] = "(Codigo LIKE '%$texto_pesquisa%' OR Descricao LIKE '%$texto_pesquisa%')";
   }

   if (!empty($where_conditions)) {
       $sql .= " WHERE " . implode(" AND ", $where_conditions);
   }

   $sql .= " GROUP BY LEFT(Codigo, 2)";

}

  $default_query = $pdo->query($sql);
  $res = $default_query->fetchAll(PDO::FETCH_ASSOC);
  
  echo <<<HTML
   
      <style>
        .s-between{
          margin-left: 20%;
        }

        .moeda{
          margin-left: auto;
        }


        .cotacao{
        
          margin-left: 3%;
        } 

        .conteiner-cot{
          margin-left: 25%;
        }

        .asterisco{
          color: red;
        }

        #btn-search{
          margin-top: 5%;
        }

        .title-secao-lista{
          margin-top: 2%;
        }

        .ncm-i-lista{
          font-size: 20px;
        }

       </style>

    <div class="d-flex">
      <h2 class="mt-1">Sumário</h2>
      <div class="conteiner-cot">
        <ul class="d-flex" style="list-style:none; margin-top:3%;">
          <li class="d-flex"><span class="fi fi-us"></span> <p class="my-auto moeda">USD</p> <p class="cotacao my-auto">0,0000000</p></li>
          <li class="d-flex s-between"> <span class="fi fi-eu fis"></span> <p class="my-auto moeda">EUR</p> <p class="cotacao my-auto">0,0000000</p></li>
          <li class="d-flex s-between"> <span class="fi fi-jp fis"></span> <p class="my-auto moeda">JPY</p> <p class="cotacao my-auto">0,0000000</p></li> 
          <li class="d-flex s-between"> <span class="fi fi-cn fis"></span> <p class="my-auto moeda">CNY</p> <p class="cotacao my-auto">0,0000000</p></li>
        </ul>
      </div>
    </div>

    <hr>

    <div>
      <form class="d-flex" style="justify-content: space-around; margin-right: 75%;" method="post">
        <div>
          <label for="data_pesquisa" class="d-flex"><p class="asterisco">*</p>Selecione a data:</label>
          <input type="date" name="data_pesquisa" id="data_pesquisa">
        </div>
        <div>
          <label for="texto_pesquisa" class="d-flex"><p class="asterisco">*</p>Pesquisa:</label>
          <input type="text" id="texto_pesquisa" name="texto_pesquisa" placeholder="Digite o NCM ou a Descrição">
        </div>
        <button type="submit" id="btn-search" class="btn btn-dark btn-sm"><i class="fa fa-search" aria-hidden="true"></i> Pesquisar</button>
      </form>
    </div>
  HTML;
 

  // Se houver resultados da consulta, exibe-os
  if (!empty($res)) {
    $current_secao = null;
    foreach ($res as $item) {
        // Se a seção atual for diferente da seção do item atual, exibe um novo título de seção
        if ($item['Secao'] != $current_secao) {
            $current_secao = $item['Secao'];
            echo "<h3 class='title-secao-lista'>Seção $current_secao</h3>";
        }
        
        // Exibe o detalhe do item atual    
        $ncmRow = $item['Codigo']; 
        echo "<p class='ncm-i-lista'><a href='index.php?pag=ncm/atributos_por_ncm&ncmwd=$ncmRow'>{$ncmRow}</a> {$item['Descricao']}</p>";
    } echo"<br>";
  } else {
    echo "Nenhum resultado encontrado.";
  }
        




?>

