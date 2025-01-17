<?php
  require_once("../conexao.php");
  require_once("../painel-adm/produto/campos.php");
  
	$pathConsulta = 'ncm/consulta';
?>

<style>
    .suggestion-list {
      border: 1px solid #ccc;
      border-radius: 4px;
      max-height: 150px;
      overflow-y: auto;
      margin-top: 5px;
      background-color: #fff;
    }
    .suggestion-item {
      padding: 8px 12px;
      cursor: pointer;
    }
    .suggestion-item:hover {
      background-color: #f1f1f1;
    }
  </style>

<class class="2btns d-flex">
	
	<div class="col-md-6 my-3">
		<a href="#" onclick="inserir()" type="button" class="btn btn-dark btn-sm">Novo Produto</a>
	</div>

	<div class="col-md-6 my-3">
		<a href="index.php?pag=<?php echo $pathConsulta ?>" type="button" class="btn btn-primary btn-sm">Consultar Classificação (NCM)</a>
	</div>

</class>


<small>
	<div class="tabela bg-light" id="listar">

	</div>
</small>

<div class="modal fade" id="modalForm" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><span id="tituloModal">Inserir Registro</span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="form" method="post">
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="<?php echo $campo1 ?>" class="form-label">Código</label>
              <input type="text" class="form-control" name="<?php echo $campo1 ?>" placeholder="Digite o Código" id="<?php echo $campo1 ?>" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="<?php echo $campo2?>" class="form-label">Código Interno</label>
              <input type="text" class="form-control" name="<?php echo $campo2?>" placeholder="Digite o Código Interno" id="<?php echo $campo2?>" required>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="<?php echo $campo3 ?>" class="form-label">Descrição</label>
              <input type="text" class="form-control" name="<?php echo $campo3 ?>" placeholder="Descrição do produto" id="<?php echo $campo3 ?>" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="<?php echo $campo4 ?>" class="form-label">Denominação</label>
              <input type="text" class="form-control" name="<?php echo $campo4 ?>" placeholder="Denominação" id="<?php echo $campo4 ?>" required>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="<?php echo $campo5 ?>" class="form-label">NCM</label>
              <input type="text" class="form-control" name="<?php echo $campo5 ?>" placeholder="Pesquise por Código ou Descrição" id="<?php echo $campo5 ?>" required onkeyup="pesquisar('ncm', 'ncm')">
              <div id="lista_ncm" class="suggestion-list"></div>
            </div>
            <div class="col-md-6 mb-3">
              <label for="<?php echo $campo6 ?>" class="form-label">Unidade de Medida</label>
              <input type="text" class="form-control" name="<?php echo $campo6 ?>" placeholder="Unidade de Medida ex: (KG)" id="<?php echo $campo6 ?>" required>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="<?php echo $campo7 ?>" class="form-label">Valor Unitário</label>
              <input type="text" class="form-control" name="<?php echo $campo7 ?>" placeholder="Valor Unitário" id="<?php echo $campo7 ?>" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="<?php echo $campo8 ?>" class="form-label">Situação</label>
              <input type="text" class="form-control" name="<?php echo $campo8 ?>" placeholder="Situação" id="<?php echo $campo8 ?>" required>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="<?php echo $campo9 ?>" class="form-label">Última Alteração (Início)</label>
              <input type="text" class="form-control" name="<?php echo $campo9 ?>" placeholder="Data da última alteração (Início)" id="<?php echo $campo9 ?>" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="<?php echo $campo10 ?>" class="form-label">Última Alteração (Fim)</label>
              <input type="text" class="form-control" name="<?php echo $campo10 ?>" placeholder="Data da última alteração (Data limite)" id="<?php echo $campo10 ?>" required>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="<?php echo $campo11 ?>" class="form-label">País de Origem</label>
              <input type="text" class="form-control" name="<?php echo $campo11 ?>" placeholder="Pesquise por Nome" id="<?php echo $campo11 ?>" required onkeyup="pesquisar('pais_origem', 'paises')">
              <div id="lista_pais_origem" class="suggestion-list"></div>
            </div>
            <div class="col-md-6 mb-3">
              <label for="<?php echo $campo12 ?>" class="form-label">CPF / CNPJ (Fabricante)</label>
              <input type="text" class="form-control" name="<?php echo $campo12 ?>" placeholder="CPF ou CNPJ" id="<?php echo $campo12 ?>" required>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="<?php echo $campo13 ?>" class="form-label">CPF / CNPJ (Raiz)</label>
              <input type="text" class="form-control" name="<?php echo $campo13 ?>" placeholder="CPF ou CNPJ" id="<?php echo $campo13 ?>" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="<?php echo $campo14 ?>" class="form-label">Modalidade</label>
              <input type="text" class="form-control" name="<?php echo $campo14 ?>" placeholder="Modalidade" id="<?php echo $campo14 ?>" required>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="<?php echo $campo15?>" class="form-label">Data (Referência)</label>
              <input type="text" class="form-control" name="<?php echo $campo15?>" placeholder="Data (Ref)" id="<?php echo $campo15?>" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="<?php echo $campo16?>" class="form-label">Operador Estrangeiro</label>
              <input type="text" class="form-control" name="<?php echo $campo16?>" placeholder="Pesquise por Código ou Nome" id="<?php echo $campo16?>" required onkeyup="pesquisar('operador_estrangeiro_codigo', 'op_estrangeiro')">
              <div id="lista_operador_estrangeiro_codigo" class="suggestion-list"></div>
            </div>
          </div>
          <small><div id="mensagem" align="center"></div></small>
          <input type="hidden" class="form-control" name="id" id="id">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Salvar</button>
        </div>
      </form>
    </div>
  </div>
