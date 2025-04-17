<?php
define('PROJECT_ROOT', __DIR__);

class PathChecker {
    private $patterns = [
        'css' => '/href=[\'"]([^\'"]+\.css)[\'"]/',
        'js' => '/src=[\'"]([^\'"]+\.js)[\'"]/',
        'ajax' => '/url:\s*[\'"]([^\'"]+)[\'"]/',
        'images' => '/src=[\'"]([^\'"]+\.(jpg|jpeg|png|gif|svg))[\'"]/',
        'php' => '/include[_once]*\([\'"]([^\'"]+\.php)[\'"]\)/',
    ];

    private $results = [];
    private $scannedFiles = [];

    public function scanDirectory($dir) {
        $files = glob($dir . '/*.{php,html,js}', GLOB_BRACE);
        foreach ($files as $file) {
            if (!in_array($file, $this->scannedFiles)) {
                $this->scannedFiles[] = $file;
                $this->scanFile($file);
            }
        }

        // Scan subdirectories
        $subdirs = glob($dir . '/*', GLOB_ONLYDIR);
        foreach ($subdirs as $subdir) {
            $this->scanDirectory($subdir);
        }
    }

    private function scanFile($file) {
        $content = file_get_contents($file);
        foreach ($this->patterns as $type => $pattern) {
            preg_match_all($pattern, $content, $matches);
            if (!empty($matches[1])) {
                foreach ($matches[1] as $path) {
                    $this->checkPath($path, $file, $type);
                }
            }
        }
    }

    private function checkPath($path, $sourceFile, $type) {
        $originalPath = $path;
        
        // Convert relative paths to absolute
        if (strpos($path, 'http') !== 0 && strpos($path, '//') !== 0) {
            if (strpos($path, '/') === 0) {
                $path = PROJECT_ROOT . $path;
            } else {
                $path = dirname($sourceFile) . '/' . $path;
            }
        }

        $exists = false;
        if (strpos($path, 'http') === 0 || strpos($path, '//') === 0) {
            // Para URLs externas, consideramos como válido por padrão
            $exists = true;
        } else {
            $exists = file_exists($path);
        }

        $this->results[] = [
            'path' => $originalPath,
            'source_file' => $sourceFile,
            'type' => $type,
            'exists' => $exists
        ];
    }

