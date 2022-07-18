<?php
session_start();
//PERM
require("./assets/php/scripts/perm.php");
//CONNECTION
require("./connection/connection.php");

$mysqli_query        = "SELECT Codigo FROM ordem_servicos WHERE motoID = {$_GET["motoID"]} and Codigo = '{$_GET["ordem"]}'";
$result              = mysqli_query($conn, $mysqli_query);
$codigo				 = mysqli_fetch_assoc($result)['Codigo'];

?>

<!DOCTYPE HTML>
<!--
	Landed by HTML5 UP
	html5up.net | @ajlkn
	Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
-->
<html>

<head>
	<title>Ordens de serviço</title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
	<!-- CSS only -->
	<!-- CSS only -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
	<link rel="stylesheet" href="assets/css/main.css" />
	<noscript>
		<link rel="stylesheet" href="assets/css/noscript.css" />
	</noscript>
</head>

<body class="is-preload">
	<div id="page-wrapper">

		<!-- Header -->
		<header id="header">
			<h1 id="logo">
				<a href="index.php"><img src="./assets/css/images/logo-branco-crop.png" style="height:60px;width:180px;"></a>
			</h1>
			<nav id="nav">
				<ul>
					<li><a href="tabelaMotos.php">Tabela Motocicletas</a></li>
					<li><a href="tabelaClientes.php">Tabela Clientes</a></li>
					<li><a href="tabelaServicos.php">Tabela Serviços</a></li>
					<li><a href="tabelaPecas.php">Tabela Peças</a></li>
				</ul>
			</nav>
		</header>

		<!-- Main -->
		<style>
			/* table theme */
			table.alt tbody td {
				text-align: center;
			}

			table.alt tfoot td {
				padding: 1px;
				text-align: center;
			}

			table.alt tfoot td.pago {
				color: red;
			}

			table.alt tfoot td.total {
				color: greenyellow;
			}

			i.fa-solid {
				padding-right: 15px;
			}

			/* modal add-item theme */
			#add-item .close {
				background: none;
				font-size: 25px;
				color: #e44c65;
				border: none;
			}

			#add-item input {
				background-color: white;
				color: black;
			}
			#add-item input:focus {
				background-color: #ababb3;
				border: 1px solid black;
			}

			#add-item input[type=file] {
				background-color: #818185;
				border: 1 solid white;
			}

			#add-item .btn-success {
				background-color: #e44c65;

			}
			/* #add-item input[type=submit]:focus {
				background-color: #db5e73;
			} */

			#add-item .btn-default {
				background-color: #1c1d26;
			}

			.modal-header {
				background-color: #272832;
			}

			.modal-body {
				background-color: #2d2e36;
			}

			.modal-content {
				background-color: #272832;
			}
		</style>
		<div id="main" class="wrapper style1">
			<div class="container">
				<header class="major">
					<h2>Ordem de Serviço Nº <?php echo $codigo ?></h2>
				</header>
				<section>
					<div class="table-wrapper">
						<table class="alt">
							<thead>
								<tr>
									<th>Descrição</th>
									<th>Tipo</th>
									<th>Preço</th>
								</tr>
							</thead>
							<tbody>
								<?php
								//ADD ITEMS

								?>
								<tr>
									<td>Fluido de Freio</td>
									<td>Substituição</td>
									<td>49,99</td>
								</tr>
								<tr>
									<td>Líquido de Arrefecimento</td>
									<td>Substituição</td>
									<td>29,99</td>
								</tr>
								<tr>
									<td>Pintura</td>
									<td>Polimento</td>
									<td>49,99</td>
								</tr>
								<tr>
									<td>Sapata de Freio</td>
									<td>Substituição</td>
									<td>129,99</td>
								</tr>
								<tr>
									<td>Bateria</td>
									<td>Recarga</td>
									<td>29,99</td>
								</tr>
								<tr>
									<td>Pintura e Sapata de freio</td>
									<td>Adiantamento</td>
									<td>-150,00</td>
								</tr>
							</tbody>
							<tfoot>
								<tr>
									<td colspan="1"></td>
									<td>Subtotal:</td>
									<td>289,95</td>
								</tr>
								<tr>
									<td colspan="1"></td>
									<td>Pago:</td>
									<td class="pago">-150,00</td>
								</tr>
								<tr>
									<td colspan="1"></td>
									<td>Total:</td>
									<td class="total">139,95</td>
								</tr>
							</tfoot>
						</table>
					</div>
					<a href="#" class="button primary"><i class="fa-solid fa-file-pdf"></i> Fazer download em PDF</a>
					<a href="#add-item" class="button primary" data-toggle="modal" data-target="#add-item"><i class="fa-solid fa-plus"></i> Adicionar Item</a>
					<a href="#" class="button secondary"> <i class="fa-solid fa-lock"></i>Fechar Ordem de serviço</a>
					<hr />
				</section>

				<!-- Detalhes 
				<section>
					<h3>Detalhes: </h3>
					<p>O <b>Motor de Partida</b> apresenta falhas de funcionamento.</p>
					<p>O Código da <b>Vela de ignição</b> desta motocicleta está diferente do especificado.</p>
					<p>O <b>Óleo de Transmissão</b> deste motor já passou do tempo de uso recomendado.</p>
					<a href="#" class="button primary">Editar detalhes</a>
					<hr />
				</section> -->

				<!-- Info Motocicleta -->
				<?php
				$mysqli_query        = "SELECT * FROM motocicletas WHERE motoID = {$_GET["motoID"]}";
				$result              = mysqli_query($conn, $mysqli_query);

				$info_moto = mysqli_fetch_assoc($result);
				?>
				<section id="additional-info">
					<h3>Informações Adicionais: </h3>
					<span class="image right" style="height:200px;width:250px;"><img src="<?php echo $info_moto["foto"] ?>" alt="" /></span>
					<p>
						<bold>Proprietario:</bold> <?php echo $info_moto["proprietario"] ?>
						<br>
						<bold>Endereço:</bold> <?php echo $info_moto["endereco"] ?>
						<br>
						<bold>Marca:</bold> <?php echo $info_moto["marca"] ?>
						<br>
						<bold>Placa:</bold> <?php echo $info_moto["placa"] ?>
						<br>
						<bold>Ano:</bold> <?php echo $info_moto["ano"] ?>
						<br>
						<bold>Modelo:</bold> <?php echo $info_moto["modelo"] ?>
						<br>
						<bold>KM:</bold> <?php echo $info_moto["km"] ?>
					</p>
					</hr>
					<!-- Modals (ADD BOOTSTRAP MODAL WAY) -->
					<!-- ADD MODAL -->
					<div id="add-item" class="modal fade">
						<div class="modal-dialog">
							<div class="modal-content">
								<form name="add-item" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST" enctype="multipart/form-data">
									<div class="modal-header">
										<h4 class="modal-title">Adicionar item na tabela</h4>
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa-solid fa-circle-xmark"></i></button>
									</div>
									<div class="modal-body">
										<div class="form-group">
											<label>Imagem</label>
											<input type="file" name="addFoto">
										</div>
										</br>
										<fieldset>
											<legend>Escolha o tipo de item:</legend>
											<input type="radio" id="adiantamento" name="item_tipo" value="adiantamento">
											<label for="adiantamento">Adiantamento</label>
											<input type="radio" id="peca" name="item_tipo" value="peca">
											<label for="peca">Peça</label>
											<input type="radio" id="servico" name="item_tipo" value="servico" checked>
											<label for="servico">Serviço</label>
										</fieldset>
										<div class="form-group col-xs-2" id="tipo">
											<label>Tipo</label>
											<input type="text" name="addMarca" class="form-control" required>
										</div>
										<div class="form-group">
											<label>Descrição</label>
											<input type="text" name="addMarca" class="form-control" required>
										</div>
										<div class="form-group">
											<label>Preço</label>
											<input type="text" name="addPlaca" class="form-control" required>
										</div>
										<div class="modal-footer">
											<input type="button" class="btn btn-default" data-dismiss="modal" value="Cancelar">
											<input type="submit" class="btn btn-success" value="Adicionar">
										</div>
								</form>
							</div>
						</div>
					</div>
				</section>
			</div>
		</div>


		<?php
		require("./assets/php/includes/main/footer.php");
		?>
	</div>
	<!-- Scripts -->
	<script src="assets/js/global/jquery.min.js"></script>
	<!-- <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script> -->
	<!-- <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script> -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
	<script>
		$("#adiantamento").on("click", function(){
			$("#tipo").hide();
		});
		$("#item").on("click", function(){
			$("#tipo").show();
		});
		$("#peca").on("click", function(){ 
			$("#tipo").show();
		});
	</script>
	<!-- <script src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script> -->
	<!-- <script src="assets/js/global/jquery.scrolly.min.js"></script>
	<script src="assets/js/global/jquery.dropotron.min.js"></script>
	<script src="assets/js/global/jquery.scrollex.min.js"></script>
	<script src="assets/js/global/browser.min.js"></script>
	<script src="assets/js/global/breakpoints.min.js"></script>
	<script src="assets/js/global/util.js"></script>
	<script src="assets/js/main.js"></script> -->

	<!-- <script src="node_modules\bootstrap\dist\js\bootstrap.min.js"></script> -->

</html>