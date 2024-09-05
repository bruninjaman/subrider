<?php
$mysqli_query = "SELECT * FROM ordem_servicos ";
$mysqli_query .= "WHERE motoId = " . $_GET['motoID'];
$servicos_result = mysqli_query($conn, $mysqli_query);

$servicos = mysqli_num_rows($servicos_result) > 0 ? true : false;
?>
<section id="banner">
    <div class="content">
        <center>
            <?php if ($servicos) {
                while ($serv = mysqli_fetch_assoc($servicos_result)) {
            ?>
                    <a href="scripts/tabelaMotoServices/open-service.php?ordem=<?php echo $serv['Codigo'] ?>" class="button"><i class="fa-regular fa-folder-open"></i> Ordem Nº<?php echo $serv['Codigo'] ?></a>
                <?php
                }
                ?>
                <hr>
                <br>
                <a class="button primary" href="scripts/tabelaMotoServices/create-service.php?motoID=<?php echo $_GET['motoID'] ?>"><i class="fa-solid fa-folder-plus"></i> Abrir nova ordem de serviço e fechar as outras</a>
                <?php              
            } else {
                ?>
                <a class="button primary" href="scripts/tabelaMotoServices/create-service.php?motoID=<?php echo $_GET['motoID'] ?>"><i class="fa-solid fa-folder-plus"></i> Abrir Ordem de serviço</a>
            <?php
            }
            ?>
        </center>
    </div>
</section>