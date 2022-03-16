<!-- ADD MODAL -->
<div id="addEmployeeModal" class="modal fade">
      <div class="modal-dialog">
         <div class="modal-content">
            <form name="addPecas" action="tabelaPecas.php?page=<?php echo $page ?>" method="POST" enctype="multipart/form-data">
               <div class="modal-header">
                  <h4 class="modal-title">Adicionar Peça</h4>
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
               </div>
               <div class="modal-body">
                  <div class="form-group">
                     <label>Imagem</label>
                     <input type="file" name="addFoto">
                  </div>
                  <div class="form-group">
                     <label>Grupo</label>
                     <input type="text" name="addGrupo" class="form-control" required>
                  </div>
                  <div class="form-group">
                     <label>Item</label>
                     <textarea name="addItem" class="form-control" required></textarea>
                  </div>
                  <div class="form-group">
                     <label>Parte</label>
                     <input type="text" name="addParte" class="form-control" required>
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