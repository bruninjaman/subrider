<?php
$mysqli_query = "SELECT motoID FROM ordem_servicos ";
$mysqli_query .= " WHERE Codigo = '" . $_GET['ordem'] . "'";

$motoid = mysqli_query($conn, $mysqli_query);
$motoid = mysqli_fetch_assoc($motoid);
?>
<section id="banner">
    <div class="content form">
        <img class="fit logogray" src="./assets/css/images/logo-branco-crop.png">
        <center>
            <form method="post" action="scripts\addparametro\add.php?ordem=<?php echo $_GET['ordem'] . "&motoID=" . $motoid['motoID'] ?>">
                <div>
                    <div class="row">
                        <div class="col-8">
                            <label>Descrição</label>
                            <input type="text" name="desc">
                        </div>
                        <div class="col-2">
                            <label>Valor</label>
                            <input type="number" step=".01" value="0" name="value">
                        </div>                       
                        <div class="col-1">
                        </div>
                        <div class="col-6">
                            <br>
                            <input id="submit" class="button primary" type="submit" value="Adicionar">
                        </div>
                        <div class="col-1">
                        </div>
                    </div>
                </div>
            </form>
        </center>
    </div>
</section>