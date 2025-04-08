<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/notification_manager.php';
require_once __DIR__ . '/../includes/session_manager.php';
require_once __DIR__ . '/../classes/Security/PermissionManager.php';

$sessionManager = new SessionManager();
$notificationManager = new NotificationManager();
$permManager = \Security\PermissionManager::getInstance();

// Verifica se o usuário está logado
if (!$sessionManager->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Verifica permissão
$permManager->loadUserPermissions($_SESSION['user_id']);
if (!$permManager->hasPermission('notifications.view')) {
    header('Location: access-denied.php');
    exit;
}

// Headers de segurança
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Inicializa gerenciador de notificações
$notificationManager = new NotificationManager();

// Obtém página atual
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 20;

// Obtém notificações
$notificacoes = $notificationManager->getTodasNotificacoes($_SESSION['user_id'], $page, $perPage);

// Define título da página
$pageTitle = "Notificações - SubRider";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Notificações</h1>
        <button class="btn btn-primary mark-all-read">
            <i class="fas fa-check-double"></i> Marcar todas como lidas
        </button>
    </div>
    
    <?php if (empty($notificacoes)): ?>
        <div class="alert alert-info">
            Você não tem notificações.
        </div>
    <?php else: ?>
        <div class="card shadow">
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php foreach ($notificacoes as $notificacao): ?>
                        <div class="list-group-item notification-item <?php echo $notificacao['lida'] ? 'bg-light' : ''; ?>"
                             data-id="<?php echo $notificacao['id']; ?>">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <div class="notification-content">
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="fas <?php echo $notificationManager->getIcone($notificacao['tipo']); ?> text-<?php echo $notificationManager->getCorTipo($notificacao['tipo']); ?> me-2"></i>
                                        <h6 class="mb-0"><?php echo htmlspecialchars($notificacao['titulo']); ?></h6>
                                    </div>
                                    <p class="mb-1"><?php echo htmlspecialchars($notificacao['mensagem']); ?></p>
                                    <small class="text-muted">
                                        <?php echo date('d/m/Y H:i', strtotime($notificacao['created_at'])); ?>
                                    </small>
                                </div>
                                <div class="notification-actions">
                                    <?php if (!$notificacao['lida']): ?>
                                        <button class="btn btn-sm btn-outline-primary mark-read" 
                                                data-id="<?php echo $notificacao['id']; ?>">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    <?php endif; ?>
                                    <button class="btn btn-sm btn-outline-danger delete-notification"
                                            data-id="<?php echo $notificacao['id']; ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <!-- Paginação -->
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page - 1; ?>">Anterior</a>
                    </li>
                <?php endif; ?>
                
                <li class="page-item active">
                    <span class="page-link"><?php echo $page; ?></span>
                </li>
                
                <?php if (count($notificacoes) == $perPage): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page + 1; ?>">Próxima</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<!-- Scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Marcar notificação como lida
    document.querySelectorAll('.mark-read').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            marcarComoLida(id);
        });
    });
    
    // Marcar todas como lidas
    document.querySelector('.mark-all-read').addEventListener('click', function() {
        marcarTodasComoLidas();
    });
    
    // Excluir notificação
    document.querySelectorAll('.delete-notification').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            if (confirm('Tem certeza que deseja excluir esta notificação?')) {
                excluirNotificacao(id);
            }
        });
    });
    
    // Funções AJAX
    function marcarComoLida(id) {
        fetch('/api/notificacoes/marcar_lida.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: id })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
    
    function marcarTodasComoLidas() {
        fetch('/api/notificacoes/marcar_todas_lidas.php', {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
    
    function excluirNotificacao(id) {
        fetch('/api/notificacoes/excluir.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: id })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?> 