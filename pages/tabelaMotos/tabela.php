<section id="banner">
    <div class="content">
        <!-- search bar -->
        <?php
        //Categorias de pesquisa
        $categoriasPesquisa = "SHOW COLUMNS FROM motocicletas";
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
                                <th><button class="sort" type="button" data-orderby="endereco">Endereço <i class="fa-solid fa-sort"></i></button> </th>
                                <th><button class="sort" type="button" data-orderby="ano">Ano <i class="fa-solid fa-sort"></i></button></th>
                                <th><button class="sort" type="button" data-orderby="modelo">Modelo <i class="fa-solid fa-sort"></i></button></th>
                                <th><button class="sort" type="button" data-orderby="marca">Marca <i class="fa-solid fa-sort"></i></button></th>
                                <th><button class="sort" type="button" data-orderby="placa">Placa <i class="fa-solid fa-sort"></i></button></th>
                                <th><button class="sort" type="button" data-orderby="km">KM <i class="fa-solid fa-sort"></i></button></th>
                                <th><button class="sort" type="button" data-orderby="proprietario">Proprietario <i class="fa-solid fa-sort"></i></button></th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="tabela-motos">
                            <?php
                            $sql_query = "SELECT * FROM motocicletas ";

                            if (isset($_GET["pesquisa"])) {
                                $sql_query .= " WHERE " . strtolower($_GET['selectPesquisa']) . " LIKE '%" . $_GET["pesquisa"] . "%' ";
                            }
                            if (isset($_GET["orderby"])) {
                                $sql_query .= " ORDER BY  " . $_GET["orderby"] . "  ";
                            } else {
                                $sql_query .= " ORDER BY motocicletas.motoId DESC "; // Default ordering by latest added (motoId)
                            }

                            $sql_query_without_limit = $sql_query;
                            $sql_query .= "LIMIT " . ((isset($_GET['page']) ? $_GET['page'] - 1 : 0) * 5) . ", 5";
                            $result = mysqli_query($conn, $sql_query);

                            if (!$result) {
                                // Exibe uma mensagem genérica ao usuário
                                echo "<tr><td colspan='9'>Nenhum resultado encontrado.</td></tr>";
                            } elseif (mysqli_num_rows($result) === 0) {
                                // Se não houver resultados
                                echo "<tr><td colspan='9'>Nenhum resultado encontrado.</td></tr>";
                            } else {
                                while ($moto = mysqli_fetch_assoc($result)) {
                            ?>
                                    <tr>
                                        <td class="img-table"><img src='<?php echo $moto['foto']; ?>'></td>
                                        <td data-cell="Endereço"><?php echo $moto['endereco']; ?></td>
                                        <td data-cell="Ano"><?php echo $moto['ano']; ?></td>
                                        <td data-cell="Modelo"><?php echo $moto['modelo']; ?></td>
                                        <td data-cell="Marca"><?php echo $moto['marca']; ?></td>
                                        <td data-cell="Placa"><?php echo $moto['placa']; ?></td>
                                        <td data-cell="Km"><?php echo KMFormat($moto['km']); ?></td>
                                        <td data-cell="Proprietario"><?php echo $moto['proprietario']; ?></td>
                                        <td>
                                            <button style="background: none; border: none;" onclick="location.href='editmotos.php?motoID=<?php echo $moto['motoId'] ?>'"><img src="./assets\css\images\edit-new.png" style="height: 28px; width: 38px;"> </button>
                                            <button style="background: none; border: none;" onclick="return deleteMoto('<?php echo $moto['motoId']; ?>')"><img src="./assets\css\images\x-button-new.png" style="height: 28px; width: 38px;"></button>
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
                            <a class="button primary" href='addmotos.php' style="display: flex; align-items: center; justify-content: center; white-space: nowrap; width: fit-content; min-width: 100%;">
                                <img src="./assets/css/images/addmoto.png" style="margin-right: 12px;">
                                Adicionar Motocicleta
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
    var csrf_token = '<?php echo $_SESSION['csrf_token']; ?>';
    xhr.open('GET', './ajax/carregarMotos.php?page=' + pagina + 
                   '&pesquisa=' + encodeURIComponent(pesquisa) + 
                   '&selectPesquisa=' + encodeURIComponent(selectPesquisa) + 
                   '&orderby=' + encodeURIComponent(orderby) + 
                   '&csrf_token=' + encodeURIComponent(csrf_token), true);
    
    xhr.onload = function() {
        if (this.status == 200) {
            document.getElementById('resultados-tabela').innerHTML = this.responseText;
            
            // Reaplica os eventos após o carregamento
            aplicarEventos();
        } else if (this.status == 403) {
            // Redirecionar para a página de login se a sessão expirou
            window.location.href = '/subrider/login.php';
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
    
    // Atualizar a função delete_confirm para funcionar com AJAX
    window.deleteMoto = function(motoID) {
        if (confirm('Deseja realmente excluir este item?')) {
            location.href = 'scripts/tabelaMotos/delete-moto.php?motoID=' + motoID;
            return true;
        }
        return false;
    };
});
</script>