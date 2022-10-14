<?php
session_start();
//PERM
require("./assets/php/scripts/perm.php");
//CONNECTION
require("./connection/connection.php");

$mysqli_query        = "SELECT Codigo FROM ordem_servicos WHERE motoID = {$_GET["motoID"]} and Codigo = '{$_GET["ordem"]}'";
$result              = mysqli_query($conn, $mysqli_query);
$codigo				 = mysqli_fetch_assoc($result)['Codigo'];


if (isset($_POST["item_tipo"])) {

	$mysqli_query = "INSERT INTO ";

	switch ($_POST["item_tipo"]) {
		case "peca":
			$mysqli_query .= "pecas (grupo,parte,item,quantidade,valor,motoID,ordem) ";
			$mysqli_query .= "VALUES ('" . $_POST["pecaGrupo"] . "','" . $_POST["pecaParte"] . "','" . $_POST["pecaItem"] . "','" . $_POST["pecaQuantidade"] . "','" . $_POST["pecaValor"] . "','" . $_GET["motoID"] . "','" . $_GET["ordem"] . "') ";
			var_dump($mysqli_query);
			break;

		case "servico":
			$mysqli_query .= "servicos (item,tipo,valor,motoID,ordem) ";
			$mysqli_query .= "VALUES ('" . $_POST["servicoItem"] . "','" . $_POST["servicoTipo"] . "','" . $_POST["servicoValor"] . "','" . $_GET["motoID"] . "','" . $_GET["ordem"] . "') ";
			var_dump($mysqli_query);
			break;

		case "adiantamento":
			$mysqli_query .= "adiantamento (descricao,valor,motoID,ordem) ";
			$mysqli_query .= "VALUES ('" . $_POST["adiantamentoDescricao"] . "','" . $_POST["adiantamentoValor"] . "','" . $_GET["motoID"] . "','" . $_GET["ordem"] . "') ";
			var_dump($mysqli_query);
			break;
	}

	mysqli_query($conn, $mysqli_query);
}
?>

<!DOCTYPE HTML>
<html>

