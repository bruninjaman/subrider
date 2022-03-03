<!-- ADD MODAL -->
<div id="addEmployeeModal" class="modal fade">
      <div class="modal-dialog">
         <div class="modal-content">
            <form name="addmoto" action="tabelaMotos.php?page=<?php echo $page ?>" method="POST" enctype="multipart/form-data">
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
                     <input type="text" name="addProp" class="form-control" required>
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