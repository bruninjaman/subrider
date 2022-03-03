<!-- Edit Modal HTML -->
<div id="editEmployeeModal" class="modal fade">
      <div class="modal-dialog">
         <div class="modal-content">
            <form id="editform" action="tabelaMotos.php?page=<?php echo $page ?>" method="POST" enctype="multipart/form-data">
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