</div>




	<!-- Modal -->
	<div class="modal fade" id="modalExcluir" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel"><span id="tituloModal">Excluir Registro</span></h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<form id="form-excluir" method="post">
					<div class="modal-body">

						Deseja Realmente excluir este Registro: <span id="nome-excluido"></span>?

						<small><div id="mensagem-excluir" align="center"></div></small>

						<input type="hidden" class="form-control" name="id-excluir"  id="id-excluir">


					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btn-fechar-excluir">Fechar</button>
						<button type="submit" class="btn btn-danger">Excluir</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Modal Dados -->
<div class="modal fade" id="modalDados" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Código: <span id="<?php echo $campo1 ?>"></span></h5>
 				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			
			<div class="modal-body">
				<small>
					<span><b>Código Interno: </b><span id="<?php echo $campo2 ?>"></span></span>
					<hr style="margin:6px;">

					<span><b>Descrição: </b><span id="<?php echo $campo3 ?>"></span></span>
					<hr style="margin:6px;">

					<span><b>Denominação: </b><span id="<?php echo $campo4 ?>"></span></span>
					<hr style="margin:6px;">

					<span><b>NCM: </b><span id="<?php echo $campo5 ?>" placeholder="Pesquise por Código ou Descrição" onkeyup="pesquisar('ncm', 'ncm')"></span></span>
					<hr style="margin:6px;">

					<span><b>Unidade de Medida: </b><span id="<?php echo $campos6 ?>"></span></span>
					<hr style="margin:6px;">

					<span><b>Valor Unitário: </b><span id="<?php echo $campo7 ?>"></span></span>
					<hr style="margin:6px;">

					<span><b>Situação: </b><span id="<?php echo $campo8 ?>"></span></span>
					<hr style="margin:6px;">

					<span><b>Última Alteração (Início): </b><span id="<?php echo $campo9 ?>"></span></span>
					<hr style="margin:6px;">

					<span><b>Última Alteração (Fim): </b><span id="<?php echo $campo10?>"></span></span>
					<hr style="margin:6px;">

					<span><b>País de Origem: </b><span id="<?php echo $campo11 ?>" placeholder="Pesquise por Nome" onkeyup="pesquisar('pais_origem', 'paises')"></span></span>
					<hr style="margin:6px;">

					<span><b>CPF / CNPJ (Fabricante): </b><span id="<?php echo $campo12 ?>"></span></span>
					<hr style="margin:6px;">

					<span><b>CPF / CNPJ (Raiz): </b><span id="<?php echo $campo13 ?>"></span></span>
					<hr style="margin:6px;">

					<span><b>Modalidade: </b><span id="<?php echo $campo14 ?>"></span></span>
					<hr style="margin:6px;">

					<span><b>Data Referência: </b><span id="<?php echo $campo15 ?>"></span></span>
					<hr style="margin:6px;">

					<span><b>Operador Estrangeiro: </b><span id="<?php echo $campo16 ?>" placeholder="Pesquise por Código ou Nome" onkeyup="pesquisar('operador_estrangeiro_codigo', 'op_estrangeiro')"></span></span>
				</small>
			</div>
		</div>
	</div>
</div>



<script type="text/javascript">var pag = "<?=$pagina?>"</script>
<script src="../js/ajax.js"></script>

<script>
	function pesquisar(campo, tabela) {
			let query = document.getElementById(campo).value;
			if (query.length > 2) {
				let xhr = new XMLHttpRequest();
				xhr.onreadystatechange = function() {
					if (this.readyState == 4 && this.status == 200) {
						document.getElementById('lista_' + campo).innerHTML = this.responseText;
						let items = document.querySelectorAll('#lista_' + campo + ' div');
						items.forEach(function(item) {
							item.classList.add('suggestion-item');
						});
					}
				};
				xhr.open("POST", "produto/pesquisas/" + tabela + ".php", true);
				xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xhr.send("query=" + query);
			} else {
				document.getElementById('lista_' + campo).innerHTML = "";
			}
		}

		function selecionarItem(campo, valor) {
			document.getElementById(campo).value = valor;
			document.getElementById('lista_' + campo).innerHTML = "";
		}
</script>