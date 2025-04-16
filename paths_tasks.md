# Tarefas para Revisão e Padronização de Caminhos

## Arquivos Include/Require_once a serem revisados

### Includes/Template
- [x] `includes/template_base.php` - Substituir `"./scripts/perm.php"`, `"./connection/connection.php"`, `"./scripts/functions.php"` por caminhos absolutos com PROJECT_ROOT (Feito em 2024-07-26)

### Páginas de Tabela
- [x] `pages/tabelaOrdens/tabela.php` - Substituir `"./includes/searchbar_unified.php"` por caminho absoluto (Feito em 2024-07-26)
- [x] `pages/tabelaMotos/tabela.php` - Verificar includes e substituir caminhos relativos (Feito em 2024-07-26)
- [x] `pages/tabelaPecas/tabela.php` - Verificar includes e substituir caminhos relativos (Feito em 2024-07-26)
- [x] `pages/tabelaServicos/tabela.php` - Substituir `"./includes/searchbar_unified.php"` por caminho absoluto (Feito em 2024-07-26)

### Scripts AJAX
- [x] `ajax/carregarOrdens.php` - Substituir `"../connection/connection.php"`, `"../scripts/functions.php"` por caminhos absolutos (Feito em 2024-07-26)
- [x] `ajax/carregarMotos.php` - Substituir `"../connection/connection.php"`, `"../scripts/functions.php"` por caminhos absolutos (Feito em 2024-07-26)
- [x] `ajax/update_date.php` - Substituir `"./../connection/connection.php"` por caminho absoluto (Feito em 2024-07-26)
- [x] `ajax/update_proprietario.php` - Substituir `"./../connection/connection.php"` por caminho absoluto (Feito em 2024-07-26)

### AJAX em subdiretórios
- [x] `pages/tabelaServicos/ajax/carregarServicos.php` - Corrigir `$_SERVER['DOCUMENT_ROOT'] . "connection/connection.php"` (falta barra) (Feito em 2024-07-26)
- [x] `pages/tabelaServicos/ajax/carregarServicos.php` - Corrigir `$_SERVER['DOCUMENT_ROOT'] . "scripts/functions.php"` (falta barra) (Feito em 2024-07-26)

### Scripts de processamento
- [x] `scripts/tabelaServicosEdit/edit-servico.php` - Substituir `"../perm.php"`, `"../../connection/connection.php"`, `"../functions.php"` por caminhos absolutos (Feito em 2024-07-26)
- [x] `scripts/tabelaServicosAdd/add-servico.php` - Substituir `"../../scripts/perm.php"`, `"../../connection/connection.php"`, `"../../scripts/functions.php"` por caminhos absolutos (Feito em 2024-07-26)
- [x] `scripts/tabelaServicos/delete-serv.php` - Substituir `"../../scripts/perm.php"`, `"../../connection/connection.php"`, `"../../scripts/functions.php"` por caminhos absolutos (Feito em 2024-07-26)
- [x] `scripts/tabelaPecasEdit/edit-peca.php` - Substituir `"../perm.php"`, `"../../connection/connection.php"`, `"../functions.php"` por caminhos absolutos (Feito em 2024-07-26)
- [x] `scripts/tabelaPecasDelete/delete-peca.php` - Substituir `"../../scripts/perm.php"`, `"../../connection/connection.php"`, `"../../scripts/functions.php"` por caminhos absolutos (Feito em 2024-07-26)
- [x] `scripts/tabelaPecasAdd/add-peca.php` - Substituir `"../../scripts/perm.php"`, `"../../connection/connection.php"`, `"../../scripts/functions.php"` por caminhos absolutos (Feito em 2024-07-26)
- [x] `scripts/tabelaOrdensEdit/edit-ordem.php` - Substituir `"../perm.php"`, `"../../connection/connection.php"`, `"../functions.php"` por caminhos absolutos (Feito em 2024-07-26)
- [x] `scripts/tabelaOrdensDelete/delete-service.php` - Verificar e corrigir caminhos para includes (Feito em 2024-07-26)
- [x] `scripts/tabelaOrdensAdd/create-service.php` - Verificar e corrigir caminhos para includes (Nome do arquivo corrigido de add-ordem.php) (Feito em 2024-07-26)
- [x] `scripts/log-in.php` - Substituir `"../connection/connection.php"`, `"functions.php"` por caminhos absolutos (Feito em 2024-07-26)
- [x] `scripts/add_login_columns.php` - Substituir `"../connection/connection.php"` por caminho absoluto (Feito em 2024-07-26)

## Revisão dos Includes do `config.php` (Usando `dirname(__DIR__)`)

- [x] Revisar e corrigir todos os `require_once` para `config.php` usando `dirname(__DIR__)` para garantir caminhos absolutos. (Feito em 2024-07-26)

## Caminhos de recursos em HTML/JS

### Referências de imagens
- [x] `pages/tabelaOrdens/tabela.php` - Substituir `"./assets\css\images\edit-ordem.png"`, `"./assets\css\images\x-button.png"` por caminhos absolutos usando PROJECT_ROOT (Feito em 2024-07-26)
- [x] `ajax/carregarOrdens.php` - Substituir `"assets/css/images/edit-ordem.png"`, `"assets/css/images/x-button.png"` por caminhos absolutos (Feito em 2024-07-26)

### Links de navegação e redirecionamento
- [x] `pages/tabelaOrdens/tabela.php` - Corrigir links `'tabelaOrdensAdd.php'`, `'ordemservico.php?ordem=<?php echo $moto['Codigo'] ?>'` para usar PROJECT_ROOT (Feito em 2024-07-26)
- [x] `ajax/carregarOrdens.php` - Corrigir links `'ordemservico.php?ordem=<?php echo $moto['Codigo'] ?>'`, `'tabelaOrdensEdit.php?ordem=<?php echo $moto['Codigo'] ?>'`, `'tabelaOrdensAdd.php'` para usar PROJECT_ROOT (Feito em 2024-07-26)
- [x] `ajax/carregarOrdens.php` - Corrigir caminho no JavaScript `'scripts/tabelaOrdensDelete/delete-service.php?ordemID='` para usar PROJECT_ROOT (Feito em 2024-07-26)

### Ajax URLs em JavaScript
- [x] `pages/tabelaOrdens/tabela.php` - Corrigir URL do ajax `'./ajax/carregarOrdens.php?page='` para usar PROJECT_ROOT (Feito em 2024-07-26)

## Arquivos a incluir o config.php e definir PROJECT_ROOT
- [x] `pages/tabelaOrdens/tabela.php` - Adicionar `require_once("../../config.php")` no início (Feito em 2024-07-26)
- [x] `pages/tabelaMotos/tabela.php` - Adicionar `require_once("../../config.php")` no início (Feito em 2024-07-26)
- [x] `pages/tabelaPecas/tabela.php` - Adicionar `require_once("../../config.php")` no início (Feito em 2024-07-26)
- [x] `pages/tabelaServicos/tabela.php` - Adicionar `require_once("../../config.php")` no início (Feito em 2024-07-26)
- [x] `