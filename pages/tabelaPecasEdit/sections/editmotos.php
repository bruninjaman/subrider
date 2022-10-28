<?php 
$sql_query = "SELECT * FROM pecas ";
$sql_query .= "WHERE pecaId = " . $_GET["pecaID"];
$result = mysqli_query($conn, $sql_query);
$result = mysqli_fetch_assoc($result);
?>
<section id="banner">
    <div class="content">
        <center>
            <form method="post" action="scripts/tabelaPecasEdit/edit-peca.php?pecaID=<?php echo $result["pecaId"] ?>" enctype="multipart/form-data">
                <h2>Editar</h2>
                <input type=hidden name=motoID value="<?php echo $result["pecaId"] ?>">
                <label>Foto:</label>
                <input name="foto" value="<?php echo $result["foto"] ?>" accept="image/*" onchange="document.getElementById('foto').src = window.URL.createObjectURL(this.files[0])" type="file"><br>
                <img id="foto" src=" <?php echo $result["foto"] ?> " style="height:400px;width:400px;" >

                <br><label>Grupo:</label>
                <input type="text" name="grupo" style="width:400px" value="<?php echo $result["grupo"] ?>"><br>

                <label>Item:</label>
                <input type="text" name="item" style="width:200px" value="<?php echo $result["item"] ?>"><br>

                <label>Ordem:</label>
                <input type="text" name="ordem" style="width:300px" value="<?php echo $result["ordem"] ?>"><br>

                <label>Parte:</label>
                <input type="text" name="parte" style="width:300px" value="<?php echo $result["parte"] ?>"><br>

                <label>quantidade:</label>
                <input type="text" name="quantidade" style="width:300px" value="<?php echo $result["quantidade"] ?>"><br>

                <label>valor:</label>
                <input type="text" name="valor" style="width:150px" value="<?php echo $result["valor"] ?>"><br>

                <input class="button primary" type="submit" value="Editar">
            </form>
        </center>
    </div>
</section>