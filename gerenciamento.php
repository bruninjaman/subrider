<!DOCTYPE html>
<html lang="en" >
   <head>
      <!-- NAVBAR CSS-->
      <link rel='stylesheet' href='https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css'>
      <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css'>
      <link rel='stylesheet' href='https://startbootstrap.com/templates/agency/font-awesome-4.1.0/css/font-awesome.min.css'>
      <link rel="stylesheet" href="./style.css">
      <meta charset="UTF-8">
      <title>CodePen - Bootstrap : CRUD Table</title>
      <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Roboto|Varela+Round'>
      <link rel='stylesheet' href='https://fonts.googleapis.com/icon?family=Material+Icons'>
      <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'>
      <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css'>
      <link rel="stylesheet" href="./gerenciamento_style.css">
   </head>
   <body>
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
               <a class="navbar-brand page-scroll" href="#page-top">[LOGO Sub-Rider]</a>
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
                     <th>Telefone</th>
                     <th>Ações</th>
                  </tr>
               </thead>
               <tbody>
                  <tr>
                     <td>
                        <span class="custom-checkbox">
                        <input type="checkbox" id="checkbox1" name="options[]" value="1">
                        <label for="checkbox1"></label>
                        </span>
                     </td>
                     <td><img src="https://blog.grao.com.br/wp-content/uploads/2020/08/comprar-a-moto-certa-540x540.jpg" style="height:100px;width:100px;"></td>
                     <td>Jonny da Silva</td>
                     <td>89 Chiaroscuro Rd, Portland, USA</td>
                     <td>(171) 555-2222</td>
                     <td>
                        <a href="#editEmployeeModal" class="edit" data-toggle="modal"><i class="material-icons" data-toggle="tooltip" title="Edit">&#xE254;</i></a>
                        <a href="#deleteEmployeeModal" class="delete" data-toggle="modal"><i class="material-icons" data-toggle="tooltip" title="Delete">&#xE872;</i></a>
                     </td>
                  </tr>
                  <tr>
                     <td>
                        <span class="custom-checkbox">
                        <input type="checkbox" id="checkbox2" name="options[]" value="1">
                        <label for="checkbox2"></label>
                        </span>
                     </td>
                     <td><img src="https://blog.grao.com.br/wp-content/uploads/2020/08/comprar-a-moto-certa-540x540.jpg" style="height:100px;width:100px;"></td>
                     <td>Jonny da Silva</td>
                     <td>89 Chiaroscuro Rd, Portland, USA</td>
                     <td>(171) 555-2222</td>
                     <td>
                        <a href="#editEmployeeModal" class="edit" data-toggle="modal"><i class="material-icons" data-toggle="tooltip" title="Edit">&#xE254;</i></a>
                        <a href="#deleteEmployeeModal" class="delete" data-toggle="modal"><i class="material-icons" data-toggle="tooltip" title="Delete">&#xE872;</i></a>
                     </td>
                  </tr>
                  <tr>
                     <td>
                        <span class="custom-checkbox">
                        <input type="checkbox" id="checkbox3" name="options[]" value="1">
                        <label for="checkbox3"></label>
                        </span>
                     </td>
                     <td><img src="https://blog.grao.com.br/wp-content/uploads/2020/08/comprar-a-moto-certa-540x540.jpg" style="height:100px;width:100px;"></td>
                     <td>Jonny da Silva</td>
                     <td>89 Chiaroscuro Rd, Portland, USA</td>
                     <td>(171) 555-2222</td>
                     <td>
                        <a href="#editEmployeeModal" class="edit" data-toggle="modal"><i class="material-icons" data-toggle="tooltip" title="Edit">&#xE254;</i></a>
                        <a href="#deleteEmployeeModal" class="delete" data-toggle="modal"><i class="material-icons" data-toggle="tooltip" title="Delete">&#xE872;</i></a>
                     </td>
                  </tr>
                  <tr>
                     <td>
                        <span class="custom-checkbox">
                        <input type="checkbox" id="checkbox4" name="options[]" value="1">
                        <label for="checkbox4"></label>
                        </span>
                     </td>
                     <td><img src="https://blog.grao.com.br/wp-content/uploads/2020/08/comprar-a-moto-certa-540x540.jpg" style="height:100px;width:100px;"></td>
                     <td>Jonny da Silva</td>
                     <td>89 Chiaroscuro Rd, Portland, USA</td>
                     <td>(171) 555-2222</td>
                     <td>
                        <a href="#editEmployeeModal" class="edit" data-toggle="modal"><i class="material-icons" data-toggle="tooltip" title="Edit">&#xE254;</i></a>
                        <a href="#deleteEmployeeModal" class="delete" data-toggle="modal"><i class="material-icons" data-toggle="tooltip" title="Delete">&#xE872;</i></a>
                     </td>
                  </tr>
                  <tr>
                     <td>
                        <span class="custom-checkbox">
                        <input type="checkbox" id="checkbox5" name="options[]" value="1">
                        <label for="checkbox5"></label>
                        </span>
                     </td>
                     <td><img src="https://blog.grao.com.br/wp-content/uploads/2020/08/comprar-a-moto-certa-540x540.jpg" style="height:100px;width:100px;"></td>
                     <td>Jonny da Silva</td>
                     <td>89 Chiaroscuro Rd, Portland, USA</td>
                     <td>(171) 555-2222</td>
                     <td>
                        <a href="#editEmployeeModal" class="edit" data-toggle="modal"><i class="material-icons" data-toggle="tooltip" title="Edit">&#xE254;</i></a>
                        <a href="#deleteEmployeeModal" class="delete" data-toggle="modal"><i class="material-icons" data-toggle="tooltip" title="Delete">&#xE872;</i></a>
                     </td>
                  </tr>
               </tbody>
            </table>
            <div class="clearfix">
               <div class="hint-text">Showing <b>5</b> out of <b>25</b> entries</div>
               <ul class="pagination">
                  <li class="page-item disabled"><a href="#">Previous</a></li>
                  <li class="page-item"><a href="#" class="page-link">1</a></li>
                  <li class="page-item"><a href="#" class="page-link">2</a></li>
                  <li class="page-item active"><a href="#" class="page-link">3</a></li>
                  <li class="page-item"><a href="#" class="page-link">4</a></li>
                  <li class="page-item"><a href="#" class="page-link">5</a></li>
                  <li class="page-item"><a href="#" class="page-link">Next</a></li>
               </ul>
            </div>
         </div>
      </div>
      <!-- Edit Modal HTML -->
      <div id="addEmployeeModal" class="modal fade">
         <div class="modal-dialog">
            <div class="modal-content">
               <form>
                  <div class="modal-header">
                     <h4 class="modal-title">Adicionar moto</h4>
                     <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  </div>
                  <div class="modal-body">
                     <div class="form-group">
                        <label>Imagem</label>
                        <input type="file" required>
                     </div>
                     <div class="form-group">
                        <label>Proprietario</label>
                        <input type="email" class="form-control" required>
                     </div>
                     <div class="form-group">
                        <label>Endereço</label>
                        <textarea class="form-control" required></textarea>
                     </div>
                     <div class="form-group">
                        <label>Telefone</label>
                        <input type="text" class="form-control" required>
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
               <form>
                  <div class="modal-header">
                     <h4 class="modal-title">Editar Motocicleta</h4>
                     <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  </div>
                  <div class="modal-body">
                     <div class="form-group">
                        <label>Imagem</label>
                        <input type="file" required>
                     </div>
                     <div class="form-group">
                        <label>Proprietario</label>
                        <input type="email" class="form-control" required>
                     </div>
                     <div class="form-group">
                        <label>Endereço</label>
                        <textarea class="form-control" required></textarea>
                     </div>
                     <div class="form-group">
                        <label>Telefone</label>
                        <input type="text" class="form-control" required>
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
               <form>
                  <div class="modal-header">
                     <h4 class="modal-title">Deletar Motocicleta</h4>
                     <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  </div>
                  <div class="modal-body">
                     <p>Tem certeza que deseja deletar esse registro?</p>
                     <p class="text-warning"><small>Está ação não pode ser desfeita.</small></p>
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
      <script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js'></script><script  src="./gerenciamento_script.js"></script>
   </body>
</html>