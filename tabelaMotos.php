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

<?php
// new tabelaMotos item (like pagination.php)
if (isset($_POST["service-form"])) {

   //Criar código
   $mysqli_query        = "SELECT * FROM ordem_servicos";
   $result              = mysqli_query($conn, $mysqli_query);
   $resultados          = mysqli_num_rows($result);

   if ($resultados > 0) {
      $last_code = 0;
      while ($item = mysqli_fetch_assoc($result)) {
         $codigo = explode("/", $item["Codigo"]);
         if ($codigo[0] > $last_code && $codigo[1] == date("Y"))
            $last_code = $codigo[0];
      }
      if ($last_code < 101)
         $last_code = 100;
      $novo_codigo = ($last_code + 1) . "/" . date("Y");
   }

   //Inserir novo código no banco de dados
   if (isset($_GET["motoID"])) {
      $motoID     = $_GET["motoID"];
   } else {
      $motoID     = 0;
   }

   $mysqli_query = "INSERT INTO ordem_servicos (motoID, Codigo, Aberto) VALUES ({$motoID},'{$novo_codigo}',1)";
   mysqli_query($conn, $mysqli_query);
   header("location: ordem.php?motoID=".$motoID."&ordem=".$novo_codigo);
}
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
      <?php
      require("./assets/php/includes/pages/tabelaMotos/header/header.php");
      require("./assets/php/includes/pages/tabelaMotos/pagination.php");
      require("./assets/php/includes/pages/tabelaMotos/sections/tabela.php");
      require("./assets/php/includes/main/footer.php");
      ?>
   </div>

   <!-- Scripts -->
   <script src="assets/js/global/jquery.scrolly.min.js"></script>
   <script src="assets/js/global/jquery.dropotron.min.js"></script>
   <script src="assets/js/global/jquery.scrollex.min.js"></script>
   <script src="assets/js/global/browser.min.js"></script>
   <script src="assets/js/global/breakpoints.min.js"></script>
   <script src="assets/js/global/util.js"></script>
   <script src="assets/js/main.js"></script>
   <script src="assets/js/global/table_sort.js"></script>
   </div>
</body>

</html>