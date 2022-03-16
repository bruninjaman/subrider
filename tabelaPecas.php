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
   <!-- Header is-preload landing -->
   <header id="header">
      <h1 id="logo">
         <a href="index.php"><img src="./assets/css/images/logo-branco-crop.png" style="height:60px;width:180px;"></a>
      </h1>
      <nav id="nav">
         <ul>
            <li><a href="tabelaServicos.php">Tabela de serviços</a></li>
            <li><a href="tabelaMotos.php">Tabela de motocicletas</a></li>
            <li><a href="tabelaClientes.php">Tabela de clientes</a></li>
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
      require("./assets/php/includes/pecas/add-modal.php");
      require("./assets/php/includes/pecas/edit-modal.php");
      require("./assets/php/includes/pecas/delete-modal.php");
      ?>
      <!-- partial -->
      <script src='https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js'></script>
      <script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js'></script>
      <script src="./assets/js/tabela_pecas.js"></script>
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