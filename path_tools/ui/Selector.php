<?php
/**
 * Interface de seleção de arquivos e diretórios
 */

namespace PathTools\UI;

use PathTools\Lib\PathChecker;

class Selector {
    /**
     * Renderiza a interface de seleção de arquivos
     * 
     * @param PathChecker $checker Instância do verificador de caminhos
     */
    public static function render(PathChecker $checker) {
        $structure = $checker->getDirectoryStructure();
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
                <?php self::renderDirectoryTree($structure); ?>
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

    /**
     * Renderiza a árvore de diretórios
     * 
     * @param array $items Itens a serem renderizados
     * @param int $level Nível de aninhamento
     */
    private static function renderDirectoryTree($items, $level = 0) {
        foreach ($items as $item) {
            if ($item['type'] === 'directory') {
                ?>
                <div class="tree-item tree-dir">
                    <label class="tree-label">
                        <input type="checkbox" class="dir-checkbox" value="<?php echo htmlspecialchars($item['path']); ?>">
                        📁 <?php echo htmlspecialchars($item['name']); ?>
                    </label>
                    <div class="tree-children">
                        <?php self::renderDirectoryTree($item['children'], $level + 1); ?>
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
} 