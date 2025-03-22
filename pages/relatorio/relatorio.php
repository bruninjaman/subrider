<!-- Include Quill Styles and Scripts -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

<!-- Include TinyMCE -->
<script src="https://cdn.tiny.cloud/1/vyfv561lwa2j1rohg50gvpg3w6rhao6tbw7ax2hwcljwlzu5/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

<!-- Include CKEditor -->
<script src="https://cdn.ckeditor.com/ckeditor5/40.1.0/classic/ckeditor.js"></script>

<?php
echo "<script>";
echo file_get_contents('pages/relatorio/relatorio.js');
echo "</script>";

echo "<style>";
echo file_get_contents('pages/relatorio/relatorio.css');
echo "</style>";
?>


<section id="banner">
  <div class="content form">
    <img class="fit logogray" src="./assets/css/images/logo-branco-crop.png">
    <center>
      <form method="post" action="scripts/add_medicao/add.php?ordem=<?php echo $_GET['ordem'] . '&motoID=' . $motoid['motoID']; ?>">
        <div>
          <div class="row">
            <div class="col-12">
              <label for="editor"><h3>Relatório de Ordem de Serviço</h3></label>
              <!-- Editor container -->
              <div id="editor">
                <h2>Relatório de Ordem de Serviço - Subrider</h2>
                <p>Data: <?php echo date('d/m/Y'); ?></p>
                <p>Ordem de Serviço Nº: <?php echo $_GET['ordem']; ?></p>
                <p>&nbsp;</p>
                <h3>Descrição do Serviço:</h3>
                <p>&nbsp;</p>
                <h3>Observações:</h3>
                <p>&nbsp;</p>
              </div>
              <input type="hidden" name="desc">
            </div>
            <div class="col-12">
              <br>
              <input id="submit" class="button primary relatorio" type="submit" value="Criar Relatório">
            </div>
          </div>
        </div>
      </form>
    </center>
  </div>
</section>