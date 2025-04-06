<?php
require_once('../../classes/RelatorioPersonalizado.php');
require_once('../../includes/config.php');
require_once('../../includes/functions.php');

// Verifica se o usuário está logado
verificaLogin();

// Obtém a lista de tabelas disponíveis
$sql = "SHOW TABLES";
$result = $conn->query($sql);
$tabelas = [];
while ($row = $result->fetch_row()) {
    $tabelas[] = $row[0];
}

// Se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $relatorio = new RelatorioPersonalizado($conn);
        
        // Adiciona campos selecionados
        if (!empty($_POST['campos'])) {
            foreach ($_POST['campos'] as $campo) {
                list($tabela, $coluna) = explode('.', $campo);
                $relatorio->adicionarCampo($tabela, $coluna);
            }
        }
        
        // Adiciona filtros
        if (!empty($_POST['filtros'])) {
            foreach ($_POST['filtros'] as $filtro) {
                if (!empty($filtro['campo']) && !empty($filtro['operador']) && isset($filtro['valor'])) {
                    list($tabela, $coluna) = explode('.', $filtro['campo']);
                    $relatorio->adicionarFiltro($tabela, $coluna, $filtro['operador'], $filtro['valor']);
                }
            }
        }
        
        // Adiciona agrupamentos
        if (!empty($_POST['agrupamentos'])) {
            foreach ($_POST['agrupamentos'] as $campo) {
                list($tabela, $coluna) = explode('.', $campo);
                $relatorio->adicionarAgrupamento($tabela, $coluna);
            }
        }
        
        // Adiciona ordenação
        if (!empty($_POST['ordenacao'])) {
            foreach ($_POST['ordenacao'] as $ordem) {
                if (!empty($ordem['campo'])) {
                    list($tabela, $coluna) = explode('.', $ordem['campo']);
                    $relatorio->adicionarOrdenacao($tabela, $coluna, $ordem['direcao'] ?? 'ASC');
                }
            }
        }
        
        // Executa o relatório
        $dados = $relatorio->executar();
        
        // Se foi solicitada exportação
        if (!empty($_POST['exportar'])) {
            $nome_arquivo = 'relatorio_' . date('Y-m-d_H-i-s');
            
            if ($_POST['exportar'] === 'csv') {
                $arquivo = $relatorio->exportarCSV($nome_arquivo . '.csv');
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . basename($arquivo) . '"');
                readfile($arquivo);
                unlink($arquivo);
                exit;
            } else if ($_POST['exportar'] === 'pdf') {
                $arquivo = $relatorio->exportarPDF($nome_arquivo . '.pdf', 'Relatório Personalizado');
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename="' . basename($arquivo) . '"');
                readfile($arquivo);
                unlink($arquivo);
                exit;
            }
        }
        
        $sucesso = true;
    } catch (Exception $e) {
        $erro = $e->getMessage();
    }
}

