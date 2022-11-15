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
                    <div class="upload-image">
                        <div class="card thmb">
                            <img src="<?php echo $result["foto"] ?>" alt="preview" />
                            <input type="file" name="foto" /><i class="fas fa-arrow-circle-up"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <label>Grupo:</label>
                    <input type="text" name="grupo" value="<?php echo $result["grupo"] ?>" required>
                </div>
                <div class="col-4">
                    <label>Item:</label>
                    <input type="text" name="item" value="<?php echo $result["item"] ?>" required>
                </div>
                <div class="col-4">
                <label>Parte:</label>
                    <input type="text" name="parte" value="<?php echo $result["parte"] ?>" required>
                </div>
            </div>
            <br>
            <input class="button primary" type="submit" value="Editar">
        </form>
    </div>
</section>