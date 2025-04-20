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

    public $results = [];
    private $scannedFiles = [];
    public $selectedPaths = [];
    public $totalFiles = 0;
    public $processedFiles = 0;
    private $debugLog = [];

    public function getDirectoryStructure($dir = PROJECT_ROOT, $relativePath = '') {
        $structure = [];
        $items = glob($dir . '/*');
        
        foreach ($items as $item) {
            $name = basename($item);
            $relPath = $relativePath ? "$relativePath/$name" : $name;
            
            if (is_dir($item)) {
                $structure[] = [
                    'type' => 'directory',
                    'name' => $name,
                    'path' => $relPath,
                    'children' => $this->getDirectoryStructure($item, $relPath)
                ];
            } else if (preg_match('/\.(php|html|js|css)$/', $name)) {
                $structure[] = [
                    'type' => 'file',
                    'name' => $name,
                    'path' => $relPath
                ];
            }
        }
        
        return $structure;
    }

    public function countScanFiles($paths) {
        $this->totalFiles = 0;
        
        foreach ($paths as $path) {
            $fullPath = PROJECT_ROOT . '/' . $path;
            
            if (is_file($fullPath) && preg_match('/\.(php|html|js)$/', $fullPath)) {
                $this->totalFiles++;
            } else if (is_dir($fullPath)) {
                // Em vez de contar todos os arquivos no diretório,
                // contamos apenas os arquivos explicitamente selecionados dentro dele
                foreach ($paths as $potentialFile) {
                    if (strpos($potentialFile, $path . '/') === 0) {
                        $fullFilePath = PROJECT_ROOT . '/' . $potentialFile;
                        if (is_file($fullFilePath) && preg_match('/\.(php|html|js)$/', $fullFilePath)) {
                            $this->totalFiles++;
                        }
                    }
                }
            }
        }
        
        return $this->totalFiles;
    }

    public function scanSelectedPaths($paths) {
        $this->results = [];
        $this->scannedFiles = [];
        $this->selectedPaths = $paths;
        $this->processedFiles = 0;
        $this->debugLog = []; // Reset do log de depuração
        
        $this->logDebug("Iniciando verificação com " . count($paths) . " caminhos selecionados: " . implode(', ', $paths));
        
        // Lista de arquivos explicitamente selecionados
        $explicitlySelectedFiles = [];
        
        // Primeiro, separamos apenas os arquivos selecionados explicitamente
        foreach ($paths as $path) {
            $fullPath = PROJECT_ROOT . '/' . $path;
            $this->logDebug("Verificando caminho selecionado: $path (Caminho completo: $fullPath)");
            
            if (is_file($fullPath) && preg_match('/\.(php|html|js)$/', $fullPath)) {
                $explicitlySelectedFiles[] = $fullPath;
                $this->logDebug("Arquivo explicitamente selecionado: $fullPath");
            } elseif (is_dir($fullPath)) {
                $this->logDebug("Diretório selecionado: $fullPath - Procurando apenas por arquivos explicitamente selecionados dentro dele");
                
                // Se for um diretório, apenas procuramos se algum arquivo dentro dele foi selecionado explicitamente
                foreach ($paths as $potentialFile) {
                    if (strpos($potentialFile, $path . '/') === 0) {
                        $fullFilePath = PROJECT_ROOT . '/' . $potentialFile;
                        if (is_file($fullFilePath) && preg_match('/\.(php|html|js)$/', $fullFilePath)) {
                            $explicitlySelectedFiles[] = $fullFilePath;
                            $this->logDebug("Arquivo explicitamente selecionado (dentro do diretório): $fullFilePath");
                        }
                    }
                }
            }
        }
        
        $this->logDebug("Total de " . count($explicitlySelectedFiles) . " arquivos explicitamente selecionados para escaneamento");
        
        // Agora, escaneamos apenas os arquivos explicitamente selecionados
        foreach ($explicitlySelectedFiles as $file) {
            if (!in_array($file, $this->scannedFiles)) {
                $this->scannedFiles[] = $file;
                $this->scanFile($file);
                $this->processedFiles++;
                $this->logDebug("Arquivo escaneado: $file");
            }
        }
        
        $this->logDebug("Verificação concluída. Total de " . count($this->results) . " caminhos encontrados.");
        return $this->results;
    }

    public function scanFile($file) {
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
        
        // Não precisamos verificar se o arquivo fonte está selecionado,
        // porque agora só escaneamos arquivos explicitamente selecionados
        
        // Convertemos o caminho para absoluto
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

        // Adicionamos o resultado para o caminho encontrado
        $this->results[] = [
            'path' => $originalPath,
            'source_file' => $sourceFile,
            'type' => $type,
            'exists' => $exists
        ];
        
        $this->logDebug("Caminho encontrado: $originalPath (Existe: " . ($exists ? "Sim" : "Não") . ")");
    }
    
    private function isPathSelected($absolutePath) {
        // Para URLs externas, não incluímos por padrão nos resultados
        if (strpos($absolutePath, 'http') === 0 || strpos($absolutePath, '//') === 0) {
            $this->logDebug("URL externa: $absolutePath - Não é considerada no verificador de seleção");
            return false;
        }
        
        // Normaliza o caminho
        $absolutePath = str_replace('\\', '/', $absolutePath);
        $projectRootPath = str_replace('\\', '/', PROJECT_ROOT);
        
        // Se o caminho está fora da raiz do projeto, não pode estar selecionado
        if (strpos($absolutePath, $projectRootPath) !== 0) {
            $this->logDebug("Caminho fora da raiz do projeto rejeitado: $absolutePath");
            return false;
        }
        
        // Converte o caminho absoluto para relativo a PROJECT_ROOT
        $relativePath = substr($absolutePath, strlen($projectRootPath) + 1);
        
        // Delega para a função isInSelectedPaths
        return $this->isInSelectedPaths($relativePath);
    }
    
    public function getProgress() {
        return $this->totalFiles > 0 ? ($this->processedFiles / $this->totalFiles) * 100 : 0;
    }

    // Função auxiliar para verificar se um caminho está contido em outro
    private function isPathInside($childPath, $parentPath) {
        // Normaliza os caminhos
        $childPath = rtrim(str_replace('\\', '/', $childPath), '/');
        $parentPath = rtrim(str_replace('\\', '/', $parentPath), '/');
        
        // Adiciona barras para garantir comparação exata de prefixo
        // Ex: "pages/login" não deve dar match em "pages/loginadmin" 
        return $childPath === $parentPath || 
               strpos($childPath . '/', $parentPath . '/') === 0;
    }
    
    // Função para verificar se um arquivo ou diretório está nas seleções do usuário
    private function isInSelectedPaths($relativePath) {
        // Verifica se está exatamente na lista
        if (in_array($relativePath, $this->selectedPaths)) {
            $this->logDebug("Caminho exato na seleção: $relativePath");
            return true;
        }
        
        // Verifica se é um subdiretório/arquivo de algum caminho selecionado
        foreach ($this->selectedPaths as $selectedPath) {
            // Se o caminho atual está contido no caminho selecionado (ex: /pages/login está em /pages)
            if ($this->isPathInside($relativePath, $selectedPath)) {
                $this->logDebug("Caminho dentro de seleção: $relativePath está em $selectedPath");
                return true;
            }
            
            // Caso especial: se o caminho é um diretório, e há caminhos selecionados dentro dele
            // Por exemplo, se relativePath = "pages" e selectedPath = "pages/login"
            // Precisamos verificar isso para permitir que o diretório pages seja escaneado
            if (is_dir(PROJECT_ROOT . '/' . $relativePath)) {
                if ($this->isPathInside($selectedPath, $relativePath)) {
                    $this->logDebug("Diretório que contém um caminho selecionado: $selectedPath está em $relativePath");
                    return true;
                }
            }
        }
        
        $this->logDebug("Caminho fora da seleção: $relativePath");
        return false;
    }

    public function displaySelector() {
        $structure = $this->getDirectoryStructure();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Path Checker - Seletor de Arquivos</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .directory-tree {
                    margin: 20px 0;
                    border: 1px solid #ddd;
                    border-radius: 4px;
                    padding: 20px;
                    max-height: 500px;
                    overflow-y: auto;
                }
                .tree-item {
                    margin: 5px 0;
                }
                .tree-dir {
                    margin-bottom: 10px;
                }
                .tree-dir > .tree-label {
                    font-weight: bold;
                    cursor: pointer;
                }
                .tree-children {
                    margin-left: 20px;
                    display: none;
                }
                .tree-expanded > .tree-children {
                    display: block;
                }
                .progress-container {
                    width: 100%;
                    background-color: #f3f3f3;
                    border-radius: 4px;
                    margin: 20px 0;
                    height: 25px;
                }
                .progress-bar {
                    height: 100%;
                    border-radius: 4px;
                    background-color: #4CAF50;
                    width: 0%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: white;
                    font-weight: bold;
                    transition: width 0.3s;
                }
                .action-buttons {
                    margin: 20px 0;
                }
                button {
                    padding: 10px 15px;
                    background-color: #007bff;
                    color: white;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                    margin-right: 10px;
                }
                button:hover {
                    background-color: #0056b3;
                }
                .select-controls {
                    margin-bottom: 15px;
                }
                .hidden {
                    display: none;
                }
                #scanning-status {
                    margin-bottom: 15px;
                    font-weight: bold;
                }
            </style>
        </head>
        <body>
            <h1>Path Checker - Seletor de Diretórios e Arquivos</h1>
            
            <div class="select-controls">
                <button id="select-all">Selecionar Todos</button>
                <button id="unselect-all">Desmarcar Todos</button>
                <button id="expand-all">Expandir Todos</button>
                <button id="collapse-all">Colapsar Todos</button>
            </div>
            
            <div class="directory-tree" id="directory-tree">
                <?php $this->renderDirectoryTree($structure); ?>
            </div>
            
            <div class="action-buttons">
                <button id="start-scan">Iniciar Verificação</button>
            </div>
            
            <div id="scanning-container" class="hidden">
                <div id="scanning-status">Preparando verificação...</div>
                <div class="progress-container">
                    <div class="progress-bar" id="progress-bar">0%</div>
                </div>
            </div>
            
            <div id="results-container" class="hidden"></div>
            
            <script>
                // Função para alternar a expansão de diretórios
                document.querySelectorAll('.tree-label').forEach(label => {
                    if (label.parentElement.classList.contains('tree-dir')) {
                        label.addEventListener('click', (e) => {
                            // Previne que o clique no checkbox cause o evento de toggle
                            if (e.target.type !== 'checkbox') {
                                label.parentElement.classList.toggle('tree-expanded');
                            }
                        });
                    }
                });
                
                // Função para selecionar/desmarcar todos os arquivos de um diretório quando ele é marcado/desmarcado
                document.querySelectorAll('.dir-checkbox').forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        const dirItem = this.closest('.tree-dir');
                        const childCheckboxes = dirItem.querySelectorAll('.tree-children input[type="checkbox"]');
                        
                        // Define o estado de todos os checkboxes dentro do diretório
                        childCheckboxes.forEach(childBox => {
                            childBox.checked = this.checked;
                        });
                        
                        // Se marcar um diretório, expandir para mostrar os filhos
                        if (this.checked) {
                            dirItem.classList.add('tree-expanded');
                        }
                        
                        // Verifica se precisa atualizar os diretórios pais
                        updateParentDirectories(dirItem.parentElement);
                    });
                });
                
                // Função para atualizar o estado dos diretórios pais com base nos filhos
                function updateParentDirectories(element) {
                    if (!element) return;
                    
                    const parentDir = element.closest('.tree-dir');
                    if (!parentDir) return;
                    
                    const parentCheckbox = parentDir.querySelector('input[type="checkbox"]');
                    const siblingCheckboxes = element.querySelectorAll('input[type="checkbox"]');
                    
                    // Verifica se todos os checkboxes irmãos estão marcados
                    const allChecked = Array.from(siblingCheckboxes).every(cb => cb.checked);
                    // Verifica se pelo menos um checkbox irmão está marcado
                    const someChecked = Array.from(siblingCheckboxes).some(cb => cb.checked);
                    
                    // Atualiza o estado do checkbox do diretório pai
                    parentCheckbox.checked = allChecked || someChecked;
                    
                    // Continua recursivamente para os diretórios pais
                    updateParentDirectories(parentDir.parentElement);
                }
                
                // Atualiza os diretórios pais quando qualquer arquivo é marcado/desmarcado
                document.querySelectorAll('.file-checkbox').forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        const parentDir = this.closest('.tree-children');
                        if (parentDir) {
                            updateParentDirectories(parentDir);
                        }
                    });
                });
                
                // Função para selecionar todos os itens
                document.getElementById('select-all').addEventListener('click', () => {
                    document.querySelectorAll('.tree-item input[type="checkbox"]').forEach(checkbox => {
                        checkbox.checked = true;
                    });
                });
                
                // Função para desmarcar todos os itens
                document.getElementById('unselect-all').addEventListener('click', () => {
                    document.querySelectorAll('.tree-item input[type="checkbox"]').forEach(checkbox => {
                        checkbox.checked = false;
                    });
                });
                
                // Função para expandir todos os diretórios
                document.getElementById('expand-all').addEventListener('click', () => {
                    document.querySelectorAll('.tree-dir').forEach(dir => {
                        dir.classList.add('tree-expanded');
                    });
                });
                
                // Função para colapsar todos os diretórios
                document.getElementById('collapse-all').addEventListener('click', () => {
                    document.querySelectorAll('.tree-dir').forEach(dir => {
                        dir.classList.remove('tree-expanded');
                    });
                });
                
                // Função para iniciar a verificação
                document.getElementById('start-scan').addEventListener('click', () => {
                    const selectedItems = [];
                    document.querySelectorAll('.tree-item input[type="checkbox"]:checked').forEach(checkbox => {
                        selectedItems.push(checkbox.value);
                    });
                    
                    if (selectedItems.length === 0) {
                        alert('Por favor, selecione pelo menos um diretório ou arquivo para verificar.');
                        return;
                    }
                    
                    // Mostrar container de progresso
                    document.getElementById('scanning-container').classList.remove('hidden');
                    document.getElementById('results-container').classList.add('hidden');
                    
                    // Contar arquivos para definir o progresso
                    fetch('path_checker.php?action=count_files', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({paths: selectedItems})
                    })
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('scanning-status').textContent = 
                            `Iniciando verificação de ${data.total_files} arquivos...`;
                        
                        // Iniciar o scan
                        startScan(selectedItems);
                    });
                });
                
                function startScan(selectedItems) {
                    // Iniciar verificação
                    fetch('path_checker.php?action=scan', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({paths: selectedItems})
                    })
                    .then(response => response.json())
                    .then(data => {
                        window.location.href = 'path_checker.php?show_results=1';
                    });
                    
                    // Atualizar barra de progresso
                    const progressInterval = setInterval(() => {
                        fetch('path_checker.php?action=progress')
                        .then(response => response.json())
                        .then(data => {
                            const progressBar = document.getElementById('progress-bar');
                            const progress = Math.round(data.progress);
                            progressBar.style.width = progress + '%';
                            progressBar.textContent = progress + '%';
                            
                            document.getElementById('scanning-status').textContent = 
                                `Verificando: ${data.processed_files} de ${data.total_files} arquivos...`;
                            
                            if (progress >= 100) {
                                clearInterval(progressInterval);
                                document.getElementById('scanning-status').textContent = 'Verificação concluída!';
                            }
                        });
                    }, 500);
                }
            </script>
        </body>
        </html>
        <?php
    }
    
    private function renderDirectoryTree($items, $level = 0) {
        foreach ($items as $item) {
            if ($item['type'] === 'directory') {
                ?>
                <div class="tree-item tree-dir">
                    <label class="tree-label">
                        <input type="checkbox" class="dir-checkbox" value="<?php echo htmlspecialchars($item['path']); ?>">
                        📁 <?php echo htmlspecialchars($item['name']); ?>
                    </label>
                    <div class="tree-children">
                        <?php $this->renderDirectoryTree($item['children'], $level + 1); ?>
                    </div>
                </div>
                <?php
            } else {
                ?>
                <div class="tree-item">
                    <label>
                        <input type="checkbox" class="file-checkbox" value="<?php echo htmlspecialchars($item['path']); ?>">
                        <?php 
                        $icon = '📄';
                        if (preg_match('/\.php$/', $item['name'])) $icon = '🐘';
                        elseif (preg_match('/\.js$/', $item['name'])) $icon = '📜';
                        elseif (preg_match('/\.css$/', $item['name'])) $icon = '🎨';
                        elseif (preg_match('/\.html$/', $item['name'])) $icon = '🌐';
                        echo $icon . ' ' . htmlspecialchars($item['name']); 
                        ?>
                    </label>
                </div>
                <?php
            }
        }
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
                
                <?php if (!empty($this->selectedPaths)): ?>
                <p><strong>Arquivos verificados:</strong></p>
                <ul>
                    <?php 
                    $maxDisplay = 10;
                    $count = 0;
                    $fileCount = 0;
                    
                    foreach ($this->selectedPaths as $path): 
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
                <?php foreach ($this->debugLog as $index => $log): ?>
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

    // Função para adicionar uma entrada ao log de depuração
    private function logDebug($message) {
        $this->debugLog[] = $message;
        // Opcionalmente, você pode salvar o log em um arquivo real
        // file_put_contents(PROJECT_ROOT . '/path_checker_debug.log', $message . PHP_EOL, FILE_APPEND);
    }

    // Função para obter o log de depuração
    public function getDebugLog() {
        return $this->debugLog;
    }
}

