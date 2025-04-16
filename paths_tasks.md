# Tarefas para Revisão e Padronização de Caminhos

## Arquivos Include/Require_once a serem revisados

### Includes/Template
- [ ] `includes/template_base.php` - Substituir `"./scripts/perm.php"`, `"./connection/connection.php"`, `"./scripts/functions.php"` por caminhos absolutos com PROJECT_ROOT

### Páginas de Tabela
- [ ] `pages/tabelaOrdens/tabela.php` - Substituir `"./includes/searchbar_unified.php"` por caminho absoluto
- [ ] `pages/tabelaMotos/tabela.php` - Verificar includes e substituir caminhos relativos
- [ ] `pages/tabelaPecas/tabela.php` - Verificar includes e substituir caminhos relativos 
- [ ] `pages/tabelaServicos/tabela.php` - Substituir `"./includes/searchbar_unified.php"` por caminho absoluto

### Scripts AJAX
- [ ] `ajax/carregarOrdens.php` - Substituir `"../connection/connection.php"`, `"../scripts/functions.php"` por caminhos absolutos
- [ ] `ajax/carregarMotos.php` - Substituir `"../connection/connection.php"`, `"../scripts/functions.php"` por caminhos absolutos
- [ ] `ajax/update_date.php` - Substituir `"./../connection/connection.php"` por caminho absoluto
- [ ] `ajax/update_proprietario.php` - Substituir `"./../connection/connection.php"` por caminho absoluto

### AJAX em subdiretórios
- [ ] `pages/tabelaServicos/ajax/carregarServicos.php` - Corrigir `$_SERVER['DOCUMENT_ROOT'] . "connection/connection.php"` (falta barra)
- [ ] `pages/tabelaServicos/ajax/carregarServicos.php` - Corrigir `$_SERVER['DOCUMENT_ROOT'] . "scripts/functions.php"` (falta barra)

### Scripts de processamento
- [ ] `scripts/tabelaServicosEdit/edit-servico.php` - Substituir `"../perm.php"`, `"../../connection/connection.php"`, `"../functions.php"` por caminhos absolutos
- [ ] `scripts/tabelaServicosAdd/add-servico.php` - Substituir `"../../scripts/perm.php"`, `"../../connection/connection.php"`, `"../../scripts/functions.php"` por caminhos absolutos
- [ ] `scripts/tabelaServicos/delete-serv.php` - Substituir `"../../scripts/perm.php"`, `"../../connection/connection.php"`, `"../../scripts/functions.php"` por caminhos absolutos
- [ ] `scripts/tabelaPecasEdit/edit-peca.php` - Substituir `"../perm.php"`, `"../../connection/connection.php"`, `"../functions.php"` por caminhos absolutos
- [ ] `scripts/tabelaPecasDelete/delete-peca.php` - Substituir `"../../scripts/perm.php"`, `"../../connection/connection.php"`, `"../../scripts/functions.php"` por caminhos absolutos
- [ ] `scripts/tabelaPecasAdd/add-peca.php` - Substituir `"../../scripts/perm.php"`, `"../../connection/connection.php"`, `"../../scripts/functions.php"` por caminhos absolutos
- [ ] `scripts/tabelaOrdensEdit/edit-ordem.php` - Substituir `"../perm.php"`, `"../../connection/connection.php"`, `"../functions.php"` por caminhos absolutos
- [ ] `scripts/tabelaOrdensDelete/delete-service.php` - Verificar e corrigir caminhos para includes
- [ ] `scripts/tabelaOrdensAdd/add-ordem.php` - Verificar e corrigir caminhos para includes
- [ ] `scripts/log-in.php` - Substituir `"../connection/connection.php"`, `"functions.php"` por caminhos absolutos
- [ ] `scripts/add_login_columns.php` - Substituir `"../connection/connection.php"` por caminho absoluto

## Caminhos de recursos em HTML/JS

### Referências de imagens
- [ ] `pages/tabelaOrdens/tabela.php` - Substituir `"./assets\css\images\edit-ordem.png"`, `"./assets\css\images\x-button.png"` por caminhos absolutos usando PROJECT_ROOT
- [ ] `ajax/carregarOrdens.php` - Substituir `"assets/css/images/edit-ordem.png"`, `"assets/css/images/x-button.png"` por caminhos absolutos

### Links de navegação e redirecionamento
- [ ] `pages/tabelaOrdens/tabela.php` - Corrigir links `'tabelaOrdensAdd.php'`, `'ordemservico.php?ordem=<?php echo $moto['Codigo'] ?>'` para usar PROJECT_ROOT
- [ ] `ajax/carregarOrdens.php` - Corrigir links `'ordemservico.php?ordem=<?php echo $moto['Codigo'] ?>'`, `'tabelaOrdensEdit.php?ordem=<?php echo $moto['Codigo'] ?>'`, `'tabelaOrdensAdd.php'` para usar PROJECT_ROOT
- [ ] `ajax/carregarOrdens.php` - Corrigir caminho no JavaScript `'scripts/tabelaOrdensDelete/delete-service.php?ordemID='` para usar PROJECT_ROOT

### Ajax URLs em JavaScript
- [ ] `pages/tabelaOrdens/tabela.php` - Corrigir URL do ajax `'./ajax/carregarOrdens.php?page='` para usar PROJECT_ROOT

## Arquivos a incluir o config.php e definir PROJECT_ROOT
- [ ] `pages/tabelaOrdens/tabela.php` - Adicionar `require_once("../../config.php")` no início
- [ ] `pages/tabelaMotos/tabela.php` - Adicionar `require_once("../../config.php")` no início
- [ ] `pages/tabelaPecas/tabela.php` - Adicionar `require_once("../../config.php")` no início
- [ ] `pages/tabelaServicos/tabela.php` - Adicionar `require_once("../../config.php")` no início
- [ ] `ajax/carregarOrdens.php` - Adicionar `require_once("../config.php")` no início
- [ ] `ajax/carregarMotos.php` - Adicionar `require_once("../config.php")` no início
- [ ] `ajax/update_date.php` - Adicionar `require_once("../config.php")` no início
- [ ] `ajax/update_proprietario.php` - Adicionar `require_once("../config.php")` no início

## Padrão de substituição

Para cada arquivo, seguir o seguinte padrão:

1. Substituir includes/requires relativos como:
   ```php
   require_once("../connection/connection.php");
   ```
   Por:
   ```php
   require_once(PROJECT_ROOT . "/connection/connection.php");
   ```

2. Substituir caminhos de imagens:
   ```html
   <img src="./assets/css/images/edit-ordem.png">
   ```
   Por:
   ```html
   <img src="<?php echo PROJECT_ROOT; ?>/assets/css/images/edit-ordem.png">
   ```

3. Substituir URLs em JavaScript:
   ```javascript
   xhr.open('GET', './ajax/carregarOrdens.php?page=');
   ```
   Por:
   ```javascript
   xhr.open('GET', PROJECT_ROOT + '/ajax/carregarOrdens.php?page=');
   ```

4. Substituir redirecionamentos:
   ```php
   location.href='tabelaOrdensAdd.php'
   ```
   Por:
   ```php
   location.href=PROJECT_ROOT + '/tabelaOrdensAdd.php'
   ``` 