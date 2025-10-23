<?php
$sql_query = "SELECT * FROM motocicletas ";
$sql_query .= "WHERE motoId = " . $_GET["motoID"];
$result = mysqli_query($conn, $sql_query);
$result = mysqli_fetch_assoc($result);
?>
<section id="banner">
    <div class="content form">
        <img class="fit logogray" src="./assets/css/images/logo-branco-crop.png">
        <form method="post" action="scripts/tabelaMotos/edit-moto.php" enctype="multipart/form-data">
            <div class="row">
                <div class="col-12">
                    <h2>Editar informações do Veículo</h2>
                </div>
            </div>
            <input type=hidden name=motoID value="<?php echo $result["motoId"] ?>">
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
            
            <!-- Seção para fotos extras -->
            <div class="row">
                <div class="col-12">
                    <h3>Fotos Extras</h3>
                    <p>Adicione fotos extras da motocicleta (peças, detalhes, etc)</p>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <div class="fotos-extras-container">
                        <?php
                        // Buscar fotos extras existentes
                        $sql_fotos_extras = "SELECT * FROM moto_fotos_extras WHERE motoId = " . $_GET["motoID"];
                        $result_fotos = mysqli_query($conn, $sql_fotos_extras);
                        
                        if (mysqli_num_rows($result_fotos) > 0) {
                            echo '<div class="fotos-existentes">';
                            echo '<h4>Fotos existentes:</h4>';
                            echo '<div class="row">';
                            
                            while ($foto = mysqli_fetch_assoc($result_fotos)) {
                                echo '<div class="col-3 foto-extra-item" data-foto-id="' . $foto["id"] . '">';
                                echo '<img src="' . $foto["foto"] . '" alt="Foto extra" />';
                                echo '<p>' . $foto["descricao"] . '</p>';
                                echo '<button type="button" class="button small" onclick="excluirFotoExtra(' . $foto["id"] . ')">Excluir</button>';
                                echo '</div>';
                            }
                            
                            echo '</div>';
                            echo '</div>';
                        }
                        ?>
                        
                        <div class="novas-fotos">
                            <h4>Adicionar novas fotos:</h4>
                            <div class="row">
                                <div class="col-12">
                                    <input type="file" name="fotos_extras[]" multiple />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <label>Descrição das fotos:</label>
                                    <input type="text" name="descricao_fotos" placeholder="Ex: Peça danificada, vista lateral, etc">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <label>Endereço:</label>
                    <input type="text" name="endereco" value="<?php echo $result["endereco"] ?>" required>
                </div>
            </div>

            <div class="row">
                <div class="col-3">
                    <label>Ano:</label>
                    <input type="text" name="ano" value="<?php echo $result["ano"] ?>" minlength="4" maxlength="4" required>
                </div>
                <div class="col-5">
                    <label>Modelo:</label>
                    <input type="text" name="modelo" value="<?php echo $result["modelo"] ?>" required>
                </div>
                <div class="col-4">
                    <label>Marca:</label>
                    <input type="text" name="marca" value="<?php echo $result["marca"] ?>" required>
                </div>
            </div>

            <div class="row">
                <div class="col-6">
                    <label>Proprietario:</label>
                    <input type="text" name="proprietario" value="<?php echo $result["proprietario"] ?>" required>
                </div>
                <div class="col-4">
                    <label>Placa:</label>
                    <input type="text" name="placa" value="<?php echo $result["placa"] ?>" required>
                </div>
                <div class="col-2">
                    <label>KM:</label>
                    <input type="number" name="KM" value="<?php echo $result["km"] ?>" required>
                </div>
            </div>
            <hr>
            <input class="button primary" type="submit" value="Editar Moto">
        </form>
    </div>
</section>

<!-- Script para gerenciar fotos extras -->
<script src="../../assets/js/fotos-extras.js"></script>