<!DOCTYPE html>

<?php
include("./connection/connection.php");

//PAGE
if (!isset($_GET["page"])) {
   $page = 1;
} else {
   $page = $_GET["page"];
}


//get all rows
$mysqli_query = "SELECT motoID FROM motocicletas";
$result = mysqli_query($conn, $mysqli_query);
$table_count = mysqli_num_rows($result);



//Adicionar moto
if (isset($_POST["proprietario"])) {
   //upload image
   $tmp_name = "";
   $tmp_name = uniqid($tmp_name, true);
   $tmp_name = $tmp_name . $_FILES['foto']['name'];
   $file_destination = 'fotos/motos/' . $tmp_name;

   move_uploaded_file($_FILES['foto']['tmp_name'], $file_destination);



   $mysqli_query = "INSERT INTO motocicletas (endereco, proprietario, marca, placa, ano, modelo, km, foto)";
   $mysqli_query .= " VALUES ('{$_POST['endereco']}','{$_POST['proprietario']}' ";
   $mysqli_query .= " ,'{$_POST['marca']}','{$_POST['placa']}','{$_POST['ano']}','{$_POST['modelo']}' ";
   $mysqli_query .= " ,'{$_POST['km']}','{$file_destination}')";

   $result = mysqli_query($conn, $mysqli_query);
   header('Location: gerenciamento.php');
}

//edit moto
if (isset($_POST["proprietario2"])) {
   //upload image
   $tmp_name = "";
   $tmp_name = uniqid($tmp_name, true);
   $tmp_name = $tmp_name . $_FILES['foto2']['name'];
   $file_destination = 'fotos/motos/' . $tmp_name;

   move_uploaded_file($_FILES['foto2']['tmp_name'], $file_destination);



   $mysqli_query = "UPDATE motocicletas";
   $mysqli_query .= " SET endereco = '{$_POST['endereco2']}', proprietario = '{$_POST['proprietario2']}',marca = '{$_POST['marca2']}',placa = '{$_POST['placa2']}',";
   $mysqli_query .= " ano = '{$_POST['ano2']}', modelo = '{$_POST['modelo2']}',km = '{$_POST['km2']}', foto = '{$file_destination}'";
   $mysqli_query .= " WHERE ";
   $mysqli_query .= " motoID = {$_POST['motoid2']}";
   $result = mysqli_query($conn, $mysqli_query);
   header('Location: gerenciamento.php');
}

