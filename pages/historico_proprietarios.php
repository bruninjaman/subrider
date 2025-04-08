<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/session_manager.php';
require_once __DIR__ . '/../classes/Security/PermissionManager.php';
require_once __DIR__ . '/../repositories/MotocicletaRepository.php';
require_once __DIR__ . '/../repositories/ProprietarioRepository.php';

$sessionManager = new SessionManager();
$permManager = \Security\PermissionManager::getInstance();

// Verifica se o usuário está logado
if (!$sessionManager->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Verifica permissão
$permManager->loadUserPermissions($_SESSION['user_id']);
if (!$permManager->hasPermission('historico.proprietarios.view')) {
    header('Location: access-denied.php');
    exit;
}

// Headers de segurança
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Inicializa repositórios
$motoRepo = new MotocicletaRepository();
$proprietarioRepo = new ProprietarioRepository();

// Obtém ID da moto da URL
$motoId = filter_input(INPUT_GET, 'moto_id', FILTER_VALIDATE_INT);

if (!$motoId) {
    header('Location: /subrider/index.php');
    exit();
}

// Busca dados da moto
$moto = $motoRepo->findById($motoId);
if (!$moto) {
    header('Location: /subrider/index.php');
    exit();
}

// Busca histórico de proprietários
$historico = $motoRepo->getHistoricoProprietarios($motoId);

// Busca proprietário atual
$proprietarioAtual = $motoRepo->getProprietarioAtual($motoId);

// Lista todos os proprietários para o select de transferência
$proprietarios = $proprietarioRepo->listar(1, 1000)['proprietarios'];

// Processa formulário de transferência
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $novoProprietarioId = filter_input(INPUT_POST, 'novo_proprietario_id', FILTER_VALIDATE_INT);
    $observacao = filter_input(INPUT_POST, 'observacao', FILTER_SANITIZE_STRING);
    
    if ($novoProprietarioId) {
        if ($motoRepo->updateProprietario($motoId, $novoProprietarioId, $observacao)) {
            header("Location: /subrider/pages/historico_proprietarios.php?moto_id=$motoId&success=1");
            exit();
        } else {
            $error = "Erro ao transferir proprietário";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico de Proprietários - <?= htmlspecialchars($moto['placa']) ?></title>
    <link rel="stylesheet" href="/subrider/assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Histórico de Proprietários</h1>
        <h2>Moto: <?= htmlspecialchars($moto['marca']) ?> <?= htmlspecialchars($moto['modelo']) ?> - Placa: <?= htmlspecialchars($moto['placa']) ?></h2>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">Transferência realizada com sucesso!</div>
        <?php endif; ?>
        
        <div class="card mb-4">
            <div class="card-header">
                <h3>Proprietário Atual</h3>
            </div>
            <div class="card-body">
                <?php if ($proprietarioAtual): ?>
                    <p><strong>Nome:</strong> <?= htmlspecialchars($proprietarioAtual['nome']) ?></p>
                    <p><strong>Desde:</strong> <?= date('d/m/Y H:i', strtotime($proprietarioAtual['data_inicio'])) ?></p>
                <?php else: ?>
                    <p>Nenhum proprietário atual registrado.</p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header">
                <h3>Transferir Proprietário</h3>
            </div>
            <div class="card-body">
                <form method="POST" class="form">
                    <div class="form-group">
                        <label for="novo_proprietario_id">Novo Proprietário:</label>
                        <select name="novo_proprietario_id" id="novo_proprietario_id" class="form-control" required>
                            <option value="">Selecione...</option>
                            <?php foreach ($proprietarios as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="observacao">Observação:</label>
                        <textarea name="observacao" id="observacao" class="form-control" rows="3"></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Transferir</button>
                </form>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h3>Histórico de Transferências</h3>
            </div>
            <div class="card-body">
                <?php if ($historico): ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Proprietário</th>
                                <th>Data Início</th>
                                <th>Data Fim</th>
                                <th>Observação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($historico as $h): ?>
                                <tr>
                                    <td><?= htmlspecialchars($h['proprietario_nome']) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($h['data_inicio'])) ?></td>
                                    <td>
                                        <?= $h['data_fim'] ? date('d/m/Y H:i', strtotime($h['data_fim'])) : 'Atual' ?>
                                    </td>
                                    <td><?= htmlspecialchars($h['observacao']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>Nenhum histórico encontrado.</p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="mt-4">
            <a href="/subrider/pages/motos.php" class="btn btn-secondary">Voltar</a>
        </div>
    </div>
    
    <script src="/subrider/assets/js/jquery.min.js"></script>
    <script src="/subrider/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html> 