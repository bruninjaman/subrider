<?php
$motocicletas_query = "SELECT * ";
$motocicletas_query .= " FROM motocicletas ";
$motocicletas_query .= " WHERE (SELECT ordem_servicos.motoID FROM ordem_servicos WHERE ordem_servicos.Codigo = '". $_GET['ordem'] . "') = motocicletas.motoId";
$result = mysqli_query($conn, $motocicletas_query); 
?>
<hr>
<section id="banner">
    <div class="content">
        <center>
                <h2>Informações sobre moto no serviço</h2>
                <?php
                    while($moto = mysqli_fetch_assoc($result)) {
                        ?>
                        <ol>
                            <li><?php echo $moto['endereco']?></li>
                            <li><?php echo $moto['proprietario']?></li>
                            <li><?php echo $moto['marca']?></li>
                            <li><?php echo $moto['placa']?></li>
                            <li><?php echo $moto['ano']?></li>
                            <li><?php echo $moto['modelo']?></li>
                            <li><?php echo $moto['km']?></li>
                            <li><img style="height:200px;width:200px;" src="<?php echo $moto['foto']?>"></li>
                        </ol>
                        <?php
                    }
                ?>
        </center>
    </div>
</section>