<!-- Edit Modal HTML -->
<div id="proprietariomodal" class="modal fade">
   <div class="modal-dialog">
      <div class="modal-content">
         <form id="editMotos" action="tabelaMotos.php" method="POST" enctype="multipart/form-data">
            <div class="modal-header">
               <h4 class="modal-title">Trocar Proprietario</h4>
               <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
               <div class="form-group">
                  <button type="button" class="btn btn-info btn-sm disabled"><b>Proprietario em 2002: </b>Augusto da Silva</button>
               </div>
               <div class="form-group">
                  <button type="button" class="btn btn-info btn-sm disabled"><b>Proprietario em 2008: </b>Leandro Toronto</button>
               </div>
               <div class="form-group">
                  <button type="button" class="btn btn-info btn-sm active"><b>Proprietario Atual: </b>João Pereira</button>
               </div>
               <div class="form-group">
                  </br>
               </div>
               <div class="form-group">
                  <button type="button" class="btn btn-primary btn-sm">Trocar Proprietario Atual</button>
                  <button type="button" class="btn btn-danger btn-sm">Cancelar</button>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>