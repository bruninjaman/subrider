<?php
// Incluir configurações de conexão e funções necessárias
include_once($_SERVER['DOCUMENT_ROOT'] . "/subrider/connection/connection.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/subrider/scripts/functions.php");

// Recuperar parâmetros da requisição
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$pesquisa = isset($_GET['pesquisa']) ? $_GET['pesquisa'] : '';
$orderby = isset($_GET['orderby']) ? $_GET['orderby'] : '';

// Construir consulta SQL
$sql_query = "SELECT * FROM pecas ";

// Aplicar filtro de pesquisa em todas as colunas relevantes
if (!empty($pesquisa)) {
    $sql_query .= " WHERE (
        grupo LIKE '%" . mysqli_real_escape_string($conn, $pesquisa) . "%' OR 
        item LIKE '%" . mysqli_real_escape_string($conn, $pesquisa) . "%' OR 
        parte LIKE '%" . mysqli_real_escape_string($conn, $pesquisa) . "%'
    )";
}

// Ordenação
if (!empty($orderby)) {
    $sql_query .= " ORDER BY " . mysqli_real_escape_string($conn, $orderby);
} else {
    $sql_query .= " ORDER BY pecas.pecaId DESC"; // Default ordering by latest added
}

// Armazenar consulta sem limite para paginação
$sql_query_without_limit = $sql_query;

// Adicionar limitação para a página atual
$offset = ($page - 1) * 5;
$sql_query .= " LIMIT $offset, 5";

// Executar consulta
$result = mysqli_query($conn, $sql_query);

// Se não houver resultados
if (!$result || mysqli_num_rows($result) === 0) {
    echo '<tr><td colspan="5" class="text-center">Nenhum resultado encontrado.</td></tr>';
    exit;
}

// HTML da tabela
?>
<div class="table-wrapper">
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Foto</th>
                    <th><button class="sort <?php echo ($orderby == 'grupo') ? 'active' : ''; ?>" type="button" data-orderby="grupo">Grupo <i class="fa-solid fa-sort"></i></button></th>
                    <th><button class="sort <?php echo ($orderby == 'item') ? 'active' : ''; ?>" type="button" data-orderby="item">Item <i class="fa-solid fa-sort"></i></button></th>
                    <th><button class="sort <?php echo ($orderby == 'parte') ? 'active' : ''; ?>" type="button" data-orderby="parte">Parte <i class="fa-solid fa-sort"></i></button></th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="tabela-pecas">
                <?php
                while ($peca = mysqli_fetch_assoc($result)) {
                ?>
                    <tr>
                        <td class="img-table">
                            <?php if (!empty($peca['foto'])) : ?>
                                <img src='<?php echo str_replace("../", "", $peca['foto']); ?>'>
                            <?php else : ?>
                                <img src="/subrider/assets/css/images/no-image.jpg">
                            <?php endif; ?>
                        </td>
                        <td data-cell="Grupo"><?php echo $peca['grupo']; ?></td>
                        <td data-cell="Item"><?php echo $peca['item']; ?></td>
                        <td data-cell="Parte"><?php echo $peca['parte']; ?></td>
                        <td>
                            <button style="background: none; border: none;" onclick="location.href='/subrider/tabelaPecasEdit.php?pecaID=<?php echo $peca['pecaId'] ?>'"><img src="/subrider/assets/css/images/edit-peca.png" style="height: 30px; width: 30px;"></button>
                            <button style="background: none; border: none;" onclick="return deletePeca('<?php echo $peca['pecaId']; ?>')"><img src="/subrider/assets/css/images/x-button-peca.png" style="height: 30px; width: 30px;"></button>
                        </td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
        <div class="row">
            <div class="col-3">
                <a class="button primary" href='../tabelaPecasAdd.php' style="display: flex; align-items: center; justify-content: center; white-space: nowrap; width: fit-content; min-width: 100%;">
                    <img src="/subrider/assets/css/images/addpeca.png" style="margin-right: 12px; width: 40px; height: 40px;">
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
        location.href = '/subrider/scripts/tabelaPecas/delete-peca.php?pecaID=' + pecaID;
        return true;
    }
    return false;
}
</script> 