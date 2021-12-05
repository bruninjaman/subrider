<!DOCTYPE html>
<?php
	require_once("connection/connection.php");
?>
<html>
	<head>
		<title>Title of the document</title>
		<link rel="stylesheet" href="fontawesome-5.15.4\css\all.css">
	</head>
	<style>
		table, th, td {
		border:1px solid black;
		}
	</style>
	<body>
		Exibição simples de tabela:
		<?php 
			require_once("includes/tabela/gerenciamento.php");
		?>
	</body>
</html>