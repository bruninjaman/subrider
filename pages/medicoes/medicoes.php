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
            <form method="post" action="pages/medicoes/add.php?ordem=<?php echo $_GET['ordem'] . "&motoID=" . $motoid['motoID'] ?>">
                <div class="menucadastrar">
                    <div class="row">
                        <div class="col-6">
                            <input id="submit" class="button primary selecionarmotor" type="submit" value="Selecionar motor">
                        </div>
                        <div class="col-6">
                            <input id="submit" class="button primary cadastrarmotor" type="submit" value="Cadastrar motor">
                        </div>
                    </div>
                </div>
                <div class="menupartes" style="display: none;">
                    <div class="row">
                        <div class="col-2">
                            <input id="submit" class="button secondary" type="submit" value="cabeçote">
                        </div>
                        <div class="col-2">
                            <input id="submit" class="button secondary" type="submit" value="motor">
                        </div>
                        <div class="col-2">
                            <input id="submit" class="button secondary" type="submit" value="virabrequim">
                        </div>
                        <div class="col-2">
                            <input id="submit" class="button secondary" type="submit" value="embreagem">
                        </div>
                        <div class="col-2">
                            <input id="submit" class="button secondary" type="submit" value="bombas">
                        </div>
                        <div class="col-2">
                            <input id="submit" class="button secondary" type="submit" value="sair">
                        </div>
                        <div class="col-1">
                        </div>
                    </div>
                </div>
                <div class="menumedicoes" style="display: none;"></div>
            </form>
        </center>
    </div>
</section>