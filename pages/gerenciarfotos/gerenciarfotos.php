<?php
if (!isset($_GET["motoID"]) || empty($_GET["motoID"])) {
    echo "<script>alert('ID da moto não fornecido!'); window.location.href='tabelaMotos.php';</script>";
    exit;
}

$sql_query = "SELECT * FROM motocicletas WHERE motoId = " . $_GET["motoID"];
$result = mysqli_query($conn, $sql_query);
$moto = mysqli_fetch_assoc($result);

if (!$moto) {
    echo "<script>alert('Moto não encontrada!'); window.location.href='tabelaMotos.php';</script>";
    exit;
}
?>
<section id="banner">
    <div class="content form">
        <img class="fit logogray" src="./assets/css/images/logo-branco-crop.png">
        <form method="post" action="scripts/gerenciarfotos/upload.php" enctype="multipart/form-data">
            <div class="row gtr-uniform">
                <div class="col-12">
                    <h2>Gerenciar Fotos do Veículo</h2>
                </div>
                <div class="col-12 col-md-8">
                    <div class="vehicle-info">
                        <h3><?php echo $moto['marca'] . ' ' . $moto['modelo'] . ' (' . $moto['ano'] . ')'; ?></h3>
                        <p><strong>Placa:</strong> <?php echo $moto['placa']; ?></p>
                        <?php if(!empty($moto['chassi'])): ?>
                        <p><strong>Chassi:</strong> <?php echo $moto['chassi']; ?></p>
                        <?php endif; ?>
                        <?php if(!empty($moto['cor'])): ?>
                        <p><strong>Cor:</strong> <?php echo $moto['cor']; ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <input type="hidden" name="motoID" value="<?php echo $moto["motoId"] ?>">
                    <!-- Foto Principal -->
                    <h4>Foto Principal</h4>
                    <div class="upload-image">
                        <div class="card thmb">
                            <img src="<?php echo $moto["foto"] ?>" alt="preview" />
                            <input type="file" name="foto_principal" /><i class="fas fa-arrow-circle-up"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fotos Adicionais -->
            <div class="row">
                <div class="col-12">
                    <h4>Adicionar Novas Fotos</h4>
                    <div class="upload-multiple">
                        <input type="file" name="fotos[]" id="fotos" multiple accept="image/*">
                        <label for="fotos" class="button primary icon solid fa-images">
                            Selecionar Fotos
                        </label>
                        <div id="file-preview" class="file-preview"></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <ul class="actions">
                        <li><input type="submit" value="Salvar Fotos" class="primary icon solid fa-save" /></li>
                    </ul>
                </div>
            </div>
        </form>

        <!-- Exibição das Fotos Existentes -->
        <div class="row">
            <div class="col-12">
                <h4>Fotos Adicionais</h4>
                <div class="gallery">
                    <?php
                    // Buscar fotos adicionais
                    $sql = "SELECT * FROM moto_fotos WHERE motoId = " . $moto['motoId'];
                    $result = mysqli_query($conn, $sql);

                    if (mysqli_num_rows($result) > 0) {
                        while ($foto = mysqli_fetch_assoc($result)) {
                            echo "<div class='gallery-item'>";
                            echo "<div class='gallery-image'>";
                            echo "<img src='" . $foto['caminho_foto'] . "' alt='Foto Adicional'>";
                            echo "<div class='gallery-actions'>";
                            echo "<a href='scripts/gerenciarfotos/delete.php?id=" . $foto['id'] . "&motoID=" . $moto['motoId'] . "' class='delete-photo' onclick='return confirm(\"Tem certeza que deseja excluir esta foto?\");'><i class='fa fa-trash'></i></a>";
                            echo "</div>";
                            echo "</div>";
                            echo "</div>";
                        }
                    } else {
                        echo "<p>Nenhuma foto adicional cadastrada.</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.vehicle-info {
    background-color: rgba(255, 255, 255, 0.1);
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.vehicle-info h3 {
    margin-bottom: 15px;
    color: #ffffff;
    font-weight: 600;
}

.vehicle-info p {
    margin: 8px 0;
    font-size: 1.1em;
}

.vehicle-info strong {
    font-weight: 600;
}

.gallery {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-top: 20px;
    justify-content: flex-start;
}

.gallery-item {
    width: 200px;
    height: 200px;
    position: relative;
    overflow: hidden;
    border: 1px solid #ddd;
    border-radius: 8px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.gallery-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
}

.gallery-image {
    width: 100%;
    height: 100%;
    position: relative;
}

.gallery-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.gallery-actions {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(0, 0, 0, 0.6);
    padding: 8px;
    display: flex;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.gallery-item:hover .gallery-actions {
    opacity: 1;
}

.gallery-actions a {
    color: white;
    margin: 0 5px;
    font-size: 18px;
}

.upload-multiple {
    margin: 20px 0;
}

.upload-multiple input[type="file"] {
    display: none;
}

.upload-multiple label.button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.75em 2em;
    transition: all 0.3s ease;
}

.file-preview {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-top: 20px;
}

.preview-item {
    width: 120px;
    height: 120px;
    position: relative;
    overflow: hidden;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
}

.preview-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.preview-item span {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(0, 0, 0, 0.6);
    color: white;
    padding: 5px;
    font-size: 12px;
    text-align: center;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

input[type="submit"].icon.fa-save:before,
input[type="submit"].icon.solid.fa-save:before {
    margin-right: 0.5em;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('fotos');
    const filePreview = document.getElementById('file-preview');

    fileInput.addEventListener('change', function() {
        filePreview.innerHTML = '';
        
        if (this.files) {
            for (let i = 0; i < this.files.length; i++) {
                const file = this.files[i];
                if (file.type.match('image.*')) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        const preview = document.createElement('div');
                        preview.className = 'preview-item';
                        preview.innerHTML = `
                            <img src="${e.target.result}" alt="Preview">
                            <span>${file.name}</span>
                        `;
                        filePreview.appendChild(preview);
                    }
                    
                    reader.readAsDataURL(file);
                }
            }
        }
    });
});
</script>