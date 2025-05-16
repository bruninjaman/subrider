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
        padding: 9px;
        margin-bottom: 6px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 1em;
    }

    .motoinfobox {
        border-radius: 5px;
        padding: 18px;
        background-color: rgba(255, 255, 255, 0.05);
        margin-bottom: 20px;
        max-width: 80%;
        margin-left: auto;
        margin-right: auto;
    }

    .moto-title {
        color: #e44c65;
        font-size: 1.4em;
        margin-bottom: 14px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding-bottom: 9px;
        text-align: center;
    }

    .imagemoto {
        text-align: center;
    }

    .imagemoto img {
        border-radius: 10px;
        width: 85%;
        max-width: 300px;
        height: auto;
        object-fit: cover;
        margin-bottom: 15px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .info-label {
        font-weight: bold;
    }

    .info-value {
        color: #e0e0e0;
    }

    .moto-info-row {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
    }

    .moto-image-col {
        flex: 0 0 35%;
        min-width: 250px;
        padding-right: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .moto-data-col {
        flex: 0 0 55%;
        min-width: 280px;
    }
    
    @media screen and (max-width: 768px) {
        .moto-info-row {
            flex-direction: column;
        }
        
        .moto-image-col, 
        .moto-data-col {
            width: 100%;
            padding-right: 0;
        }
        
        .motoinfobox {
            padding: 14px;
        }
        
        .imagemoto img {
            margin-right: 0;
            width: 75%;
        }
    }
</style>
<section id="motoinfo">
    <div class="motoinfobox">
        <div class="moto-title">Informações da Motocicleta</div>
        <?php
        while ($moto = mysqli_fetch_assoc($result)) {
        ?>
            <div class="moto-info-row">
                <div class="moto-image-col">
                    <div class="imagemoto">
                        <img src="<?php echo $moto['foto'] ?>" alt="Imagem da Motocicleta">
                    </div>
                </div>
                <div class="moto-data-col">
                    <ul class="motoinfo">
                        <li>
                            <span class="info-label">Proprietário:</span>
                            <span class="info-value"><?php echo $moto['proprietario'] ?></span>
                        </li>
                        <li>
                            <span class="info-label">Endereço:</span>
                            <span class="info-value"><?php echo $moto['endereco'] ?></span>
                        </li>
                        <li>
                            <span class="info-label">Marca:</span>
                            <span class="info-value"><?php echo $moto['marca'] ?></span>
                        </li>
                        <li>
                            <span class="info-label">Modelo:</span>
                            <span class="info-value"><?php echo $moto['modelo'] ?></span>
                        </li>
                        <li>
                            <span class="info-label">Placa:</span>
                            <span class="info-value"><?php echo $moto['placa'] ?></span>
                        </li>
                        <li>
                            <span class="info-label">Ano:</span>
                            <span class="info-value"><?php echo $moto['ano'] ?></span>
                        </li>
                        <li>
                            <span class="info-label">Quilometragem:</span>
                            <span class="info-value"><?php echo $moto['km'] ?></span>
                        </li>
                    </ul>
                </div>
            </div>
        <?php
        }
        ?>
    </div>
</section>