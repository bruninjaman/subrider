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
                    
                    <?php if (!$result['exists']): ?>
                    <form class="edit-form" method="post" action="path_editor.php">
                        <input type="hidden" name="source_file" value="<?php echo htmlspecialchars($result['source_file']); ?>">
                        <input type="hidden" name="old_path" value="<?php echo htmlspecialchars($result['path']); ?>">
                        <input type="text" name="new_path" value="<?php echo htmlspecialchars($result['path']); ?>" style="width: 300px;">
                        <input type="submit" value="Update Path" style="margin-left: 10px;">
                    </form>
                    <?php endif; ?>
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