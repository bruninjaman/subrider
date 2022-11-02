<?php
$sql_query = "SELECT * FROM motocicletas ";
$sql_query .= "WHERE motoId = " . $_GET["motoID"];
$result = mysqli_query($conn, $sql_query);
$result = mysqli_fetch_assoc($result);
?>
<section id="banner">
    <div class="content form">
        <img class="fit logogray" src="./assets/css/images/logo-branco-crop.png">
        <form method="post" action="scripts/tabelaMotos/edit-moto.php" enctype="multipart/form-data">
            <div class="row">
                <div class="col-12">
                    <h2>Editar informações do Veículo</h2>
                </div>
            </div>
            <input type=hidden name=motoID value="<?php echo $result["motoId"] ?>">
            <div class="row">
                <div class="col-12">
                    <label>Foto:</label>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <input name="foto" value="<?php echo $result["foto"] ?>" accept="image/*" onchange="document.getElementById('foto').src = window.URL.createObjectURL(this.files[0])" type="file"><br>
                </div>
            </div>
            <div class="row">
                <div class="col-5">
                    <br>
                    <img id="foto" src=" <?php echo $result["foto"] ?> " style="height:200px;width:200px;">
                    <br>
                </div>
                <div class="col-7">
                    <label>Endereço:</label>
                    <input type="text" name="endereco"  value="<?php echo $result["endereco"] ?>">

                    <label>Ano:</label>
                    <input type="text" name="ano" value="<?php echo $result["ano"] ?>">

                    <label>Modelo:</label>
                    <input type="text" name="modelo" value="<?php echo $result["modelo"] ?>">
                </div>
            </div>
            <div class="row">
                <div class="col-5">
                    <label>Marca:</label>
                    <input type="text" name="marca" value="<?php echo $result["marca"] ?>">

                </div>
                <div class="col-5">
                    <label>Placa:</label>
                    <input type="text" name="placa" value="<?php echo $result["placa"] ?>">

                </div>
                <div class="col-2">
                    <label>KM:</label>
                    <input type="text" name="KM"  value="<?php echo KMUnformat($result["km"]) ?>">
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <label>Proprietario:</label>
                    <input type="text" name="proprietario"  value="<?php echo $result["proprietario"] ?>">
                </div>
            </div>
            <hr>
            <input class="button primary" type="submit" value="Editar Moto">
        </form>
    </div>
</section>