<head>
	<title>Ordens de serviço</title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
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
						<?php
						$mysqli_query_adiantamento	= "SELECT * FROM adiantamento ";
						$mysqli_query_adiantamento	.= "WHERE ordem = '".$codigo."' ";
						$result              = mysqli_query($conn, $mysqli_query_adiantamento);

						$mysqli_query_pecas	= "SELECT * FROM pecas ";
						$mysqli_query_pecas	.= "WHERE ordem = '".$codigo."' ";
						$result2              = mysqli_query($conn, $mysqli_query);

						$mysqli_query_servicos	= "SELECT * FROM servicos ";
						$mysqli_query_servicos	.= "WHERE ordem = '".$codigo."' ";
						$result3              = mysqli_query($conn, $mysqli_query);

						$tablerows = mysqli_num_rows($result);
						$tablerows2 = mysqli_num_rows($result2);
						$tablerows3 = mysqli_num_rows($result3);
						if ($tablerows+$tablerows2+$tablerows3  > 0) {
						?>
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

									$total = 0;
									$pago = 0;
									$tdcount = 0;

									while ($item = mysqli_fetch_assoc($result)) {
										if ($item["servicoItem"] != null) {
											echo "<tr><td>" . $item["servicoItem"] . "</td>";
											echo "<td>" . $item["servicoTipo"] . "</td>";
											echo "<td>" . $item["servicoValor"] . "</td></tr>";
											$total += $item["servicoValor"];
											$tdcount++;
										}
										if ($item["produtoItem"] != null) {
											echo "<tr><td>" . $item["produtoItem"] . "</td>";
											echo "<td>" . $item["produtoGrupo"] . "/" . $item["produtoParte"] . "</td>";
											echo "<td>" . $item["produtoValor"] . " x" . $item["produtoQuantidade"] . "</td></tr>";
											$total += $item["produtoValor"] * $item["produtoQuantidade"];
											$tdcount++;
										}
										if ($item["adiantamentoDescricao"] != null) {
											echo "<tr><td>" . $item["adiantamentoDescricao"] . "</td>";
											echo "<td>Adiantamento</td>";
											echo "<td>" . $item["adiantamentoValor"] . "</td></tr>";
											$total += $item["adiantamentoValor"];
											$pago += $item["adiantamentoValor"];
											$tdcount++;
										}
									}
									if ($tdcount == 0) { // provisório ?
										echo "<tr><td>Nenhum</td><td>Item</td><td>Adicionado</td></tr>";
									}

									?>
								</tbody>
								<tfoot>
									<tr>
										<td colspan="1"></td>
										<td>Subtotal:</td>
										<td><?php echo $total - $pago ?></td>
									</tr>
									<tr>
										<td colspan="1"></td>
										<td>Pago:</td>
										<td class="pago"><?php echo  $pago > 0 ? $pago : "Nada" ?>
										</td>
									</tr>
									<tr>
										<td colspan="1"></td>
										<td>Total:</td>
										<td class="total"><?php echo $total ?></td>
									</tr>
								</tfoot>
							</table>
						<?php
						} else {
							echo "no results found";
						}
						?>
					</div>
					<?php
					if ($tablerows > 0) {
					?>
						<a href="#" class="button primary"><i class="fa-solid fa-file-pdf"></i> Fazer download em PDF</a>
					<?php
					}
					?>
					<a href="#add-item" class="button primary" data-toggle="modal" data-target="#add-item"><i class="fa-solid fa-plus"></i> Adicionar Item</a>
					<a href="#" class="button secondary">
						<?php
						//open or closed
						$mysqli_query       = "SELECT Aberto FROM ordem_servicos WHERE Codigo = '{$_GET["ordem"]}'";
						$result             = mysqli_query($conn, $mysqli_query);

						while ($ordem = mysqli_fetch_assoc($result)) {
							if ($ordem["Aberto"] == 1)
								echo "<i class='fa-solid fa-lock'></i> Fechar Ordem de serviço";
							else
								echo "<i class='fa-solid fa-lock-open'></i> Abrir Ordem de serviço";
						}
						?>
					</a>
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
					<!-- ADD MODAL -->
					<div id="add-item" class="modal fade">
						<div class="modal-dialog">
							<div class="modal-content">
								<form action="<?php echo $_SERVER['PHP_SELF'] . "?motoID=" . $_GET['motoID'] . "&ordem=" . $_GET['ordem']; ?>" method="POST" enctype="multipart/form-data">
									<div class="modal-header">
										<h4 class="modal-title">Adicionar item na tabela</h4>
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa-solid fa-circle-xmark"></i></button>
									</div>
									<div class="modal-body">
										<!-- Load Image (not required) -->
										<div class="form-group">
											<label>Imagem</label>
											<input type="file" name="addFoto">
										</div>
										</br>
										<!-- Choose item type -->
										<fieldset>
											<legend>Escolha o tipo de item:</legend>
											<input type="radio" id="adiantamento" name="item_tipo" value="adiantamento">
											<label for="adiantamento">Adiantamento</label>
											<input type="radio" id="peca" name="item_tipo" value="peca">
											<label for="peca">Peça</label>
											<input type="radio" id="servico" name="item_tipo" value="servico" checked>
											<label for="servico">Serviço</label>
										</fieldset>
										<!-- Adiantamento -->
										<div id="adiantamentoFormgroup" style="display:none">
											<div>
												<label>Descrição</label>
												<input type="text" name="adiantamentoDescricao">
											</div>
											<div>
												<label>Valor</label>
												<input type="text" name="adiantamentoValor">
											</div>
										</div>
										<!-- Peça -->
										<div id="pecaFormgroup" style="display:none">
											<div>
												<label>Grupo</label>
												<input type="text" name="pecaGrupo">
											</div>
											<div>
												<label>Parte</label>
												<input type="text" name="pecaParte">
											</div>
											<div>
												<label>Item</label>
												<input type="text" name="pecaItem">
											</div>
											<div>
												<label>Quantidade</label>
												<input type="number" name="pecaQuantidade">
											</div>
											<div>
												<label>Valor</label>
												<input type="text" name="pecaValor">
											</div>
										</div>
										<!-- Serviço -->
										<div id="servicoFormgroup" style="display:none">
											<div>
												<label>Item</label>
												<input type="text" name="servicoItem">
											</div>
											<div>
												<label>Tipo</label>
												<input type="text" name="servicoTipo">
											</div>
											<div>
												<label>Valor</label>
												<input type="text" name="servicoValor">
											</div>
										</div>
										<!-- Submit Add/Cancel  -->
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
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
	<script>
		$("#adiantamento").on("click", function() {
			//$("#tipo input").val("");
			$("#adiantamentoFormgroup").show();
			$("#adiantamentoFormgroup input").val("");

			$("#pecaFormgroup").hide();
			$("#pecaFormgroup input").val("");

			$("#servicoFormgroup").hide();
			$("#servicoFormgroup input").val("");
		});

		$("#servico").on("click", function() {
			$("#adiantamentoFormgroup").hide();
			$("#adiantamentoFormgroup input").val("");

			$("#pecaFormgroup").hide();
			$("#pecaFormgroup input").val("");

			$("#servicoFormgroup").show();
			$("#servicoFormgroup input").val("");
		});

		$("#peca").on("click", function() {
			$("#adiantamentoFormgroup").hide();
			$("#adiantamentoFormgroup input").val("");

			$("#pecaFormgroup").show();
			$("#pecaFormgroup input").val("");

			$("#servicoFormgroup").hide();
			$("#servicoFormgroup input").val("");
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