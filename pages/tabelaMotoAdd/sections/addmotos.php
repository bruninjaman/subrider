<section id="banner">
    <div class="content form">
        <img class="fit logogray" src="./assets/css/images/logo-branco-crop.png">
        <form method="post" action="scripts/tabelaMotos/add-moto.php" enctype="multipart/form-data">
            <div class="row">
                <div class="col-12">
                    <h2>Adicionar novo veículo</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="upload-image">
                        <div class="card thmb">
                            <img src="https://www.honda.com.br/motos/sites/hda/files/styles/product_860x550/public/2022-08/CG%20Start%20-%20Azul%20Perolizado.png?itok=RG1S7qe5" alt="preview" />
                            <input type="file" name="foto" /><i class="fas fa-arrow-circle-up"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <label>Endereço:</label>
                    <input type="text" name="endereco" required>
                </div>
            </div>
            <div class="row">
                <div class="col-3">
                    <label>Ano:</label>
                    <input type="text" name="ano" minlength="4" maxlength="4" required>
                </div>
                <div class="col-5">
                    <label>Modelo:</label>
                    <input type="text" name="modelo" required>
                </div>
                <div class="col-4">
                    <label>Marca:</label>
                    <input type="text" name="marca" required>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <label>Proprietario:</label>
                    <input type="text" name="proprietario" required>
                </div>
                <div class="col-4">
                    <label>Placa:</label>
                    <input type="text" name="placa" required>
                </div>
                <div class="col-2">
                    <label>KM:</label>
                    <input type="number" name="KM" required>
                </div>
            </div>
            <hr>
            <input class="button primary" type="submit" value="Salvar">
        </form>
    </div>
</section>