<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../classes/AvaliacaoManager.php';
require_once __DIR__ . '/../includes/session_manager.php';
require_once __DIR__ . '/../classes/Security/PermissionManager.php';

$sessionManager = new SessionManager();
$avaliacaoManager = new AvaliacaoManager();
$permManager = \Security\PermissionManager::getInstance();

// Verifica se o usuário está logado
if (!$sessionManager->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Verifica permissão
$permManager->loadUserPermissions($_SESSION['user_id']);
if (!$permManager->hasPermission('avaliacoes.view')) {
    header('Location: access-denied.php');
    exit;
}

// Headers de segurança
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Processa ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['acao'])) {
        $avaliacaoId = (int) $_POST['avaliacao_id'];
        
        switch ($_POST['acao']) {
            case 'aprovar':
                if ($avaliacaoManager->aprovarAvaliacao($avaliacaoId)) {
                    $mensagem = "Avaliação aprovada com sucesso!";
                    $tipo = "success";
                }
                break;
                
            case 'rejeitar':
                $motivo = trim($_POST['motivo']);
                if ($avaliacaoManager->rejeitarAvaliacao($avaliacaoId, $motivo)) {
                    $mensagem = "Avaliação rejeitada com sucesso!";
                    $tipo = "success";
                }
                break;
        }
    }
}

// Filtros
$filtros = [
    'status' => $_GET['status'] ?? '',
    'data_inicio' => $_GET['data_inicio'] ?? '',
    'data_fim' => $_GET['data_fim'] ?? ''
];

// Paginação
$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$perPage = 20;

// Busca avaliações
$resultado = $avaliacaoManager->listarAvaliacoes($filtros, $page, $perPage);
$avaliacoes = $resultado['avaliacoes'];
$total = $resultado['total'];
$totalPaginas = ceil($total / $perPage);

// Estatísticas
$estatisticas = $avaliacaoManager->calcularEstatisticas();

// Inclui o cabeçalho
require_once __DIR__ . '/../includes/header.php';
?>

