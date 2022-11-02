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
                    <label>Foto:</label>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <input name="foto" accept="image/*" onchange="document.getElementById('foto').src = window.URL.createObjectURL(this.files[0])" type="file">
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-5">

                    <img id="foto" src="https://spassodourado.com.br/wp-content/uploads/2015/01/default-placeholder.png" style="height:200px;width:200px;">

                </div>
                <div class="col-5">
                    <label>Grupo:</label>
                    <input type="text" name="grupo">

                    <label>Item:</label>
                    <input type="text" name="item">
                </div>
                <div class="col-2">
                    <label>Quantidade:</label>
                    <input type="number" name="quantidade">
                    <label>Valor:</label>
                    <input type="number" name="valor">
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <label>Parte:</label>
                    <input type="text" name="parte">
                </div>
            </div>
            <br>
            <input class="button primary" type="submit" value="Adicionar">
        </form>
    </div>
</section>