    public function displayResults() {
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
                .fix-all-btn {
                    background-color: #28a745;
                    color: white;
                    border: none;
                    padding: 10px 20px;
                    border-radius: 4px;
                    cursor: pointer;
                    margin-top: 10px;
                }
                .fix-all-btn:hover {
                    background-color: #218838;
                }
                .processing {
                    opacity: 0.7;
                    cursor: not-allowed;
                }
            </style>
        </head>
        <body>
            <h1>Path Checker Results</h1>
            
            <?php if (isset($_GET['message'])): ?>
                <div class="message <?php echo $_GET['status']; ?>">
                    <?php echo htmlspecialchars($_GET['message']); ?>
                </div>
            <?php endif; ?>

            <div class="filters">
                <div class="filter-group">
                    <label>Filtrar por Tipo:</label>
                    <div class="checkbox-group">
                        <?php foreach ($this->patterns as $type => $pattern): ?>
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
                $validPaths = count(array_filter($this->results, function($r) { return $r['exists']; }));
                $invalidPaths = count(array_filter($this->results, function($r) { return !$r['exists']; }));
                ?>
                <p>
                    Total de paths encontrados: <?php echo count($this->results); ?><br>
                    Paths válidos: <?php echo $validPaths; ?><br>
                    Paths inválidos: <?php echo $invalidPaths; ?>
                </p>
                <?php if ($invalidPaths > 0): ?>
                    <button id="fixAllPaths" class="fix-all-btn">Corrigir Todos os Paths Inválidos</button>
                <?php endif; ?>
            </div>

            <div id="results-container">
            <?php foreach ($this->results as $index => $result): ?>
                <div class="path-item <?php echo $result['exists'] ? 'valid' : 'invalid'; ?>" 
                     data-type="<?php echo htmlspecialchars($result['type']); ?>"
                     data-status="<?php echo $result['exists'] ? 'valid' : 'invalid'; ?>">
                    <span class="type-badge"><?php echo htmlspecialchars($result['type']); ?></span>
                    <strong>Path:</strong> <?php echo htmlspecialchars($result['path']); ?><br>
                    <strong>Source:</strong> <?php echo htmlspecialchars($result['source_file']); ?><br>
                    <strong>Status:</strong> <?php echo $result['exists'] ? '✅ Valid' : '❌ Invalid'; ?>
                    
                    <form class="edit-form" method="post" action="path_editor.php">
                        <input type="hidden" name="source_file" value="<?php echo htmlspecialchars($result['source_file']); ?>">
                        <input type="hidden" name="old_path" value="<?php echo htmlspecialchars($result['path']); ?>">
                        <input type="text" name="new_path" value="<?php echo htmlspecialchars($result['path']); ?>" style="width: 300px;">
                        <input type="submit" value="Update Path" style="margin-left: 10px;">
                    </form>
                </div>
            <?php endforeach; ?>
            </div>

            <script>
            // Remove a mensagem após 5 segundos
            setTimeout(function() {
                var message = document.querySelector('.message');
                if (message) {
                    message.style.display = 'none';
                }
            }, 5000);

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
                    .then(response => response.json())
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
                        } else {
                            showMessage(data.message, 'error');
                        }
                    })
                    .catch(error => {
                        showMessage('Erro ao processar a requisição', 'error');
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

            // Função para converter path absoluto para relativo
            function getRelativePath(from, to) {
                console.log('Convertendo path:', { from, to });

                // Se o path contém variáveis PHP, preserva-as
                if (to.includes('<?php') || to.includes('${')) {
                    console.log('Path contém variáveis PHP, mantendo como está:', to);
                    return to;
                }

                // Normaliza os paths para usar forward slashes
                from = from.replace(/\\/g, '/');
                to = to.replace(/\\/g, '/');

                // Se o path de destino já é uma URL ou path relativo, retorna como está
                if (to.startsWith('http') || to.startsWith('//') || to.startsWith('./') || to.startsWith('../')) {
                    console.log('Path já é URL ou relativo:', to);
                    return to;
                }

                // Remove o nome do arquivo do path de origem
                const fromDir = from.substring(0, from.lastIndexOf('/'));
                
                // Se o path de destino é absoluto (começa com /), mantém como está
                if (to.startsWith('/')) {
                    console.log('Path é absoluto, mantendo:', to);
                    return to;
                }

                // Divide os paths em partes
                const fromParts = fromDir.split('/').filter(Boolean);
                const toParts = to.split('/').filter(Boolean);

                console.log('Parts:', { fromParts, toParts });

                // Encontra os diretórios comuns
                let commonLength = 0;
                const minLength = Math.min(fromParts.length, toParts.length);
                
                while (commonLength < minLength && fromParts[commonLength] === toParts[commonLength]) {
                    commonLength++;
                }

                // Constrói o path relativo
                const upCount = fromParts.length - commonLength;
                const relativeParts = [];

                // Adiciona '../' para cada nível que precisa subir
                for (let i = 0; i < upCount; i++) {
                    relativeParts.push('..');
                }

                // Adiciona o restante do path
                relativeParts.push(...toParts.slice(commonLength));

                const relativePath = relativeParts.join('/');
                console.log('Path relativo gerado:', relativePath);

                return relativePath;
            }

            // Função para corrigir todos os paths inválidos
            async function fixAllInvalidPaths() {
                const button = document.getElementById('fixAllPaths');
                button.classList.add('processing');
                button.textContent = 'Processando...';
                button.disabled = true;

                const invalidItems = document.querySelectorAll('.path-item.invalid');
                let fixed = 0;
                let errors = 0;
                let errorDetails = [];

                for (const item of invalidItems) {
                    const form = item.querySelector('.edit-form');
                    const sourceFile = form.querySelector('input[name="source_file"]').value;
                    const oldPath = form.querySelector('input[name="old_path"]').value;
                    
                    try {
                        console.log('Processando item:', { sourceFile, oldPath });
                        
                        // Se o path contém variáveis PHP, mantém como está
                        const newPath = oldPath.includes('<?php') || oldPath.includes('${') 
                            ? oldPath 
                            : getRelativePath(sourceFile, oldPath);

                        console.log('Novo path gerado:', newPath);
                        
                        const formData = new FormData();
                        formData.append('source_file', sourceFile);
                        formData.append('old_path', oldPath);
                        formData.append('new_path', newPath);
                        formData.append('has_php_vars', oldPath.includes('<?php') || oldPath.includes('${'));
                        
                        const response = await fetch('path_editor.php', {
                            method: 'POST',
                            body: formData
                        });
                        const data = await response.json();
                        
                        if (data.success) {
                            fixed++;
                            item.classList.remove('invalid');
                            item.classList.add('valid');
                            item.dataset.status = 'valid';
                            
                            const statusText = item.querySelector('strong:last-of-type');
                            statusText.nextSibling.textContent = '✅ Valid';
                            
                            form.querySelector('input[name="new_path"]').value = newPath;
                            form.querySelector('input[name="old_path"]').value = newPath;
                        } else {
                            errors++;
                            errorDetails.push(`Erro em ${sourceFile}: ${data.message}`);
                            console.error('Erro ao processar:', { sourceFile, oldPath, newPath, error: data.message });
                        }
                    } catch (error) {
                        console.error('Erro:', error);
                        errors++;
                        errorDetails.push(`Erro em ${sourceFile}: ${error.message}`);
                    }
                }

                button.classList.remove('processing');
                button.disabled = false;
                button.textContent = 'Corrigir Todos os Paths Inválidos';

                // Mostra mensagem com detalhes dos erros
                const message = `Correção automática concluída: ${fixed} paths corrigidos, ${errors} erros\n` +
                              (errorDetails.length > 0 ? '\nDetalhes dos erros:\n' + errorDetails.join('\n') : '');
                
                showMessage(message, errors === 0 ? 'success' : 'error');
                
                // Atualiza o resumo
                const summary = document.querySelector('.summary p');
                const total = document.querySelectorAll('.path-item').length;
                const valid = document.querySelectorAll('.path-item.valid').length;
                const invalid = total - valid;
                
                summary.innerHTML = `
                    Total de paths encontrados: ${total}<br>
                    Paths válidos: ${valid}<br>
                    Paths inválidos: ${invalid}
                `;
                
                if (invalid === 0) {
                    button.style.display = 'none';
                }
            }

            // Adiciona o evento de clique ao botão de correção
            document.getElementById('fixAllPaths')?.addEventListener('click', fixAllInvalidPaths);
            </script>
        </body>
        </html>
        <?php
    }
}

// Uso da ferramenta
if (php_sapi_name() !== 'cli') {
    $checker = new PathChecker();
    $checker->scanDirectory(PROJECT_ROOT);
    $checker->displayResults();
}
?> 