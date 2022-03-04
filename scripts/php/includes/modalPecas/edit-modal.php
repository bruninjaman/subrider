 <!-- Edit Modal HTML -->
 <div id="editEmployeeModal" class="modal fade">
      <div class="modal-dialog">
         <div class="modal-content">
            <form id="editPecas" action="tabelaPecas.php?page=<?php echo $page ?>" method="POST" enctype="multipart/form-data">
               <div class="modal-header">
                  <h4 class="modal-title">Editar Peça</h4>
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
                     <label>Grupo</label>
                     <input type="text" name="editGrupo" class="form-control editGrupo" required>
                  </div>
                  <div class="form-group">
                     <label>Item</label>
                     <textarea name="editItem" class="form-control editItem" required></textarea>
                  </div>
                  <div class="form-group">
                     <label>Parte</label>
                     <input type="text" name="editParte" class="form-control editParte" required>
                  </div>
                  <div class="form-group hidden">
                     <input type="text" name="editPecaId" class="form-control editPecaId" required>
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