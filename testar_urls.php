<?php
// Script para testar as URLs e redirecionamentos no projeto
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'config.php');

// Array com todas as URLs principais para testar
$urlsTeste = [
    // Páginas principais
    [
        'titulo' => 'Páginas Principais',
        'urls' => [
            ['nome' => 'Home/Index', 'url' => PROJECT_ROOT_URL . '/index.php'],
            ['nome' => 'Login', 'url' => PROJECT_ROOT_URL . '/login.php'],
            ['nome' => 'Proprietário', 'url' => PROJECT_ROOT_URL . '/proprietario.php'],
            ['nome' => 'Ordem de Serviço', 'url' => PROJECT_ROOT_URL . '/ordemservico.php'],
            ['nome' => 'Medições', 'url' => PROJECT_ROOT_URL . '/medicoes.php'],
            ['nome' => 'Relatório', 'url' => PROJECT_ROOT_URL . '/relatorio.php']
        ]
    ],
    // Tabelas
    [
        'titulo' => 'Páginas de Tabelas',
        'urls' => [
            ['nome' => 'Tabela Motos', 'url' => PROJECT_ROOT_URL . '/tabelaMotos.php'],
            ['nome' => 'Tabela Peças', 'url' => PROJECT_ROOT_URL . '/tabelaPecas.php'],
            ['nome' => 'Tabela Ordens', 'url' => PROJECT_ROOT_URL . '/tabelaOrdens.php'],
            ['nome' => 'Tabela Serviços', 'url' => PROJECT_ROOT_URL . '/tabelaServicos.php']
        ]
    ],
    // Formulários
    [
        'titulo' => 'Formulários',
        'urls' => [
            ['nome' => 'Adicionar Moto', 'url' => PROJECT_ROOT_URL . '/addmotos.php'],
            ['nome' => 'Editar Moto', 'url' => PROJECT_ROOT_URL . '/editmotos.php'],
            ['nome' => 'Adicionar Peça', 'url' => PROJECT_ROOT_URL . '/tabelaPecasAdd.php'],
            ['nome' => 'Editar Peça', 'url' => PROJECT_ROOT_URL . '/tabelaPecasEdit.php'],
            ['nome' => 'Adicionar Ordem', 'url' => PROJECT_ROOT_URL . '/tabelaOrdensAdd.php'],
            ['nome' => 'Editar Ordem', 'url' => PROJECT_ROOT_URL . '/tabelaOrdensEdit.php'],
            ['nome' => 'Adicionar Serviço', 'url' => PROJECT_ROOT_URL . '/tabelaServicosAdd.php'],
            ['nome' => 'Editar Serviço', 'url' => PROJECT_ROOT_URL . '/tabelaServicosEdit.php']
        ]
    ],
    // Assets
    [
        'titulo' => 'Assets Principais',
        'urls' => [
            ['nome' => 'CSS Principal', 'url' => PROJECT_ROOT_URL . '/assets/css/main.css'],
            ['nome' => 'CSS Tabela', 'url' => PROJECT_ROOT_URL . '/assets/css/table.css'],
            ['nome' => 'JS Principal', 'url' => PROJECT_ROOT_URL . '/assets/js/main.js'],
            ['nome' => 'Imagem Editar', 'url' => PROJECT_ROOT_URL . '/assets/css/images/edit-new.png'],
            ['nome' => 'Imagem Excluir', 'url' => PROJECT_ROOT_URL . '/assets/css/images/x-button.png']
        ]
    ],
    // Scripts AJAX
    [
        'titulo' => 'Scripts AJAX',
        'urls' => [
            ['nome' => 'Carregar Motos', 'url' => PROJECT_ROOT_URL . '/ajax/carregarMotos.php'],
            ['nome' => 'Carregar Ordens', 'url' => PROJECT_ROOT_URL . '/ajax/carregarOrdens.php'],
            ['nome' => 'Update Date', 'url' => PROJECT_ROOT_URL . '/ajax/update_date.php'],
            ['nome' => 'Update Proprietário', 'url' => PROJECT_ROOT_URL . '/ajax/update_proprietario.php']
        ]
    ]
];

// Informações do ambiente
$envInfo = [
    'PROJECT_ROOT_PATH' => PROJECT_ROOT_PATH,
    'PROJECT_ROOT_URL' => PROJECT_ROOT_URL,
    'DS' => DS,
    'SERVER_NAME' => $_SERVER['SERVER_NAME'] ?? 'N/A',
    'PHP_SELF' => $_SERVER['PHP_SELF'] ?? 'N/A',
    'DOCUMENT_ROOT' => $_SERVER['DOCUMENT_ROOT'] ?? 'N/A'
];