<section id="banner">
    <div class="content">
        <div class="container">
            <div class="row mb-4">
                <div class="col-12">
                    <h2>Gerenciamento de Avaliações</h2>
                </div>
            </div>
            
            <!-- Estatísticas -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5>Total de Avaliações</h5>
                            <h3><?php echo $estatisticas['total']; ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5>Média Geral</h5>
                            <h3><?php echo $estatisticas['media']; ?>/5</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5>Avaliações Positivas</h5>
                            <h3><?php echo $estatisticas['positivas']; ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5>Avaliações Negativas</h5>
                            <h3><?php echo $estatisticas['negativas']; ?></h3>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Filtros -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form method="get" class="row">
                                <div class="col-md-3">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <option value="">Todos</option>
                                        <option value="pendente" <?php echo $filtros['status'] === 'pendente' ? 'selected' : ''; ?>>Pendente</option>
                                        <option value="aprovada" <?php echo $filtros['status'] === 'aprovada' ? 'selected' : ''; ?>>Aprovada</option>
                                        <option value="rejeitada" <?php echo $filtros['status'] === 'rejeitada' ? 'selected' : ''; ?>>Rejeitada</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>Data Início</label>
                                    <input type="date" name="data_inicio" class="form-control" 
                                           value="<?php echo $filtros['data_inicio']; ?>">
                                </div>
                                <div class="col-md-3">
                                    <label>Data Fim</label>
                                    <input type="date" name="data_fim" class="form-control" 
                                           value="<?php echo $filtros['data_fim']; ?>">
                                </div>
                                <div class="col-md-3">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="button primary form-control">Filtrar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php if (isset($mensagem)): ?>
                <div class="alert alert-<?php echo $tipo; ?>">
                    <?php echo $mensagem; ?>
                </div>
            <?php endif; ?>
            
            <!-- Lista de Avaliações -->
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>OS</th>
                                    <th>Cliente</th>
                                    <th>Nota</th>
                                    <th>Comentário</th>
                                    <th>Data</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($avaliacoes as $avaliacao): ?>
                                    <tr>
                                        <td>#<?php echo htmlspecialchars($avaliacao['ordem_id']); ?></td>
                                        <td><?php echo htmlspecialchars($avaliacao['proprietario_nome']); ?></td>
                                        <td>
                                            <?php
                                            for ($i = 1; $i <= 5; $i++) {
                                                echo $i <= $avaliacao['nota'] 
                                                    ? '<i class="fas fa-star text-warning"></i>' 
                                                    : '<i class="far fa-star"></i>';
                                            }
                                            ?>
                                        </td>
                                        <td><?php echo nl2br(htmlspecialchars($avaliacao['comentario'])); ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($avaliacao['data_avaliacao'])); ?></td>
                                        <td>
                                            <?php
                                            $statusClasses = [
                                                'pendente' => 'warning',
                                                'aprovada' => 'success',
                                                'rejeitada' => 'danger'
                                            ];
                                            $statusClass = $statusClasses[$avaliacao['status']] ?? 'secondary';
                                            ?>
                                            <span class="badge badge-<?php echo $statusClass; ?>">
                                                <?php echo ucfirst($avaliacao['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($avaliacao['status'] === 'pendente'): ?>
                                                <button type="button" class="btn btn-sm btn-success aprovar-btn" 
                                                        data-id="<?php echo $avaliacao['id']; ?>">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger rejeitar-btn" 
                                                        data-id="<?php echo $avaliacao['id']; ?>">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                
                                <?php if (empty($avaliacoes)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center">Nenhuma avaliação encontrada.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Paginação -->
                    <?php if ($totalPaginas > 1): ?>
                        <nav>
                            <ul class="pagination justify-content-center">
                                <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                                    <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>&status=<?php echo $filtros['status']; ?>&data_inicio=<?php echo $filtros['data_inicio']; ?>&data_fim=<?php echo $filtros['data_fim']; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal de Rejeição -->
<div class="modal fade" id="rejeicaoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post">
                <div class="modal-header">
                    <h5 class="modal-title">Rejeitar Avaliação</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="acao" value="rejeitar">
                    <input type="hidden" name="avaliacao_id" id="rejeicaoAvaliacaoId">
                    
                    <div class="form-group">
                        <label for="motivo">Motivo da Rejeição</label>
                        <textarea name="motivo" id="motivo" rows="3" class="form-control" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Rejeitar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Aprovação -->
<div class="modal fade" id="aprovacaoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post">
                <div class="modal-header">
                    <h5 class="modal-title">Aprovar Avaliação</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Tem certeza que deseja aprovar esta avaliação?</p>
                    <input type="hidden" name="acao" value="aprovar">
                    <input type="hidden" name="avaliacao_id" id="aprovacaoAvaliacaoId">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Aprovar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Botão de rejeição
    $('.rejeitar-btn').click(function() {
        var id = $(this).data('id');
        $('#rejeicaoAvaliacaoId').val(id);
        $('#rejeicaoModal').modal('show');
    });
    
    // Botão de aprovação
    $('.aprovar-btn').click(function() {
        var id = $(this).data('id');
        $('#aprovacaoAvaliacaoId').val(id);
        $('#aprovacaoModal').modal('show');
    });
});
</script>

<style>
.card {
    margin-bottom: 1em;
}

.badge {
    padding: 0.5em 1em;
}

.badge-warning {
    background-color: #ffc107;
    color: #000;
}

.badge-success {
    background-color: #28a745;
    color: #fff;
}

.badge-danger {
    background-color: #dc3545;
    color: #fff;
}

.table td {
    vertical-align: middle;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    line-height: 1.5;
    border-radius: 0.2rem;
}

.pagination {
    margin-top: 1em;
}

.page-link {
    color: #4a90e2;
}

.page-item.active .page-link {
    background-color: #4a90e2;
    border-color: #4a90e2;
}
</style>

<?php
require_once __DIR__ . '/../includes/footer.php';
?> 