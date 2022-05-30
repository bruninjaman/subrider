<!-- tabela -->
<section id="one" class=crud-table>
    <div class="container">
        <div class="table-wrapper">
            <div class="table-title">
                <div class="row">
                    <div class="col-sm-6">
                        <h2>Gerenciar <b>Peças</b></h2>
                    </div>
                    <div class="col-sm-5">
                        <a href="#addEmployeeModal" class="btn btn-success" data-toggle="modal"><i class="material-icons">&#xE147;</i> <span>Adicionar Peça</span></a>
                        <a data-toggle="modal"><label class="searchbar">Buscar:</label></a><input id='search-box' placeholder='Search'>
                    </div>
                </div>
            </div>
            <table class="table table-striped table-hover table-sortable" id="table-id">
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Grupo</th>
                        <th>Parte</th>
                        <th>Item</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    showPecas($conn, $page);
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
        </div>
        <?php
        require("./assets/php/includes/pecas/add-modal.php");
        require("./assets/php/includes/pecas/edit-modal.php");
        require("./assets/php/includes/pecas/delete-modal.php");
        ?>
    </div>
    <!-- partial -->
    <script src='https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js'></script>
    <script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js'></script>
    <script src="./assets/js/tabela_pecas.js"></script>
    <script src="assets/js/table_sort.js"></script>
    <script src="./assets/js/table.pagination.js"></script>
</section>