?>
<!DOCTYPE html>
<html>
<head>
    <title>Teste de URLs - SubRider</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px; 
            line-height: 1.6;
        }
        h1 { color: #333; }
        h2 { 
            color: #555; 
            margin-top: 20px; 
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .url-container {
            margin-bottom: 30px;
        }
        .url-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 10px;
            margin-top: 15px;
        }
        .url-item {
            background-color: #f8f8f8;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 4px;
        }
        .url-item a {
            display: block;
            color: #0066cc;
            text-decoration: none;
            margin-bottom: 5px;
            word-break: break-all;
        }
        .url-item a:hover {
            text-decoration: underline;
        }
        .url-path {
            font-size: 12px;
            color: #666;
            word-break: break-all;
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
        .test-buttons {
            margin: 20px 0;
        }
        button {
            padding: 8px 12px;
            margin-right: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        #testResults {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            display: none;
        }
    </style>
</head>
<body>
    <h1>Teste de URLs - SubRider</h1>
    
    <div class="env-info">
        <div class="env-title">Informações do Ambiente:</div>
        <?php foreach ($envInfo as $key => $value): ?>
            <div class="env-item">
                <div class="env-key"><?php echo htmlspecialchars($key); ?>:</div>
                <div class="env-value"><?php echo htmlspecialchars($value); ?></div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div class="test-buttons">
        <button onclick="testarTodasURLs()">Testar Todas as URLs</button>
        <button onclick="limparResultados()">Limpar Resultados</button>
    </div>
    
    <div id="testResults"></div>
    
    <?php foreach ($urlsTeste as $grupo): ?>
        <div class="url-container">
            <h2><?php echo htmlspecialchars($grupo['titulo']); ?></h2>
            <div class="url-grid">
                <?php foreach ($grupo['urls'] as $item): ?>
                    <div class="url-item">
                        <a href="<?php echo htmlspecialchars($item['url']); ?>" target="_blank">
                            <?php echo htmlspecialchars($item['nome']); ?>
                        </a>
                        <div class="url-path"><?php echo htmlspecialchars($item['url']); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
    
    <script>
        // Função para testar todas as URLs via AJAX
        function testarTodasURLs() {
            const resultDiv = document.getElementById('testResults');
            resultDiv.style.display = 'block';
            resultDiv.innerHTML = '<p>Testando URLs... Por favor aguarde.</p>';
            
            const urls = [];
            document.querySelectorAll('.url-item a').forEach(function(link) {
                urls.push({
                    nome: link.textContent.trim(),
                    url: link.getAttribute('href')
                });
            });
            
            // Criar uma tabela para exibir os resultados
            let tableHTML = '<table border="1" style="width: 100%; border-collapse: collapse;">';
            tableHTML += '<tr><th style="padding: 8px;">URL</th><th style="padding: 8px;">Status</th><th style="padding: 8px;">Tempo (ms)</th></tr>';
            
            // Testar cada URL sequencialmente
            let completedTests = 0;
            
            function testarProximaURL(index) {
                if (index >= urls.length) {
                    tableHTML += '</table>';
                    resultDiv.innerHTML = tableHTML;
                    return;
                }
                
                const item = urls[index];
                const startTime = performance.now();
                
                fetch(item.url, { method: 'HEAD' })
                    .then(response => {
                        const endTime = performance.now();
                        const timeElapsed = Math.round(endTime - startTime);
                        const statusColor = response.ok ? 'green' : 'red';
                        
                        tableHTML += `<tr>
                            <td style="padding: 8px;">${item.nome} (${item.url})</td>
                            <td style="padding: 8px; color: ${statusColor};">${response.status} ${response.statusText}</td>
                            <td style="padding: 8px;">${timeElapsed}</td>
                        </tr>`;
                        
                        completedTests++;
                        resultDiv.innerHTML = `<p>Testando URLs... (${completedTests}/${urls.length})</p>` + tableHTML + '</table>';
                        
                        testarProximaURL(index + 1);
                    })
                    .catch(error => {
                        tableHTML += `<tr>
                            <td style="padding: 8px;">${item.nome} (${item.url})</td>
                            <td style="padding: 8px; color: red;">Erro: ${error.message}</td>
                            <td style="padding: 8px;">-</td>
                        </tr>`;
                        
                        completedTests++;
                        resultDiv.innerHTML = `<p>Testando URLs... (${completedTests}/${urls.length})</p>` + tableHTML + '</table>';
                        
                        testarProximaURL(index + 1);
                    });
            }
            
            testarProximaURL(0);
        }
        
        function limparResultados() {
            const resultDiv = document.getElementById('testResults');
            resultDiv.style.display = 'none';
            resultDiv.innerHTML = '';
        }
    </script>
</body>
</html> 