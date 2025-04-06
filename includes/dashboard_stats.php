<?php
/**
 * Classe responsável por fornecer estatísticas para o dashboard
 */
class DashboardStats {
    private $db;
    
    public function __construct() {
        require_once __DIR__ . '/database.php';
        $this->db = new Database();
    }
    
    /**
     * Retorna o total de motos cadastradas
     */
    public function getTotalMotos(): int {
        $sql = "SELECT COUNT(*) as total FROM motocicletas WHERE deleted_at IS NULL";
        $result = $this->db->query($sql);
        return (int) $result[0]['total'];
    }
    
    /**
     * Retorna o total de proprietários ativos
     */
    public function getTotalProprietarios(): int {
        $sql = "SELECT COUNT(*) as total FROM proprietarios WHERE deleted_at IS NULL";
        $result = $this->db->query($sql);
        return (int) $result[0]['total'];
    }
    
    /**
     * Retorna o total de ordens de serviço
     */
    public function getTotalOrdensServico(): int {
        $sql = "SELECT COUNT(*) as total FROM ordens_servico WHERE deleted_at IS NULL";
        $result = $this->db->query($sql);
        return (int) $result[0]['total'];
    }
    
    /**
     * Retorna o faturamento do mês atual
     */
    public function getFaturamentoMensal(): float {
        $sql = "SELECT COALESCE(SUM(valor_total), 0) as total 
                FROM ordens_servico 
                WHERE MONTH(data_finalizacao) = MONTH(CURRENT_DATE)
                AND YEAR(data_finalizacao) = YEAR(CURRENT_DATE)
                AND status = 'finalizada'
                AND deleted_at IS NULL";
        $result = $this->db->query($sql);
        return (float) $result[0]['total'];
    }
    
    /**
     * Retorna os serviços pendentes
     */
    public function getServicosPendentes(): array {
        $sql = "SELECT os.id, m.modelo as moto, p.nome as proprietario,
                       os.data_entrada, os.status
                FROM ordens_servico os
                JOIN motocicletas m ON os.motocicleta_id = m.id
                JOIN proprietarios p ON m.proprietario_id = p.id
                WHERE os.status != 'finalizada'
                AND os.deleted_at IS NULL
                ORDER BY os.data_entrada ASC
                LIMIT 10";
        
        $result = $this->db->query($sql);
        
        // Adiciona classes de status para o Bootstrap
        foreach ($result as &$row) {
            $row['status_class'] = $this->getStatusClass($row['status']);
        }
        
        return $result;
    }
    
    /**
     * Retorna as últimas ordens de serviço
     */
    public function getUltimasOrdens(int $limit = 5): array {
        $sql = "SELECT os.id, m.modelo as moto, p.nome as proprietario,
                       os.data_entrada, os.status, os.valor_total
                FROM ordens_servico os
                JOIN motocicletas m ON os.motocicleta_id = m.id
                JOIN proprietarios p ON m.proprietario_id = p.id
                WHERE os.deleted_at IS NULL
                ORDER BY os.data_entrada DESC
                LIMIT ?";
        
        return $this->db->query($sql, [$limit]);
    }
    
    /**
     * Retorna dados para o gráfico de faturamento
     */
    public function getDadosGraficoFaturamento(): array {
        // Últimos 6 meses
        $sql = "SELECT 
                    DATE_FORMAT(data_finalizacao, '%Y-%m') as mes,
                    SUM(valor_total) as total
                FROM ordens_servico
                WHERE data_finalizacao >= DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH)
                AND status = 'finalizada'
                AND deleted_at IS NULL
                GROUP BY DATE_FORMAT(data_finalizacao, '%Y-%m')
                ORDER BY mes ASC";
        
        $result = $this->db->query($sql);
        
        $labels = [];
        $data = [];
        
        foreach ($result as $row) {
            $date = new DateTime($row['mes'] . '-01');
            $labels[] = $date->format('M/Y');
            $data[] = (float) $row['total'];
        }
        
        return [
            'labels' => $labels,
            'datasets' => [[
                'label' => 'Faturamento (R$)',
                'data' => $data,
                'borderColor' => '#4e73df',
                'backgroundColor' => 'rgba(78, 115, 223, 0.05)',
                'fill' => true
            ]]
        ];
    }
    
    /**
     * Retorna dados para o gráfico de status das ordens
     */
    public function getDadosGraficoStatus(): array {
        $sql = "SELECT status, COUNT(*) as total
                FROM ordens_servico
                WHERE deleted_at IS NULL
                GROUP BY status";
        
        $result = $this->db->query($sql);
        
        $labels = [];
        $data = [];
        $backgroundColor = [];
        $statusColors = [
            'aguardando' => '#f6c23e',
            'em_andamento' => '#36b9cc',
            'finalizada' => '#1cc88a',
            'cancelada' => '#e74a3b'
        ];
        
        foreach ($result as $row) {
            $labels[] = ucfirst(str_replace('_', ' ', $row['status']));
            $data[] = (int) $row['total'];
            $backgroundColor[] = $statusColors[$row['status']] ?? '#858796';
        }
        
        return [
            'labels' => $labels,
            'datasets' => [[
                'data' => $data,
                'backgroundColor' => $backgroundColor
            ]]
        ];
    }
    
    /**
     * Retorna a classe CSS do Bootstrap para cada status
     */
    private function getStatusClass(string $status): string {
        $classes = [
            'aguardando' => 'warning',
            'em_andamento' => 'info',
            'finalizada' => 'success',
            'cancelada' => 'danger'
        ];
        
        return $classes[$status] ?? 'secondary';
    }
} 