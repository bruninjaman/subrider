<!DOCTYPE html>

<?php
//CONNECTION
require("./connection/connection.php");
//FUNCTIONS
require("./scripts/php/tabelas/functions.php");

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
   <!-- NAVBAR CSS-->
   <link rel='stylesheet' href='https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css'>
   <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css'>
   <link rel='stylesheet' href='https://startbootstrap.com/templates/agency/font-awesome-4.1.0/css/font-awesome.min.css'>
   <link rel="stylesheet" href="./css/default-navbar.css">
   <meta charset="UTF-8">
   <title>Tabela de Peças</title>
   <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Roboto|Varela+Round'>
   <link rel='stylesheet' href='https://fonts.googleapis.com/icon?family=Material+Icons'>
   <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'>
   <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css'>
   <link rel="stylesheet" href="./css/crud-table.css">
</head>

<body class="crud-table defaultpage">
   <?php
   require("./scripts/php/includes/nav-bar/tabelaPecas/navbar.php");
   ?>
   <!-- partial:index.partial.html -->
   <div class="container">
      <div class="table-wrapper">
         <div class="table-title">
            <div class="row">
               <div class="col-sm-6">
                  <h2>Gerenciar <b>Peças</b></h2>
               </div>
               <div class="col-sm-6">
                  <a href="#addEmployeeModal" class="btn btn-success" data-toggle="modal"><i class="material-icons">&#xE147;</i> <span>Adicionar Peça</span></a>
                  <a href="#deleteEmployeeModal" class="btn btn-danger" data-toggle="modal"><i class="material-icons">&#xE15C;</i> <span>Deletar</span></a>
               </div>
            </div>
         </div>
         <table class="table table-striped table-hover">
            <thead>
               <tr>
                  <th>
                     <span class="custom-checkbox">
                        <input type="checkbox" id="selectAll">
                        <label for="selectAll"></label>
                     </span>
                  </th>
                  <th>Foto</th>
                  <th>Grupo</th>
                  <th>Parte</th>
                  <th>Item</th>
               </tr>
            </thead>
            <tbody>
               <?php
               showPecas($conn, $page);
               ?>
            </tbody>
         </table>
         <div class="clearfix">
            <?php
            showPecasMain($table_count, $page);
            ?>
            </ul>
         </div>
      </div>
   </div>
   <?php
   require("./scripts/php/includes/modalPecas/add-modal.php");
   require("./scripts/php/includes/modalPecas/edit-modal.php");
   require("./scripts/php/includes/modalPecas/delete-modal.php");
   ?>
   <!-- partial -->
   <script src='https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js'></script>
   <script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js'></script>
   <script src="./scripts/tabela_pecas.js"></script>
</body>

</html>