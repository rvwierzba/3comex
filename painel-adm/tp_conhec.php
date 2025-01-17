<?php
  require_once("../conexao.php");
  require_once("../painel-adm/tp_conhec/campos.php");
  
?>


<div class="col-md-12 my-3">
	<a href="#" onclick="inserir()" type="button" class="btn btn-dark btn-sm">Novo Tipo de Conhecimento</a>
	<a href="index.php?pag=tp_conhec/atualizar_tab" type="button" class="btn btn-primary btn-sm">Atualizar tabela</a>
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
						<label for="exampleFormControlInput1" class="form-label">CÓD.</label>
						<input type="text" class="form-control" name="<?php echo $campo1 ?>" placeholder="<?php echo $campo1 ?>" id="<?php echo $campo1 ?>" required>
					</div>

          <div class="mb-3">
						<label for="exampleFormControlInput1" class="form-label">Descrição</label>
						<input type="text" class="form-control" name="<?php echo $campo2 ?>" placeholder="<?php echo $campo2 ?>" id="<?php echo $campo2 ?>" required>
					</div>

          <div class="mb-3">
						<label for="exampleFormControlInput1" class="form-label">Indicador tipo básico</label>
						<input type="text" class="form-control" name="<?php echo $campo3 ?>" placeholder="<?php echo $campo3 ?>" id="<?php echo $campo3 ?>" required>
					</div>

          <div class="mb-3">
						<label for="exampleFormControlInput1" class="form-label">Data Início</label>
						<input type="date" class="form-control" name="<?php echo $campo4 ?>" placeholder="<?php echo $campo4 ?>" id="<?php echo $campo4 ?>" required>
					</div>

          <div class="mb-3">
						<label for="exampleFormControlInput1" class="form-label">Data Fim</label>
						<input type="date" class="form-control" name="<?php echo $campo5 ?>" placeholder="<?php echo $campo5 ?>" id="<?php echo $campo5 ?>" required>
					</div>

          <div class="mb-3">
						<label for="exampleFormControlInput1" class="form-label">Versão (interno)</label>
						<input type="text" class="form-control" name="<?php echo $campo6 ?>" placeholder="<?php echo $campo6 ?>" id="<?php echo $campo6 ?>" required>
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

            <hr style="margin:6px;">

            <span><b><?php echo $campo3 ?>: </b><span id="campo3"></span></span>
            
            <hr style="margin:6px;">

            <span><b><?php echo $campo4 ?>: </b><span id="campo4"></span></span>

            <hr style="margin:6px;">

            <span><b><?php echo $campo5 ?>: </b><span id="campo5"></span></span>
            
            <hr style="margin:6px;">

            <span><b><?php echo $campo6 ?>: </b><span id="campo6"></span></span>
            
           
				</small>

			</div>
		</div>
	</div>
</div>



<script type="text/javascript">var pag = "<?=$pagina?>"</script>
<script src="../js/ajax.js"></script>