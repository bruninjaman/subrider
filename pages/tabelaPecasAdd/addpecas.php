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
                <div class="col-6">
                    <label>Grupo:</label>
                    <input type="text" name="grupo" required>

                    <label>Item:</label>
                    <input type="text" name="item" required>

                    <label>Parte:</label>
                    <input type="text" name="parte" required>
                </div>
                <div class="col-6">
                    <div class="upload-image">
                        <div class="card thmb">
                            <img src="https://www.abecom.com.br/wp-content/uploads/elementor/thumbs/engrenagem-ou-roda-dentada-ot6vess300oq2nd1xp9ar7ex1cg39ftlquaqn7vsas.jpg" alt="preview" />
                            <input type="file" name="foto" /><i class="fas fa-arrow-circle-up"></i>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <input class="button primary" type="submit" value="Adicionar">
        </form>
    </div>
</section>