<?php
$sql_query = "SELECT * FROM motocicletas ";
$sql_query .= "WHERE motoId = " . $_GET["motoID"];
$result = mysqli_query($conn, $sql_query);
$result = mysqli_fetch_assoc($result);

// Exibir mensagens de erro ou sucesso
if (isset($_SESSION['success_message'])) {
    echo '<div class="alert success">' . $_SESSION['success_message'] . '</div>';
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['error_message'])) {
    echo '<div class="alert error">' . $_SESSION['error_message'] . '</div>';
    unset($_SESSION['error_message']);
}

$validation_errors = isset($_SESSION['validation_errors']) ? $_SESSION['validation_errors'] : [];
unset($_SESSION['validation_errors']);
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
                            <img src="<?php echo $result["foto"] ?>" alt="preview" id="foto-preview" />
                            <input type="file" name="foto" id="foto-input" accept="image/*" /><i class="fas fa-arrow-circle-up"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <label>Endereço:</label>
                    <input type="text" name="endereco" value="<?php echo $result["endereco"] ?>" required 
                           class="<?php echo isset($validation_errors['endereco']) ? 'error' : ''; ?>">
                    <?php if (isset($validation_errors['endereco'])): ?>
                        <span class="error-message"><?php echo $validation_errors['endereco']; ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row">
                <div class="col-3">
                    <label>Ano:</label>
                    <input type="text" name="ano" value="<?php echo $result["ano"] ?>" minlength="4" maxlength="4" required
                           class="<?php echo isset($validation_errors['ano']) ? 'error' : ''; ?>">
                    <?php if (isset($validation_errors['ano'])): ?>
                        <span class="error-message"><?php echo $validation_errors['ano']; ?></span>
                    <?php endif; ?>
                </div>
                <div class="col-5">
                    <label>Modelo:</label>
                    <input type="text" name="modelo" value="<?php echo $result["modelo"] ?>" required
                           class="<?php echo isset($validation_errors['modelo']) ? 'error' : ''; ?>">
                    <?php if (isset($validation_errors['modelo'])): ?>
                        <span class="error-message"><?php echo $validation_errors['modelo']; ?></span>
                    <?php endif; ?>
                </div>
                <div class="col-4">
                    <label>Marca:</label>
                    <input type="text" name="marca" value="<?php echo $result["marca"] ?>" required
                           class="<?php echo isset($validation_errors['marca']) ? 'error' : ''; ?>">
                    <?php if (isset($validation_errors['marca'])): ?>
                        <span class="error-message"><?php echo $validation_errors['marca']; ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row">
                <div class="col-6">
                    <label>Proprietario:</label>
                    <input type="text" name="proprietario" value="<?php echo $result["proprietario"] ?>" required
                           class="<?php echo isset($validation_errors['proprietario']) ? 'error' : ''; ?>">
                    <?php if (isset($validation_errors['proprietario'])): ?>
                        <span class="error-message"><?php echo $validation_errors['proprietario']; ?></span>
                    <?php endif; ?>
                </div>
                <div class="col-4">
                    <label>Placa:</label>
                    <input type="text" name="placa" value="<?php echo $result["placa"] ?>" required
                           class="<?php echo isset($validation_errors['placa']) ? 'error' : ''; ?>"
                           pattern="[A-Z]{3}[0-9][A-Z][0-9]{2}" 
                           title="Formato Mercosul: ABC1D23">
                    <?php if (isset($validation_errors['placa'])): ?>
                        <span class="error-message"><?php echo $validation_errors['placa']; ?></span>
                    <?php endif; ?>
                </div>
                <div class="col-2">
                    <label>KM:</label>
                    <input type="number" name="KM" value="<?php echo $result["km"] ?>" required min="0"
                           class="<?php echo isset($validation_errors['KM']) ? 'error' : ''; ?>">
                    <?php if (isset($validation_errors['KM'])): ?>
                        <span class="error-message"><?php echo $validation_errors['KM']; ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <hr>
            <input class="button primary" type="submit" value="Editar Moto">
        </form>
    </div>
</section>

<script>
// Preview da imagem antes do upload
document.getElementById('foto-input').onchange = function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('foto-preview').src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
};
</script>