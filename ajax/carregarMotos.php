<?php
// Incluir configurações de conexão e funções necessárias
include_once("../connection/connection.php");
include_once("../scripts/functions.php");

// Recuperar parâmetros da requisição
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$pesquisa = isset($_GET['pesquisa']) ? $_GET['pesquisa'] : '';
$selectPesquisa = isset($_GET['selectPesquisa']) ? $_GET['selectPesquisa'] : '';
$orderby = isset($_GET['orderby']) ? $_GET['orderby'] : '';

// Construir consulta SQL
$sql_query = "SELECT * FROM motocicletas ";

// Aplicar filtro de pesquisa
if (!empty($pesquisa) && !empty($selectPesquisa)) {
    $sql_query .= " WHERE " . strtolower($selectPesquisa) . " LIKE '%" . $pesquisa . "%' ";
}

// Ordenação
if (!empty($orderby)) {
    $sql_query .= " ORDER BY " . $orderby . " ";
} else {
    $sql_query .= " ORDER BY motocicletas.motoId DESC "; // Default ordering by latest added
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
    <div class="table-wrapper" style="overflow: hidden;">
        <table class="table">
            <thead>
                <tr>
                    <th>Foto</th>
                    <th><button class="sort <?php echo ($orderby == 'endereco') ? 'active' : ''; ?>" type="button" data-orderby="endereco">Endereço <i class="fa-solid fa-sort"></i></button> </th>
                    <th><button class="sort <?php echo ($orderby == 'ano') ? 'active' : ''; ?>" type="button" data-orderby="ano">Ano <i class="fa-solid fa-sort"></i></button></th>
                    <th><button class="sort <?php echo ($orderby == 'modelo') ? 'active' : ''; ?>" type="button" data-orderby="modelo">Modelo <i class="fa-solid fa-sort"></i></button></th>
                    <th><button class="sort <?php echo ($orderby == 'marca') ? 'active' : ''; ?>" type="button" data-orderby="marca">Marca <i class="fa-solid fa-sort"></i></button></th>
                    <th><button class="sort <?php echo ($orderby == 'placa') ? 'active' : ''; ?>" type="button" data-orderby="placa">Placa <i class="fa-solid fa-sort"></i></button></th>
                    <th><button class="sort <?php echo ($orderby == 'km') ? 'active' : ''; ?>" type="button" data-orderby="km">KM <i class="fa-solid fa-sort"></i></button></th>
                    <th><button class="sort <?php echo ($orderby == 'proprietario') ? 'active' : ''; ?>" type="button" data-orderby="proprietario">Proprietario <i class="fa-solid fa-sort"></i></button></th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="tabela-motos">
                <?php
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
                            <td class="img-table"><img src='<?php echo str_replace("../", "", $moto['foto']); ?>'></td>
                            <td data-cell="Endereço"><?php echo $moto['endereco']; ?></td>
                            <td data-cell="Ano"><?php echo $moto['ano']; ?></td>
                            <td data-cell="Modelo"><?php echo $moto['modelo']; ?></td>
                            <td data-cell="Marca"><?php echo $moto['marca']; ?></td>
                            <td data-cell="Placa"><?php echo $moto['placa']; ?></td>
                            <td data-cell="Km"><?php echo KMFormat($moto['km']); ?></td>
                            <td data-cell="Proprietario"><?php echo $moto['proprietario']; ?></td>
                            <td>
                                <button style="background: none; border: none;" onclick="location.href='editmotos.php?motoID=<?php echo $moto['motoId'] ?>'"><img src="assets/css/images/edit-new.png" style="height: 28px; width: 38px;"> </button>
                                <button style="background: none; border: none;" onclick="return deleteMoto('<?php echo $moto['motoId']; ?>')"><img src="assets/css/images/x-button-new.png" style="height: 28px; width: 38px;"></button>
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
                    <img src="assets/css/images/addmoto.png" style="margin-right: 12px;">
                    Adicionar Motocicleta
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
// Adicionar o script para a função delete_confirm
function deleteMoto(motoID) {
    if (confirm('Deseja realmente excluir este item?')) {
        location.href = 'scripts/tabelaMotos/delete-moto.php?motoID=' + motoID;
        return true;
    }
    return false;
}
</script> 