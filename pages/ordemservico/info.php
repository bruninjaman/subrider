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
        padding: 12px;
        margin-bottom: 8px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 1em;
        transition: background-color 0.2s ease;
    }
    
    .motoinfo li:hover {
        background-color: rgba(255, 255, 255, 0.03);
    }

    .motoinfobox {
        border-radius: 8px;
        padding: 20px;
        background-color: rgba(255, 255, 255, 0.05);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
        max-width: 85%;
        margin-left: auto;
        margin-right: auto;
        transition: transform 0.3s ease;
    }
    
    .motoinfobox:hover {
        transform: translateY(-3px);
    }

    .moto-title {
        color: #e44c65;
        font-size: 1.5em;
        margin-bottom: 16px;
        border-bottom: 2px solid rgba(228, 76, 101, 0.3);
        padding-bottom: 10px;
        text-align: center;
    }

    .imagemoto {
        text-align: center;
    }

    .imagemoto img {
        border-radius: 10px;
        width: 85%;
        max-width: 320px;
        height: auto;
        object-fit: cover;
        margin-bottom: 15px;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 3px solid rgba(255, 255, 255, 0.1);
    }
    
    .imagemoto img:hover {
        transform: scale(1.02);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.4);
    }

    .info-label {
        font-weight: bold;
        color: #cccccc;
    }

    .info-value {
        color: #e0e0e0;
        font-weight: 300;
    }

    .moto-info-row {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 20px;
    }

    .moto-image-col {
        flex: 0 0 35%;
        min-width: 250px;
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
        }
        
        .motoinfobox {
            max-width: 95%;
            padding: 15px;
        }
        
        .imagemoto img {
            width: 80%;
        }
        
        .motoinfo li {
            padding: 10px;
            margin-bottom: 6px;
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