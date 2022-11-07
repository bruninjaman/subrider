<section id="banner">
    <div class="content form">
        <img class="fit logogray" src="./assets/css/images/logo-branco-crop.png">
        <form method="post" action="scripts/tabelaServicosAdd/add-servico.php" enctype="multipart/form-data">
            <div class="row">
                <div class="col-12">
                    <h2>Adicionar Serviço</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-2">
                    <label>valor:</label>
                    <input type="number" name="valor" >
                </div>
                <div class="col-4">

                    <label>Ordem de serviço:</label>
                    <input type="text" name="ordem" >

                </div>
                <div class="col-6">
                    <label>tipo:</label>
                    <input type="text" name="tipo" >
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <label>Item:</label>
                    <input type="text" name="item" >
                </div>
            </div>
            <br>
            <input class="button primary" type="submit" value="Adicionar">
        </form>
    </div>
</section>