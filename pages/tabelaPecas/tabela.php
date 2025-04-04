<section id="banner">
    <div class="content">
        <!-- search bar -->
        <?php
        //Categorias de pesquisa
        $categoriasPesquisa = "SHOW COLUMNS FROM pecas";
        $resultCategorias = mysqli_query($conn, $categoriasPesquisa);
        include_once("./includes/searchbar.php");
        ?>
        <div id="resultados-tabela">
            <div class="table-wrapper">
                <div class="table-wrapper" style="overflow: hidden;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Foto</th>
                                <th><button class="sort" type="button" data-orderby="grupo">Grupo <i class="fa-solid fa-sort"></i></button> </th>
                                <th><button class="sort" type="button" data-orderby="item">Item <i class="fa-solid fa-sort"></i></button></th>
                                <th><button class="sort" type="button" data-orderby="parte">Parte <i class="fa-solid fa-sort"></i></button></th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="tabela-pecas">
                            <?php
                            $sql_query = "SELECT * FROM pecas ";

                            if (isset($_GET["pesquisa"])) {
                                $sql_query .= " WHERE " . strtolower($_GET['selectPesquisa']) . " LIKE '%" . $_GET["pesquisa"] . "%' ";
                            }
                            if (isset($_GET["orderby"])) {
                                $sql_query .= " ORDER BY  " . $_GET["orderby"] . "  ";
                            } else {
                                $sql_query .= " ORDER BY pecas.pecaId DESC "; // Default ordering by latest added
                            }

                            $sql_query_without_limit = $sql_query;
                            $sql_query .= "LIMIT " . ((isset($_GET['page']) ? $_GET['page'] - 1 : 0) * 5) . ", 5";
                            $result = mysqli_query($conn, $sql_query);

                            if (!$result) {
                                // Exibe uma mensagem genérica ao usuário
                                echo "<tr><td colspan='5'>Nenhum resultado encontrado.</td></tr>";
                            } elseif (mysqli_num_rows($result) === 0) {
                                // Se não houver resultados
                                echo "<tr><td colspan='5'>Nenhum resultado encontrado.</td></tr>";
                            } else {
                                while ($peca = mysqli_fetch_assoc($result)) {
                            ?>
                                    <tr>
                                        <td class="img-table"><img src='<?php echo $peca['foto']; ?>'></td>
                                        <td data-cell="Grupo"><?php echo $peca['grupo']; ?></td>
                                        <td data-cell="Item"><?php echo $peca['item']; ?></td>
                                        <td data-cell="Parte"><?php echo $peca['parte']; ?></td>
                                        <td>
                                            <button style="background: none; border: none;" onclick="location.href='tabelaPecasEdit.php?pecaID=<?php echo $peca['pecaId'] ?>'"><img src="./assets/css/images/edit-peca.png" style="height: 30px; width: 30px;"></button>
                                            <button style="background: none; border: none;" onclick="return deletePeca('<?php echo $peca['pecaId']; ?>')"><img src="./assets/css/images/x-button-peca.png" style="height: 30px; width: 30px;"></button>
                                        </td>
                                    </tr>
                            <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                    <div class="row">
                        <div class="col-3">
                            <a class="button primary" href='tabelaPecasAdd.php' style="display: flex; align-items: center; justify-content: center; white-space: nowrap; width: fit-content; min-width: 100%;">
                                <img src="./assets/css/images/addpeca.png" style="margin-right: 12px; width: 40px; height: 40px;">
                                Adicionar Item
                            </a>
                        </div>
                        <div class="col-9" id="paginacao-container">
                            <?php
                            $sql_query = $sql_query_without_limit;
                            pagination($conn, $sql_query);
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Script para carregar a tabela sem recarregar a página -->
<script>
// Função global para carregar os dados via AJAX
window.carregarTabela = function(pagina = 1, pesquisa = '', selectPesquisa = '', orderby = '') {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', '/subrider/pages/tabelaPecas/ajax/carregarPecas.php?page=' + pagina + 
                   '&pesquisa=' + encodeURIComponent(pesquisa) + 
                   '&selectPesquisa=' + encodeURIComponent(selectPesquisa) + 
                   '&orderby=' + encodeURIComponent(orderby), true);
    
    xhr.onload = function() {
        if (this.status == 200) {
            document.getElementById('resultados-tabela').innerHTML = this.responseText;
            
            // Reaplica os eventos após o carregamento
            aplicarEventos();
        }
    };
    
    xhr.send();
};

// Função para aplicar eventos aos elementos carregados
function aplicarEventos() {
    // Eventos para os botões de ordenação
    document.querySelectorAll('.sort').forEach(function(botao) {
        botao.addEventListener('click', function() {
            var orderby = this.getAttribute('data-orderby');
            var pesquisa = document.getElementById('input-pesquisa') ? 
                          document.getElementById('input-pesquisa').value : '';
            var selectPesquisa = document.getElementById('selectPesquisa') ? 
                                document.getElementById('selectPesquisa').value : '';
            
            window.carregarTabela(1, pesquisa, selectPesquisa, orderby);
        });
    });
    
    // Eventos para os links de paginação
    document.querySelectorAll('.paginacao-btn').forEach(function(botao) {
        botao.addEventListener('click', function(e) {
            e.preventDefault();
            
            var pagina = this.getAttribute('data-page');
            var pesquisa = document.getElementById('input-pesquisa') ? 
                          document.getElementById('input-pesquisa').value : '';
            var selectPesquisa = document.getElementById('selectPesquisa') ? 
                                document.getElementById('selectPesquisa').value : '';
            var orderby = document.querySelector('.sort.active') ? 
                         document.querySelector('.sort.active').getAttribute('data-orderby') : '';
            
            window.carregarTabela(pagina, pesquisa, selectPesquisa, orderby);
        });
    });
}

// Inicializar eventos quando o documento carrega
document.addEventListener('DOMContentLoaded', function() {
    aplicarEventos();
});
</script>