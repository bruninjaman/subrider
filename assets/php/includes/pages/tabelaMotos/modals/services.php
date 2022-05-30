<!-- Edit Modal HTML -->
<div id="ordemservicos" class="modal fade">
   <div class="modal-dialog">
      <div class="modal-content">
         <form id="editMotos" action="tabelaMotos.php" method="POST" enctype="multipart/form-data">
            <div class="modal-header">
               <h4 class="modal-title">Ordems de Serviço</h4>
               <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body ordem-serv">
               <div class="form-group">
                  <label>Escolha um serviço para visualizar: </label>
               </div>
               <div class="form-group">
                  <a href="ordem.php"><button type="button" class="btn btn-info btn-block">Serviço 101/2022</button></a>
               </div>
               <div class="form-group">
               <button type="button" class="btn btn-info btn-block disabled">Serviço 102/2022</button>
               <button type="button" class="btn btn-primary btn-block">Reabrir</button>
               </div>
               <div class="form-group">
               <button type="button" class="btn btn-info btn-block disabled">Serviço 103/2022</button>
               <button type="button" class="btn btn-primary btn-block">Reabrir</button>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>