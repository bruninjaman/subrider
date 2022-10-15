<?php 
$sql_query = "SELECT * FROM motocicletas ";
$sql_query .= "WHERE motoId = " . $_GET["motoID"];
$result = mysqli_query($conn, $sql_query);
$result = mysqli_fetch_assoc($result);
?>
<section id="banner">
    <div class="content">
        <center>
            <form method="post" action="/add-moto.php">
                <h2>Adicionar</h2>
                <label>Foto:</label>
                <input accept="image/*" onchange="document.getElementById('foto').src = window.URL.createObjectURL(this.files[0])" type="file" name="foto"><br>
                <img id="foto" src="https://spassodourado.com.br/wp-content/uploads/2015/01/default-placeholder.png" style="height:400px;width:400px;" >

                <br><label>Endereço:</label>
                <input type="text" name="endereco" style="width:400px"><br>

                <label>Ano:</label>
                <input type="text" name="ano" style="width:200px"><br>

                <label>Modelo:</label>
                <input type="text" name="modelo" style="width:300px"><br>

                <label>Marca:</label>
                <input type="text" name="marca" style="width:300px"><br>

                <label>Placa:</label>
                <input type="text" name="placa" style="width:300px"><br>

                <label>KM:</label>
                <input type="text" name="KM" style="width:150px"><br>

                <label>Proprietario:</label>
                <input type="text" name="proprietario" style="width:400px"><br>

                <input class="button primary" type="submit" value="Adicionar">
            </form>
        </center>
    </div>
</section>