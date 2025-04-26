<?php
// Incluir configurações de conexão e funções necessárias
include_once($_SERVER['DOCUMENT_ROOT'] . "/subrider/connection/connection.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/subrider/scripts/functions.php");

// Recuperar parâmetros da requisição
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$pesquisa = isset($_GET['pesquisa']) ? $_GET['pesquisa'] : '';
$selectPesquisa = isset($_GET['selectPesquisa']) ? $_GET['selectPesquisa'] : '';
$orderby = isset($_GET['orderby']) ? $_GET['orderby'] : '';

// Construir consulta SQL
$sql_query = "SELECT * FROM servicos ";

// Aplicar filtro de pesquisa
if (!empty($pesquisa)) {
    if ($selectPesquisa == 'all') {
        // Pesquisar em múltiplos campos
        $sql_query .= " WHERE (
            item LIKE '%" . $pesquisa . "%' OR 
            tipo LIKE '%" . $pesquisa . "%'
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
    $sql_query .= " ORDER BY servicos.servicoId DESC "; // Default ordering by latest added
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
        <table class="table servicetable">
            <thead>
                <tr>
                    <th><button class="sort <?php echo ($orderby == 'item') ? 'active' : ''; ?>" type="button" data-orderby="item">Item <i class="fa-solid fa-sort"></i></button></th>
                    <th><button class="sort <?php echo ($orderby == 'tipo') ? 'active' : ''; ?>" type="button" data-orderby="tipo">Tipo <i class="fa-solid fa-sort"></i></button></th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="tabela-servicos">
                <?php
                if (!$result) {
                    echo "<tr><td colspan='3'>Nenhum resultado encontrado.</td></tr>";
                } elseif (mysqli_num_rows($result) === 0) {
                    echo "<tr><td colspan='3'>Nenhum resultado encontrado.</td></tr>";
                } else {
                    while ($servico = mysqli_fetch_assoc($result)) {
                ?>
                        <tr>
                            <td data-cell="Item"><?php echo $servico['item']; ?></td>
                            <td data-cell="Tipo"><?php echo $servico['tipo']; ?></td>
                            <td>
                                <button style="background: none; border: none;" onclick="location.href='/subrider/tabelaServicosEdit.php?servicoID=<?php echo $servico['servicoId'] ?>'"><img src="/subrider/assets/css/images/edit.png" style="height: 30px; width: 30px;"></button>
                                <button style="background: none; border: none;" onclick="return deleteServico('<?php echo $servico['servicoId']; ?>')"><img src="/subrider/assets/css/images/x-button.png" style="height: 30px; width: 30px;"></button>
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
                <a class="button primary" href='../tabelaServicosAdd.php'>Adicionar Serviço</a>
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
// Adicionar o script para a função deleteServico
function deleteServico(servicoID) {
    if (confirm('Deseja realmente excluir este item?')) {
        location.href = '/subrider/scripts/tabelaServicos/delete-serv.php?servID=' + servicoID;
        return true;
    }
    return false;
}
</script> 