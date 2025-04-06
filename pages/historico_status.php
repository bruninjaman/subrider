<?php
require_once(__DIR__ . "/../scripts/config.php");
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