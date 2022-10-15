<?php 
$sql_query = "SELECT * FROM motocicletas ";
$sql_query .= "WHERE motoId = " . $_GET["motoID"];
$result = mysqli_query($conn, $sql_query);
$result = mysqli_fetch_assoc($result);
?>
<section id="banner">
    <div class="content">
        <center>
            <form method="post" action="assets/php/scripts/tabelaMotos/edit-moto.php">
                <h2>Editar</h2>
                <label>Foto:</label>
                <input accept="image/*" onchange="document.getElementById('foto').src = window.URL.createObjectURL(this.files[0])" type="file" name="foto"><br>
                <img id="foto" src=" <?php echo $result["foto"] ?> " style="height:400px;width:400px;" >

                <br><label>Endereço:</label>
                <input type="text" name="endereco" style="width:400px" value=" <?php echo $result["endereco"] ?> "><br>

                <label>Ano:</label>
                <input type="text" name="ano" style="width:200px" value=" <?php echo $result["ano"] ?> "><br>

                <label>Modelo:</label>
                <input type="text" name="modelo" style="width:300px" value=" <?php echo $result["modelo"] ?> "><br>

                <label>Marca:</label>
                <input type="text" name="marca" style="width:300px" value=" <?php echo $result["marca"] ?> "><br>

                <label>Placa:</label>
                <input type="text" name="placa" style="width:300px" value=" <?php echo $result["placa"] ?> "><br>

                <label>KM:</label>
                <input type="text" name="KM" style="width:150px" value=" <?php echo $result["km"] ?> "><br>

                <label>Proprietario:</label>
                <input type="text" name="proprietario" style="width:400px" value=" <?php echo $result["proprietario"] ?> "><br>

                <input class="button primary" type="submit" value="Editar">
            </form>
        </center>
    </div>
</section>