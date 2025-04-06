<?php
require_once '../includes/session_manager.php';
require_once '../includes/database.php';

$session = new SessionManager();

// Verifica se o cliente está logado
if (!$session->isClienteLoggedIn()) {
    header('Location: login.php');
    exit;
}

$clienteId = $session->getClienteId();
$db = new Database();

// Parâmetros de paginação
$pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$porPagina = 10;
$offset = ($pagina - 1) * $porPagina;

// Filtros
$status = $_GET['status'] ?? '';
$moto = $_GET['moto'] ?? '';
$dataInicio = $_GET['data_inicio'] ?? '';
$dataFim = $_GET['data_fim'] ?? '';

// Constrói a query base
$sqlBase = "FROM ordens_servico os
           JOIN motocicletas m ON os.motocicleta_id = m.id
           WHERE m.proprietario_id = ?
           AND os.deleted_at IS NULL";
$params = [$clienteId];

// Aplica filtros
if ($status) {
    $sqlBase .= " AND os.status = ?";
    $params[] = $status;
}
if ($moto) {
    $sqlBase .= " AND m.id = ?";
    $params[] = $moto;
}
if ($dataInicio) {
    $sqlBase .= " AND os.data_entrada >= ?";
    $params[] = $dataInicio;
}
if ($dataFim) {
    $sqlBase .= " AND os.data_entrada <= ?";
    $params[] = $dataFim;
}

// Busca total de registros
$sqlCount = "SELECT COUNT(*) as total " . $sqlBase;
$total = $db->query($sqlCount, $params)[0]['total'];
$totalPaginas = ceil($total / $porPagina);

// Busca os registros da página atual
$sql = "SELECT os.id, os.data_entrada, os.data_saida, os.status, os.descricao,
               os.valor_total, m.marca, m.modelo, m.placa
        $sqlBase
        ORDER BY os.data_entrada DESC
        LIMIT $porPagina OFFSET $offset";
$ordensServico = $db->query($sql, $params);

// Busca motos do cliente para o filtro
$sqlMotos = "SELECT id, marca, modelo, placa 
             FROM motocicletas 
             WHERE proprietario_id = ? 
             AND deleted_at IS NULL
             ORDER BY marca, modelo";
$motos = $db->query($sqlMotos, [$clienteId]);

$pageTitle = 'Histórico de Serviços - Área do Cliente';
require_once '../includes/header.php';
?>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col">
            <h2>Histórico de Serviços</h2>
        </div>
        <div class="col-auto">
            <a href="dashboard.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    
    <!-- Filtros -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="get" class="row g-3">
                <div class="col-md-3">
                    <label for="moto" class="form-label">Moto</label>
                    <select class="form-select" id="moto" name="moto">
                        <option value="">Todas</option>
                        <?php foreach ($motos as $m): ?>
                            <option value="<?php echo $m['id']; ?>" 
                                    <?php echo $moto == $m['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($m['marca'] . ' ' . $m['modelo']); ?>
                                (<?php echo htmlspecialchars($m['placa']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Todos</option>
                        <option value="aguardando" <?php echo $status === 'aguardando' ? 'selected' : ''; ?>>
                            Aguardando
                        </option>
                        <option value="em_andamento" <?php echo $status === 'em_andamento' ? 'selected' : ''; ?>>
                            Em Andamento
                        </option>
                        <option value="concluido" <?php echo $status === 'concluido' ? 'selected' : ''; ?>>
                            Concluído
                        </option>
                        <option value="cancelado" <?php echo $status === 'cancelado' ? 'selected' : ''; ?>>
                            Cancelado
                        </option>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="data_inicio" class="form-label">Data Início</label>
                    <input type="date" class="form-control" id="data_inicio" name="data_inicio" 
                           value="<?php echo $dataInicio; ?>">
                </div>
                
                <div class="col-md-2">
                    <label for="data_fim" class="form-label">Data Fim</label>
                    <input type="date" class="form-control" id="data_fim" name="data_fim" 
                           value="<?php echo $dataFim; ?>">
                </div>
                
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                    <a href="?<?php echo http_build_query(['pagina' => 1]); ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Limpar
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Lista de Ordens de Serviço -->
    <?php if (empty($ordensServico)): ?>
        <div class="alert alert-info">
            Nenhuma ordem de serviço encontrada.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Moto</th>
                        <th>Serviço</th>
                        <th>Status</th>
                        <th>Valor</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ordensServico as $os): ?>
                        <tr>
                            <td>
                                <?php echo date('d/m/Y', strtotime($os['data_entrada'])); ?>
                                <?php if ($os['data_saida']): ?>
                                    <br>
                                    <small class="text-muted">
                                        Concluído em: <?php echo date('d/m/Y', strtotime($os['data_saida'])); ?>
                                    </small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($os['marca'] . ' ' . $os['modelo']); ?>
                                <br>
                                <small class="text-muted">
                                    Placa: <?php echo htmlspecialchars($os['placa']); ?>
                                </small>
                            </td>
                            <td>
                                <?php echo nl2br(htmlspecialchars($os['descricao'])); ?>
                            </td>
                            <td>
                                <span class="badge bg-<?php 
                                    echo match($os['status']) {
                                        'aguardando' => 'warning',
                                        'em_andamento' => 'info',
                                        'concluido' => 'success',
                                        'cancelado' => 'danger',
                                        default => 'secondary'
                                    };
                                ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $os['status'])); ?>
                                </span>
                            </td>
                            <td>
                                R$ <?php echo number_format($os['valor_total'], 2, ',', '.'); ?>
                            </td>
                            <td>
                                <a href="ordem_servico.php?id=<?php echo $os['id']; ?>" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> Detalhes
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Paginação -->
        <?php if ($totalPaginas > 1): ?>
            <nav aria-label="Navegação do histórico" class="mt-4">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?php echo $pagina <= 1 ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?<?php 
                            $_GET['pagina'] = $pagina - 1;
                            echo http_build_query($_GET);
                        ?>">
                            Anterior
                        </a>
                    </li>
                    
                    <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                        <li class="page-item <?php echo $i === $pagina ? 'active' : ''; ?>">
                            <a class="page-link" href="?<?php 
                                $_GET['pagina'] = $i;
                                echo http_build_query($_GET);
                            ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                    
                    <li class="page-item <?php echo $pagina >= $totalPaginas ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?<?php 
                            $_GET['pagina'] = $pagina + 1;
                            echo http_build_query($_GET);
                        ?>">
                            Próxima
                        </a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?> 