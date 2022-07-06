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
					<li><a href="#">Tabela Motocicletas</a></li>
					<li><a href="#">Tabela Clientes</a></li>
					<li><a href="#">Tabela Serviços</a></li>

				</ul>
			</nav>
		</header>

		<!-- Main -->
		<style>
			table.alt tbody td {
				text-align: center;
			}

			table.alt tfoot td {
				padding: 1px;
				text-align: center;
			}
			table.alt tfoot td.pago {
				color:red;
			}
			table.alt tfoot td.total {
				color: greenyellow;
			}
			i.fa-solid {
				padding-right: 15px;
			}
		</style>
		<div id="main" class="wrapper style1">
			<div class="container">
				<header class="major">
					<h2>Ordem de Serviço Nº 101/2022</h2>
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

					<a href="#" class="button primary"><i class="fa-solid fa-plus"></i> Adicionar serviço</a>
					<a href="#" class="button primary"><i class="fa-solid fa-plus"></i> Adicionar adiantamento</a>
					<a href="#" class="button secondary"> <i class="fa-solid fa-lock"></i>Fechar Ordem de serviço</a>
					<hr />
				</section>

				<!-- Text -->
				<section>
					<h3>Detalhes: </h3>
					<p>O <b>Motor de Partida</b> apresenta falhas de funcionamento.</p>
					<p>O Código da <b>Vela de ignição</b> desta motocicleta está diferente do especificado.</p>
					<p>O <b>Óleo de Transmissão</b> deste motor já passou do tempo de uso recomendado.</p>
					<a href="#" class="button primary">Editar detalhes</a>
					<hr />
				</section>

				<!-- Image -->
				<section>
					<h3>Informações:</h3>
					<h4>Motocicleta</h4>
					<p><span class="image left"><img src="./fotos/motos/61ffdd4406fdc4.21287213IMG_20211214_084044.jpg" alt="" /></span>
						<bold>Marca:</bold> X
					</p>
					<p>
						<bold>Modelo:</bold> X
					</p>
					<p>
						<bold>Proprietario:</bold> X
					</p>
					<p>
						<bold>blablabla:</bold> X
					</p>
					<p>
						<bold>blablabla:</bold> X
					</p>
				</section>

			</div>
		</div>

		<?php
		require("./assets/php/includes/main/footer.php");
		?>

	</div>

	<!-- Scripts -->
	<script src="assets/js/global/jquery.min.js"></script>
	<script src="assets/js/global/jquery.scrolly.min.js"></script>
	<script src="assets/js/global/jquery.dropotron.min.js"></script>
	<script src="assets/js/global/jquery.scrollex.min.js"></script>
	<script src="assets/js/global/browser.min.js"></script>
	<script src="assets/js/global/breakpoints.min.js"></script>
	<script src="assets/js/global/util.js"></script>
	<script src="assets/js/main.js"></script>

</body>

</html>