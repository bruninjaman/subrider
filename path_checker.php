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
        
        if (strpos($path, 'http') !== 0 && strpos($path, '//') !== 0) {
            if (strpos($path, '/') === 0) {
                $path = PROJECT_ROOT . $path;
            } else {
                $path = dirname($sourceFile) . '/' . $path;
            }
            $path = realpath($path) ?: $path;
        }

        $exists = false;
        if (strpos($path, 'http') === 0 || strpos($path, '//') !== 0) {
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
                    margin-bottom: 20px;
                    padding: 10px 20px;
                    background-color: #28a745;
                    color: white;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                }
                .fix-all-btn:hover {
                    background-color: #218838;
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

            <button class="fix-all-btn" onclick="fixAllInvalidPaths()">Fix All Invalid Paths</button>

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
                    <strong>Path:</strong> <span class="path-text"><?php echo htmlspecialchars($result['path']); ?></span><br>
                    <strong>Source:</strong> <?php echo htmlspecialchars($result['source_file']); ?><br>
                    <strong>Status:</strong> <span class="status-text"><?php echo $result['exists'] ? '✅ Valid' : '❌ Invalid'; ?></span>
                    
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
            // Remove message after 5 seconds
            setTimeout(function() {
                var message = document.querySelector('.message');
                if (message) {
                    message.style.display = 'none';
                }
            }, 5000);

            // Function to show temporary message
            function showMessage(message, status) {
                const container = document.createElement('div');
                container.className = `message ${status}`;
                container.textContent = message;
                document.querySelector('h1').insertAdjacentElement('afterend', container);
                setTimeout(() => container.remove(), 5000);
            }

            // Function to calculate relative path
            function getRelativePath(fromPath, toPath) {
                const root = '<?php echo addslashes(PROJECT_ROOT); ?>'.replace(/\\\\/g, '/');
                fromPath = fromPath.replace(root, '').replace(/^\/+/, '');
                toPath = toPath.replace(root, '').replace(/^\/+/, '');
                
                const fromParts = fromPath.split('/').filter(p => p);
                const toParts = toPath.split('/').filter(p => p);
                fromParts.pop(); // Remove filename from source path
                
                let commonLength = 0;
                while (commonLength < fromParts.length && 
                       commonLength < toParts.length && 
                       fromParts[commonLength] === toParts[commonLength]) {
                    commonLength++;
                }
                
                const upLevels = fromParts.length - commonLength;
                const upPath = upLevels > 0 ? Array(upLevels).fill('..').join('/') : '.';
                const remainingPath = toParts.slice(commonLength).join('/');
                
                return upPath + (remainingPath ? '/' + remainingPath : '');
            }

            // Function to fix all invalid paths
            async function fixAllInvalidPaths() {
                if (!confirm('Are you sure you want to fix all invalid paths?')) {
                    return;
                }

                const invalidItems = document.querySelectorAll('.path-item.invalid');
                const updates = [];

                for (const item of invalidItems) {
                    const sourceFile = item.querySelector('input[name="source_file"]').value;
                    const oldPath = item.querySelector('input[name="old_path"]').value;
                    const type = item.dataset.type;
                    const filename = oldPath.split('/').pop();

                    try {
                        const response = await fetch(`find_file.php?filename=${encodeURIComponent(filename)}&type=${encodeURIComponent(type)}`, {
                            headers: { 'Accept': 'application/json' }
                        });
                        const text = await response.text(); // Get raw response for debugging
                        let data;
                        try {
                            data = JSON.parse(text);
                        } catch (e) {
                            console.error('Invalid JSON from find_file.php:', text);
                            throw new Error(`Invalid JSON response from find_file.php: ${text.slice(0, 100)}`);
                        }

                        if (data.found && data.path) {
                            const relativePath = getRelativePath(sourceFile, data.path);
                            updates.push({
                                source_file: sourceFile,
                                old_path: oldPath,
                                new_path: relativePath
                            });
                        } else {
                            console.warn(`File not found for ${filename} (type: ${type})`);
                        }
                    } catch (error) {
                        console.error(`Error finding file ${filename}:`, error);
                        showMessage(`Failed to process ${filename}: ${error.message}`, 'error');
                    }
                }

                if (updates.length === 0) {
                    showMessage('No invalid paths to fix or no matching files found.', 'success');
                    return;
                }

                // Send updates to path_editor.php
                try {
                    const response = await fetch('path_editor.php', {
                        method: 'POST',
                        headers: { 
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ updates })
                    });
                    const text = await response.text(); // Get raw response for debugging
                    let data;
                    try {
                        data = JSON.parse(text);
                    } catch (e) {
                        console.error('Invalid JSON from path_editor.php:', text);
                        throw new Error(`Invalid JSON response from path_editor.php: ${text.slice(0, 100)}`);
                    }

                    if (data.success) {
                        updates.forEach(update => {
                            const item = Array.from(document.querySelectorAll('.path-item')).find(i => 
                                i.querySelector('input[name="source_file"]').value === update.source_file &&
                                i.querySelector('input[name="old_path"]').value === update.old_path
                            );
                            if (item && data.results[update.source_file + '|' + update.old_path]) {
                                item.classList.remove('invalid', 'valid', 'updated');
                                item.classList.add(data.results[update.source_file + '|' + update.old_path].exists ? 'valid' : 'invalid');
                                item.dataset.status = data.results[update.source_file + '|' + update.old_path].exists ? 'valid' : 'invalid';
                                item.querySelector('.status-text').textContent = 
                                    data.results[update.source_file + '|' + update.old_path].exists ? '✅ Valid' : '❌ Invalid';
                                item.querySelector('input[name="old_path"]').value = update.new_path;
                                item.querySelector('input[name="new_path"]').value = update.new_path;
                                item.querySelector('.path-text').textContent = update.new_path;
                            }
                        });
                        showMessage(data.message, 'success');
                        updateVisibility();
                    } else {
                        showMessage(data.message || 'Failed to update paths', 'error');
                    }
                } catch (error) {
                    console.error('Error updating paths:', error);
                    showMessage(`Error processing request: ${error.message}`, 'error');
                }
            }

            // Mark item as updated when path is edited
            document.querySelectorAll('.edit-form input[name="new_path"]').forEach(input => {
                input.addEventListener('input', function() {
                    const form = this.closest('.path-item');
                    if (this.value !== this.form.querySelector('input[name="old_path"]').value) {
                        form.classList.add('updated');
                        form.classList.remove('valid', 'invalid');
                    } single {
                        form.classList.remove('updated');
                        form.classList.add(form.dataset.status);
                    }
                });
            });

            // Handle single form submission via AJAX
            document.querySelectorAll('.edit-form').forEach(form => {
                form.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    const pathItem = this.closest('.path-item');
                    
                    try {
                        const response = await fetch('path_editor.php', {
                            method: 'POST',
                            body: formData,
                            headers: { 'Accept': 'application/json' }
                        });
                        const text = await response.text(); // Get raw response for debugging
                        let data;
                        try {
                            data = JSON.parse(text);
                        } catch (e) {
                            console.error('Invalid JSON from path_editor.php:', text);
                            throw new Error(`Invalid JSON response from path_editor.php: ${text.slice(0, 100)}`);
                        }

                        if (data.success) {
                            pathItem.classList.remove('updated', 'valid', 'invalid');
                            pathItem.classList.add(data.exists ? 'valid' : 'invalid');
                            pathItem.dataset.status = data.exists ? 'valid' : 'invalid';
                            pathItem.querySelector('.status-text').textContent = data.exists ? '✅ Valid' : '❌ Invalid';
                            pathItem.querySelector('input[name="old_path"]').value = 
                                pathItem.querySelector('input[name="new_path"]').value;
                            pathItem.querySelector('.path-text').textContent = 
                                pathItem.querySelector('input[name="new_path"]').value;
                            showMessage(data.message, 'success');
                        } else {
                            showMessage(data.message || 'Failed to update path', 'error');
                        }
                    } catch (error) {
                        console.error('Error updating path:', error);
                        showMessage(`Error processing request: ${error.message}`, 'error');
                    }
                });
            });

            // Update visibility based on filters
            function updateVisibility() {
                const selectedTypes = Array.from(document.querySelectorAll('.type-filter:checked')).map(cb => cb.value);
                const selectedStatuses = Array.from(document.querySelectorAll('.status-filter:checked')).map(cb => cb.value);
                
                document.querySelectorAll('.path-item').forEach(item => {
                    const type = item.dataset.type;
                    const status = item.dataset.status;
                    item.classList.toggle('hidden', !selectedTypes.includes(type) || !selectedStatuses.includes(status));
                });
            }

            // Add filter event listeners
            document.querySelectorAll('.type-filter, .status-filter').forEach(checkbox => {
                checkbox.addEventListener('change', updateVisibility);
            });

            // Initialize visibility
            updateVisibility();
            </script>
        </body>
        </html>
        <?php
    }
}

if (php_sapi_name() !== 'cli') {
    $checker = new PathChecker();
    $checker->scanDirectory(PROJECT_ROOT);
    $checker->displayResults();
}
?>