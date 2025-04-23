<?php
/**
 * Interface de exibição de resultados da verificação
 */

namespace PathTools\UI;

use PathTools\Lib\PathChecker;

class Results {
    /**
     * Renderiza a interface de resultados
     * 
     * @param PathChecker $checker Instância do verificador de caminhos
     */
    public static function render(PathChecker $checker) {
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Path Checker Results</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .path-item { margin: 10px 0; padding: 10px; border: 1px solid #ddd; }
                .valid { background-color: #dff0d8; border-color: #d6e9c6; }
                .invalid { background-color: #f2dede; border-color: #ebccd1; }
                .updated { background-color: #fcf8e3; border-color: #faebcc; }
                .edit-form { margin-top: 10px; }
                .type-badge {
                    display: inline-block;
                    padding: 2px 6px;
                    border-radius: 3px;
                    font-size: 12px;
                    margin-right: 5px;
                    background: #007bff;
                    color: white;
                }
                .message {
                    padding: 10px;
                    margin-bottom: 20px;
                    border-radius: 4px;
                }
                .message.success {
                    background-color: #dff0d8;
                    border: 1px solid #d6e9c6;
                    color: #3c763d;
                }
                .message.error {
                    background-color: #f2dede;
                    border: 1px solid #ebccd1;
                    color: #a94442;
                }
                .summary {
                    margin-bottom: 20px;
                    padding: 10px;
                    background-color: #f8f9fa;
                    border-radius: 4px;
                }
                .filters {
                    background: #f8f9fa;
                    padding: 15px;
                    border-radius: 4px;
                    margin-bottom: 20px;
                }
                .filter-group {
                    margin-bottom: 10px;
                }
                .filter-group label {
                    font-weight: bold;
                    margin-bottom: 5px;
                    display: block;
                }
                .filter-group .checkbox-group {
                    display: flex;
                    gap: 15px;
                }
                .checkbox-label {
                    display: flex;
                    align-items: center;
                    gap: 5px;
                }
                .hidden {
                    display: none !important;
                }
                .back-button {
                    padding: 10px 15px;
                    background-color: #6c757d;
                    color: white;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                    margin: 20px 0;
                    text-decoration: none;
                    display: inline-block;
                }
                .back-button:hover {
                    background-color: #5a6268;
                }
                .selection-info {
                    background-color: #e2f0fd;
                    border: 1px solid #b8daff;
                    border-radius: 4px;
                    padding: 10px;
                    margin-bottom: 20px;
                    color: #004085;
                }
                .action-button {
                    padding: 10px 15px;
                    color: white;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                    text-decoration: none;
                    display: inline-block;
                    margin-right: 10px;
                }
                .autofix-button {
                    background-color: #28a745;
                }
                .autofix-button:hover {
                    background-color: #218838;
                }
                .btn-container {
                    display: flex;
                    gap: 10px;
                    margin-bottom: 20px;
                }
                .checkbox-select-all {
                    margin-bottom: 10px;
                }
                .path-item-checkbox {
                    float: right;
                }
                .loading-overlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(0, 0, 0, 0.5);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 9999;
                }
                .loading-spinner {
                    width: 50px;
                    height: 50px;
                    border: 5px solid #f3f3f3;
                    border-top: 5px solid #3498db;
                    border-radius: 50%;
                    animation: spin 2s linear infinite;
                }
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
                .debug-log {
                    background-color: #f8f9fa;
                    border: 1px solid #ddd;
                    border-radius: 4px;
                    padding: 15px;
                    margin-top: 30px;
                    font-family: monospace;
                    max-height: 300px;
                    overflow-y: auto;
                }
                .debug-log h3 {
                    margin-top: 0;
                    border-bottom: 1px solid #ddd;
                    padding-bottom: 10px;
                }
                .debug-log-entry {
                    margin-bottom: 5px;
                    border-bottom: 1px dotted #eee;
                    padding-bottom: 5px;
                }
                .debug-button {
                    background-color: #17a2b8;
                }
                .debug-button:hover {
                    background-color: #138496;
                }
                .test-button {
                    background-color: #ffc107;
                    color: #000;
                    border: none;
                    border-radius: 4px;
                    padding: 3px 8px;
                    margin-left: 10px;
                    cursor: pointer;
                    font-size: 12px;
                }
                .test-button:hover {
                    background-color: #e0a800;
                }
                .test-result {
                    margin-top: 10px;
                    padding: 8px;
                    border-radius: 4px;
                    background-color: #f8f9fa;
                    border: 1px solid #ddd;
                    font-size: 12px;
                }
                .test-result pre {
                    margin: 5px 0;
                    overflow-x: auto;
                    white-space: pre-wrap;
                    word-wrap: break-word;
                }
            </style>
        </head>
        <body>
            <h1>Path Checker Results</h1>
            
            <a href="path_checker.php" class="back-button">Voltar ao Seletor</a>
            
            <?php if (isset($_GET['message'])): ?>
                <div class="message <?php echo $_GET['status']; ?>">
                    <?php echo htmlspecialchars($_GET['message']); ?>
                </div>
            <?php endif; ?>

            <div class="selection-info">
                <p><strong>Nota:</strong> Apenas os caminhos encontrados dentro dos arquivos explicitamente selecionados foram verificados.</p>
                
                <?php if (!empty($checker->selectedPaths)): ?>
                <p><strong>Arquivos verificados:</strong></p>
                <ul>
                    <?php 
                    $maxDisplay = 10;
                    $count = 0;
                    $fileCount = 0;
                    
                    foreach ($checker->selectedPaths as $path): 
                        $fullPath = PROJECT_ROOT . '/' . $path;
                        if (is_file($fullPath) && preg_match('/\.(php|html|js)$/', $fullPath)):
                            $fileCount++;
                            if ($count < $maxDisplay):
                    ?>
                        <li><?php echo htmlspecialchars($path); ?></li>
                    <?php 
                            $count++;
                            endif;
                        endif;
                    endforeach; 
                    
                    if ($fileCount > $maxDisplay):
                    ?>
                        <li>...e mais <?php echo $fileCount - $maxDisplay; ?> arquivos</li>
                    <?php endif; ?>
                </ul>
                <?php endif; ?>
            </div>

            <div class="btn-container">
                <button id="autofix-button" class="action-button autofix-button">AutoFix Paths Selecionados</button>
                <label class="checkbox-select-all">
                    <input type="checkbox" id="select-all-items"> Selecionar Todos Inválidos
                </label>
                <button id="toggle-debug" class="action-button debug-button">Mostrar Log de Depuração</button>
            </div>

            <div class="filters">
                <div class="filter-group">
                    <label>Filtrar por Tipo:</label>
                    <div class="checkbox-group">
                        <?php foreach ($checker->patterns as $type => $pattern): ?>
                            <label class="checkbox-label">
                                <input type="checkbox" class="type-filter" value="<?php echo $type; ?>" checked>
                                <?php echo ucfirst($type); ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="filter-group">
                    <label>Filtrar por Status:</label>
                    <div class="checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" class="status-filter" value="valid" checked>
                            Válidos
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" class="status-filter" value="invalid" checked>
                            Inválidos
                        </label>
                    </div>
                </div>
            </div>

            <div class="summary">
                <h3>Resumo</h3>
                <?php
                $validPaths = count(array_filter($checker->results, function($r) { return $r['exists']; }));
                $invalidPaths = count(array_filter($checker->results, function($r) { return !$r['exists']; }));
                ?>
                <p>
                    Total de paths encontrados: <?php echo count($checker->results); ?><br>
                    Paths válidos: <?php echo $validPaths; ?><br>
                    Paths inválidos: <?php echo $invalidPaths; ?>
                </p>
            </div>

            <div id="results-container">
            <?php foreach ($checker->results as $index => $result): ?>
                <div class="path-item <?php echo $result['exists'] ? 'valid' : 'invalid'; ?>" 
                     data-type="<?php echo htmlspecialchars($result['type']); ?>"
                     data-status="<?php echo $result['exists'] ? 'valid' : 'invalid'; ?>"
                     data-id="<?php echo $index; ?>">
                    <?php if (!$result['exists']): ?>
                    <input type="checkbox" class="path-item-checkbox" data-path="<?php echo htmlspecialchars($result['path']); ?>" 
                           data-source="<?php echo htmlspecialchars($result['source_file']); ?>">
                    <?php endif; ?>
                    <span class="type-badge"><?php echo htmlspecialchars($result['type']); ?></span>
                    <strong>Path:</strong> <?php echo htmlspecialchars($result['path']); ?><br>
                    <strong>Source:</strong> <?php echo htmlspecialchars($result['source_file']); ?><br>
                    <strong>Status:</strong> <?php echo $result['exists'] ? '✅ Valid' : '❌ Invalid'; ?>
                    
                    <?php if (!$result['exists']): ?>
                    <button class="test-button" data-path="<?php echo htmlspecialchars($result['path']); ?>" 
                            data-source="<?php echo htmlspecialchars($result['source_file']); ?>">
                        Test AutoFix
                    </button>
                    <?php endif; ?>
                    
                    <form class="edit-form" method="post" action="path_editor.php">
                        <input type="hidden" name="source_file" value="<?php echo htmlspecialchars($result['source_file']); ?>">
                        <input type="hidden" name="old_path" value="<?php echo htmlspecialchars($result['path']); ?>">
                        <input type="hidden" name="return_url" value="path_checker.php?show_results=1">
                        <input type="text" name="new_path" value="<?php echo htmlspecialchars($result['path']); ?>" style="width: 300px;">
                        <input type="submit" value="Update Path" style="margin-left: 10px;">
                    </form>
                </div>
            <?php endforeach; ?>
            </div>

            <div id="debug-log" class="debug-log hidden">
                <h3>Log de Depuração</h3>
                <?php foreach ($checker->getDebugLog() as $index => $log): ?>
                    <div class="debug-log-entry"><?php echo htmlspecialchars($log); ?></div>
                <?php endforeach; ?>
            </div>

            <div id="loading-overlay" class="loading-overlay hidden">
                <div class="loading-spinner"></div>
            </div>

            <script>
            // Remove a mensagem após 5 segundos
            setTimeout(function() {
                var message = document.querySelector('.message');
                if (message) {
                    message.style.display = 'none';
                }
            }, 5000);

            // Toggle para exibir/ocultar o log de depuração
            document.getElementById('toggle-debug').addEventListener('click', function() {
                const debugLog = document.getElementById('debug-log');
                debugLog.classList.toggle('hidden');
                this.textContent = debugLog.classList.contains('hidden') ? 
                    'Mostrar Log de Depuração' : 'Ocultar Log de Depuração';
            });

            // Função para mostrar mensagem temporária
            function showMessage(message, status) {
                const container = document.createElement('div');
                container.className = `message ${status}`;
                container.textContent = message;
                
                document.querySelector('h1').insertAdjacentElement('afterend', container);
                
                setTimeout(() => container.remove(), 5000);
            }

            // Adiciona o estado "atualizado" quando o campo é modificado
            document.querySelectorAll('.edit-form input[name="new_path"]').forEach(input => {
                input.addEventListener('input', function() {
                    const form = this.closest('.path-item');
                    if (this.value !== this.form.querySelector('input[name="old_path"]').value) {
                        form.classList.add('updated');
                        form.classList.remove('valid', 'invalid');
                    } else {
                        form.classList.remove('updated');
                        form.classList.add(form.dataset.status === 'valid' ? 'valid' : 'invalid');
                    }
                });
            });

            // Intercepta o envio do formulário para usar AJAX
            document.querySelectorAll('.edit-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    const pathItem = this.closest('.path-item');
                    
                    fetch('path_editor.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Erro de rede: ' + response.status);
                        }
                        return response.text().then(text => {
                            try {
                                return JSON.parse(text);
                            } catch (e) {
                                console.error('Erro ao processar resposta do servidor:', text);
                                throw new Error('Resposta inválida do servidor: ' + e.message);
                            }
                        });
                    })
                    .then(data => {
                        if (data.success) {
                            // Atualiza o status do item
                            pathItem.classList.remove('updated', 'valid', 'invalid');
                            pathItem.classList.add(data.exists ? 'valid' : 'invalid');
                            pathItem.dataset.status = data.exists ? 'valid' : 'invalid';
                            
                            // Atualiza o texto de status
                            const statusText = pathItem.querySelector('strong:last-of-type');
                            statusText.nextSibling.textContent = data.exists ? '✅ Valid' : '❌ Invalid';
                            
                            // Atualiza o valor do old_path
                            this.querySelector('input[name="old_path"]').value = 
                                this.querySelector('input[name="new_path"]').value;
                            
                            showMessage(data.message, 'success');
                            
                            // Se houver uma URL de retorno, redirecionar após 1.5 segundos
                            if (data.return_url && formData.get('return_url')) {
                                setTimeout(() => {
                                    window.location.href = data.return_url;
                                }, 1500);
                            }
                        } else {
                            const errorMessage = data.message || 'Erro desconhecido';
                            showMessage(errorMessage, 'error');
                            console.error('Erro retornado pelo servidor:', data);
                        }
                    })
                    .catch(error => {
                        showMessage('Erro ao processar a requisição: ' + error.message, 'error');
                        console.error('Error:', error);
                    });
                });
            });

            // Função para atualizar a visibilidade dos items
            function updateVisibility() {
                const selectedTypes = Array.from(document.querySelectorAll('.type-filter:checked')).map(cb => cb.value);
                const selectedStatuses = Array.from(document.querySelectorAll('.status-filter:checked')).map(cb => cb.value);
                
                document.querySelectorAll('.path-item').forEach(item => {
                    const type = item.dataset.type;
                    const status = item.dataset.status;
                    
                    const typeMatch = selectedTypes.includes(type);
                    const statusMatch = selectedStatuses.includes(status);
                    
                    item.classList.toggle('hidden', !typeMatch || !statusMatch);
                });
            }

            // Adiciona listeners para todos os checkboxes
            document.querySelectorAll('.type-filter, .status-filter').forEach(checkbox => {
                checkbox.addEventListener('change', updateVisibility);
            });

            // Inicializa a visibilidade
            updateVisibility();

            // Selecionar/desmarcar todos os itens inválidos
            document.getElementById('select-all-items').addEventListener('change', function() {
                document.querySelectorAll('.path-item[data-status="invalid"] .path-item-checkbox').forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });

            // Função para mostrar/esconder o indicador de carregamento
            function toggleLoading(show) {
                document.getElementById('loading-overlay').classList.toggle('hidden', !show);
            }

            // Botão de AutoFix
            document.getElementById('autofix-button').addEventListener('click', function() {
                // Coletar os itens selecionados
                const selectedItems = [];
                document.querySelectorAll('.path-item-checkbox:checked').forEach(checkbox => {
                    selectedItems.push({
                        path: checkbox.dataset.path,
                        source_file: checkbox.dataset.source
                    });
                });
                
                if (selectedItems.length === 0) {
                    showMessage('Selecione pelo menos um caminho inválido para corrigir.', 'error');
                    return;
                }
                
                // Confirmação do usuário
                if (!confirm(`Deseja tentar corrigir automaticamente ${selectedItems.length} caminhos?`)) {
                    return;
                }
                
                // Mostrar indicador de carregamento
                toggleLoading(true);
                
                // Enviar solicitação de autofix
                const formData = new FormData();
                formData.append('autofix', 'true');
                formData.append('items', JSON.stringify(selectedItems));
                formData.append('return_url', 'path_checker.php?show_results=1');
                
                fetch('path_editor.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erro de rede: ' + response.status);
                    }
                    return response.text().then(text => {
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            console.error('Erro ao processar resposta do servidor:', text);
                            throw new Error('Resposta inválida do servidor: ' + e.message);
                        }
                    });
                })
                .then(data => {
                    toggleLoading(false);
                    
                    if (data.success) {
                        showMessage(data.message, 'success');
                        
                        // Atualizar UI com os resultados
                        data.results.forEach(result => {
                            if (result.success) {
                                document.querySelectorAll('.path-item-checkbox').forEach(checkbox => {
                                    if (checkbox.dataset.path === result.old_path && 
                                        checkbox.dataset.source === result.source_file) {
                                        
                                        const pathItem = checkbox.closest('.path-item');
                                        
                                        // Atualizar status e classe
                                        pathItem.classList.remove('invalid');
                                        pathItem.classList.add('valid');
                                        pathItem.dataset.status = 'valid';
                                        
                                        // Atualizar texto de status
                                        const statusText = pathItem.querySelector('strong:last-of-type');
                                        statusText.nextSibling.textContent = '✅ Valid';
                                        
                                        // Atualizar campo de input
                                        const input = pathItem.querySelector('input[name="new_path"]');
                                        input.value = result.new_path;
                                        
                                        // Atualizar campo hidden
                                        const oldPathInput = pathItem.querySelector('input[name="old_path"]');
                                        oldPathInput.value = result.new_path;
                                        
                                        // Remover o checkbox
                                        checkbox.remove();
                                    }
                                });
                            }
                        });
                        
                        // Redirecionar se necessário após um atraso
                        if (data.return_url) {
                            setTimeout(() => {
                                window.location.href = data.return_url;
                            }, 2000);
                        }
                    } else {
                        const errorMessage = data.message || 'Erro desconhecido';
                        showMessage(errorMessage, 'error');
                        console.error('Erro retornado pelo servidor:', data);
                    }
                })
                .catch(error => {
                    toggleLoading(false);
                    showMessage('Erro ao processar a requisição: ' + error.message, 'error');
                    console.error('Error:', error);
                });
            });

            // Handler para botões de teste de AutoFix
            document.querySelectorAll('.test-button').forEach(button => {
                button.addEventListener('click', function() {
                    const path = this.dataset.path;
                    const source = this.dataset.source;
                    const pathItem = this.closest('.path-item');
                    
                    // Remover resultado anterior se existir
                    const oldResult = pathItem.querySelector('.test-result');
                    if (oldResult) oldResult.remove();
                    
                    // Mostrar indicador de carregamento
                    toggleLoading(true);
                    
                    // Enviar solicitação para testar a correção automática
                    const formData = new FormData();
                    formData.append('test_autofix', 'true');
                    formData.append('path', path);
                    formData.append('source_file', source);
                    
                    fetch('path_editor.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Erro de rede: ' + response.status);
                        }
                        return response.json();
                    })
                    .then(data => {
                        toggleLoading(false);
                        
                        // Criar contêiner para o resultado do teste
                        const resultDiv = document.createElement('div');
                        resultDiv.className = 'test-result';
                        
                        // Adicionar título
                        const resultTitle = document.createElement('h4');
                        resultTitle.textContent = 'Resultado do Teste';
                        resultDiv.appendChild(resultTitle);
                        
                        // Adicionar informações do resultado
                        if (data.success) {
                            resultDiv.innerHTML += `
                                <p><strong>Status:</strong> <span style="color:green">Sucesso</span></p>
                                <p><strong>Caminho encontrado:</strong> ${data.new_path || 'N/A'}</p>
                                <p><strong>Caminho existe:</strong> ${data.exists ? 'Sim' : 'Não'}</p>
                                <p><strong>Substituições:</strong> ${data.replacements || 0}</p>
                            `;
                            
                            // Botão para aplicar a correção
                            if (data.new_path) {
                                const applyButton = document.createElement('button');
                                applyButton.textContent = 'Aplicar Correção';
                                applyButton.style.backgroundColor = '#28a745';
                                applyButton.style.color = 'white';
                                applyButton.style.border = 'none';
                                applyButton.style.padding = '5px 10px';
                                applyButton.style.borderRadius = '4px';
                                applyButton.style.cursor = 'pointer';
                                applyButton.style.marginTop = '5px';
                                
                                applyButton.addEventListener('click', () => {
                                    // Preencher o formulário de edição
                                    const form = pathItem.querySelector('.edit-form');
                                    form.querySelector('input[name="new_path"]').value = data.new_path;
                                    
                                    // Enviar o formulário
                                    form.dispatchEvent(new Event('submit'));
                                });
                                
                                resultDiv.appendChild(applyButton);
                            }
                        } else {
                            resultDiv.innerHTML += `
                                <p><strong>Status:</strong> <span style="color:red">Falha</span></p>
                                <p><strong>Mensagem:</strong> ${data.message || 'Erro desconhecido'}</p>
                            `;
                        }
                        
                        // Adicionar detalhes técnicos se disponíveis
                        if (data.debug_info) {
                            const debugPre = document.createElement('pre');
                            debugPre.textContent = JSON.stringify(data.debug_info, null, 2);
                            resultDiv.appendChild(debugPre);
                        }
                        
                        // Adicionar ao item
                        pathItem.appendChild(resultDiv);
                    })
                    .catch(error => {
                        toggleLoading(false);
                        showMessage('Erro ao processar o teste: ' + error.message, 'error');
                        console.error('Error:', error);
                    });
                });
            });
            </script>
        </body>
        </html>
        <?php
    }
} 