<?php
// Script hub para centralizar e facilitar o acesso aos testes de caminho
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'config.php');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Central de Testes - SubRider</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
            margin-top: 0;
        }
        h2 {
            color: #555;
            margin-top: 25px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        .test-group {
            margin-bottom: 30px;
        }
        .test-card {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .test-card h3 {
            margin-top: 0;
            color: #444;
        }
        .test-card p {
            margin-bottom: 15px;
        }
        .test-card a {
            display: inline-block;
            padding: 8px 15px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
        }
        .test-card a:hover {
            background-color: #45a049;
        }
        .env-info {
            background-color: #f0f0f0;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .env-title {
            font-weight: bold;
            margin-bottom: 10px;
        }
        .env-item {
            display: flex;
            margin-bottom: 5px;
        }
        .env-key {
            font-weight: bold;
            width: 180px;
        }
        .env-value {
            word-break: break-all;
        }
        footer {
            text-align: center;
            margin-top: 30px;
            color: #777;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Central de Testes - SubRider</h1>
        
        <div class="env-info">
            <div class="env-title">Informações do Ambiente:</div>
            <div class="env-item">
                <div class="env-key">PROJECT_ROOT_PATH:</div>
                <div class="env-value"><?php echo htmlspecialchars(PROJECT_ROOT_PATH); ?></div>
            </div>
            <div class="env-item">
                <div class="env-key">PROJECT_ROOT_URL:</div>
                <div class="env-value"><?php echo htmlspecialchars(PROJECT_ROOT_URL); ?></div>
            </div>
            <div class="env-item">
                <div class="env-key">DIRECTORY_SEPARATOR:</div>
                <div class="env-value"><?php echo htmlspecialchars(DS); ?></div>
            </div>
            <div class="env-item">
                <div class="env-key">SERVER_NAME:</div>
                <div class="env-value"><?php echo htmlspecialchars($_SERVER['SERVER_NAME'] ?? 'N/A'); ?></div>
            </div>
            <div class="env-item">
                <div class="env-key">DOCUMENT_ROOT:</div>
                <div class="env-value"><?php echo htmlspecialchars($_SERVER['DOCUMENT_ROOT'] ?? 'N/A'); ?></div>
            </div>
        </div>
        
        <div class="test-group">
            <h2>Testes Disponíveis</h2>
            
            <div class="test-card">
                <h3>1. Teste de Caminhos no Sistema de Arquivos</h3>
                <p>Verifica se todos os arquivos e diretórios críticos existem no sistema de arquivos.</p>
                <a href="<?php echo PROJECT_ROOT_URL; ?>/testar_caminhos.php" target="_blank">Executar Teste</a>
            </div>
            
            <div class="test-card">
                <h3>2. Teste de URLs</h3>
                <p>Testa a acessibilidade de todas as URLs principais do sistema e verifica se estão sendo geradas corretamente.</p>
                <a href="<?php echo PROJECT_ROOT_URL; ?>/testar_urls.php" target="_blank">Executar Teste</a>
            </div>
            
            <div class="test-card">
                <h3>3. Teste de Redirecionamentos</h3>
                <p>Simula envios de formulários para verificar se os redirecionamentos estão funcionando corretamente.</p>
                <a href="<?php echo PROJECT_ROOT_URL; ?>/testar_redirecionamentos.php" target="_blank">Executar Teste</a>
            </div>
        </div>
        
        <div class="test-group">
            <h2>Documentação</h2>
            
            <div class="test-card">
                <h3>Documentação dos Testes</h3>
                <p>Instruções detalhadas sobre como executar e interpretar os testes.</p>
                <a href="<?php echo PROJECT_ROOT_URL; ?>/testes_caminhos_README.md" target="_blank">Ver Documentação</a>
            </div>
            
            <div class="test-card">
                <h3>Progresso de Implementação</h3>
                <p>Status atual da implementação e correção de caminhos no projeto.</p>
                <a href="<?php echo PROJECT_ROOT_URL; ?>/PROGRESSO.md" target="_blank">Ver Progresso</a>
            </div>
        </div>
        
        <footer>
            <p>Desenvolvido para o Projeto SubRider - <?php echo date('Y'); ?></p>
        </footer>
    </div>
</body>
</html> 