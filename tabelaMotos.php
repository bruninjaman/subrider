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
   <title>Tabela Motocicletas</title>
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
   <div id="page-wrapper">
      <!-- Header -->
      <header id="header">
         <h1 id="logo">
            <a href="index.php"><img src="./assets/css/images/logo-branco-crop.png" style="height:60px;width:180px;"></a>
         </h1>
         <nav id="nav">
            <ul>
               <li><a href="tabelaPecas.php">Tabela de peças</a></li>
               <li><a href="tabelaServicos.php">Tabela de serviços</a></li>
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

      <div class="form-group" style="display: none;">
         <!--		Show Numbers Of Rows 		-->
         <select class="form-control" name="state" id="maxRows">
            <option value="5000">Show ALL Rows</option>
            <option value="5">5</option>
            <option value="10">10</option>
            <option value="15">15</option>
            <option value="20">20</option>
            <option value="50">50</option>
            <option value="70">70</option>
            <option value="100">100</option>
         </select>

      </div>
      <!-- tabela -->
      <section id="one" class=crud-table>
         <div class="content">
            <div class="container">
               <div class="table-wrapper">
                  <div class="table-title">
                     <div class="row">
                        <div class="col-sm-6">
                           <h2>Gerenciar <b>Motocicletas</b></h2>
                        </div>
                        <div class="col-sm-6">
                           <a href="#addEmployeeModal" class="btn btn-success" data-toggle="modal"><i class="material-icons">&#xE147;</i> <span>Adicionar moto</span></a>                        
                        </div>
                     </div>
                  </div>
                  <table class="table table-striped table-hover table-sortable" id="table-id">
                     <thead>
                        <tr>                          
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
                     <!--		Start Pagination -->
                     <div class='pagination-container'>
                        <nav>
                           <ul class="pagination">

                              <li data-page="prev">
                                 <span>
                                    < <span class="sr-only">(current)
                                 </span></span>
                              </li>
                              <!--	Here the JS Function Will Add the Rows -->
                              <li data-page="next" id="prev">
                                 <span> > <span class="sr-only">(current)</span></span>
                              </li>
                           </ul>
                        </nav>
                     </div>

                  </div> <!-- 		End of Container -->
                  </ul>
               </div>
            </div>
         </div>
         <?php
         require("./assets/php/includes/motos/add-modal.php");
         require("./assets/php/includes/motos/edit-modal.php");
         require("./assets/php/includes/motos/remove-modal.php");
         ?>
   </div>
   <!-- Tabela -->
   <script src='https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js'></script>
   <script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js'></script>
   <script src="./assets/js/tabela_moto.js"></script>
   <script src="./assets/js/table.pagination.js"></script>
   </section>

   <!-- Banner
      <section id="banner">
         <div class="content">
            <header>
               <h2>Tabela de motocicletas</h2>
               <p>Tabela de dados motocicletas.<br /> Os dados das motocicletas serão guardados aqui.</p>
            </header>
            <span class="image"><img src="images/Close.jpg" alt="" /></span>
         </div>
         <a href="#one" class="goto-next scrolly">Proximo</a>
      </section> -->



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

   <!-- Scripts -->
   <script src="assets/js/jquery.scrolly.min.js"></script>
   <script src="assets/js/jquery.dropotron.min.js"></script>
   <script src="assets/js/jquery.scrollex.min.js"></script>
   <script src="assets/js/browser.min.js"></script>
   <script src="assets/js/breakpoints.min.js"></script>
   <script src="assets/js/util.js"></script>
   <script src="assets/js/main.js"></script>
   <script src="assets/js/table_sort.js"></script>
   </div>
</body>

</html>