 <!-- Edit Modal HTML -->
 <div id="editEmployeeModal" class="modal fade">
      <div class="modal-dialog">
         <div class="modal-content">
            <form id="editform" action="tabelaPecas.php?page=<?php echo $page ?>" method="POST" enctype="multipart/form-data">
               <div class="modal-header">
                  <h4 class="modal-title">Editar Peça</h4>
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
                     <label>Grupo</label>
                     <input type="text" name="grupo2" class="form-control editGrupo" required>
                  </div>
                  <div class="form-group">
                     <label>Item</label>
                     <textarea class="form-control editItem" name="item2" required></textarea>
                  </div>
                  <div class="form-group">
                     <label>Parte</label>
                     <input type="text" name="parte2" class="form-control editParte" required>
                  </div>
                  <div class="form-group hidden">
                     <input type="text" name="pecaId2" class="form-control editpecaId" required>
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