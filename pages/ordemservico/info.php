<?php
$motocicletas_query = "SELECT * ";
$motocicletas_query .= " FROM motocicletas ";
$motocicletas_query .= " WHERE (SELECT ordem_servicos.motoID FROM ordem_servicos WHERE ordem_servicos.Codigo = '" . $_GET['ordem'] . "') = motocicletas.motoId";
$result = mysqli_query($conn, $motocicletas_query);
?>
<style>
    .motoinfo {
        list-style-type: none;
        padding: 0;
        text-align: left;
    }

    .motoinfo li {
        padding: 5px;
    }

    .motoinfobox {
        border: 1px solid #ccc;
        border-radius: 5px;
        padding: 6em;
    }

    .imagemoto img {
        margin-right: 100px;
        border-radius: 25px;
        width: 90%;
        height: 50%;
        object-fit: cover;
    }

    #motoinfo {
        display: inline-block;
        margin: 30px;
    }
</style>
<section id="motoinfo">
    <div class="content motoinfobox">
        <?php
        while ($moto = mysqli_fetch_assoc($result)) {
        ?>
            <div class="row moto-info-row">
                <div class="col-6 moto-image-col">
                    <span class="imagemoto">
                        <img src="<?php echo $moto['foto'] ?>" alt="Imagem da Motocicleta">
                    </span>
                </div>
                <div class="col-6 moto-data-col">
                    <ul class="motoinfo">
                        <li><b>Endereço:</b> <span class="info-value"><?php echo $moto['endereco'] ?></span></li>
                        <li><b>Proprietário Atual:</b> <span class="info-value"><?php echo $moto['proprietario'] ?></span></li>
                        <li><b>Marca:</b> <span class="info-value"><?php echo $moto['marca'] ?></span></li>
                        <li><b>Placa:</b> <span class="info-value"><?php echo $moto['placa'] ?></span></li>
                        <li><b>Ano:</b> <span class="info-value"><?php echo $moto['ano'] ?></span></li>
                        <li><b>Modelo:</b> <span class="info-value"><?php echo $moto['modelo'] ?></span></li>
                        <li><b>Quilometragem:</b> <span class="info-value"><?php echo $moto['km'] ?></span></li>
                    </ul>
                </div>
            </div>
        <?php
        }
        ?>
    </div>
</section>