//deletar motocicleta
if (isset($_POST["motoid3"])) {

   $mysqli_query = "DELETE FROM motocicletas WHERE";
   $mysqli_query .= " motoID = {$_POST['motoid3']}";
   var_dump($mysqli_query);
   $result = mysqli_query($conn, $mysqli_query);
   header('Location: gerenciamento.php');
}
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
   <!-- Navigation -->
   <nav class="navbar navbar-default navbar-fixed-top navbar-shrink">
      <div class="container">
         <!-- Brand and toggle get grouped for better mobile display -->
         <div class="navbar-header page-scroll">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
               <span class="sr-only">Toggle navigation</span>
               <span class="icon-bar"></span>
               <span class="icon-bar"></span>
               <span class="icon-bar"></span>
            </button>
            <img src="./imgs/logo-branco.png" style="height:60px;width:180px;">
         </div>
         <!-- Collect the nav links, forms, and other content for toggling -->
         <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav navbar-right">
               <li class="hidden active">
                  <a href="#page-top"></a>
               </li>
               <li class="">
                  <a class="page-scroll" href="index.php">Voltar</a>
               </li>
            </ul>
         </div>
         <!-- /.navbar-collapse -->
      </div>
      <!-- /.container-fluid -->
   </nav>
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
            $mysqli_query = "SELECT * FROM motocicletas limit " . (($page - 1) * 5) . ",5";

            $result = mysqli_query($conn, $mysqli_query);

            while ($row = mysqli_fetch_assoc($result)) {
               include("./includes/tablecontent/tablecontent.php");
            }

               ?>
            </tbody>
         </table>
         <div class="clearfix">
            <?php
            $items_showing = ($page * 5);
            if ($items_showing > $table_count)
               $items_showing = $table_count;
            ?>
            <div class="hint-text">Mostrando <b><?php echo $items_showing ?></b> de <b><?php echo $table_count ?></b> resultados.</div>
            <ul class="pagination">
               <?php

               $table_current_page = $page;

               //previous next
               if ($page <= 1) {
                  echo '<li class="page-item disabled"><a href="#">Anterior</a></li>';
               } else {
                  echo '<li class="page-item"><a href="?page=' . $page - 1 . '">Anterior</a></li>';
               }

               $i = 0;
               while ($i <= 10) {
                  $table_page_index = ($i - 5);

                  //continue loop if page is not positive number
                  if ($table_page_index <= 0) {
                     $i++;
                     continue;
                  }

                  //determine the current active page
                  if ($table_current_page == $table_page_index) {
                     $addclass = ' active';
                  } else {
                     $addclass = '';
                  }

                  //disable pages that are empty
                  if (ceil($table_count / 5) < $table_page_index) {
                     $addclass .= ' disabled';
                     echo '<li class="page-item ' . $addclass . '"><a href="?page=' . $page . '" class="page-link"> ' . $table_page_index . ' </a></li>';
                  } else
                     echo '<li class="page-item ' . $addclass . '"><a href="?page=' . $table_page_index . '" class="page-link"> ' . $table_page_index . ' </a></li>';

                  $i++;
               }

               //disable next
               if ($page >= $table_count / 5) {
                  echo '<li class="page-item disabled"><a href="#" class="page-link">Proximo</a></li>';
               } else {
                  $next_prev = "";
                  echo '<li class="page-item"><a href="?page=' . $page + 1 . '" class="page-link">Proximo</a></li>';
               }
               ?>
            </ul>
         </div>
      </div>
   </div>
   <!-- Edit Modal HTML -->
   <div id="addEmployeeModal" class="modal fade">
      <div class="modal-dialog">
         <div class="modal-content">
            <form name="addmoto" action="gerenciamento.php?page=<?php echo $page ?>" method="POST" enctype="multipart/form-data">
               <div class="modal-header">
                  <h4 class="modal-title">Adicionar moto</h4>
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
               </div>
               <div class="modal-body">
                  <div class="form-group">
                     <label>Imagem</label>
                     <input type="file" name="editfoto">
                  </div>
                  <div class="form-group">
                     <label>Proprietario</label>
                     <input type="text" name="editproprietario" class="form-control" required>
                  </div>
                  <div class="form-group">
                     <label>Endereço</label>
                     <textarea class="form-control" name="editendereco" required></textarea>
                  </div>
                  <div class="form-group">
                     <label>Marca</label>
                     <input type="text" name="editmarca" class="form-control" required>
                  </div>
                  <div class="form-group">
                     <label>Placa</label>
                     <input type="text" name="editplaca" class="form-control" required>
                  </div>
                  <div class="form-group">
                     <label>Ano</label>
                     <input type="text" name="editano" class="form-control" required>
                  </div>
                  <div class="form-group">
                     <label>Modelo</label>
                     <input type="text" name="editmodelo" class="form-control" required>
                  </div>
                  <div class="form-group">
                     <label>KM</label>
                     <input type="text" name="editkm" class="form-control" required>
                  </div>
               </div>
               <div class="modal-footer">
                  <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancelar">
                  <input type="submit" class="btn btn-success" value="Adicionar">
               </div>
            </form>
         </div>
      </div>
   </div>
   <!-- Edit Modal HTML -->
   <div id="editEmployeeModal" class="modal fade">
      <div class="modal-dialog">
         <div class="modal-content">
            <form id="editform" action="gerenciamento.php?page=<?php echo $page ?>" method="POST" enctype="multipart/form-data">
               <div class="modal-header">
                  <h4 class="modal-title">Editar Motocicleta</h4>
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
               </div>
               <div class="modal-body">
                  <div class="form-group">
                     <label>Imagem</label>
                     <br>
                     <img class="editimage" style="height:100px;width:100px;">
                     <input type="file" name="foto2">
                  </div>
                  <div class="form-group">
                     <label>Proprietario</label>
                     <input type="text" name="proprietario2" class="form-control editproprietario" required>
                  </div>
                  <div class="form-group">
                     <label>Endereço</label>
                     <textarea class="form-control editendereco" name="endereco2" required></textarea>
                  </div>
                  <div class="form-group">
                     <label>Marca</label>
                     <input type="text" name="marca2" class="form-control editmarca" required>
                  </div>
                  <div class="form-group">
                     <label>Placa</label>
                     <input type="text" name="placa2" class="form-control editplaca" required>
                  </div>
                  <div class="form-group">
                     <label>Ano</label>
                     <input type="text" name="ano2" class="form-control editano" required>
                  </div>
                  <div class="form-group">
                     <label>Modelo</label>
                     <input type="text" name="modelo2" class="form-control editmodelo" required>
                  </div>
                  <div class="form-group">
                     <label>KM</label>
                     <input type="text" name="km2" class="form-control editkm" required>
                  </div>
                  <div class="form-group hidden">
                     <input type="text" name="motoid2" class="form-control editmotoid" required>
                  </div>
               </div>
               <div class="modal-footer">
                  <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancelar">
                  <input type="submit" class="btn btn-info" value="Salvar">
               </div>
            </form>
         </div>
      </div>
   </div>
   <!-- Delete Modal HTML -->
   <div id="deleteEmployeeModal" class="modal fade">
      <div class="modal-dialog">
         <div class="modal-content">
            <form id="deleteform" action="gerenciamento.php?page=<?php echo $page ?>" method="POST">
               <div class="modal-header">
                  <h4 class="modal-title">Deletar Motocicleta</h4>
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
               </div>
               <div class="modal-body">
                  <p>Tem certeza que deseja deletar esse registro?</p>
                  <p class="text-warning"><small>Está ação não pode ser desfeita.</small></p>
               </div>
               <div class="form-group hidden">
                  <input type="text" name="motoid3" class="form-control editmotoid3" required>
               </div>
               <div class="modal-footer">
                  <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancelar">
                  <input type="submit" class="btn btn-danger" value="Deletar">
               </div>
            </form>
         </div>
      </div>
   </div>
   <!-- partial -->
   <script src='https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js'></script>
   <script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js'></script>
   <script src="./scripts/gerenciamento_script.js"></script>
</body>

</html>