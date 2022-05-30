<!DOCTYPE html>

<?php
session_start();
//PERM
require("./assets/php/scripts/perm.php");
//CONNECTION
require("./connection/connection.php");
//FUNCTIONS
require("./assets/php/scripts/functions.php");


//PAGE
$page = getPage();


//TABLE COUNT
$mysqli_query = "SELECT pecaId FROM pecas";
$result = mysqli_query($conn, $mysqli_query);
$table_count = getTableCount($conn, $mysqli_query);


//ADD-EDIT-DELETE
addPeca($conn);
editPeca($conn);
deletePeca($conn)
?>

<html lang="en">

<head>
   <title>Tabela de Peças</title>
   <meta charset="utf-8" />
   <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
   <link rel="stylesheet" href="assets/css/main.css" />
   <noscript>
      <link rel="stylesheet" href="assets/css/noscript.css" />
   </noscript>

   <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Roboto|Varela+Round'>
   <link rel='stylesheet' href='https://fonts.googleapis.com/icon?family=Material+Icons'>
   <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'>
   <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css'>
   <link rel="stylesheet" href="./assets/css/crud-table.css">
</head>

<body class="">
   <?php
   require("./assets/php/includes/pages/tabelaPecas/header/header.php");
   require("./assets/php/includes/pages/tabelaPecas/pagination.php");
   require("./assets/php/includes/pages/tabelaPecas/sections/tabela.php");
   require("./assets/php/includes/main/footer.php");
   ?>
</body>

</html>