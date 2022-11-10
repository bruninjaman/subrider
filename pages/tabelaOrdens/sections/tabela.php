<section id="banner">
    <div class="content">
        <div class="table-wrapper">
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Ordem</th>
                            <th>Foto</th>
                            <th>Ano</th>
                            <th>Modelo</th>
                            <th>Marca</th>
                            <th>Proprietario</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql_query = " SELECT * FROM ordem_servicos ";
                        $sql_query .= " LEFT JOIN motocicletas ON motocicletas.motoId = ordem_servicos.motoID ";
                        $sql_query .= " LIMIT " . ((isset($_GET['page']) ? $_GET['page'] - 1 : 0) * 5) . ", 5";
                        $result = mysqli_query($conn, $sql_query);
                        while ($moto = mysqli_fetch_assoc($result)) {
                        ?>
                            <tr>
                                <td><?php echo $moto['Codigo']; ?></td>
                                <td><img src='<?php echo $moto['foto']; ?>' style="height:100px;width:100px;"></td>
                                <td><?php echo $moto['ano']; ?></td>
                                <td><?php echo $moto['modelo']; ?></td>
                                <td><?php echo $moto['marca']; ?></td>
                                <td><?php echo $moto['proprietario']; ?></td>
                                <td>
                                    <!-- <button onclick="location.href='tabelaMotoServices.php?motoID=<?php echo $moto['motoId'] ?>'"><i class="fa-solid fa-toolbox me-2"></i></i> Serviços</button> -->
                                    <!-- <button onclick="location.href='tabelaMotoProprietario.php?motoID=<?php echo $moto['motoId'] ?>'"><i class="fa-solid fa-address-book me-2"></i> Proprietarios</button> -->
                                    <button onclick="location.href='services.php?ordem=<?php echo $moto['Codigo'] ?>'"><i class="fa-sharp fa-solid fa-delete-left me-2"></i> Abrir Ordem <?php echo $moto['Codigo']; ?></button>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-3">
                        <a class="button primary" href='tabelaOrdensAdd.php'>Gerar Ordem de Serviço</a>
                    </div>
                    <div class="col-9">
                        <?php
                        $sql_query = " SELECT * FROM ordem_servicos ";
                        $sql_query .= " LEFT JOIN motocicletas ON motocicletas.motoId = ordem_servicos.motoID ";
                        pagination($conn, $sql_query);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>