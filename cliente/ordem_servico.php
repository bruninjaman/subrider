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
$ordemId = $_GET['id'] ?? 0;
$db = new Database();

// Busca a ordem de serviço
$sql = "SELECT os.*, m.marca, m.modelo, m.placa, m.ano,
               m.quilometragem, m.cor, m.chassi
        FROM ordens_servico os
        JOIN motocicletas m ON os.motocicleta_id = m.id
        WHERE os.id = ?
        AND m.proprietario_id = ?
        AND os.deleted_at IS NULL";

$ordem = $db->query($sql, [$ordemId, $clienteId]);

if (empty($ordem)) {
    header('Location: historico_servicos.php');
    exit;
}

$ordem = $ordem[0];

// Busca os serviços realizados
$sql = "SELECT *
        FROM servicos_realizados
        WHERE ordem_servico_id = ?
        ORDER BY id";
$servicos = $db->query($sql, [$ordemId]);

// Busca as peças utilizadas
$sql = "SELECT p.*, pp.quantidade, pp.valor_unitario
        FROM pecas_pedido pp
        JOIN pecas p ON pp.peca_id = p.id
        WHERE pp.ordem_servico_id = ?
        ORDER BY p.nome";
$pecas = $db->query($sql, [$ordemId]);

// Busca o histórico de status
$sql = "SELECT *
        FROM historico_status_os
        WHERE ordem_servico_id = ?
        ORDER BY data_hora DESC";
$historico = $db->query($sql, [$ordemId]);

