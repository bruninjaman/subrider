<section id="banner">
    <div class="content">
        <center>
            <form method="post" action="assets/php/scripts/tabelaMotos/add-moto.php">
                <h2>Pagina de ordem nº <?php echo $_GET['ordem']?></h2>
        </center>

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
                        $page = 1;
                        $sql_query = "SELECT * FROM motocicletas ";
                        $sql_query .= "LIMIT " . $page * 5;
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
                                <td><?php echo $moto['km']; ?></td>
                                <td><?php echo $moto['proprietario']; ?></td>
                                <td>
                                    <button onclick="location.href='tabelaMotoServices.php?motoID=<?php echo $moto['motoId'] ?>'"><i class="fa-solid fa-toolbox me-2"></i></i> Serviços</button>
                                    <button onclick="location.href='tabelaMotoProprietario.php?motoID=<?php echo $moto['motoId'] ?>'"><i class="fa-solid fa-address-book me-2"></i> Proprietarios</button>
                                    <button onclick="location.href='tabelaMotoAdd.php?motoID=<?php echo $moto['motoId'] ?>'"><i class="fa-solid fa-plus me-2"></i> Adicionar </button>
                                    <button onclick="location.href='tabelaMotoEdit.php?motoID=<?php echo $moto['motoId'] ?>'"><i class="fa-solid fa-user-pen me-2"></i> Editar </button>
                                    <button onclick="location.href='assets/php/scripts/tabelaMotos/delete-moto.php?motoID=<?php echo $moto['motoId'] ?>'" ><i class="fa-sharp fa-solid fa-delete-left me-2"></i> Deletar</button>
                                    <!-- <button onclick="return confirm('Deseja realmente excluir este item?')" ><i class="fa-sharp fa-solid fa-delete-left me-2"></i> Deletar</button> -->
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
                <div class="w3-twothird">
                    <ul class="pagination">
                        <li><a href="javascript:void(0)">«</a></li>
                        <li><a class="w3-red" href="javascript:void(0)">1</a></li>
                        <li><a href="javascript:void(0)">2</a></li>
                        <li><a href="javascript:void(0)">3</a></li>
                        <li><a href="javascript:void(0)">4</a></li>
                        <li><a href="javascript:void(0)">5</a></li>
                        <li><a href="javascript:void(0)">6</a></li>
                        <li><a href="javascript:void(0)">»</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>