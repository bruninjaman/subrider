<?php
$sql_query = "SELECT * FROM pecas ";
$sql_query .= "WHERE pecaId = " . $_GET["pecaID"];
$result = mysqli_query($conn, $sql_query);
$result = mysqli_fetch_assoc($result);
?>
<section id="banner">
    <div class="content form">
        <img class="fit logogray" src="./assets/css/images/logo-branco-crop.png">
        <form method="post" action="scripts/tabelaPecasEdit/edit-peca.php?pecaID=<?php echo $result["pecaId"] ?>" enctype="multipart/form-data">
            <div class="row">
                <div class="col-12">
                    <h2>Editar Peça</h2>
                </div>
            </div>
            <input type=hidden name=motoID value="<?php echo $result["pecaId"] ?>">
            <div class="row">
                <div class="col-12">
                    <label>Foto:</label>
                </div>
            </div>
            <div class="row">
                <div class="col-5">
                    <input name="foto" value="<?php echo $result["foto"] ?>" accept="image/*" onchange="document.getElementById('foto').src = window.URL.createObjectURL(this.files[0])" type="file"><br>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-5">
                    <img id="foto" src=" <?php echo $result["foto"] ?> " style="height:200px;width:200px;">
                </div>
                <div class="col-5">
                    <label>Grupo:</label>
                    <input type="text" name="grupo" value="<?php echo $result["grupo"] ?>">

                    <label>Item:</label>
                    <input type="text" name="item" value="<?php echo $result["item"] ?>">
                    <label>Ordem:</label>
                    <input type="text" name="ordem" value="<?php echo $result["ordem"] ?>">
                </div>
                <div class="col-2">
                    <label>quantidade:</label>
                    <input type="text" name="quantidade" value="<?php echo $result["quantidade"] ?>">

                    <label>valor:</label>
                    <input type="text" name="valor" value="<?php echo $result["valor"] ?>">
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <label>Parte:</label>
                    <input type="text" name="parte" value="<?php echo $result["parte"] ?>">
                </div>
            </div>
            <br>
            <input class="button primary" type="submit" value="Editar">
        </form>
    </div>
</section>