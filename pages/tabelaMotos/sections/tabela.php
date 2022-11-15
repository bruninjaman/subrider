<section id="banner">
    <div class="content">
        <div class="table-wrapper">
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>Endereço</th>
                            <th>Ano</th>
                            <th>Modelo</th>
                            <th>Marca</th>
                            <th>Placa</th>
                            <th>KM</th>
                            <th>Proprietario</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql_query = "SELECT * FROM motocicletas ";
                        $sql_query .= "LIMIT " . ((isset($_GET['page']) ? $_GET['page'] - 1 : 0) * 5) . ", 5";
                        $result = mysqli_query($conn, $sql_query);
                        while ($moto = mysqli_fetch_assoc($result)) {
                        ?>
                            <tr>
                                <td><img src='<?php echo $moto['foto']; ?>' style="height:100px;width:100px;"></td>
                                <td><?php echo $moto['endereco']; ?></td>
                                <td><?php echo $moto['ano']; ?></td>
                                <td><?php echo $moto['modelo']; ?></td>
                                <td><?php echo $moto['marca']; ?></td>
                                <td><?php echo $moto['placa']; ?></td>
                                <td><?php echo KMFormat($moto['km']); ?></td>
                                <td><?php echo $moto['proprietario']; ?></td>
                                <td>
                                    <!-- <button onclick="location.href='tabelaMotoServices.php?motoID=<?php echo $moto['motoId'] ?>'"><i class="fa-solid fa-toolbox me-2"></i></i> Serviços</button> -->
                                    <!-- <button onclick="location.href='tabelaMotoProprietario.php?motoID=<?php echo $moto['motoId'] ?>'"><i class="fa-solid fa-address-book me-2"></i> Proprietarios</button> -->
                                    <button style="background: none; border: none;" onclick="location.href='tabelaMotoEdit.php?motoID=<?php echo $moto['motoId'] ?>'"><img src="./assets\css\images\edit.png" style="height: 30x; width: 30px;"> </button>
                                    <button style="background: none; border: none;" onclick="return delete_confirm('Deseja realmente excluir este item?',<?php echo $moto['motoId'] ?>)"><img src="./assets\css\images\x-button.png" style="height: 30px; width: 30px;"></button>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-3">
                        <a class="button primary" href='tabelaMotoAdd.php'>Adicionar Motocicleta</a>
                    </div>
                    <div class="col-9">
                        <?php
                        $sql_query = "SELECT * FROM motocicletas ";
                        pagination($conn, $sql_query);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>