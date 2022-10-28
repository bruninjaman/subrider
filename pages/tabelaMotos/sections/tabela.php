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
                        $sql_query .= "LIMIT " . ((isset($_GET['page']) ? $_GET['page'] : 0) * 5) . ", 5";
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
                                    <button onclick="location.href='tabelaMotoServices.php?motoID=<?php echo $moto['motoId'] ?>'"><i class="fa-solid fa-toolbox me-2"></i></i> Serviços</button>
                                    <button onclick="location.href='tabelaMotoProprietario.php?motoID=<?php echo $moto['motoId'] ?>'"><i class="fa-solid fa-address-book me-2"></i> Proprietarios</button>
                                    <button onclick="location.href='tabelaMotoAdd.php?motoID=<?php echo $moto['motoId'] ?>'"><i class="fa-solid fa-plus me-2"></i> Adicionar </button>
                                    <button onclick="location.href='tabelaMotoEdit.php?motoID=<?php echo $moto['motoId'] ?>'"><i class="fa-solid fa-user-pen me-2"></i> Editar </button>
                                    <button onclick="return delete_confirm('Deseja realmente excluir este item?',<?php echo $moto['motoId'] ?>)" ><i class="fa-sharp fa-solid fa-delete-left me-2"></i> Deletar</button>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
                <link rel="stylesheet" href="./assets/css/pagination.css">
                <div class="pagination-style">
                    <ul class="pagination">
                        <li><a href="tabelaMotos.php?page=<?php echo isset($_GET['page']) && $_GET['page'] > 0  ? $_GET['page']-1 : 0 ?>">«</a></li>
                        <li><a href="tabelaMotos.php?page=<?php echo 1 ?>">1</a></li>
                        <li><a href="tabelaMotos.php?page=<?php echo 2 ?>">2</a></li>
                        <li><a href="tabelaMotos.php?page=<?php echo 3 ?>">3</a></li>
                        <li><a href="tabelaMotos.php?page=<?php echo 4 ?>">4</a></li>
                        <li><a href="tabelaMotos.php?page=<?php echo 5 ?>">5</a></li>
                        <li><a href="tabelaMotos.php?page=<?php echo 6 ?>">6</a></li>
                        <li><a href="tabelaMotos.php?page=<?php echo isset($_GET['page']) ? $_GET['page']+1 : 0 ?>">»</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>