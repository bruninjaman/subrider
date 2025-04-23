<!DOCTYPE html>
<html>
<head>
    <title>Path Checker - Seletor de Arquivos</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .directory-tree {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 15px;
            max-height: 400px;
            overflow: auto;
        }
        .directory-tree ul {
            list-style-type: none;
            padding-left: 20px;
            margin: 0;
        }
        .directory-tree li {
            margin: 5px 0;
            padding: 2px 0;
        }
        .directory-tree .toggle {
            display: inline-block;
            width: 15px;
            cursor: pointer;
        }
        .directory-tree .file .toggle {
            visibility: hidden;
        }
        .directory-tree .directory {
            font-weight: bold;
        }
        .directory-tree .file {
            font-weight: normal;
        }
        .directory-tree .collapsed {
            display: none;
        }
        .controls {
            margin: 15px 0;
        }
        .submit-button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .submit-button:hover {
            background-color: #45a049;
        }
        .selected-count {
            margin-bottom: 10px;
            font-weight: bold;
        }
        .search-box {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
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
        .path-tools-actions a:hover {
            text-decoration: underline;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="path-tools-header">
        <div class="path-tools-title">PathChecker - Verificador de Caminhos</div>
        <div class="path-tools-actions">
            <a href="../path_editor.php" title="Editar caminhos">PathEditor</a>
            <a href="../index.php" title="Voltar para a página inicial">Início</a>
        </div>
    </div>

    <div class="help-text">
        Selecione os arquivos ou pastas que deseja verificar. Clique nos triângulos para expandir/recolher diretórios.
        Para verificar um diretório inteiro, marque a caixa ao lado do nome do diretório.
    </div>

    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <input type="text" id="searchBox" class="search-box" placeholder="Buscar arquivos..." oninput="filterTree()">
        
        <div class="directory-tree">
            <ul>
                <?php $this->renderDirectoryTree($structure); ?>
            </ul>
        </div>
        
        <div class="selected-count">
            <span id="selectedCount">0</span> item(s) selecionado(s)
        </div>
        
        <div class="controls">
            <button type="submit" name="scan" class="submit-button">Verificar Caminhos</button>
        </div>
    </form>

    <script>
        // Função para expandir/recolher diretórios
        function toggleDirectory(id) {
            const element = document.getElementById(id);
            const isCollapsed = element.classList.contains('collapsed');
            
            if (isCollapsed) {
                element.classList.remove('collapsed');
                const toggleIcon = element.previousElementSibling;
                if (toggleIcon && toggleIcon.classList.contains('toggle')) {
                    toggleIcon.textContent = '▼';
                }
            } else {
                element.classList.add('collapsed');
                const toggleIcon = element.previousElementSibling;
                if (toggleIcon && toggleIcon.classList.contains('toggle')) {
                    toggleIcon.textContent = '▶';
                }
            }
        }
        
        // Função para lidar com a marcação/desmarcação
        function handleCheck(checkbox) {
            // Atualizar contagem
            updateSelectedCount();
            
            // Se está no modo de diretório, marcar/desmarcar todos os itens filhos
            const li = checkbox.closest('li');
            if (li && li.classList.contains('directory')) {
                const childCheckboxes = li.querySelectorAll('ul input[type="checkbox"]');
                childCheckboxes.forEach(child => {
                    child.checked = checkbox.checked;
                });
            }
        }
        
        // Função para atualizar a contagem de itens selecionados
        function updateSelectedCount() {
            const checkboxes = document.querySelectorAll('input[type="checkbox"]:checked');
            document.getElementById('selectedCount').textContent = checkboxes.length;
        }
        
        // Função para filtrar árvore de diretórios
        function filterTree() {
            const searchText = document.getElementById('searchBox').value.toLowerCase();
            const allItems = document.querySelectorAll('.directory-tree li');
            
            allItems.forEach(item => {
                const label = item.querySelector('label');
                if (!label) return;
                
                const itemText = label.textContent.toLowerCase();
                const shouldShow = itemText.includes(searchText);
                
                item.style.display = shouldShow ? '' : 'none';
                
                // Se é diretório e corresponde à pesquisa, expande
                if (shouldShow && item.classList.contains('directory') && searchText.length > 0) {
                    const ul = item.querySelector('ul');
                    if (ul) {
                        ul.classList.remove('collapsed');
                        const toggleIcon = ul.previousElementSibling;
                        if (toggleIcon && toggleIcon.classList.contains('toggle')) {
                            toggleIcon.textContent = '▼';
                        }
                    }
                }
            });
        }
    </script>
</body>
</html> 