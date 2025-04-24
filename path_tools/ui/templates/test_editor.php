<!DOCTYPE html>
<html>
<head>
    <title>Path Editor - Correção de Caminhos</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input[type="text"], .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .form-actions {
            margin-top: 20px;
        }
        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-right: 10px;
        }
        .btn-primary {
            background-color: #4CAF50;
            color: white;
        }
        .btn-secondary {
            background-color: #95a5a6;
            color: white;
        }
        .results {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-left: 4px solid #3498db;
        }
        .help-text {
            background-color: #f8f9fa;
            padding: 10px;
            border-left: 4px solid #4CAF50;
            margin-bottom: 15px;
        }
        .path-tools-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        .path-tools-title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        .path-tools-actions a {
            margin-left: 15px;
            text-decoration: none;
            color: #666;
        }
        .status {
            padding: 10px;
            margin-top: 15px;
            border-radius: 4px;
        }
        .status-error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .status-success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        #result-container {
            display: none;
        }
        
        <?php echo $extraCSS ?? ''; ?>
    </style>
</head>
<body>
    <div class="container">
        <div class="path-tools-header">
            <div class="path-tools-title">PathEditor - Correção de Caminhos</div>
            <div class="path-tools-actions">
                <a href="path_checker.php" title="Verificador de caminhos">PathChecker</a>
                <a href="../index.php" title="Voltar para a página inicial">Início</a>
            </div>
        </div>

        <div class="help-text">
            <p>Esta ferramenta permite corrigir caminhos quebrados em seus arquivos. Você pode:</p>
            <ul>
                <li>Corrigir manualmente caminhos específicos em arquivos do projeto</li>
                <li>Usar o PathChecker para verificar múltiplos arquivos de uma vez</li>
            </ul>
        </div>

        <h2>Correção Manual de Caminhos</h2>
        
        <form id="update-form">
            <div class="form-group">
                <label for="source_file">Arquivo Fonte:</label>
                <input type="text" id="source_file" name="source_file" required placeholder="Caminho completo para o arquivo onde o caminho está sendo usado">
            </div>
            
            <div class="form-group">
                <label for="old_path">Caminho Quebrado:</label>
                <input type="text" id="old_path" name="old_path" required placeholder="Caminho que está quebrado e precisa ser corrigido">
            </div>
            
            <div class="form-group">
                <label for="new_path">Novo Caminho:</label>
                <input type="text" id="new_path" name="new_path" required placeholder="Novo caminho para substituir o caminho quebrado">
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Aplicar Correção</button>
                <button type="button" class="btn btn-secondary" onclick="clearForm()">Limpar</button>
            </div>
        </form>
        
        <div id="result-container" class="results">
            <h3>Resultado:</h3>
            <div id="result-content"></div>
        </div>
        
        <?php echo $extraHTML ?? ''; ?>
    </div>

    <script>
        // Função para limpar o formulário
        function clearForm() {
            document.getElementById('source_file').value = '';
            document.getElementById('old_path').value = '';
            document.getElementById('new_path').value = '';
            document.getElementById('result-container').style.display = 'none';
            if (document.getElementById('included-paths-container')) {
                document.getElementById('included-paths-container').style.display = 'none';
            }
        }
        
        // Processar formulário de atualização
        document.getElementById('update-form').addEventListener('submit', function(event) {
            event.preventDefault();
            
            const sourceFile = document.getElementById('source_file').value;
            const oldPath = document.getElementById('old_path').value;
            const newPath = document.getElementById('new_path').value;
            
            if (!sourceFile || !oldPath || !newPath) {
                // Preencher campos vazios com valores padrão
                if (!sourceFile) {
                    document.getElementById('source_file').value = 'index.php'; // Valor padrão
                    sourceFile = 'index.php';
                }
                if (!oldPath) {
                    document.getElementById('old_path').value = 'path/not/specified';
                    oldPath = 'path/not/specified';
                }
                if (!newPath) {
                    // Se não tiver novo caminho, usa o antigo
                    document.getElementById('new_path').value = oldPath;
                    newPath = oldPath;
                }
            }
            
            // Mostrar container de resultado
            document.getElementById('result-container').style.display = 'block';
            document.getElementById('result-content').innerHTML = 'Processando...';
            
            // Preparar dados para a requisição
            const formData = new FormData();
            formData.append('source_file', sourceFile);
            formData.append('old_path', oldPath);
            formData.append('new_path', newPath);
            
            // Enviar requisição AJAX
            fetch('path_editor.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                let html = '';
                
                if (data.success) {
                    html += '<div class="status status-success">';
                    html += `<p>${data.message}</p>`;
                    html += '</div>';
                } else {
                    html += '<div class="status status-error">';
                    html += `<p>Erro: ${data.message}</p>`;
                    html += '</div>';
                }
                
                document.getElementById('result-content').innerHTML = html;
            })
            .catch(error => {
                console.error('Erro:', error);
                document.getElementById('result-content').innerHTML = `
                    <div class="status status-error">
                        <p>Erro ao processar a solicitação: ${error}</p>
                    </div>
                `;
            });
        });
        
        <?php echo $extraJS ?? ''; ?>
    </script>
</body>
</html> 