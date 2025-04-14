<?php
// Incluir configurações de conexão e funções necessárias
include_once($_SERVER['DOCUMENT_ROOT'] . "connection/connection.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "scripts/functions.php");

// Recuperar parâmetros da requisição
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$pesquisa = isset($_GET['pesquisa']) ? $_GET['pesquisa'] : '';
$orderby = isset($_GET['orderby']) ? $_GET['orderby'] : '';

// Construir consulta SQL
$sql_query = "SELECT * FROM servicos ";

// Aplicar filtro de pesquisa em todas as colunas relevantes
if (!empty($pesquisa)) {
    $sql_query .= " WHERE (
        item LIKE '%" . $pesquisa . "%' OR 
        tipo LIKE '%" . $pesquisa . "%'
    )";
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
if ($offset < 0) {
    $offset = 0;
    $page = 1;
}
$sql_query .= " LIMIT " . $offset . ", 5";

// Executar consulta
$result = mysqli_query($conn, $sql_query);

// Se não houver resultados
if (!$result || mysqli_num_rows($result) === 0) {
    echo '<tr><td colspan="7" class="text-center">Nenhum resultado encontrado.</td></tr>';
    exit;
}

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
                                <button style="background: none; border: none;" onclick="location.href='/tabelaServicosEdit.php?servicoID=<?php echo $servico['servicoId'] ?>'"><img src="/assets/css/images/edit.png" style="height: 30px; width: 30px;"></button>
                                <button style="background: none; border: none;" onclick="return deleteServico('<?php echo $servico['servicoId']; ?>')"><img src="/assets/css/images/x-button.png" style="height: 30px; width: 30px;"></button>
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
        location.href = '/scripts/tabelaServicos/delete-serv.php?servID=' + servicoID;
        return true;
    }
    return false;
}
</script> 