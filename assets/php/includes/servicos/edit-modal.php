 <!-- Edit Modal HTML -->
 <div id="editEmployeeModal" class="modal fade">
      <div class="modal-dialog">
         <div class="modal-content">
            <form id="editServicos" action="tabelaServicos.php?page=<?php echo $page ?>" method="POST" enctype="multipart/form-data">
               <div class="modal-header">
                  <h4 class="modal-title">Editar Serviço</h4>
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
               </div>
               <div class="modal-body">
                  <div class="form-group">
                     <label>Item</label>
                     <input type="text" name="editItem" class="form-control editItem" required>
                  </div>
                  <div class="form-group">
                     <label>Tipo</label>
                     <input type="text" name="addTipo" class="form-control" required>
                  </div>
                  <div class="form-group hidden">
                     <input type="text" name="editServicoId" class="form-control editServicoId" required>
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