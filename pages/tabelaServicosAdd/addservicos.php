<section id="banner">
    <div class="content form">
        <img class="fit logogray" src="./assets/css/images/logo-branco-crop.png">
        <form method="post" action="scripts/tabelaServicosAdd/add-servico.php" enctype="multipart/form-data">
            <div class="row">
                <div class="col-12">
                    <h2>Adicionar Serviço</h2>
                    <?php
                    if (isset($_SESSION['form_errors']) && is_array($_SESSION['form_errors'])) {
                        echo '<div class="alert error" style="color: red; border: 1px solid red; padding: 10px; margin-bottom: 15px;">';
                        foreach ($_SESSION['form_errors'] as $error) {
                            echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '<br>';
                        }
                        echo '</div>';
                        unset($_SESSION['form_errors']);
                    }
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <label for="item">Item:</label>
                    <input type="text" name="item" id="item" required>
                </div>
                <div class="col-6">
                    <label for="tipo">Tipo:</label>
                    <input type="text" name="tipo" id="tipo" required>
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <label for="preco">Preço (R$):</label>
                    <input type="text" name="preco" id="preco" required placeholder="0,00" pattern="[0-9]+([,\.][0-9]{1,2})?">
                    <small>Use vírgula para centavos.</small>
                </div>
                <div class="col-8">
                    <label for="descricao">Descrição:</label>
                    <textarea name="descricao" id="descricao" rows="3" required></textarea>
                </div>
            </div>
            <br>
            <input class="button primary" type="submit" value="Adicionar">
        </form>
    </div>
</section>

<script src="assets/js/global/jquery.min.js"></script> 
<script src="assets/js/global/jquery.mask.min.js"></script>
<script>
$(document).ready(function(){
  $('#preco').mask('#.##0,00', {reverse: true});
});
</script>