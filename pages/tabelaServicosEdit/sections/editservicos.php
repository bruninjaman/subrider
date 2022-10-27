<?php 
$sql_query = "SELECT * FROM servicos ";
$sql_query .= "WHERE servicoId = " . $_GET["servicoID"];
$result = mysqli_query($conn, $sql_query);
$result = mysqli_fetch_assoc($result);
?>
<section id="banner">
    <div class="content">
        <center>
            <form method="post" action="scripts/tabelaServicosEdit/edit-servico.php?servicoID=<?php echo $result["servicoId"] ?>" enctype="multipart/form-data">
                <h2>Editar</h2>
                <input type=hidden name="servicoID" value="<?php echo $result["servicoId"]; ?>">

                <br><label>item:</label>
                <input type="text" name="item" style="width:400px" value=" <?php echo $result["item"] ?> "><br>

                <label>ordem:</label>
                <input type="text" name="ordem" style="width:200px" value=" <?php echo $result["ordem"] ?> "><br>

                <label>tipo:</label>
                <input type="text" name="tipo" style="width:300px" value=" <?php echo $result["tipo"] ?> "><br>

                <label>valor:</label>
                <input type="text" name="valor" style="width:300px" value=" <?php echo $result["valor"] ?> "><br>

                <input class="button primary" type="submit" value="Editar">
            </form>
        </center>
    </div>
</section>