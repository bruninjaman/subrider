<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/dashboard_stats.php';

// Obtém estatísticas
$stats = new DashboardStats();
$totalMotos = $stats->getTotalMotos();
$totalProprietarios = $stats->getTotalProprietarios();
$totalOrdensServico = $stats->getTotalOrdensServico();
$faturamentoMensal = $stats->getFaturamentoMensal();
$servicosPendentes = $stats->getServicosPendentes();
$ultimasOrdens = $stats->getUltimasOrdens(5);

// Define título da página
$pageTitle = "Dashboard - SubRider";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid py-4">
    <h1 class="h3 mb-4">Dashboard</h1>
    
    <!-- Cards de Estatísticas -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total de Motos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalMotos; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-motorcycle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Proprietários</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalProprietarios; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Ordens de Serviço</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalOrdensServico; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Faturamento Mensal</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">R$ <?php echo number_format($faturamentoMensal, 2, ',', '.'); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Faturamento por Mês</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="graficoFaturamento"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Status das Ordens</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie">
                        <canvas id="graficoStatus"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Serviços Pendentes -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Serviços Pendentes</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>OS</th>
                                    <th>Moto</th>
                                    <th>Proprietário</th>
                                    <th>Data Entrada</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($servicosPendentes as $servico): ?>
                                <tr>
                                    <td>#<?php echo $servico['id']; ?></td>
                                    <td><?php echo htmlspecialchars($servico['moto']); ?></td>
                                    <td><?php echo htmlspecialchars($servico['proprietario']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($servico['data_entrada'])); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $servico['status_class']; ?>">
                                            <?php echo htmlspecialchars($servico['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="ordem_servico.php?id=<?php echo $servico['id']; ?>" 
                                           class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts dos Gráficos -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gráfico de Faturamento
    var ctxFaturamento = document.getElementById('graficoFaturamento').getContext('2d');
    new Chart(ctxFaturamento, {
        type: 'line',
        data: <?php echo json_encode($stats->getDadosGraficoFaturamento()); ?>,
        options: {
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Gráfico de Status
    var ctxStatus = document.getElementById('graficoStatus').getContext('2d');
    new Chart(ctxStatus, {
        type: 'doughnut',
        data: <?php echo json_encode($stats->getDadosGraficoStatus()); ?>,
        options: {
            maintainAspectRatio: false
        }
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?> 