<!-- Edit Modal HTML -->
<div id="editEmployeeModal" class="modal fade">
      <div class="modal-dialog">
         <div class="modal-content">
            <form id="editMotos" action="tabelaMotos.php?page=<?php echo $page ?>" method="POST" enctype="multipart/form-data">
               <div class="modal-header">
                  <h4 class="modal-title">Editar Motocicleta</h4>
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
               </div>
               <div class="modal-body">
                  <div class="form-group">
                     <label>Imagem</label>
                     <br>
                     <img class="editFoto" style="height:100px;width:100px;">
                     <input type="file" name="editFoto">
                  </div>
                  <div class="form-group">
                     <label>Proprietario</label>
                     <input type="text" name="editProp" class="form-control editProp" required>
                  </div>
                  <div class="form-group">
                     <label>Endereço</label>
                     <textarea name="editEnder" class="form-control editEnder" required></textarea>
                  </div>
                  <div class="form-group">
                     <label>Marca</label>
                     <input type="text" name="editMarca" class="form-control editMarca" required>
                  </div>
                  <div class="form-group">
                     <label>Placa</label>
                     <input type="text" name="editPlaca" class="form-control editPlaca" required>
                  </div>
                  <div class="form-group">
                     <label>Ano</label>
                     <input type="text" name="editAno" class="form-control editAno" required>
                  </div>
                  <div class="form-group">
                     <label>Modelo</label>
                     <input type="text" name="editModel" class="form-control editModel" required>
                  </div>
                  <div class="form-group">
                     <label>KM</label>
                     <input type="text" name="editKm" class="form-control editKm" required>
                  </div>
                  <div class="form-group hidden">
                     <input type="text" name="editMotoId" class="form-control editMotoId" required>
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