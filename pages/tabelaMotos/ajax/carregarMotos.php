<?php
// Incluir configurações de conexão e funções necessárias
include_once($_SERVER['DOCUMENT_ROOT'] . "/subrider/connection/connection.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/subrider/scripts/functions.php");

// Recuperar parâmetros da requisição
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$pesquisa = isset($_GET['pesquisa']) ? $_GET['pesquisa'] : '';
$orderby = isset($_GET['orderby']) ? $_GET['orderby'] : '';

// Construir consulta SQL
$sql_query = "SELECT * FROM motocicletas ";

// Aplicar filtro de pesquisa em todas as colunas relevantes
if (!empty($pesquisa)) {
    $sql_query .= " WHERE (
        modelo LIKE '%" . mysqli_real_escape_string($conn, $pesquisa) . "%' OR 
        marca LIKE '%" . mysqli_real_escape_string($conn, $pesquisa) . "%' OR 
        placa LIKE '%" . mysqli_real_escape_string($conn, $pesquisa) . "%' OR 
        proprietario LIKE '%" . mysqli_real_escape_string($conn, $pesquisa) . "%' OR 
        ano LIKE '%" . mysqli_real_escape_string($conn, $pesquisa) . "%' OR 
        endereco LIKE '%" . mysqli_real_escape_string($conn, $pesquisa) . "%'
    )";
}

// Ordenação
if (!empty($orderby)) {
    $sql_query .= " ORDER BY " . mysqli_real_escape_string($conn, $orderby);
} else {
    $sql_query .= " ORDER BY motocicletas.motoId DESC"; // Default ordering by latest added
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
    echo '<tr><td colspan="9" class="text-center">Nenhum resultado encontrado.</td></tr>';
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
                    <th><button class="sort <?php echo ($orderby == 'endereco') ? 'active' : ''; ?>" type="button" data-orderby="endereco">Endereço <i class="fa-solid fa-sort"></i></button></th>
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
                            <button style="background: none; border: none;" onclick="location.href='<?php echo $baseAddress; ?>/editmotos.php?motoID=<?php echo $moto['motoId'] ?>'">
                                <img src="<?php echo $baseAddress; ?>/assets/css/images/edit-new.png" style="height: 28px; width: 38px;">
                            </button>
                            <button style="background: none; border: none;" onclick="return deleteMoto('<?php echo $moto['motoId']; ?>')">
                                <img src="<?php echo $baseAddress; ?>/assets/css/images/x-button-new.png" style="height: 28px; width: 38px;">
                            </button>
                        </td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
        <div class="row">
            <div class="col-3">
                <a class="button primary" href='<?php echo $baseAddress; ?>/addmotos.php' style="display: flex; align-items: center; justify-content: center;">
                    <img src="<?php echo $baseAddress; ?>/assets/css/images/addmoto.png" style="margin-right: 12px;">
                    Adicionar Moto
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
// Função para deletar moto
function deleteMoto(motoID) {
    if (confirm('Deseja realmente excluir este item?')) {
        location.href = '<?php echo $baseAddress; ?>/scripts/tabelaMotos/delete-moto.php?motoID=' + motoID;
        return true;
    }
    return false;
}
</script> 