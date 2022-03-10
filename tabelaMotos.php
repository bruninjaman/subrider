<!DOCTYPE html>

<?php
//CONNECTION
require("./connection/connection.php");
//FUNCTIONS
require("./scripts/php/tabelas/functions.php");

//PAGE
$page = getPage();

//TABLE COUNT
$mysqli_query = "SELECT motoID FROM motocicletas";
$result = mysqli_query($conn, $mysqli_query);
$table_count = mysqli_num_rows($result);

//ADD-EDIT-DELETE
addMoto($conn);
editMoto($conn);
deleteMoto($conn)

?>

<html lang="en">

<head>
   <!-- NAVBAR CSS-->
   <link rel='stylesheet' href='https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css'>
   <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css'>
   <link rel='stylesheet' href='https://startbootstrap.com/templates/agency/font-awesome-4.1.0/css/font-awesome.min.css'>
   <link rel="stylesheet" href="./css/default-navbar.css">
   <meta charset="UTF-8">
   <title>Gerenciamento Tabela</title>
   <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Roboto|Varela+Round'>
   <link rel='stylesheet' href='https://fonts.googleapis.com/icon?family=Material+Icons'>
   <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'>
   <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css'>
   <link rel="stylesheet" href="./css/crud-table.css">
</head>

<body class="crud-table defaultpage">
   <?php
   require("./scripts/php/includes/nav-bar/tabelaMotos/navbar.php");
   ?>
   <!-- partial:index.partial.html -->
   <div class="container">
      <div class="table-wrapper">
         <div class="table-title">
            <div class="row">
               <div class="col-sm-6">
                  <h2>Gerenciar <b>Motocicletas</b></h2>
               </div>
               <div class="col-sm-6">
                  <a href="#addEmployeeModal" class="btn btn-success" data-toggle="modal"><i class="material-icons">&#xE147;</i> <span>Adicionar moto</span></a>
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
                  <th>Proprietario</th>
                  <th>Endereço</th>
                  <th>Marca</th>
                  <th>Placa</th>
                  <th>Ano</th>
                  <th>Modelo</th>
                  <th>KM</th>
                  <th>Ações</th>
               </tr>
            </thead>
            <tbody>
               <?php
               showMotos($conn, $page);
               ?>
            </tbody>
         </table>
         <div class="clearfix">
            <?php
            showMotosMain($table_count, $page);
            ?>
            </ul>
         </div>
      </div>
   </div>
   <?php
   require("./scripts/php/includes/modalMotos/add-modal.php");
   require("./scripts/php/includes/modalMotos/edit-modal.php");
   require("./scripts/php/includes/modalMotos/remove-modal.php");
   ?>
   <!-- partial -->
   <script src='https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js'></script>
   <script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js'></script>
   <script src="./scripts/tabela_moto.js"></script>
</body>

</html>