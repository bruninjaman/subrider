<section id="banner">
    <div class="content form">
        <img class="fit logogray" src="./assets/css/images/logo-branco-crop.png">
        <form method="post" action="scripts/tabelaPecasAdd/add-peca.php" enctype="multipart/form-data">
            <div class="row">
                <div class="col-12">
                    <h2>Adicionar peça</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <input name="foto" accept="image/*" onchange="document.getElementById('foto').src = window.URL.createObjectURL(this.files[0])" type="file">
                </div>
                <div class="col-6">
                    <label>Grupo:</label>
                    <input type="text" name="grupo">

                    <label>Item:</label>
                    <input type="text" name="item">

                    <label>Parte:</label>
                    <input type="text" name="parte">
                </div>
                <div class="col-6">
                    <img id="foto" src="https://spassodourado.com.br/wp-content/uploads/2015/01/default-placeholder.png" style="height:200px;width:200px;">
                </div>
            </div>
            <br>
            <input class="button primary" type="submit" value="Adicionar">
        </form>
    </div>
</section>