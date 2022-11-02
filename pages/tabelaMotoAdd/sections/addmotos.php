<section id="banner">
    <div class="content form">
        <img class="fit logogray" src="./assets/css/images/logo-branco-crop.png">
        <form method="post" action="scripts/tabelaMotos/add-moto.php" enctype="multipart/form-data">
            <div class="row">
                <div class="col-7">
                    <h2>Adicionar novo veículo</h2>
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
            <div class="row">
                <div class="col-5">
                    <br>
                    <img id="foto" class="fit left" src="https://spassodourado.com.br/wp-content/uploads/2015/01/default-placeholder.png" style="height:200px;width:200px;">
                    <br>
                </div>
                <div class="col-7">
                    <label>Endereço:</label>
                    <input type="text" name="endereco">
                    <label>Ano:</label>
                    <input type="text" name="ano">
                    <label>Modelo:</label>
                    <input type="text" name="modelo">
                </div>
            </div>
            <div class="row">
                <div class="col-5">
                    <label>Marca:</label>
                    <input type="text" name="marca">
                </div>
                <div class="col-5">
                    <label>Placa:</label>
                    <input type="text" name="placa">
                </div>
                <div class="col-2">
                    <label>KM:</label>
                    <input type="number" name="KM">
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <label>Proprietario:</label>
                    <input type="text" name="proprietario">
                </div>
            </div>
            <hr>
            <input class="button primary" type="submit" value="Salvar">
        </form>
    </div>
</section>