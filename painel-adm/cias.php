<?php 

	require_once("../conexao.php");
	require_once("verificar.php");
	$pagina = 'cias';

	require_once($pagina."/campos.php");

?>



<div class="col-md-12 my-3">
	<a href="#" onclick="inserir()" type="button" class="btn btn-dark btn-sm">Nova Cia Aérea</a>
</div>

<small>
	<div class="tabela bg-light" id="listar"></div>
</small>



<!-- ModalExcluir -->
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


	
	<!-- ModalDados -->
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

					<div class="mb-3">
						<label for="exampleFormControlInput1" class="form-label"><?php echo $campo1 ?></label>
						<input type="text" class="form-control" name="<?php echo $campo1 ?>" placeholder="<?php echo $campo1 ?>" id="<?php echo $campo1 ?>">
					</div>

					<div class="mb-3">
						<label for="exampleFormControlInput1" class="form-label"><?php echo $campo2 ?></label>
						<input type="text" class="form-control" name="<?php echo $campo2 ?>" placeholder="<?php echo $campo2 ?>" id="<?php echo $campo2 ?>">
					</div>

					<div class="mb-3">
						<label for="exampleFormControlInput1" class="form-label"><?php echo $campo3 ?></label>
						<input type="text" class="form-control" name="<?php echo $campo3 ?>" placeholder="<?php echo $campo3 ?>" id="<?php echo $campo3 ?>">
					</div>
									
					<div class="mb-3">
						<label for="exampleFormControlInput1" class="form-label"><?php echo $campo4 ?></label>
						<input type="text" class="form-control" name="<?php echo $campo4 ?>" placeholder="<?php echo $campo4 ?>" id="<?php echo $campo4 ?>">
					</div>

					<div class="mb-3">
						<label for="exampleFormControlInput1" class="form-label"><?php echo $campo5 ?></label>
						<input type="text" class="form-control" name="<?php echo $campo5 ?>" placeholder="<?php echo $campo5 ?>" id="<?php echo $campo5 ?>">
					</div>

					<div class="mb-3">
						<label for="exampleFormControlInput1" class="form-label"><?php echo $campo6 ?></label>
						<input type="text" class="form-control" name="<?php echo $campo1 ?>" placeholder="<?php echo $campo6 ?>" id="<?php echo $campo6 ?>">
					</div>

					<div class="mb-3">
						<label for="exampleFormControlInput1" class="form-label"><?php echo $campo7 ?></label>
						<input type="text" class="form-control" name="<?php echo $campo7 ?>" placeholder="<?php echo $campo7 ?>" id="<?php echo $campo7 ?>">
					</div>

					<div class="mb-3">
						<label for="exampleFormControlInput1" class="form-label"><?php echo $campo8 ?></label>
						<input type="text" class="form-control" name="<?php echo $campo8 ?>" placeholder="<?php echo $campo8 ?>" id="<?php echo $campo8 ?>">
					</div>

					<div class="mb-3">
						<label for="exampleFormControlInput1" class="form-label"><?php echo $campo9 ?></label>
						<input type="text" class="form-control" name="<?php echo $campo9 ?>" placeholder="<?php echo $campo9 ?>" id="<?php echo $campo9 ?>">
					</div>

					<div class="mb-3">
						<label for="exampleFormControlInput1" class="form-label"><?php echo $campo10 ?></label>
						<input type="text" class="form-control" name="<?php echo $campo10 ?>" placeholder="<?php echo $campo10 ?>" id="<?php echo $campo10 ?>">
					</div>

					<div class="mb-3">
						<label for="exampleFormControlInput1" class="form-label"><?php echo $campo11 ?></label>
						<input type="text" class="form-control" name="<?php echo $campo11 ?>" placeholder="<?php echo $campo11 ?>" id="<?php echo $campo11 ?>">
					</div>

					<div class="mb-3">
						<label for="exampleFormControlInput1" class="form-label"><?php echo $campo12 ?></label>
						<input type="text" class="form-control" name="<?php echo $campo12 ?>" placeholder="<?php echo $campo12 ?>" id="<?php echo $campo12 ?>">
					</div>

					<div class="mb-3">
						<label for="exampleFormControlInput1" class="form-label"><?php echo $campo1 ?></label>
						<input type="text" class="form-control" name="<?php echo $campo13 ?>" placeholder="<?php echo $campo13 ?>" id="<?php echo $campo13 ?>">
					</div>

					<div class="mb-3">
						<label for="exampleFormControlInput1" class="form-label"><?php echo $campo14 ?></label>
						<input type="text" class="form-control" name="<?php echo $campo14 ?>" placeholder="<?php echo $campo14 ?>" id="<?php echo $campo14 ?>">
					</div>

					<div class="mb-3">
						<label for="exampleFormControlInput1" class="form-label"><?php echo $campo15 ?></label>
						<input type="text" class="form-control" name="<?php echo $campo15 ?>" placeholder="<?php echo $campo15 ?>" id="<?php echo $campo15 ?>">
					</div>

					<div class="mb-3">
						<label for="exampleFormControlInput1" class="form-label"><?php echo $campo16 ?></label>
						<input type="text" class="form-control" name="<?php echo $campo16 ?>" placeholder="<?php echo $campo16 ?>" id="<?php echo $campo16 ?>">
					</div>

					<div class="mb-3">
						<label for="exampleFormControlInput1" class="form-label"><?php echo $campo17 ?></label>
						<input type="text" class="form-control" name="<?php echo $campo17 ?>" placeholder="<?php echo $campo17 ?>" id="<?php echo $campo17 ?>">
					</div>

					<div class="mb-3">
						<label for="exampleFormControlInput1" class="form-label"><?php echo $campo18 ?></label>
						<input type="text" class="form-control" name="<?php echo $campo18 ?>" placeholder="<?php echo $campo18 ?>" id="<?php echo $campo18 ?>">
					</div>

					<div class="mb-3">
						<label for="exampleFormControlInput1" class="form-label"><?php echo $campo19 ?></label>
						<input type="date" class="form-control" name="<?php echo $campo19 ?>" placeholder="<?php echo $campo19 ?>" id="<?php echo $campo19 ?>">
					</div>

					<div class="mb-3">
						<label for="exampleFormControlInput1" class="form-label"><?php echo $campo20 ?></label>
						<input type="text" class="form-control" name="<?php echo $campo20 ?>" placeholder="<?php echo $campo20 ?>" id="<?php echo $campo20 ?>">
					</div>

					<div class="mb-3">
						<label for="exampleFormControlInput1" class="form-label"><?php echo $campo21 ?></label>
						<select class="form-control" name="<?php echo $campo21 ?>" placeholder="<?php echo $campo21 ?>" id="<?php echo $campo21 ?>">
						<?php 

							$query = $pdo->query("SELECT CODIGO_NUMERICO, NOME FROM paises ORDER BY NOME");
							$res = $query->fetchAll(PDO::FETCH_ASSOC);
							for($i=1; $i <= @count($res); $i++){
								foreach ($res[$i] as $key => $value){ }
										$cod =  $res[$i]['CODIGO_NUMERICO'];
										$countryName =  $res[$i]['NOME'];

						?>

									<option value="<?php echo $cod ?>"><?php echo $cod . ' - ' . $countryName ?></option>;
									
							<?php } ?>

						
					</select>
					</div>
					
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


	
<script type="text/javascript">var pag = "<?=$pagina?>"</script>
<script src="../js/ajax.js"></script>


<script>
	$(document).ready(function() {
		$('#<?=$campo15?>').mask('00.000.000/0000-00');
		

	});
</script>