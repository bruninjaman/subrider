<!-- search bar -->
<form action="" method="get">
    <input type="hidden" name="page" value="1">
    <div class="row">
        <div class="col-3">
            <select name="selectPesquisa" id="pesquisa">
                <?php
                //this while is getting all columns from our table
                while ($categorias = mysqli_fetch_assoc($resultCategorias)) {
                    if ($categorias['Field'] == "km" ||$categorias['Field'] == "Id" || $categorias['Field'] == "foto" || $categorias['Field'] == "motoId" || $categorias['Field'] == "ordem" || $categorias['Field'] == "valor" || $categorias['Field'] == "servID" || $categorias['Field'] == "Aberto" || $categorias['Field'] == "pecaId" || $categorias['Field'] == "motoID" || $categorias['Field'] == "quantidade" || $categorias['Field'] == "servicoId") {
                        continue;
                    }
                ?>
                    <option value="<?php echo $categorias['Field'] ?>"><?php echo ucfirst($categorias['Field']) ?></option>
                <?php
                }
                ?>
            </select>
        </div>
        <div class="col-4 search">
            <input type="text" name="pesquisa" placeholder="Pesquisar..">
            <button type="submit"><i class="fa fa-search"></i></button>
        </div>
        
    </div>
</form>
<!-- fim da barra de pesquisa -->
