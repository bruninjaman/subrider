<!-- ADD MODAL -->
<div id="addEmployeeModal" class="modal fade">
      <div class="modal-dialog">
         <div class="modal-content">
            <form name="addServicos" action="tabelaServicos.php?page=<?php echo $page ?>" method="POST" enctype="multipart/form-data">
               <div class="modal-header">
                  <h4 class="modal-title">Adicionar Serviço</h4>
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
               </div>
               <div class="modal-body">
                  <div class="form-group">
                     <label>Item</label>
                     <input type="text" name="addItem" class="form-control" required>
                  </div>
                  <div class="form-group">
                     <label>Tipo</label>
                     <textarea class="form-control" name="addTipo" required></textarea>
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