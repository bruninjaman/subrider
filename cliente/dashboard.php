<?php
require_once '../includes/client_manager.php';
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

// Busca dados do cliente
$sql = "SELECT nome FROM proprietarios WHERE id = ?";
$cliente = $db->query($sql, [$clienteId])[0];

// Busca motos do cliente
$sql = "SELECT id, marca, modelo, placa, ano 
        FROM motocicletas 
        WHERE proprietario_id = ? 
        AND deleted_at IS NULL";
$motos = $db->query($sql, [$clienteId]);

// Busca ordens de serviço recentes
$sql = "SELECT os.id, os.data_entrada, os.status, os.descricao,
               m.marca, m.modelo, m.placa
        FROM ordens_servico os
        JOIN motocicletas m ON os.motocicleta_id = m.id
        WHERE m.proprietario_id = ?
        AND os.deleted_at IS NULL
        ORDER BY os.data_entrada DESC
        LIMIT 5";
$ordensServico = $db->query($sql, [$clienteId]);

$pageTitle = 'Dashboard - Área do Cliente';
require_once '../includes/header.php';
?>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col">
            <h2>Bem-vindo, <?php echo htmlspecialchars($cliente['nome']); ?>!</h2>
        </div>
        <div class="col-auto">
            <a href="logout.php" class="btn btn-outline-danger">
                <i class="fas fa-sign-out-alt"></i> Sair
            </a>
        </div>
    </div>
    
    <div class="row">
        <!-- Minhas Motos -->
        <div class="col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-motorcycle"></i> Minhas Motos
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($motos)): ?>
                        <p class="text-muted">Nenhuma moto cadastrada.</p>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($motos as $moto): ?>
                                <a href="moto.php?id=<?php echo $moto['id']; ?>" 
                                   class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">
                                            <?php echo htmlspecialchars($moto['marca']); ?> 
                                            <?php echo htmlspecialchars($moto['modelo']); ?>
                                        </h6>
                                        <small class="text-muted">
                                            <?php echo htmlspecialchars($moto['ano']); ?>
                                        </small>
                                    </div>
                                    <small class="text-muted">
                                        Placa: <?php echo htmlspecialchars($moto['placa']); ?>
                                    </small>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Ordens de Serviço Recentes -->
        <div class="col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-tools"></i> Ordens de Serviço Recentes
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($ordensServico)): ?>
                        <p class="text-muted">Nenhuma ordem de serviço encontrada.</p>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($ordensServico as $os): ?>
                                <a href="ordem_servico.php?id=<?php echo $os['id']; ?>" 
                                   class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">
                                            <?php echo htmlspecialchars($os['marca']); ?> 
                                            <?php echo htmlspecialchars($os['modelo']); ?>
                                            (<?php echo htmlspecialchars($os['placa']); ?>)
                                        </h6>
                                        <small>
                                            <?php echo date('d/m/Y', strtotime($os['data_entrada'])); ?>
                                        </small>
                                    </div>
                                    <p class="mb-1">
                                        <?php echo htmlspecialchars($os['descricao']); ?>
                                    </p>
                                    <small class="text-muted">
                                        Status: 
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
                                    </small>
                                </a>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="text-end mt-3">
                            <a href="ordens_servico.php" class="btn btn-sm btn-outline-info">
                                Ver Todas
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Preferências -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-header bg-secondary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-cog"></i> Preferências
                    </h5>
                </div>
                <div class="card-body">
                    <?php
                    $clientManager = new ClientManager();
                    $preferencias = $clientManager->getPreferencias($clienteId);
                    ?>
                    
                    <form method="post" action="atualizar_preferencias.php">
                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="notificacao_email" 
                                   name="notificacao_email" value="1" 
                                   <?php echo $preferencias['notificacao_email'] ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="notificacao_email">
                                Receber notificações por email
                            </label>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="notificacao_whatsapp" 
                                   name="notificacao_whatsapp" value="1"
                                   <?php echo $preferencias['notificacao_whatsapp'] ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="notificacao_whatsapp">
                                Receber notificações por WhatsApp
                            </label>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="tema">Tema</label>
                            <select class="form-select" id="tema" name="tema">
                                <option value="light" <?php echo $preferencias['tema'] === 'light' ? 'selected' : ''; ?>>
                                    Claro
                                </option>
                                <option value="dark" <?php echo $preferencias['tema'] === 'dark' ? 'selected' : ''; ?>>
                                    Escuro
                                </option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            Salvar Preferências
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Links Úteis -->
        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-link"></i> Links Úteis
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="minhas_motos.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-motorcycle"></i> Gerenciar Minhas Motos
                        </a>
                        <a href="historico_servicos.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-history"></i> Histórico de Serviços
                        </a>
                        <a href="alterar_senha.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-key"></i> Alterar Senha
                        </a>
                        <a href="ajuda.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-question-circle"></i> Ajuda
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?> 