<?php
$sql_query = "SELECT * FROM servicos ";
$sql_query .= "WHERE servicoId = " . $_GET["servicoID"];
$result = mysqli_query($conn, $sql_query);
$result = mysqli_fetch_assoc($result);
?>
<section id="banner">
    <div class="content form">
        <img class="fit logogray" src="./assets/css/images/logo-branco-crop.png">
        <form method="post" action="scripts/tabelaServicosEdit/edit-servico.php?servicoID=<?php echo $result["servicoId"] ?>" enctype="multipart/form-data">
            <div class="row">
                <div class="col-12">
                    <h2>Editar Serviço</h2>
                </div>
            </div>
            <input type=hidden name="servicoID" value="<?php echo $result["servicoId"]; ?>">
            <div class="row">
                <div class="col-6">

                    <label>tipo:</label>
                    <input type="text" name="tipo" value="<?php echo $result["tipo"] ?>">
                </div>
                <div class="col-6">

                    <label>item:</label>
                    <input type="text" name="item" value="<?php echo $result["item"] ?>">
                </div>
            </div>
            <br>
            <input class="button primary" type="submit" value="Editar">
        </form>
    </div>
</section>