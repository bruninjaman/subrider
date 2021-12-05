<!DOCTYPE html>
<?php
	require_once("connection/connection.php");
?>
<html>
	<head>
	<title>Title of the document</title>
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