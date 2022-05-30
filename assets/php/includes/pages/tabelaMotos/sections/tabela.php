<!-- tabela -->
<section id="one" class=crud-table>
    <div class="content">
        <div class="container">
            <div class="table-wrapper">
                <div class="table-title">
                    <div class="row">
                        <div class="col-sm-6">
                            <h2>Gerenciar <b>Motocicletas</b></h2>
                        </div>
                        <div class="col-sm-5">
                            <a data-toggle="modal"><label class="searchbar">Buscar:</label></a><input id='search-box' placeholder='Search'>
                            <a href="#addEmployeeModal" class="btn btn-success" data-toggle="modal"><i class="material-icons">&#xE147;</i> <span>Adicionar moto</span></a>
                        </div>
                    </div>
                </div>
                <table class="table table-striped table-hover table-sortable" id="table-id">
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>Proprietario</th>
                            <th>Endereço</th>
                            <th>Marca</th>
                            <th>Placa</th>
                            <th>Ano</th>
                            <th>Modelo</th>
                            <th>KM</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        showMotos($conn, $page);
                        ?>
                    </tbody>
                </table>
                <div class="clearfix">
                    <!--		Start Pagination -->
                    <div class='pagination-container'>
                        <nav>
                            <ul class="pagination">

                                <li data-page="prev">
                                    <span>
                                        < <span class="sr-only">(current)
                                    </span></span>
                                </li>
                                <!--	Here the JS Function Will Add the Rows -->
                                <li data-page="next" id="prev">
                                    <span> > <span class="sr-only">(current)</span></span>
                                </li>
                            </ul>
                        </nav>
                    </div>

                </div> <!-- 		End of Container -->
                </ul>
            </div>
        </div>
    </div>
    <?php
    require("./assets/php/includes/pages/tabelaMotos/modals/add.php");
    require("./assets/php/includes/pages/tabelaMotos/modals/edit.php");
    require("./assets/php/includes/pages/tabelaMotos/modals/owner.php");
    require("./assets/php/includes/pages/tabelaMotos/modals/services.php");
    require("./assets/php/includes/pages/tabelaMotos/modals/delete.php");
    ?>
    <!-- Tabela -->
    <script src='https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js'></script>
    <script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js'></script>
    <script src="./assets/js/tabela_moto.js"></script>
    <script src="./assets/js/table.pagination.js"></script>
</section>