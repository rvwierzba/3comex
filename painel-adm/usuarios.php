<?php 

	require_once("../conexao.php");
	require_once("verificar.php");
	require_once("../painel-adm/usuarios/campos.php");


?>

<div class="col-md-12 my-3">
	<a href="#" onclick="inserir()" type="button" class="btn btn-dark btn-sm">Novo Usuário</a>
</div>

<small>
	<div class="tabela bg-light" id="listar"></div>
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
						<label for="exampleFormControlInput1" class="form-label"><?php echo $campo1 ?></label>
						<input type="text" class="form-control" name="<?php echo $campo1 ?>" placeholder="<?php echo $campo1 ?>" id="<?php echo $campo1 ?>" required>
					</div>

					<div class="mb-3">
						<label for="exampleFormControlInput1" class="form-label"><?php echo $campo2 ?></label>
						<input type="email" class="form-control" name="<?php echo $campo2 ?>" placeholder="<?php echo $campo2 ?>" id="<?php echo $campo2 ?>" required>
					</div>

					<div class="mb-3">
						<label for="exampleFormControlInput1" class="form-label"><?php echo $campo3 ?></label>
						<input type="password" class="form-control" name="<?php echo htmlspecialchars($campo3); ?>" placeholder="Senha" id="<?php echo htmlspecialchars($campo3); ?>" required>
					</div>

					
					<div class="mb-3">
						<label for="exampleFormControlInput1" class="form-label">Nível </label>
						<select class="form-select" aria-label="Default select example" name="<?php echo $campo4 ?>" id="<?php echo $campo4 ?>">
							<?php 
							$query = $pdo->query("SELECT * FROM niveis order by nivel asc");
							$res = $query->fetchAll(PDO::FETCH_ASSOC);
							for($i=0; $i < @count($res); $i++){
								foreach ($res[$i] as $key => $value){	}
									$id_item = $res[$i]['id'];
									$nome_item = $res[$i]['nivel'];
								?>
								<option value="<?php echo $nome_item ?>"><?php echo $nome_item ?></option>

							<?php } ?>


						</select>
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


<script type="text/javascript">var pag = "<?=$pagina?>"</script>
<script src="../js/ajax.js"></script>


<script type="text/javascript">
$(document).ready(function() {
    // Tentativa de garantir que o preventDefault seja chamado para este formulário específico,
    // independentemente de como o ajax.js compartilhado o trata.
    $("#form").on('submit', function(event) {
        console.log("Handler de submit ADICIONAL (em usuarios.php) foi chamado.");

        if (event.isDefaultPrevented && event.isDefaultPrevented()) { // Verifica se já foi prevenido
            console.log("Comportamento padrão do formulário JÁ foi prevenido (provavelmente pelo ajax.js ou outro script).");
        } else {
            console.log("Comportamento padrão do formulário NÃO foi prevenido ainda. Prevenindo agora em usuarios.php...");
            event.preventDefault();
        }
        // Este handler apenas tenta garantir o preventDefault.
        // A chamada AJAX em si ainda será feita pelo seu ajax.js compartilhado.
    });

	// Garante que o comportamento padrão de submit seja prevenido para o formulário de exclusão
    $("#form-excluir").on('submit', function(event) {
        // console.log("Handler de submit adicional (em usuarios.php) para #form-excluir."); // Para debug
        if (!(event.isDefaultPrevented && event.isDefaultPrevented())) { // Se não foi prevenido ainda
            // console.log("Prevenindo default para #form-excluir em usuarios.php..."); // Para debug
            event.preventDefault();
        }
    });

});
</script>


