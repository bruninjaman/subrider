<?php
if (isset($_GET["motoID"])) {
   $motoID     = $_GET["motoID"];
} else {
   $motoID     = 0;
}
?>
<!-- Edit Modal HTML -->
<div id="ordemservicos" class="modal fade">
   <div class="modal-dialog">
      <div class="modal-content">
         <form id="editMotos" action="tabelaMotos.php<?php echo isset($_GET["motoID"]) ? "?motoID=" . $motoID : ""; ?>" method="POST" enctype="multipart/form-data">
            <?php
            if (isset($_GET["motoID"])) {
               $motoID     = $_GET["motoID"];
            } else {
               $motoID     = 0;
            }

            $mysqli_query  = "SELECT * FROM ordem_servicos WHERE motoID = {$motoID}";
            $result        = mysqli_query($conn, $mysqli_query);
            $rows          = mysqli_num_rows($result);

            if ($rows == 0) {
            ?>
               <div class="modal-header">
                  <h4 class="modal-title">Ordems de Serviço</h4>
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
               </div>
               <div class="modal-body ordem-serv">
                  <div class="form-group">
                     <button type="submit" name="service-form" value="add" class="btn btn-primary btn-block">Abrir nova ordem de serviço</button>
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
                        <button type="button" class="btn btn-primary btn-block" onclick="<?php echo "location.href='ordem.php?motoID=".$_GET["motoID"]."&ordem=".$row["Codigo"]."'"; ?>">
                           <?php
                           if ($row['Aberto']) {
                              echo "<i class='fa-solid fa-lock-open'></i> Aberta";
                           } else {
                              echo "<i class='fa-solid fa-lock'></i> Fechada";
                           }
                           ?>
                        </button>
                     <!-- </div> -->
                  <?php
                  }
                  ?>
                  <button type="submit" name="service-form" value="add" class="btn btn-warning btn-block"> criar nova ordem de serviço</button>
                  <!-- many buttons = many submits with different values -->
               </div>
            <?php
            }
            ?>
         </form>
      </div>
   </div>
</div>