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
$motoId = $_GET['id'] ?? 0;
$db = new Database();

// Busca a moto
$sql = "SELECT m.*, 
               (SELECT COUNT(*) FROM ordens_servico os 
                WHERE os.motocicleta_id = m.id 
                AND os.deleted_at IS NULL) as total_servicos,
               (SELECT COUNT(*) FROM ordens_servico os 
                WHERE os.motocicleta_id = m.id 
                AND os.status != 'concluido'
                AND os.status != 'cancelado'
                AND os.deleted_at IS NULL) as servicos_pendentes,
               (SELECT SUM(valor_total) FROM ordens_servico os 
                WHERE os.motocicleta_id = m.id 
                AND os.deleted_at IS NULL) as total_gasto
        FROM motocicletas m
        WHERE m.id = ?
        AND m.proprietario_id = ?
        AND m.deleted_at IS NULL";

$moto = $db->query($sql, [$motoId, $clienteId]);

if (empty($moto)) {
    header('Location: minhas_motos.php');
    exit;
}

$moto = $moto[0];

// Busca o histórico de proprietários
$sql = "SELECT hp.*, p.nome as proprietario_nome
        FROM historico_proprietarios hp
        JOIN proprietarios p ON hp.proprietario_id = p.id
        WHERE hp.motocicleta_id = ?
        ORDER BY hp.data_inicio DESC";
$historico = $db->query($sql, [$motoId]);

// Busca as últimas ordens de serviço
$sql = "SELECT os.id, os.data_entrada, os.status, os.descricao, os.valor_total
        FROM ordens_servico os
        WHERE os.motocicleta_id = ?
        AND os.deleted_at IS NULL
        ORDER BY os.data_entrada DESC
        LIMIT 5";
$ordensServico = $db->query($sql, [$motoId]);

$pageTitle = 'Detalhes da Moto - Área do Cliente';
require_once '../includes/header.php';
?>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col">
            <h2>
                <?php echo htmlspecialchars($moto['marca'] . ' ' . $moto['modelo']); ?>
                <small class="text-muted">
                    (<?php echo htmlspecialchars($moto['placa']); ?>)
                </small>
            </h2>
        </div>
        <div class="col-auto">
            <a href="minhas_motos.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    
    <div class="row">
        <!-- Informações Principais -->
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle"></i> Informações da Moto
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Identificação</h6>
                            <p>
                                <strong>Marca:</strong> <?php echo htmlspecialchars($moto['marca']); ?><br>
                                <strong>Modelo:</strong> <?php echo htmlspecialchars($moto['modelo']); ?><br>
                                <strong>Placa:</strong> <?php echo htmlspecialchars($moto['placa']); ?><br>
                                <strong>Ano:</strong> <?php echo htmlspecialchars($moto['ano']); ?><br>
                                <strong>Cor:</strong> <?php echo htmlspecialchars($moto['cor']); ?><br>
                                <strong>Chassi:</strong> <?php echo htmlspecialchars($moto['chassi']); ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6>Dados Técnicos</h6>
                            <p>
                                <strong>Quilometragem:</strong> 
                                <?php echo number_format($moto['quilometragem'], 0, ',', '.'); ?> km<br>
                                <strong>Cilindrada:</strong> <?php echo htmlspecialchars($moto['cilindrada']); ?> cc<br>
                                <strong>Combustível:</strong> <?php echo htmlspecialchars($moto['combustivel']); ?>
                            </p>
                            
                            <h6>Estatísticas</h6>
                            <p>
                                <strong>Total de Serviços:</strong> <?php echo $moto['total_servicos']; ?><br>
                                <strong>Serviços Pendentes:</strong> <?php echo $moto['servicos_pendentes']; ?><br>
                                <strong>Total Gasto:</strong> 
                                R$ <?php echo number_format($moto['total_gasto'] ?? 0, 2, ',', '.'); ?>
                            </p>
                        </div>
                    </div>
                    
                    <?php if ($moto['observacoes']): ?>
                        <h6>Observações</h6>
                        <p><?php echo nl2br(htmlspecialchars($moto['observacoes'])); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Últimos Serviços -->
            <div class="card shadow mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-tools"></i> Últimos Serviços
                    </h5>
                    <a href="historico_servicos.php?moto=<?php echo $moto['id']; ?>" 
                       class="btn btn-sm btn-outline-primary">
                        Ver Todos
                    </a>
                </div>
                <div class="card-body">
                    <?php if (empty($ordensServico)): ?>
                        <p class="text-muted">Nenhum serviço registrado.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>Serviço</th>
                                        <th>Status</th>
                                        <th class="text-end">Valor</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($ordensServico as $os): ?>
                                        <tr>
                                            <td>
                                                <?php echo date('d/m/Y', strtotime($os['data_entrada'])); ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($os['descricao']); ?></td>
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
                                            <td class="text-end">
                                                R$ <?php echo number_format($os['valor_total'], 2, ',', '.'); ?>
                                            </td>
                                            <td class="text-end">
                                                <a href="ordem_servico.php?id=<?php echo $os['id']; ?>" 
                                                   class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Lateral -->
        <div class="col-md-4">
            <!-- Histórico de Proprietários -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history"></i> Histórico de Proprietários
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($historico)): ?>
                        <p class="text-muted">Nenhum histórico registrado.</p>
                    <?php else: ?>
                        <div class="timeline">
                            <?php foreach ($historico as $h): ?>
                                <div class="timeline-item">
                                    <div class="timeline-marker"></div>
                                    <div class="timeline-content">
                                        <div class="timeline-heading">
                                            <strong>
                                                <?php echo htmlspecialchars($h['proprietario_nome']); ?>
                                            </strong>
                                            <small class="text-muted d-block">
                                                <?php echo date('d/m/Y', strtotime($h['data_inicio'])); ?>
                                                <?php if ($h['data_fim']): ?>
                                                    até <?php echo date('d/m/Y', strtotime($h['data_fim'])); ?>
                                                <?php else: ?>
                                                    até hoje
                                                <?php endif; ?>
                                            </small>
                                        </div>
                                        <?php if ($h['observacoes']): ?>
                                            <div class="timeline-body">
                                                <?php echo nl2br(htmlspecialchars($h['observacoes'])); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Próximas Manutenções -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calendar-alt"></i> Próximas Manutenções
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        Em breve você poderá acompanhar aqui as próximas manutenções recomendadas 
                        para sua moto.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Timeline */
.timeline {
    position: relative;
    padding: 0;
    list-style: none;
}

.timeline-item {
    position: relative;
    padding-left: 24px;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: 0;
    top: 0;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background-color: #6c757d;
}

.timeline-item:not(:last-child):before {
    content: '';
    position: absolute;
    left: 5px;
    top: 12px;
    bottom: -20px;
    width: 2px;
    background-color: #e9ecef;
}

.timeline-content {
    padding-bottom: 10px;
}

.timeline-heading {
    margin-bottom: 5px;
}

.timeline-body {
    padding-left: 5px;
    border-left: 2px solid #e9ecef;
}
</style>

<?php require_once '../includes/footer.php'; ?> 