$pageTitle = 'Detalhes da Ordem de Serviço - Área do Cliente';
require_once '../includes/header.php';
?>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col">
            <h2>Ordem de Serviço #<?php echo str_pad($ordem['id'], 6, '0', STR_PAD_LEFT); ?></h2>
        </div>
        <div class="col-auto">
            <a href="historico_servicos.php" class="btn btn-secondary">
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
                        <i class="fas fa-info-circle"></i> Informações Principais
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Moto</h6>
                            <p>
                                <?php echo htmlspecialchars($ordem['marca'] . ' ' . $ordem['modelo']); ?><br>
                                <small class="text-muted">
                                    Placa: <?php echo htmlspecialchars($ordem['placa']); ?><br>
                                    Ano: <?php echo htmlspecialchars($ordem['ano']); ?><br>
                                    Cor: <?php echo htmlspecialchars($ordem['cor']); ?><br>
                                    Chassi: <?php echo htmlspecialchars($ordem['chassi']); ?><br>
                                    Quilometragem: <?php echo number_format($ordem['quilometragem'], 0, ',', '.'); ?> km
                                </small>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6>Datas</h6>
                            <p>
                                Entrada: <?php echo date('d/m/Y', strtotime($ordem['data_entrada'])); ?><br>
                                <?php if ($ordem['data_saida']): ?>
                                    Saída: <?php echo date('d/m/Y', strtotime($ordem['data_saida'])); ?><br>
                                <?php endif; ?>
                                <?php if ($ordem['previsao']): ?>
                                    Previsão: <?php echo date('d/m/Y', strtotime($ordem['previsao'])); ?>
                                <?php endif; ?>
                            </p>
                            
                            <h6>Status</h6>
                            <p>
                                <span class="badge bg-<?php 
                                    echo match($ordem['status']) {
                                        'aguardando' => 'warning',
                                        'em_andamento' => 'info',
                                        'concluido' => 'success',
                                        'cancelado' => 'danger',
                                        default => 'secondary'
                                    };
                                ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $ordem['status'])); ?>
                                </span>
                            </p>
                        </div>
                    </div>
                    
                    <h6>Descrição</h6>
                    <p><?php echo nl2br(htmlspecialchars($ordem['descricao'])); ?></p>
                    
                    <?php if ($ordem['observacoes']): ?>
                        <h6>Observações</h6>
                        <p><?php echo nl2br(htmlspecialchars($ordem['observacoes'])); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Serviços Realizados -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-tools"></i> Serviços Realizados
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($servicos)): ?>
                        <p class="text-muted">Nenhum serviço registrado.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Serviço</th>
                                        <th>Descrição</th>
                                        <th class="text-end">Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($servicos as $servico): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($servico['nome']); ?></td>
                                            <td><?php echo htmlspecialchars($servico['descricao']); ?></td>
                                            <td class="text-end">
                                                R$ <?php echo number_format($servico['valor'], 2, ',', '.'); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2" class="text-end">Total Serviços:</th>
                                        <th class="text-end">
                                            R$ <?php 
                                                echo number_format(array_sum(array_column($servicos, 'valor')), 2, ',', '.');
                                            ?>
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Peças Utilizadas -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-cogs"></i> Peças Utilizadas
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($pecas)): ?>
                        <p class="text-muted">Nenhuma peça registrada.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Peça</th>
                                        <th>Quantidade</th>
                                        <th class="text-end">Valor Unit.</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pecas as $peca): ?>
                                        <tr>
                                            <td>
                                                <?php echo htmlspecialchars($peca['nome']); ?>
                                                <?php if ($peca['marca']): ?>
                                                    <br>
                                                    <small class="text-muted">
                                                        Marca: <?php echo htmlspecialchars($peca['marca']); ?>
                                                    </small>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo $peca['quantidade']; ?></td>
                                            <td class="text-end">
                                                R$ <?php echo number_format($peca['valor_unitario'], 2, ',', '.'); ?>
                                            </td>
                                            <td class="text-end">
                                                R$ <?php 
                                                    echo number_format(
                                                        $peca['quantidade'] * $peca['valor_unitario'], 
                                                        2, ',', '.'
                                                    );
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-end">Total Peças:</th>
                                        <th class="text-end">
                                            R$ <?php 
                                                $totalPecas = array_sum(array_map(
                                                    fn($p) => $p['quantidade'] * $p['valor_unitario'], 
                                                    $pecas
                                                ));
                                                echo number_format($totalPecas, 2, ',', '.');
                                            ?>
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Lateral -->
        <div class="col-md-4">
            <!-- Resumo Financeiro -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-dollar-sign"></i> Resumo Financeiro
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td>Total Serviços:</td>
                            <td class="text-end">
                                R$ <?php 
                                    echo number_format(
                                        array_sum(array_column($servicos, 'valor')), 
                                        2, ',', '.'
                                    );
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Total Peças:</td>
                            <td class="text-end">
                                R$ <?php echo number_format($totalPecas, 2, ',', '.'); ?>
                            </td>
                        </tr>
                        <?php if ($ordem['desconto']): ?>
                            <tr>
                                <td>Desconto:</td>
                                <td class="text-end text-success">
                                    - R$ <?php echo number_format($ordem['desconto'], 2, ',', '.'); ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <th>Total:</th>
                            <th class="text-end">
                                R$ <?php echo number_format($ordem['valor_total'], 2, ',', '.'); ?>
                            </th>
                        </tr>
                    </table>
                    
                    <?php if ($ordem['forma_pagamento']): ?>
                        <p class="mb-0">
                            <strong>Forma de Pagamento:</strong><br>
                            <?php echo htmlspecialchars($ordem['forma_pagamento']); ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Histórico de Status -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history"></i> Histórico de Status
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($historico)): ?>
                        <p class="text-muted">Nenhum histórico registrado.</p>
                    <?php else: ?>
                        <div class="timeline">
                            <?php foreach ($historico as $h): ?>
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-<?php 
                                        echo match($h['status']) {
                                            'aguardando' => 'warning',
                                            'em_andamento' => 'info',
                                            'concluido' => 'success',
                                            'cancelado' => 'danger',
                                            default => 'secondary'
                                        };
                                    ?>"></div>
                                    <div class="timeline-content">
                                        <div class="timeline-heading">
                                            <span class="badge bg-<?php 
                                                echo match($h['status']) {
                                                    'aguardando' => 'warning',
                                                    'em_andamento' => 'info',
                                                    'concluido' => 'success',
                                                    'cancelado' => 'danger',
                                                    default => 'secondary'
                                                };
                                            ?>">
                                                <?php echo ucfirst(str_replace('_', ' ', $h['status'])); ?>
                                            </span>
                                            <small class="text-muted ms-2">
                                                <?php echo date('d/m/Y H:i', strtotime($h['data_hora'])); ?>
                                            </small>
                                        </div>
                                        <?php if ($h['observacao']): ?>
                                            <div class="timeline-body">
                                                <?php echo nl2br(htmlspecialchars($h['observacao'])); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
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