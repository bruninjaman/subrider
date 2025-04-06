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

// Busca os dados do cliente
$sql = "SELECT p.*, pc.notificacoes_email, pc.notificacoes_sms, pc.tema
        FROM proprietarios p
        LEFT JOIN preferencias_cliente pc ON p.id = pc.proprietario_id
        WHERE p.id = ?";
$cliente = $db->query($sql, [$clienteId])[0];

// Busca estatísticas
$sql = "SELECT 
            COUNT(DISTINCT m.id) as total_motos,
            COUNT(DISTINCT os.id) as total_servicos,
            SUM(os.valor_total) as total_gasto,
            MAX(os.data_entrada) as ultimo_servico
        FROM proprietarios p
        LEFT JOIN motocicletas m ON p.id = m.proprietario_id AND m.deleted_at IS NULL
        LEFT JOIN ordens_servico os ON m.id = os.motocicleta_id AND os.deleted_at IS NULL
        WHERE p.id = ?";
$stats = $db->query($sql, [$clienteId])[0];

// Busca o último acesso
$sql = "SELECT data_acesso, ip 
        FROM tokens_acesso 
        WHERE proprietario_id = ? 
        ORDER BY data_acesso DESC 
        LIMIT 2";
$acessos = $db->query($sql, [$clienteId]);

$pageTitle = 'Meu Perfil - Área do Cliente';
require_once '../includes/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <!-- Perfil Principal -->
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user"></i> Meu Perfil
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center mb-4">
                            <div class="avatar-circle mb-3">
                                <span class="avatar-initials">
                                    <?php 
                                    $iniciais = array_map(
                                        function($p) { return strtoupper($p[0]); },
                                        explode(' ', $cliente['nome'])
                                    );
                                    echo implode('', array_slice($iniciais, 0, 2));
                                    ?>
                                </span>
                            </div>
                            <h4><?php echo htmlspecialchars($cliente['nome']); ?></h4>
                            <p class="text-muted">
                                Cliente desde <?php echo date('d/m/Y', strtotime($cliente['created_at'])); ?>
                            </p>
                        </div>
                        <div class="col-md-8">
                            <h6>Informações Pessoais</h6>
                            <p>
                                <strong>CPF/CNPJ:</strong> <?php echo htmlspecialchars($cliente['documento']); ?><br>
                                <strong>Email:</strong> <?php echo htmlspecialchars($cliente['email']); ?><br>
                                <strong>Telefone:</strong> <?php echo htmlspecialchars($cliente['telefone']); ?><br>
                                <strong>Celular:</strong> <?php echo htmlspecialchars($cliente['celular']); ?>
                            </p>
                            
                            <h6>Endereço</h6>
                            <p>
                                <?php echo htmlspecialchars($cliente['logradouro']); ?>, 
                                <?php echo htmlspecialchars($cliente['numero']); ?>
                                <?php if ($cliente['complemento']): ?>
                                    - <?php echo htmlspecialchars($cliente['complemento']); ?>
                                <?php endif; ?><br>
                                <?php echo htmlspecialchars($cliente['bairro']); ?><br>
                                <?php echo htmlspecialchars($cliente['cidade']); ?> - 
                                <?php echo htmlspecialchars($cliente['estado']); ?><br>
                                CEP: <?php echo htmlspecialchars($cliente['cep']); ?>
                            </p>
                            
                            <div class="mt-4">
                                <a href="editar_perfil.php" class="btn btn-primary me-2">
                                    <i class="fas fa-edit"></i> Editar Perfil
                                </a>
                                <a href="alterar_senha.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-key"></i> Alterar Senha
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Preferências -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-cog"></i> Preferências
                    </h5>
                </div>
                <div class="card-body">
                    <form action="atualizar_preferencias.php" method="post" class="row">
                        <div class="col-md-6">
                            <h6>Notificações</h6>
                            <div class="form-check mb-2">
                                <input type="checkbox" class="form-check-input" id="notificacoes_email" 
                                       name="notificacoes_email" value="1" 
                                       <?php echo $cliente['notificacoes_email'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="notificacoes_email">
                                    Receber notificações por email
                                </label>
                            </div>
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" id="notificacoes_sms" 
                                       name="notificacoes_sms" value="1"
                                       <?php echo $cliente['notificacoes_sms'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="notificacoes_sms">
                                    Receber notificações por SMS
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6>Aparência</h6>
                            <div class="mb-3">
                                <label for="tema" class="form-label">Tema</label>
                                <select class="form-select" id="tema" name="tema">
                                    <option value="claro" <?php echo $cliente['tema'] == 'claro' ? 'selected' : ''; ?>>
                                        Claro
                                    </option>
                                    <option value="escuro" <?php echo $cliente['tema'] == 'escuro' ? 'selected' : ''; ?>>
                                        Escuro
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Salvar Preferências
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Lateral -->
        <div class="col-md-4">
            <!-- Estatísticas -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar"></i> Estatísticas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="stat-item">
                        <div class="stat-label">Motos Cadastradas</div>
                        <div class="stat-value"><?php echo $stats['total_motos']; ?></div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Total de Serviços</div>
                        <div class="stat-value"><?php echo $stats['total_servicos']; ?></div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Total Investido</div>
                        <div class="stat-value">
                            R$ <?php echo number_format($stats['total_gasto'] ?? 0, 2, ',', '.'); ?>
                        </div>
                    </div>
                    <?php if ($stats['ultimo_servico']): ?>
                        <div class="stat-item">
                            <div class="stat-label">Último Serviço</div>
                            <div class="stat-value">
                                <?php echo date('d/m/Y', strtotime($stats['ultimo_servico'])); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Últimos Acessos -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clock"></i> Últimos Acessos
                    </h5>
                </div>
                <div class="card-body">
                    <?php foreach ($acessos as $i => $acesso): ?>
                        <div class="access-item <?php echo $i === 0 ? 'current' : ''; ?>">
                            <div class="access-time">
                                <?php echo date('d/m/Y H:i:s', strtotime($acesso['data_acesso'])); ?>
                            </div>
                            <div class="access-ip text-muted">
                                IP: <?php echo htmlspecialchars($acesso['ip']); ?>
                            </div>
                            <?php if ($i === 0): ?>
                                <div class="access-badge">
                                    <span class="badge bg-success">Atual</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Avatar */
.avatar-circle {
    width: 120px;
    height: 120px;
    background-color: #6c757d;
    border-radius: 50%;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
}

.avatar-initials {
    color: white;
    font-size: 2.5rem;
    font-weight: bold;
}

/* Estatísticas */
.stat-item {
    padding: 15px 0;
    border-bottom: 1px solid #e9ecef;
}

.stat-item:last-child {
    border-bottom: none;
}

.stat-label {
    color: #6c757d;
    font-size: 0.875rem;
}

.stat-value {
    font-size: 1.25rem;
    font-weight: bold;
}

/* Acessos */
.access-item {
    padding: 10px 0;
    border-bottom: 1px solid #e9ecef;
    position: relative;
}

.access-item:last-child {
    border-bottom: none;
}

.access-item.current {
    background-color: #f8f9fa;
    padding: 10px;
    border-radius: 4px;
}

.access-time {
    font-weight: bold;
}

.access-ip {
    font-size: 0.875rem;
}

.access-badge {
    position: absolute;
    top: 10px;
    right: 10px;
}
</style>

<?php require_once '../includes/footer.php'; ?> 