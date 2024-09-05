<!-- Include Quill Styles and Scripts -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

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
      <form method="post" action="scripts/addparametro/add.php?ordem=<?php echo $_GET['ordem'] . '&motoID=' . $motoid['motoID']; ?>">
        <div>
          <div class="row">
            <div class="col-12">
              <label for="editor"><h3>Relatório</h3></label>
              <!-- Quill editor container with dark background and light text -->
              <div id="editor">
                <p>Relatório da <strong>Subrider</strong>,</p>
                <p><br /></p>
              </div>

              <!-- Hidden textarea to store the content for form submission -->
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