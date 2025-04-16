<?php
// Adiciona config
// Caminho absoluto para config.php
require_once(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config.php'); 

// Incluir configurações de conexão e funções necessárias
// Caminhos corrigidos
include_once(PROJECT_ROOT_PATH . DS . "connection" . DS . "connection.php");
include_once(PROJECT_ROOT_PATH . DS . "scripts" . DS . "functions.php");

// Recuperar parâmetros da requisição
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$pesquisa = isset($_GET['pesquisa']) ? $_GET['pesquisa'] : '';
$orderby = isset($_GET['orderby']) ? $_GET['orderby'] : '';

// Construir consulta SQL
$sql_query = " SELECT * FROM ordem_servicos ";
$sql_query .= " LEFT JOIN motocicletas ON motocicletas.motoId = ordem_servicos.motoID ";

// Aplicar filtro de pesquisa em todas as colunas relevantes
if (!empty($pesquisa)) {
    $sql_query .= " WHERE (
        ordem_servicos.Codigo LIKE '%" . $pesquisa . "%' OR 
        motocicletas.modelo LIKE '%" . $pesquisa . "%' OR 
        motocicletas.marca LIKE '%" . $pesquisa . "%' OR 
        motocicletas.placa LIKE '%" . $pesquisa . "%' OR 
        motocicletas.proprietario LIKE '%" . $pesquisa . "%' OR 
        motocicletas.ano LIKE '%" . $pesquisa . "%'
    )";
}

// Ordenação
if (!empty($orderby)) {
    $sql_query .= " ORDER BY " . $orderby . " ";
} else {
    $sql_query .= " ORDER BY ordem_servicos.servID DESC "; // Default ordering by latest added
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
        <table class="table">
            <thead>
                <tr>
                    <th>Foto</th>
                    <th><button class="sort <?php echo ($orderby == 'Codigo') ? 'active' : ''; ?>" type="button" data-orderby="Codigo">Ordem <i class="fa-solid fa-sort"></i></button> </th>
                    <th><button class="sort <?php echo ($orderby == 'ano') ? 'active' : ''; ?>" type="button" data-orderby="ano">Ano <i class="fa-solid fa-sort"></i></button></th>
                    <th><button class="sort <?php echo ($orderby == 'modelo') ? 'active' : ''; ?>" type="button" data-orderby="modelo">Modelo <i class="fa-solid fa-sort"></i></button></th>
                    <th><button class="sort <?php echo ($orderby == 'marca') ? 'active' : ''; ?>" type="button" data-orderby="marca">Marca <i class="fa-solid fa-sort"></i></button></th>
                    <th><button class="sort <?php echo ($orderby == 'proprietario') ? 'active' : ''; ?>" type="button" data-orderby="proprietario">Proprietario <i class="fa-solid fa-sort"></i></button></th>
                    <th>Ir para ordem</th>
                </tr>
            </thead>
            <tbody id="tabela-ordens">
                <?php
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
                            <td class="img-table"><img src='<?php echo PROJECT_ROOT_URL . "/" . str_replace(["../", "..\\"], "", $moto['foto']); ?>'></td>
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
                                <button class="ordembutton" style="color:white;" onclick="location.href='<?php echo PROJECT_ROOT_URL; ?>/ordemservico.php?ordem=<?php echo $moto['Codigo'] ?>'"><?php echo $moto['Codigo']; ?></button>
                                <button class="ordemedit" style="background: none; border: none;" onclick="location.href='<?php echo PROJECT_ROOT_URL; ?>/tabelaOrdensEdit.php?ordem=<?php echo $moto['Codigo'] ?>'"><img src="<?php echo PROJECT_ROOT_URL; ?>/assets/css/images/edit-ordem.png" style="height: 2em; width: 2em;"> </button>
                                <button style="background: none; border: none;" onclick="return deleteServico('<?php echo $moto['servID']; ?>','<?php echo $moto['Codigo']; ?>')"><img src="<?php echo PROJECT_ROOT_URL; ?>/assets/css/images/x-button.png" style="height: 30px; width: 30px;"></button>
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
                <a class="button primary" href='<?php echo PROJECT_ROOT_URL; ?>/tabelaOrdensAdd.php'>Gerar Ordem de Serviço</a>
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
function deleteServico(ordemID, Ordem) {
    if (confirm('Deseja realmente excluir este item?')) {
        location.href = '<?php echo PROJECT_ROOT_URL; ?>/scripts/tabelaOrdensDelete/delete-service.php?ordemID=' + ordemID + '&Ordem=' + Ordem;
        return true;
    }
    return false;
}
</script> 