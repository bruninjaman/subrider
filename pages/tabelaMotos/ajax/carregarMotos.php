<?php
// Habilitar exibição de erros para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log de debug
function writeLog($message) {
    $logFile = dirname(__FILE__) . '/debug.log';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
}

try {
    // Usar caminho relativo para includes
    $rootPath = dirname(__FILE__) . '/../../../';
    
    // Log dos caminhos para debug
    writeLog("Root Path: " . $rootPath);
    
    // Incluir configurações de conexão e funções necessárias
    require_once($rootPath . "connection/connection.php");
    require_once($rootPath . "scripts/functions.php");

    // Verificar conexão
    if (!$conn) {
        throw new Exception("Erro na conexão com o banco de dados: " . mysqli_connect_error());
    }

    // Recuperar parâmetros da requisição
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $pesquisa = isset($_GET['pesquisa']) ? $_GET['pesquisa'] : '';
    $orderby = isset($_GET['orderby']) ? $_GET['orderby'] : '';

    // Log dos parâmetros recebidos
    writeLog("Parâmetros: page=$page, pesquisa=$pesquisa, orderby=$orderby");

    // Construir consulta SQL com prepared statements
    $sql_query = "SELECT * FROM motocicletas";
    $params = array();

    if (!empty($pesquisa)) {
        $sql_query .= " WHERE (
            modelo LIKE ? OR 
            marca LIKE ? OR 
            placa LIKE ? OR 
            proprietario LIKE ? OR 
            ano LIKE ? OR 
            endereco LIKE ?
        )";
        $searchTerm = "%$pesquisa%";
        $params = array_fill(0, 6, $searchTerm);
    }

    // Ordenação com validação
    $validColumns = ['endereco', 'ano', 'modelo', 'marca', 'placa', 'km', 'proprietario', 'motoId'];
    if (!empty($orderby) && in_array($orderby, $validColumns)) {
        $sql_query .= " ORDER BY $orderby";
    } else {
        $sql_query .= " ORDER BY motoId DESC";
    }

    // Log da query
    writeLog("SQL Query: " . $sql_query);

    // Armazenar consulta sem limite para paginação
    $sql_query_without_limit = $sql_query;

    // Adicionar limitação para a página atual
    $offset = ($page - 1) * 5;
    $sql_query .= " LIMIT ?, ?";
    array_push($params, $offset, 5);

    // Preparar e executar a query
    $stmt = mysqli_prepare($conn, $sql_query);
    if ($stmt === false) {
        throw new Exception("Erro ao preparar query: " . mysqli_error($conn));
    }

    if (!empty($params)) {
        $types = str_repeat('s', count($params));
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Erro ao executar query: " . mysqli_stmt_error($stmt));
    }

    $result = mysqli_stmt_get_result($stmt);

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

<?php
} catch (Exception $e) {
    writeLog("Erro: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
    exit;
} 