// Obtém a estrutura das tabelas
$estrutura = [];
foreach ($tabelas as $tabela) {
    $sql = "SHOW COLUMNS FROM $tabela";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $estrutura[$tabela][] = $row['Field'];
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório Personalizado - SubRider</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        .campo-container, .filtro-container, .ordem-container {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .remover-btn {
            color: red;
            cursor: pointer;
            margin-left: 10px;
        }
        .resultados {
            margin-top: 20px;
            overflow-x: auto;
        }
        .resultados table {
            width: 100%;
            border-collapse: collapse;
        }
        .resultados th, .resultados td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .resultados th {
            background-color: #f5f5f5;
        }
        .acoes {
            margin-top: 20px;
            text-align: right;
        }
    </style>
</head>
<body>
    <?php include('../../includes/header.php'); ?>
    
    <div class="container">
        <h1>Relatório Personalizado</h1>
        
        <?php if (isset($erro)): ?>
            <div class="erro"><?php echo $erro; ?></div>
        <?php endif; ?>
        
        <form method="post" id="form-relatorio">
            <h2>Campos</h2>
            <div id="campos-container">
                <div class="campo-container">
                    <select name="campos[]" required>
                        <option value="">Selecione um campo</option>
                        <?php foreach ($estrutura as $tabela => $colunas): ?>
                            <optgroup label="<?php echo $tabela; ?>">
                                <?php foreach ($colunas as $coluna): ?>
                                    <option value="<?php echo $tabela . '.' . $coluna; ?>">
                                        <?php echo $tabela . ' - ' . $coluna; ?>
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php endforeach; ?>
                    </select>
                    <span class="remover-btn">×</span>
                </div>
            </div>
            <button type="button" id="adicionar-campo">Adicionar Campo</button>
            
            <h2>Filtros</h2>
            <div id="filtros-container"></div>
            <button type="button" id="adicionar-filtro">Adicionar Filtro</button>
            
            <h2>Agrupamento</h2>
            <div id="agrupamentos-container"></div>
            <button type="button" id="adicionar-agrupamento">Adicionar Agrupamento</button>
            
            <h2>Ordenação</h2>
            <div id="ordenacao-container"></div>
            <button type="button" id="adicionar-ordenacao">Adicionar Ordenação</button>
            
            <div class="acoes">
                <button type="submit" name="exportar" value="csv">Exportar CSV</button>
                <button type="submit" name="exportar" value="pdf">Exportar PDF</button>
                <button type="submit">Visualizar</button>
            </div>
        </form>
        
        <?php if (isset($dados)): ?>
            <div class="resultados">
                <?php if (empty($dados)): ?>
                    <p>Nenhum resultado encontrado.</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <?php foreach (array_keys($dados[0]) as $coluna): ?>
                                    <th><?php echo htmlspecialchars($coluna); ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dados as $linha): ?>
                                <tr>
                                    <?php foreach ($linha as $valor): ?>
                                        <td><?php echo htmlspecialchars($valor); ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        // Templates para adicionar elementos dinamicamente
        const campoTemplate = `
            <div class="campo-container">
                <select name="campos[]" required>
                    <option value="">Selecione um campo</option>
                    <?php foreach ($estrutura as $tabela => $colunas): ?>
                        <optgroup label="<?php echo $tabela; ?>">
                            <?php foreach ($colunas as $coluna): ?>
                                <option value="<?php echo $tabela . '.' . $coluna; ?>">
                                    <?php echo $tabela . ' - ' . $coluna; ?>
                                </option>
                            <?php endforeach; ?>
                        </optgroup>
                    <?php endforeach; ?>
                </select>
                <span class="remover-btn">×</span>
            </div>
        `;
        
        const filtroTemplate = `
            <div class="filtro-container">
                <select name="filtros[][campo]" required>
                    <option value="">Selecione um campo</option>
                    <?php foreach ($estrutura as $tabela => $colunas): ?>
                        <optgroup label="<?php echo $tabela; ?>">
                            <?php foreach ($colunas as $coluna): ?>
                                <option value="<?php echo $tabela . '.' . $coluna; ?>">
                                    <?php echo $tabela . ' - ' . $coluna; ?>
                                </option>
                            <?php endforeach; ?>
                        </optgroup>
                    <?php endforeach; ?>
                </select>
                <select name="filtros[][operador]" required>
                    <option value="=">Igual a</option>
                    <option value="!=">Diferente de</option>
                    <option value=">">Maior que</option>
                    <option value="<">Menor que</option>
                    <option value=">=">Maior ou igual a</option>
                    <option value="<=">Menor ou igual a</option>
                    <option value="LIKE">Contém</option>
                </select>
                <input type="text" name="filtros[][valor]" required placeholder="Valor">
                <span class="remover-btn">×</span>
            </div>
        `;
        
        const agrupamentoTemplate = `
            <div class="campo-container">
                <select name="agrupamentos[]">
                    <option value="">Selecione um campo</option>
                    <?php foreach ($estrutura as $tabela => $colunas): ?>
                        <optgroup label="<?php echo $tabela; ?>">
                            <?php foreach ($colunas as $coluna): ?>
                                <option value="<?php echo $tabela . '.' . $coluna; ?>">
                                    <?php echo $tabela . ' - ' . $coluna; ?>
                                </option>
                            <?php endforeach; ?>
                        </optgroup>
                    <?php endforeach; ?>
                </select>
                <span class="remover-btn">×</span>
            </div>
        `;
        
        const ordenacaoTemplate = `
            <div class="ordem-container">
                <select name="ordenacao[][campo]">
                    <option value="">Selecione um campo</option>
                    <?php foreach ($estrutura as $tabela => $colunas): ?>
                        <optgroup label="<?php echo $tabela; ?>">
                            <?php foreach ($colunas as $coluna): ?>
                                <option value="<?php echo $tabela . '.' . $coluna; ?>">
                                    <?php echo $tabela . ' - ' . $coluna; ?>
                                </option>
                            <?php endforeach; ?>
                        </optgroup>
                    <?php endforeach; ?>
                </select>
                <select name="ordenacao[][direcao]">
                    <option value="ASC">Crescente</option>
                    <option value="DESC">Decrescente</option>
                </select>
                <span class="remover-btn">×</span>
            </div>
        `;
        
        // Funções para adicionar elementos
        document.getElementById('adicionar-campo').addEventListener('click', function() {
            document.getElementById('campos-container').insertAdjacentHTML('beforeend', campoTemplate);
        });
        
        document.getElementById('adicionar-filtro').addEventListener('click', function() {
            document.getElementById('filtros-container').insertAdjacentHTML('beforeend', filtroTemplate);
        });
        
        document.getElementById('adicionar-agrupamento').addEventListener('click', function() {
            document.getElementById('agrupamentos-container').insertAdjacentHTML('beforeend', agrupamentoTemplate);
        });
        
        document.getElementById('adicionar-ordenacao').addEventListener('click', function() {
            document.getElementById('ordenacao-container').insertAdjacentHTML('beforeend', ordenacaoTemplate);
        });
        
        // Função para remover elementos
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remover-btn')) {
                e.target.parentElement.remove();
            }
        });
    </script>
</body>
</html> 