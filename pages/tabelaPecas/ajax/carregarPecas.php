<?php
// Incluir configurações de conexão e funções necessárias
include_once(__DIR__ . "/../../../connection/connection.php");
include_once(__DIR__ . "/../../../scripts/functions.php");
include_once(__DIR__ . "/../../../config.php");

// Recuperar parâmetros da requisição
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$pesquisa = isset($_GET['pesquisa']) ? $_GET['pesquisa'] : '';
$selectPesquisa = isset($_GET['selectPesquisa']) ? $_GET['selectPesquisa'] : '';
$orderby = isset($_GET['orderby']) ? $_GET['orderby'] : '';

// Construir consulta SQL
$sql_query = "SELECT * FROM pecas ";

// Aplicar filtro de pesquisa
if (!empty($pesquisa)) {
    if ($selectPesquisa == 'all') {
        // Pesquisar em múltiplos campos
        $sql_query .= " WHERE (
            item LIKE '%" . $pesquisa . "%' OR 
            grupo LIKE '%" . $pesquisa . "%' OR 
            parte LIKE '%" . $pesquisa . "%'
        ) ";
    } elseif (!empty($selectPesquisa)) {
        // Pesquisa em campo específico
        $sql_query .= " WHERE " . strtolower($selectPesquisa) . " LIKE '%" . $pesquisa . "%' ";
    }
}

// Ordenação
if (!empty($orderby)) {
    $sql_query .= " ORDER BY " . $orderby . " ";
} else {
    $sql_query .= " ORDER BY pecas.pecaId DESC "; // Default ordering by latest added
}

// Armazenar consulta sem limite para paginação
$sql_query_without_limit = $sql_query;

// Adicionar limitação para a página atual
$offset = ($page - 1) * 5;
$sql_query .= " LIMIT " . $offset . ", 5";

// Executar consulta
$result = mysqli_query($conn, $sql_query);

// HTML da tabela
?>
<div class="table-wrapper">
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Foto</th>
                    <th><button class="sort <?php echo ($orderby == 'Grupo') ? 'active' : ''; ?>" type="button" data-orderby="Grupo">Grupo <i class="fa-solid fa-sort"></i></button> </th>
                    <th><button class="sort <?php echo ($orderby == 'Item') ? 'active' : ''; ?>" type="button" data-orderby="Item">Item <i class="fa-solid fa-sort"></i></button></th>
                    <th><button class="sort <?php echo ($orderby == 'Parte') ? 'active' : ''; ?>" type="button" data-orderby="Parte">Parte <i class="fa-solid fa-sort"></i></button></th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="tabela-pecas">
                <?php
                if (!$result) {
                    echo "<tr><td colspan='5'>Nenhum resultado encontrado.</td></tr>";
                } elseif (mysqli_num_rows($result) === 0) {
                    echo "<tr><td colspan='5'>Nenhum resultado encontrado.</td></tr>";
                } else {
                    while ($peca = mysqli_fetch_assoc($result)) {
                ?>
                        <tr>
                            <td class="img-table"><img src='<?php echo $baseAddress . "/" . str_replace("../", "", $peca['foto']); ?>'></td>
                            <td data-cell="Grupo"><?php echo $peca['grupo']; ?></td>
                            <td data-cell="Item"><?php echo $peca['item']; ?></td>
                            <td data-cell="Parte"><?php echo $peca['parte']; ?></td>
                            <td>
                                <button style="background: none; border: none;" onclick="location.href='<?php echo $baseAddress; ?>/tabelaPecasEdit.php?pecaID=<?php echo $peca['pecaId'] ?>'"><img src="<?php echo $baseAddress; ?>/assets/css/images/edit-peca.png" style="height: 30px; width: 30px;"></button>
                                <button style="background: none; border: none;" onclick="return deletePeca('<?php echo $peca['pecaId']; ?>')"><img src="<?php echo $baseAddress; ?>/assets/css/images/x-button-peca.png" style="height: 30px; width: 30px;"></button>
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
                <a class="button primary" href='<?php echo $baseAddress; ?>/tabelaPecasAdd.php' style="display: flex; align-items: center; justify-content: center; white-space: nowrap; width: fit-content; min-width: 100%;">
                    <img src="<?php echo $baseAddress; ?>/assets/css/images/addpeca.png" style="margin-right: 12px; width: 40px; height: 40px;">
                    Adicionar Item
                </a>
            </div>
            <div class="col-9" id="paginacao-container">
                <?php
                pagination($conn, $sql_query_without_limit);
                ?>
            </div>
        </div>
    </div>
</div>

<script>
// Adicionar o script para a função deletePeca
function deletePeca(pecaID) {
    if (confirm('Deseja realmente excluir este item?')) {
        location.href = '<?php echo $baseAddress; ?>/scripts/tabelaPecasDelete/delete-peca.php?pecaID=' + pecaID;
        return true;
    }
    return false;
}
</script> 