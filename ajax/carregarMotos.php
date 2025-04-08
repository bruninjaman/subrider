<?php
// Incluir configurações de conexão e funções necessárias
require_once("../config.php");

// Verificar CSRF token
if (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== $_SESSION['csrf_token']) {
    http_response_code(403);
    die('Acesso negado: Token CSRF inválido');
}

// Verificar se o usuário está logado
if (!isset($_SESSION['user']) || !isset($_SESSION['type'])) {
    http_response_code(403);
    die('Acesso negado: Usuário não autenticado');
}

// Recuperar e sanitizar parâmetros da requisição
$page = isset($_GET['page']) ? filter_var($_GET['page'], FILTER_VALIDATE_INT) : 1;
$page = ($page < 1) ? 1 : $page;

$pesquisa = isset($_GET['pesquisa']) ? sanitize_input($_GET['pesquisa']) : '';
$selectPesquisa = isset($_GET['selectPesquisa']) ? sanitize_input($_GET['selectPesquisa']) : '';
$orderby = isset($_GET['orderby']) ? sanitize_input($_GET['orderby']) : '';

// Validar campos de ordenação permitidos
$allowed_order_fields = ['endereco', 'ano', 'modelo', 'marca', 'placa', 'km', 'proprietario', 'motoId'];
if (!empty($orderby) && !in_array($orderby, $allowed_order_fields)) {
    $orderby = 'motoId'; // Campo padrão seguro
}

// Validar campos de pesquisa permitidos
$allowed_search_fields = ['endereco', 'ano', 'modelo', 'marca', 'placa', 'proprietario'];
if (!empty($selectPesquisa) && !in_array($selectPesquisa, $allowed_search_fields)) {
    security_log("Tentativa de pesquisa em campo inválido: " . $selectPesquisa, "WARNING");
    die('Campo de pesquisa inválido');
}

// Construir consulta SQL com prepared statement
$sql_query = "SELECT * FROM motocicletas";
$params = [];
$types = "";

// Aplicar filtro de pesquisa
if (!empty($pesquisa) && !empty($selectPesquisa)) {
    $sql_query .= " WHERE " . $selectPesquisa . " LIKE ?";
    $params[] = "%" . $pesquisa . "%";
    $types .= "s";
}

// Ordenação
if (!empty($orderby)) {
    $sql_query .= " ORDER BY " . $orderby;
} else {
    $sql_query .= " ORDER BY motoId DESC";
}

// Armazenar consulta sem limite para paginação
$sql_query_without_limit = $sql_query;
$params_without_limit = $params;
$types_without_limit = $types;

// Adicionar limitação para a página atual
$offset = ($page - 1) * 5;
$sql_query .= " LIMIT ?, ?";
$params[] = $offset;
$params[] = 5;
$types .= "ii";

// Preparar e executar a consulta
$stmt = mysqli_prepare($conn, $sql_query);
if ($stmt) {
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    security_log("Erro na preparação da consulta SQL", "ERROR");
    die('Erro ao processar a consulta');
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
                    echo "<tr><td colspan='9'>Erro ao carregar dados.</td></tr>";
                } elseif (mysqli_num_rows($result) === 0) {
                    echo "<tr><td colspan='9'>Nenhum resultado encontrado.</td></tr>";
                } else {
                    while ($moto = mysqli_fetch_assoc($result)) {
                        // Escapar dados antes de exibir
                        $moto = array_map('escape_output', $moto);
                ?>
                        <tr>
                            <td class="img-table"><img src='<?php echo str_replace("../", "", $moto['foto']); ?>' alt="Foto da moto"></td>
                            <td data-cell="Endereço"><?php echo $moto['endereco']; ?></td>
                            <td data-cell="Ano"><?php echo $moto['ano']; ?></td>
                            <td data-cell="Modelo"><?php echo $moto['modelo']; ?></td>
                            <td data-cell="Marca"><?php echo $moto['marca']; ?></td>
                            <td data-cell="Placa"><?php echo $moto['placa']; ?></td>
                            <td data-cell="Km"><?php echo KMFormat($moto['km']); ?></td>
                            <td data-cell="Proprietario"><?php echo $moto['proprietario']; ?></td>
                            <td>
                                <button style="background: none; border: none;" onclick="location.href='editmotos.php?motoID=<?php echo $moto['motoId']; ?>'">
                                    <img src="assets/css/images/edit-new.png" alt="Editar" style="height: 28px; width: 38px;">
                                </button>
                                <button style="background: none; border: none;" onclick="return deleteMoto('<?php echo $moto['motoId']; ?>')">
                                    <img src="assets/css/images/x-button-new.png" alt="Excluir" style="height: 28px; width: 38px;">
                                </button>
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
                    <img src="assets/css/images/addmoto.png" alt="Adicionar moto" style="margin-right: 12px;">
                    Adicionar Motocicleta
                </a>
            </div>
            <div class="col-9" id="paginacao-container">
                <?php
                // Preparar e executar a consulta de paginação
                $stmt_count = mysqli_prepare($conn, $sql_query_without_limit);
                if ($stmt_count) {
                    if (!empty($params_without_limit)) {
                        mysqli_stmt_bind_param($stmt_count, $types_without_limit, ...$params_without_limit);
                    }
                    mysqli_stmt_execute($stmt_count);
                    $result_count = mysqli_stmt_get_result($stmt_count);
                    pagination($conn, $result_count);
                }
                ?>
            </div>
        </div>
    </div>
</div>

<script>
// Adicionar o script para a função delete_confirm
function deleteMoto(motoID) {
    if (confirm('Deseja realmente excluir este item?')) {
        const csrf_token = '<?php echo $_SESSION['csrf_token']; ?>';
        location.href = `scripts/tabelaMotos/delete-moto.php?motoID=${motoID}&csrf_token=${csrf_token}`;
        return true;
    }
    return false;
}
</script> 