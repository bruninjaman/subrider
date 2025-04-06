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

// Busca as motos do cliente
$sql = "SELECT m.*, 
               (SELECT COUNT(*) FROM ordens_servico os 
                WHERE os.motocicleta_id = m.id 
                AND os.deleted_at IS NULL) as total_servicos,
               (SELECT COUNT(*) FROM ordens_servico os 
                WHERE os.motocicleta_id = m.id 
                AND os.status != 'concluido'
                AND os.status != 'cancelado'
                AND os.deleted_at IS NULL) as servicos_pendentes
        FROM motocicletas m
        WHERE m.proprietario_id = ?
        AND m.deleted_at IS NULL
        ORDER BY m.marca, m.modelo";

$motos = $db->query($sql, [$clienteId]);

$pageTitle = 'Minhas Motos - Área do Cliente';
require_once '../includes/header.php';
?>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col">
            <h2>Minhas Motos</h2>
        </div>
        <div class="col-auto">
            <a href="dashboard.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    
    <?php if (empty($motos)): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Você não possui motos cadastradas.
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php foreach ($motos as $moto): ?>
                <div class="col">
                    <div class="card shadow h-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <?php echo htmlspecialchars($moto['marca'] . ' ' . $moto['modelo']); ?>
                            </h5>
                        </div>
                        
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>Placa:</strong> <?php echo htmlspecialchars($moto['placa']); ?><br>
                                <strong>Ano:</strong> <?php echo htmlspecialchars($moto['ano']); ?><br>
                                <strong>Cor:</strong> <?php echo htmlspecialchars($moto['cor']); ?><br>
                                <strong>Quilometragem:</strong> 
                                <?php echo number_format($moto['quilometragem'], 0, ',', '.'); ?> km
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <span class="badge bg-info">
                                        <?php echo $moto['total_servicos']; ?> serviços
                                    </span>
                                    
                                    <?php if ($moto['servicos_pendentes'] > 0): ?>
                                        <span class="badge bg-warning">
                                            <?php echo $moto['servicos_pendentes']; ?> pendentes
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="btn-group w-100">
                                <a href="moto.php?id=<?php echo $moto['id']; ?>" 
                                   class="btn btn-outline-primary">
                                    <i class="fas fa-eye"></i> Detalhes
                                </a>
                                <a href="historico_servicos.php?moto=<?php echo $moto['id']; ?>" 
                                   class="btn btn-outline-info">
                                    <i class="fas fa-history"></i> Serviços
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?> 