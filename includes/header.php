<?php
session_start();
require_once(__DIR__ . '/functions.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: /login.php');
    exit;
}

// Obtém o nome do usuário
$usuario_nome = $_SESSION['usuario_nome'] ?? 'Usuário';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SubRider - Sistema de Gestão de Oficina</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="/index.php">Início</a></li>
                <li><a href="/tabelaMotos.php">Motos</a></li>
                <li><a href="/pages/proprietario/proprietarios.php">Proprietários</a></li>
                <li><a href="/ordemservico.php">Ordens de Serviço</a></li>
                <li class="dropdown">
                    <a href="#" class="dropbtn">Relatórios</a>
                    <div class="dropdown-content">
                        <a href="/pages/relatorios/personalizado.php">Relatório Personalizado</a>
                        <a href="/pages/relatorios/os.php">Relatório de OS</a>
                        <a href="/pages/relatorios/motos.php">Relatório de Motos</a>
                    </div>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropbtn"><?php echo htmlspecialchars($usuario_nome); ?></a>
                    <div class="dropdown-content">
                        <a href="/scripts/logout.php">Sair</a>
                    </div>
                </li>
            </ul>
        </nav>
    </header>
    <main>