 <!-- Delete Modal HTML -->
 <div id="deleteEmployeeModal" class="modal fade">
      <div class="modal-dialog">
         <div class="modal-content">
            <form id="deleteform" action="tabelaPecas.php?page=<?php echo $page ?>" method="POST">
               <div class="modal-header">
                  <h4 class="modal-title">Deletar Peça</h4>
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
               </div>
               <div class="modal-body">
                  <p>Tem certeza que deseja deletar esse registro?</p>
                  <p class="text-warning"><small>Está ação não pode ser desfeita.</small></p>
               </div>
               <div class="form-group hidden">
                  <input type="text" name="pecaId3" class="form-control pecaId3" required>
               </div>
               <div class="modal-footer">
                  <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancelar">
                  <input type="submit" class="btn btn-danger" value="Deletar">
               </div>
            </form>
         </div>
      </div>
   </div>