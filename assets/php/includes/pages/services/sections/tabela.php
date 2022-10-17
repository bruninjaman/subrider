<section id="banner">
    <div class="content">
        <center>
            <form method="post" action="assets/php/scripts/tabelaMotos/add-moto.php">
                <h2>Pagina de ordem nº <?php echo $_GET['ordem'] ?></h2>
        </center>

        <div class="table-wrapper">
            <div class="table-wrapper">
                <?php
                $page = 5;
                $items_ordem_query = 'SELECT 1 as type, pecas.grupo grupo, pecas.parte parte, pecas.item item, pecas.foto foto, pecas.valor, pecas.quantidade FROM pecas ';
                $items_ordem_query .= "WHERE pecas.ordem = '". $_GET['ordem']."' ";
                $items_ordem_query .= "UNION ";
                $items_ordem_query .= "SELECT 2, Null, Null, servicos.item, Null, servicos.valor, 1 FROM servicos ";
                $items_ordem_query .= "WHERE servicos.ordem = '". $_GET['ordem']."' ";
                $items_ordem_query .= "UNION ";
                $items_ordem_query .= "SELECT 3, null, null, adiantamento.descricao, Null, adiantamento.valor, 1 FROM adiantamento ";
                $items_ordem_query .= "WHERE adiantamento.ordem = '". $_GET['ordem']."' ";
                $items_ordem_query .= "LIMIT " . $page * 5;
                $result = mysqli_query($conn, $items_ordem_query);
                ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th colspan=3>Grupo/Parte/Item</th>
                            <th>Quantidade</th>
                            <th>Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        while ($item = mysqli_fetch_assoc($result)) {
                        ?>
                            <tr>
                                <td><img src='<?php echo $item['foto']; ?>' style="height:100px;width:100px;"></td>
                                <td colspan=3><?php echo $item['grupo'] != null ? "".$item['grupo'] : ""; ?><?php echo $item['parte'] != null ? "/".$item['parte']. "/" : ""; ?><?php echo $item['item'] != null ? "".$item['item'] : ""; ?></td>
                                <td><?php echo $item['quantidade']; ?></td>
                                <td><?php echo $item['valor']; ?></td>
                                <td>
                                    <button onclick="location.href='#'"><i class="fa-solid fa-plus me-2"></i> Adicionar </button>
                                    <button onclick="location.href='#'"><i class="fa-solid fa-user-pen me-2"></i> Editar </button>
                                    <button onclick="location.href='#'"><i class="fa-sharp fa-solid fa-delete-left me-2"></i> Deletar</button>
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