// Processamento das requisições AJAX
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    $checker = new PathChecker();
    
    switch ($_GET['action']) {
        case 'count_files':
            $data = json_decode(file_get_contents('php://input'), true);
            $total = $checker->countScanFiles($data['paths']);
            echo json_encode(['total_files' => $total]);
            exit;
            
        case 'scan':
            $data = json_decode(file_get_contents('php://input'), true);
            $results = $checker->scanSelectedPaths($data['paths']);
            // Salvar resultados e caminhos selecionados na sessão
            session_start();
            $_SESSION['path_checker_results'] = $results;
            $_SESSION['path_checker_selected_paths'] = $data['paths'];
            echo json_encode(['success' => true]);
            exit;
            
        case 'progress':
            echo json_encode([
                'progress' => $checker->getProgress(),
                'total_files' => $checker->totalFiles,
                'processed_files' => $checker->processedFiles
            ]);
            exit;
    }
}

// Uso da ferramenta
if (php_sapi_name() !== 'cli') {
    session_start();
    $checker = new PathChecker();
    
    if (isset($_GET['show_results']) && isset($_SESSION['path_checker_results'])) {
        $checker->results = $_SESSION['path_checker_results'];
        
        // Se temos os caminhos selecionados na sessão, vamos passá-los para o checker
        if (isset($_SESSION['path_checker_selected_paths'])) {
            $checker->selectedPaths = $_SESSION['path_checker_selected_paths'];
        }
        
        $checker->displayResults();
    } else {
        $checker->displaySelector();
    }
}
?> 