<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/session_manager.php';
require_once __DIR__ . '/../classes/Security/PermissionManager.php';

$sessionManager = new SessionManager();
$permManager = \Security\PermissionManager::getInstance();

// Verifica se o usuário está logado
if (!$sessionManager->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Verifica permissão
$permManager->loadUserPermissions($_SESSION['user_id']);
if (!$permManager->hasPermission('historico.status.view')) {
    header('Location: access-denied.php');
    exit;
}

// Headers de segurança
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

require_once(__DIR__ . "/../classes/StatusOrdem.php");

if (isset($_GET['ordem'])) {
    $ordem_id = $_GET['ordem'];
    $status_manager = new StatusOrdem($conn, $ordem_id);
    $historico = $status_manager->getHistorico();
?>
    <div class="box">
        <h3>Histórico de Status</h3>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Status</th>
                        <th>Observação</th>
                        <th>Usuário</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($historico as $registro) { ?>
                        <tr>
                            <td><?php echo date('d/m/Y H:i', strtotime($registro['data_mudanca'])); ?></td>
                            <td><?php echo $registro['status']; ?></td>
                            <td><?php echo $registro['observacao']; ?></td>
                            <td><?php echo $registro['usuario']; ?></td>
                        </tr>
                    <?php } ?>
                    <?php if (empty($historico)) { ?>
                        <tr>
                            <td colspan="4" class="text-center">Nenhum registro de mudança de status encontrado.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
<?php
}
?>