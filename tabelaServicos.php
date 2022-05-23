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
$mysqli_query = "SELECT servicoId FROM servicos";
$result = mysqli_query($conn, $mysqli_query);
$table_count = getTableCount($conn, $mysqli_query);


//ADD-EDIT-DELETE
addServico($conn);
editServico($conn);
deleteServico($conn);
?>

<html lang="en">

<head>
   <title>Tabela Serviços</title>
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
   <!-- Header is-preload landing -->
   <header id="header">
      <h1 id="logo">
         <a href="index.php"><img src="./assets/css/images/logo-branco-crop.png" style="height:60px;width:180px;"></a>
      </h1>
      <nav id="nav">
         <ul>
            <li><a href="tabelaMotos.php">Tabela de motocicletas</a></li>
            <li><a href="tabelaPecas.php">Tabela de peças</a></li>
            <li><a href="#">Tabela de clientes</a></li>
            <?php
            if (isset($_SESSION["user"])) {
               echo '<li>';
               echo '<a class="button primary" href="assets/php/scripts/log-out.php">Sair</a>';
               echo '</li>';
            } else {
               echo '<li>';
               echo '<a class="button primary" href="login.php">Entrar</a>';
               echo '</li><li>';
               echo '<a class="button secondary" href="cadastrar.php">Criar conta</a>';
               echo '</li>';
            }
            ?>
         </ul>
      </nav>
   </header>
   <!-- tabela -->
   <section id="one" class=crud-table>
      <div class="container">
         <div class="table-wrapper">
            <div class="table-title">
               <div class="row">
                  <div class="col-sm-6">
                     <h2>Gerenciar <b>Serviços</b></h2>
                  </div>
                  <div class="col-sm-6">
                     <a href="#addEmployeeModal" class="btn btn-success" data-toggle="modal"><i class="material-icons">&#xE147;</i> <span>Adicionar Serviço</span></a>
                     <a href="#deleteEmployeeModal" class="btn btn-danger" data-toggle="modal"><i class="material-icons">&#xE15C;</i> <span>Deletar</span></a>
                  </div>
               </div>
            </div>
            <table class="table table-striped table-hover table-sortable">
               <thead>
                  <tr>
                     <th>
                        <span class="custom-checkbox">
                           <input type="checkbox" id="selectAll">
                           <label for="selectAll"></label>
                        </span>
                     </th>
                     <th>Item</th>
                     <th>Tipo</th>
                  </tr>
               </thead>
               <tbody>
                  <?php
                  showServicos($conn, $page);
                  ?>
               </tbody>
            </table>
            <div class="clearfix">
               <?php
               showServicosMain($table_count, $page);
               ?>
               </ul>
            </div>
         </div>
      </div>
      <?php
      require("./assets/php/includes/servicos/add-modal.php");
      require("./assets/php/includes/servicos/edit-modal.php");
      require("./assets/php/includes/servicos/remove-modal.php");
      ?>
      <!-- partial -->
      <script src='https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js'></script>
      <script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js'></script>
      <script src="./assets/js/tabela_servicos.js"></script>
      <script src="assets/js/table_sort.js"></script>
   </section>

   <!-- Footer -->
   <footer id="footer">
      <ul class="icons">
         <li><a href="#" target="_blank" class="icon brands alt fa-twitter"><span class="label">Twitter</span></a></li>
         <li><a href="#" target="_blank" class="icon brands alt fa-facebook-f"><span class="label">Facebook</span></a></li>
         <li><a href="https://www.youtube.com/channel/UC_rUL6tWuwx-iACNG_uHZVA?sub_confirmation=1" target="_blank" class="icon brands alt fa-youtube"><span class="label">Youtube</span></a></li>
         <li><a href="https://www.instagram.com/xandov/" target="_blank" class="icon brands alt fa-instagram"><span class="label">Instagram</span></a></li>
         <li><a href="#" target="_blank" class="icon brands alt fa-github"><span class="label">GitHub</span></a></li>
         <li><a href="#" target="_blank" class="icon solid alt fa-envelope"><span class="label">Email</span></a></li>
      </ul>
      <ul class="copyright">
         <li>&copy; Whatsapp: (61) 98128-2136.</li>
         <li>Fale com: <a href="http://html5up.net">Robson Alexandre</a></li>
      </ul>
   </footer>
</body>

</html>