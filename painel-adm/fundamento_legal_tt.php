<?php
  require_once("../conexao.php");
  require_once("../painel-adm/fundamento_legal_tt/campos.php");
?>

<div class="col-md-12 my-3">
	<a href="#" onclick="inserir()" type="button" class="btn btn-dark btn-sm">Novo Fundamento Legal</a>
	<a href="index.php?pag=fundamento_legal_tt/atualizar_tab" type="button" class="btn btn-primary btn-sm">Atualizar tabela</a>
</div>

<small>
	<div class="tabela bg-light" id="listar">
	</div>
</small>

<!-- Modal Inserir/Editar -->
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
						<label for="CODIGO" class="form-label">CÓD.</label>
						<input type="text" class="form-control" name="CODIGO" id="CODIGO">
					</div>

					<div class="mb-3">
						<label for="DESCRICAO" class="form-label">Descrição</label>
						<input type="text" class="form-control" name="DESCRICAO VARCHAR" id="DESCRICAO VARCHAR">
					</div>

					<div class="mb-3">
						<label for="MNEMONICO_SISTEMA_CONTROLE" class="form-label">Mnemonico Sistema Controle</label>
						<input type="text" class="form-control" name="MNEMONICO_SISTEMA_CONTROLE" id="MNEMONICO_SISTEMA_CONTROLE">
					</div>

					<div class="mb-3">
						<label for="CODIGO_BENEFICIO_FISCAL_SISEN" class="form-label">Código Benefício Fiscal SISEN</label>
						<input type="text" class="form-control" name="CODIGO_BENEFICIO_FISCAL_SISEN" id="CODIGO_BENEFICIO_FISCAL_SISEN">
					</div>

					<div class="mb-3">
						<label for="IN_PERMITIR_DUIMP_VIN_TERCEIROS" class="form-label">Permitir DUIMP de terceiros</label>
						<input type="text" class="form-control" name="IN_PERMITIR_DUIMP_VIN_TERCEIROS" id="IN_PERMITIR_DUIMP_VIN_TERCEIROS">
					</div>

					<div class="mb-3">
						<label for="IN_PERMITE_REGISTRO_PESSOA_FISICA" class="form-label">Permitir registro de pessoa física</label>
						<input type="text" class="form-control" name="IN_PERMITE_REGISTRO_PESSOA_FISICA" id="IN_PERMITE_REGISTRO_PESSOA_FISICA">
					</div>

					<div class="mb-3">
						<label for="DATA_INICIO" class="form-label">Data Início</label>
						<input type="date" class="form-control" name="DATA_INICIO" id="DATA_INICIO">
					</div>

					<div class="mb-3">
						<label for="DATA_FIM" class="form-label">Data Fim</label>
						<input type="date" class="form-control" name="DATA_FIM" id="DATA_FIM">
					</div>

					<div class="mb-3">
						<label for="INTERNO_VERSAO" class="form-label">Versão (interno)</label>
						<input type="text" class="form-control" name="INTERNO_VERSAO" id="INTERNO_VERSAO">
					</div>

					<small><div id="mensagem" align="center"></div></small>

					<input type="hidden" class="form-control" name="id" id="id">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btn-fechar">Fechar</button>
					<button type="submit" class="btn btn-primary">Salvar</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Modal Excluir -->
<div class="modal fade" id="modalExcluir" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel"><span id="tituloModal">Excluir Registro</span></h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form id="form-excluir" method="post">
				<div class="modal-body">

					Deseja realmente excluir este registro: <span id="nome-excluido"></span>?

					<small><div id="mensagem-excluir" align="center"></div></small>

					<input type="hidden" class="form-control" name="id-excluir" id="id-excluir">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btn-fechar-excluir">Fechar</button>
					<button type="submit" class="btn btn-danger">Excluir</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Modal Mostrar Dados -->
<div class="modal fade" id="modalDados" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel"><?php echo $campo1 ?>: <span id="campo1"></span></h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			
			<div class="modal-body">
				<small>
					<span><b><?php echo $campo1 ?>: </b><span id="campo1"></span></span>
					<hr style="margin:6px;">

					<span><b><?php echo $campo2 ?>: </b><span id="campo2"></span></span>
					<hr style="margin:6px;">

					<span><b><?php echo $campo3 ?>: </b><span id="campo3"></span></span>
					<hr style="margin:6px;">

					<span><b><?php echo $campo4 ?>: </b><span id="campo4"></span></span>
					<hr style="margin:6px;">

					<span><b><?php echo $campo5 ?>: </b><span id="campo5"></span></span>
					<hr style="margin:6px;">

					<span><b><?php echo $campo6 ?>: </b><span id="campo6"></span></span>
					<hr style="margin:6px;">

					<span><b><?php echo $campo7 ?>: </b><span id="campo7"></span></span>
					<hr style="margin:6px;">

					<span><b><?php echo $campo8 ?>: </b><span id="campo8"></span></span>
					<hr style="margin:6px;">

					<span><b><?php echo $campo9 ?>: </b><span id="campo9"></span></span>
				</small>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">var pag = "<?=$pagina?>"</script>
<script src="../js/ajax.js"></script>
