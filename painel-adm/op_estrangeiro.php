<?php
  require_once("../conexao.php");
  require_once("../painel-adm/op_estrangeiro/campos.php");
  
?>


<div class="col-md-12 my-3">
	<a href="#" onclick="inserir()" type="button" class="btn btn-dark btn-sm">Novo Operador Est.</a>
</div>

<small>
	<div class="tabela bg-light" id="listar">

	</div>
</small>

<!-- Modal -->
<div class="modal fade" id="modalForm" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel"><span id="tituloModal">Inserir Registro</span></h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form id="form" method="post">
				<div class="modal-body">

					<div class="mb-3">
						<label for="exampleFormControlInput1" class="form-label">CPF/CNPJ Raíz</label>
						<input type="text" class="form-control" name="<?php echo $campo1 ?>" placeholder="CPF/CNPJ Raíz" id="<?php echo $campo1 ?>" required>
					</div>

          <div class="mb-3">
						<label for="exampleFormControlInput1" class="form-label">Tin</label>
						<input type="text" class="form-control" name="<?php echo $campo2 ?>" placeholder="Tin" id="<?php echo $campo2 ?>" required>
					</div>
									
          <div class="mb-3">
						<label for="exampleFormControlInput1" class="form-label">CÓD.</label>
						<input type="text" class="form-control" name="<?php echo $campo3 ?>" placeholder="Código" id="<?php echo $campo3 ?>" required>
					</div>

          <div class="mb-3">
						<label for="exampleFormControlInput1" class="form-label">CÓD. (interno)</label>
						<input type="text" class="form-control" name="<?php echo $campo4 ?>" placeholder="Código interno" id="<?php echo $campo4 ?>" required>
					</div>

          <div class="mb-3">
						<label for="exampleFormControlInput1" class="form-label">Nome</label>
						<input type="text" class="form-control" name="<?php echo $campo5 ?>" placeholder="Nome" id="<?php echo $campo5 ?>" required>
					</div>

          <div class="mb-3">
						<label for="exampleFormControlInput1" class="form-label">País de Origem</label>
						<input type="text" class="form-control" name="<?php echo $campo6 ?>" placeholder="País de Origem" id="<?php echo $campo6 ?>" required>
					</div>

					<small><div id="mensagem" align="center"></div></small>

					<input type="hidden" class="form-control" name="id"  id="id">


				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btn-fechar">Fechar</button>
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
				<h5 class="modal-title" id="exampleModalLabel"><?php echo $campo1 ?>: <span id="campo1"></span></h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			
					<div class="modal-body">
					<small>
						<span><b><?php echo $campo2 ?>: </b><span id="campo2"></span></span>
						<span class="mx-4"><b>Tin:</b> <span id="campo4" ></span>
					</span>	
					<hr style="margin:6px;">

					<!--<span><b><?php echo $campo5 ?>:</b> <span id="campo5"></span></span> -->
					<span><b><?php echo $campo5 ?>:</b> <span id="campo5" ></span>
					</span>	
					<hr style="margin:6px;">

					<span><b><?php echo $campo6 ?>:</b> <span id="campo6" ></span>
					</span>	
					<hr style="margin:6px;">
					

				</small>

			</div>
		</div>
	</div>
</div>



<script type="text/javascript">var pag = "<?=$pagina?>"</script>
<script src="../js/ajax.js"></script>