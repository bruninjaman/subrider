<section id="banner">
    <div class="content">
        <center>
            <form method="post" action="scripts/tabelaPecasAdd/add-peca.php" enctype="multipart/form-data">
                <h2>Adicionar</h2>
                <label>Foto:</label>
                <input name="foto" accept="image/*" onchange="document.getElementById('foto').src = window.URL.createObjectURL(this.files[0])" type="file">
                <br>
                <img id="foto" src="https://spassodourado.com.br/wp-content/uploads/2015/01/default-placeholder.png" style="height:400px;width:400px;" >

                <br><label>Grupo:</label>
                <input type="text" name="grupo" style="width:400px"><br>

                <label>Item:</label>
                <input type="text" name="item" style="width:200px"><br>

                <label>Quantidade:</label>
                <input type="text" name="quantidade" style="width:300px"><br>

                <label>Parte:</label>
                <input type="text" name="parte" style="width:300px"><br>

                <label>Valor:</label>
                <input type="text" name="valor" style="width:300px"><br>

                <input class="button primary" type="submit" value="Adicionar">
            </form>
        </center>
    </div>
</section>