<?php
require_once(__DIR__ . "/../../classes/HistoricoMoto.php");

// Paginação
$pagina = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
$registros_por_pagina = 10;
$offset = ($pagina - 1) * $registros_por_pagina;

// Instancia o histórico
$historico = new HistoricoMoto($conn, $_GET['motoID'], $_SESSION['userId']);

// Busca os registros
$registros = $historico->buscarHistorico($registros_por_pagina, $offset);
$total_registros = $historico->contarRegistros();
$total_paginas = ceil($total_registros / $registros_por_pagina);
?>

<section id="historico">
    <div class="content">
        <h3>Histórico de Alterações</h3>
        
        <?php if (empty($registros)): ?>
            <p class="text-center">Nenhuma alteração registrada.</p>
        <?php else: ?>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Campo</th>
                            <th>Valor Anterior</th>
                            <th>Novo Valor</th>
                            <th>Usuário</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($registros as $registro): ?>
                            <tr>
                                <td><?php echo date('d/m/Y H:i', strtotime($registro['data_alteracao'])); ?></td>
                                <td><?php echo $registro['campo_alterado']; ?></td>
                                <td><?php echo $historico->formatarValor($registro['campo_alterado'], $registro['valor_antigo']); ?></td>
                                <td><?php echo $historico->formatarValor($registro['campo_alterado'], $registro['valor_novo']); ?></td>
                                <td><?php echo $registro['usuario_nome']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if ($total_paginas > 1): ?>
                <div class="paginacao">
                    <?php if ($pagina > 1): ?>
                        <a href="?motoID=<?php echo $_GET['motoID']; ?>&pagina=<?php echo ($pagina - 1); ?>" class="button small">Anterior</a>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                        <a href="?motoID=<?php echo $_GET['motoID']; ?>&pagina=<?php echo $i; ?>" 
                           class="button small <?php echo $i == $pagina ? 'primary' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($pagina < $total_paginas): ?>
                        <a href="?motoID=<?php echo $_GET['motoID']; ?>&pagina=<?php echo ($pagina + 1); ?>" class="button small">Próxima</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<style>
.paginacao {
    text-align: center;
    margin-top: 20px;
}

.paginacao .button {
    margin: 0 5px;
}

.paginacao .button.primary {
    background-color: #4CAF50;
}

.text-center {
    text-align: center;
}
</style> 