<!DOCTYPE html>
<html>
<head>
    <title>Path Checker - Resultados</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .results-container {
            margin-top: 20px;
        }
        .result-item {
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            overflow: hidden;
        }
        .result-header {
            display: flex;
            justify-content: space-between;
            padding: 8px 15px;
            background-color: #f5f5f5;
        }
        .result-content {
            padding: 10px 15px;
        }
        .result-type {
            font-weight: bold;
            color: #555;
        }
        .status-invalid {
            color: white;
            background-color: #e74c3c;
            padding: 3px 8px;
            border-radius: 3px;
            font-weight: bold;
        }
        .status-valid {
            color: white;
            background-color: #2ecc71;
            padding: 3px 8px;
            border-radius: 3px;
            font-weight: bold;
        }
        .result-path, .result-source {
            margin-bottom: 5px;
            word-break: break-all;
        }
        .result-actions {
            margin-top: 10px;
        }
        .edit-button, .autofix-button {
            padding: 5px 10px;
            margin-right: 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        .edit-button {
            background-color: #3498db;
            color: white;
        }
        .autofix-button {
            background-color: #2ecc71;
            color: white;
        }
        .edit-form {
            margin-top: 10px;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 3px;
        }
        .form-group {
            margin-bottom: 10px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input[type="text"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 3px;
            box-sizing: border-box;
        }
        .form-actions {
            margin-top: 10px;
        }
        .save-button, .cancel-button {
            padding: 5px 10px;
            margin-right: 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        .save-button {
            background-color: #2ecc71;
            color: white;
        }
        .cancel-button {
            background-color: #e74c3c;
            color: white;
        }
        .summary {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-left: 4px solid #3498db;
        }
        .filters {
            display: flex;
            margin-bottom: 15px;
            gap: 10px;
        }
        .filter-button {
            padding: 8px 12px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            background-color: #f5f5f5;
        }
        .filter-button.active {
            background-color: #3498db;
            color: white;
        }
        .actions-bar {
            margin: 20px 0;
            display: flex;
            justify-content: space-between;
        }
        .back-button, .autofix-all-button {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .back-button {
            background-color: #95a5a6;
            color: white;
            text-decoration: none;
            display: inline-block;
        }
        .autofix-all-button {
            background-color: #2ecc71;
            color: white;
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
        .valid {
            background-color: #f0fff0;
        }
        .invalid {
            background-color: #fff0f0;
        }
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <div class="path-tools-header">
        <div class="path-tools-title">PathChecker - Resultados</div>
        <div class="path-tools-actions">
            <a href="../path_editor.php" title="Editar caminhos">PathEditor</a>
            <a href="../index.php" title="Voltar para a página inicial">Início</a>
        </div>
    </div>

    <div class="summary">
        <h3>Resumo da Verificação</h3>
        <p>Total de caminhos verificados: <strong><?php echo count($results); ?></strong></p>
        <p>Caminhos inválidos: <strong><?php echo $errorCount; ?></strong></p>
        <p>Avisos (caminhos com código PHP): <strong><?php echo $warningCount; ?></strong></p>
    </div>

    <div class="filters">
        <button class="filter-button active" data-filter="all">Todos (<?php echo count($results); ?>)</button>
        <button class="filter-button" data-filter="invalid">Inválidos (<?php echo $errorCount; ?>)</button>
        <button class="filter-button" data-filter="valid">Válidos (<?php echo count($results) - $errorCount; ?>)</button>
    </div>

    <?php if ($errorCount > 0): ?>
    <div class="actions-bar">
        <a href="<?php echo htmlspecialchars($returnUrl); ?>" class="back-button">Voltar</a>
    </div>
    <?php endif; ?>

    <div class="results-container">
        <?php foreach ($results as $index => $result): ?>
            <?php echo $this->renderResultItem($result, $index); ?>
        <?php endforeach; ?>
    </div>

    <div class="actions-bar">
        <a href="<?php echo htmlspecialchars($returnUrl); ?>" class="back-button">Voltar</a>
    </div>

    <script>
        // Debug do URL para o path_editor.php
        console.log("URL do path_editor: <?php echo $pathEditorUrl; ?>");
        
        // Função para exibir formulário de edição
        function showEditForm(index) {
            document.getElementById('edit-form-' + index).style.display = 'block';
        }
        
        // Função para esconder formulário de edição
        function hideEditForm(index) {
            document.getElementById('edit-form-' + index).style.display = 'none';
        }
        
        // Função para atualizar caminho
        function updatePath(event, index) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            
            const sourceFile = formData.get('source_file');
            const oldPath = formData.get('old_path');
            const newPath = formData.get('new_path');
            
            if (!newPath) {
                // Remover o alerta e continuar com o processamento
                // Usar um valor padrão ou vazio para o novo caminho
                formData.set('new_path', oldPath); // Usar o caminho antigo como padrão
            }
            
            // Adicionar URL de retorno
            formData.append('return_url', window.location.href);
            
            // Enviar requisição AJAX
            fetch('<?php echo $pathEditorUrl; ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Atualizar o item na interface
                    document.getElementById('result-path-' + index).textContent = 'Caminho: ' + newPath;
                    document.getElementById('result-status-' + index).textContent = 'Válido';
                    document.getElementById('result-status-' + index).className = 'status-valid';
                    document.getElementById('result-item-' + index).className = 'result-item valid';
                    document.getElementById('edit-form-' + index).style.display = 'none';
                    
                    // Remover o alerta de sucesso
                } else {
                    alert('Erro: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao processar a solicitação: ' + error);
            });
        }
        
        // Inicializar comportamento dos filtros
        document.querySelectorAll('.filter-button').forEach(button => {
            button.addEventListener('click', function() {
                const filter = this.getAttribute('data-filter');
                
                // Atualizar botões de filtro
                document.querySelectorAll('.filter-button').forEach(btn => {
                    btn.classList.remove('active');
                });
                this.classList.add('active');
                
                // Filtrar resultados
                document.querySelectorAll('.result-item').forEach(item => {
                    if (filter === 'all') {
                        item.classList.remove('hidden');
                    } else if (filter === 'invalid') {
                        if (item.classList.contains('invalid')) {
                            item.classList.remove('hidden');
                        } else {
                            item.classList.add('hidden');
                        }
                    } else if (filter === 'valid') {
                        if (item.classList.contains('valid')) {
                            item.classList.remove('hidden');
                        } else {
                            item.classList.add('hidden');
                        }
                    }
                });
            });
        });
    </script>
</body>
</html> 