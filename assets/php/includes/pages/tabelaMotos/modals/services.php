<!-- Edit Modal HTML -->
<div id="ordemservicos" class="modal fade">
   <div class="modal-dialog">
      <div class="modal-content">
         <form id="editMotos" action="tabelaMotos.php" method="POST" enctype="multipart/form-data">
            <?php
            $mysqli_query = "SELECT * FROM ordem_servicos WHERE motoID = 50";
            $result = mysqli_query($conn, $mysqli_query);
            $rows = mysqli_num_rows($result);
            //$result = mysqli_fetch_assoc($result);
            if ($rows == 0) {
            ?>
               <div class="modal-header">
                  <h4 class="modal-title">Ordems de Serviço</h4>
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
               </div>
               <div class="modal-body ordem-serv">
                  <div class="form-group">
                     <button type="button" class="btn btn-primary btn-block">Abrir nova ordem de serviço</button>
                  </div>
               </div>
            <?php
            } else {
            ?>
               <div class="modal-header">
                  <h4 class="modal-title">Ordems de Serviço</h4>
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
               </div>
               <div class="modal-body ordem-serv">
                  <div class="form-group">
                     <label>Escolha um serviço para visualizar: </label>
                  </div>
                  <?php


                  while ($row = mysqli_fetch_assoc($result)) {
                  ?>
                     <div class="form-group">
                        <button type="button" class="btn btn-info btn-block disabled"><?php echo "{$row['Codigo']}" ?></button>
                        <button type="button" class="btn btn-primary btn-block">
                           <?php
                           if ($row['Aberto']) {
                              echo "Fechar";
                           } else {
                              echo "Reabrir";
                           }
                           ?>
                        </button>
                     </div>
                  <?php
                  }
                  ?>
                  <button type="button" class="btn btn-warning btn-block"> criar nova ordem de serviço</button>
               </div>
            <?php
            }
            ?>
         </form>
      </div>
   </div>
</div>