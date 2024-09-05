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
    }
    .imagemoto img {
        margin-right: 100px;
        border-radius: 25px;
        width: 90%;
        height: 50%;
        object-fit: cover;      
    }
</style>
<section id="banner">
    <div class="content motoinfobox">
        <?php
        while ($moto = mysqli_fetch_assoc($result)) {
        ?>
            <div class="row">
                <div class="col-6">
                    <span class="imagemoto">
                        <img src="<?php echo $moto['foto'] ?>">
                    </span>
                </div>
                <div class="col-6">
                    <ul class="motoinfo">
                        <li><b>Endereço:</b> <?php echo $moto['endereco'] ?></li>
                        <li><b>proprietário</b> <?php echo $moto['proprietario'] ?></li>
                        <li><b>Marca</b> <?php echo $moto['marca'] ?></li>
                        <li><b>Placa</b> <?php echo $moto['placa'] ?></li>
                        <li><b>Ano</b> <?php echo $moto['ano'] ?></li>
                        <li><b>Modelo</b> <?php echo $moto['modelo'] ?></li>
                        <li><b>KM</b> <?php echo $moto['km'] ?></li>
                    </ul>
                </div>
            </div>
        <?php
        }
        ?>
    </div>
</section>