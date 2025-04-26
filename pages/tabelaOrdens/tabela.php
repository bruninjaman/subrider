<section id="banner">
    <div class="content">
        <!-- search bar -->
        <?php
        include_once("./includes/searchbar.php");
        ?>
        <div id="resultados-tabela">
            <div class="table-wrapper">
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Foto</th>
                                <th><button class="sort" type="button" data-orderby="Codigo">Ordem <i class="fa-solid fa-sort"></i></button> </th>
                                <th><button class="sort" type="button" data-orderby="ano">Ano <i class="fa-solid fa-sort"></i></button></th>
                                <th><button class="sort" type="button" data-orderby="modelo">Modelo <i class="fa-solid fa-sort"></i></button></th>
                                <th><button class="sort" type="button" data-orderby="marca">Marca <i class="fa-solid fa-sort"></i></button></th>
                                <th><button class="sort" type="button" data-orderby="proprietario">Proprietario <i class="fa-solid fa-sort"></i></button></th>
                                <th>Ir para ordem</th>
                            </tr>
                        </thead>
                        <tbody id="tabela-ordens">
                            <?php
                            $sql_query = " SELECT * FROM ordem_servicos ";
                            $sql_query .= " LEFT JOIN motocicletas ON motocicletas.motoId = ordem_servicos.motoID ";
                            
                            if (isset($_GET["pesquisa"]) && !empty($_GET["pesquisa"])) {
                                $pesquisa = $_GET["pesquisa"];
                                $selectPesquisa = isset($_GET['selectPesquisa']) && !empty($_GET['selectPesquisa']) ? $_GET['selectPesquisa'] : 'all';
                                
                                if ($selectPesquisa == 'all') {
                                    // Pesquisar em múltiplos campos
                                    $sql_query .= " WHERE (
                                        modelo LIKE '%" . $pesquisa . "%' OR 
                                        marca LIKE '%" . $pesquisa . "%' OR 
                                        proprietario LIKE '%" . $pesquisa . "%' OR 
                                        proprietario_ordem LIKE '%" . $pesquisa . "%' OR 
                                        ano LIKE '%" . $pesquisa . "%' OR 
                                        Codigo LIKE '%" . $pesquisa . "%'
                                    ) ";
                                } else {
                                    // Pesquisa em campo específico
                                    $sql_query .= " WHERE " . strtolower($selectPesquisa) . " LIKE '%" . $pesquisa . "%' ";
                                }
                            }
                            
                            if (isset($_GET["orderby"])) {
                                $sql_query .= " ORDER BY  " . $_GET["orderby"] . "  ";
                            } else {
                                $sql_query .= " ORDER BY ordem_servicos.servID DESC "; // Default ordering by latest added
                            }
                            
                            $sql_query_without_limit = $sql_query;
                            $sql_query .= " LIMIT " . ((isset($_GET['page']) ? $_GET['page'] - 1 : 0) * 5) . ", 5";
                            $result = mysqli_query($conn, $sql_query);
                            if (!$result) {
                                // Exibe uma mensagem genérica ao usuário
                                echo "<tr><td colspan='7'>Nenhum resultado encontrado.</td></tr>";
                            } elseif (mysqli_num_rows($result) === 0) {
                                // Se não houver resultados
                                echo "<tr><td colspan='7'>Nenhum resultado encontrado.</td></tr>";
                            } else {
                                while ($moto = mysqli_fetch_assoc($result)) {
                            ?>
                                    <tr>
                                        <td class="img-table"><img src='<?php echo $moto['foto']; ?>'></td>
                                        <td data-cell="Ordem"><?php echo $moto['Codigo']; ?></td>
                                        <td data-cell="Ano"><?php echo $moto['ano']; ?></td>
                                        <td data-cell="Modelo"><?php echo $moto['modelo']; ?></td>
                                        <td data-cell="Marca"><?php echo $moto['marca']; ?></td>
                                        <td data-cell="Proprietario"><?php echo $moto['proprietario_ordem']; ?></td>
                                        <td>
                                            <style>
                                                .ordembutton {
                                                    background-color: transparent;
                                                    font-size: 1.5em;
                                                    border: none;
                                                }
                                            </style>
                                            <button class="ordembutton" style="color:white;" onclick="location.href='ordemservico.php?ordem=<?php echo $moto['Codigo'] ?>'"><?php echo $moto['Codigo']; ?></button>
                                            <div style="display: flex; gap: 10px; align-items: center;">
                                                <button class="ordemedit" style="background: none; border: none;" onclick="location.href='tabelaOrdensEdit.php?ordem=<?php echo $moto['Codigo'] ?>'"><img src="./assets\css\images\edit-ordem.png" style="height: 2em; width: 2em;"> </button>
                                                <button style="background: none; border: none;" onclick="return deleteServico(<?php echo $moto['motoId'] ?>,'<?php echo $moto['Codigo'] ?>')"><img src="./assets\css\images\x-button.png" style="height: 30px; width: 30px;"></button>
                                            </div>
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
                            <a class="button primary" href='tabelaOrdensAdd.php'>Gerar Ordem de Serviço</a>
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
// Função para carregar os dados via AJAX
window.carregarTabela = function(pagina = 1, pesquisa = '', selectPesquisa = 'all', orderby = '') {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', './ajax/carregarOrdens.php?page=' + pagina + 
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
            
            window.carregarTabela(1, pesquisa, 'all', orderby);
        });
    });
    
    // Eventos para os links de paginação
    document.querySelectorAll('.paginacao-btn').forEach(function(botao) {
        botao.addEventListener('click', function(e) {
            e.preventDefault();
            
            var pagina = this.getAttribute('data-page');
            var pesquisa = document.getElementById('input-pesquisa') ? 
                          document.getElementById('input-pesquisa').value : '';
            var orderby = document.querySelector('.sort.active') ? 
                         document.querySelector('.sort.active').getAttribute('data-orderby') : '';
            
            window.carregarTabela(pagina, pesquisa, 'all', orderby);
        });
    });
}

// Inicializar eventos quando o documento carrega
document.addEventListener('DOMContentLoaded', function() {
    aplicarEventos();
});
</script>