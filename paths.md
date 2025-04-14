# Caminhos Modificados

## Arquivos Modificados para Usar PROJECT_ROOT

### Arquivos de Template
- `includes/template_base.php` - Adicionado `require_once("../config.php")` e alterado caminhos de includes

### Arquivos de Tabelas
- `pages/tabelaServicos/tabela.php` - Adicionado `require_once("../../config.php")` e alterado caminhos
- `pages/tabelaPecas/tabela.php` - Adicionado `require_once("../../config.php")` e alterado caminhos
- `pages/tabelaOrdens/tabela.php` - Adicionado `require_once("../../config.php")` e alterado caminhos
- `pages/tabelaMotos/tabela.php` - Adicionado `require_once("../../config.php")` e alterado caminhos

### Arquivos AJAX
- `ajax/update_date.php` - Adicionado `require_once("../config.php")` e alterado caminhos
- `ajax/update_proprietario.php` - Adicionado `require_once("../config.php")` e alterado caminhos
- `ajax/carregarOrdens.php` - Adicionado `require_once("../config.php")` e alterado caminhos
- `ajax/carregarMotos.php` - Adicionado `require_once("../config.php")` e alterado caminhos

### Arquivo de Configuração
- `config.php` - Modificado a definição de PROJECT_ROOT para usar `__DIR__` em vez de `dirname(__DIR__)`

## Tipos de Modificações Realizadas

### Nos includes PHP
Substituição de caminhos relativos como:
- `./includes/searchbar_unified.php` → `PROJECT_ROOT . "/includes/searchbar_unified.php"`
- `../connection/connection.php` → `PROJECT_ROOT . "/connection/connection.php"`
- `../scripts/functions.php` → `PROJECT_ROOT . "/scripts/functions.php"`

### Nos caminhos de AJAX em JavaScript
Substituição de caminhos relativos como:
- `/pages/tabelaServicos/ajax/carregarServicos.php` → `PROJECT_ROOT + '/pages/tabelaServicos/ajax/carregarServicos.php'`
- `./ajax/carregarOrdens.php` → `PROJECT_ROOT + '/ajax/carregarOrdens.php'`

### Nos caminhos de imagens
Substituição de caminhos relativos como:
- `./assets/css/images/edit-ordem.png` → `<?php echo PROJECT_ROOT; ?>/assets/css/images/edit-ordem.png`
- `./assets/css/images/x-button.png` → `<?php echo PROJECT_ROOT; ?>/assets/css/images/x-button.png`

### Nos links de redirecionamento
Substituição de caminhos utilizados em funções JavaScript como:
- `'scripts/tabelaOrdensDelete/delete-service.php'` → `PROJECT_ROOT + '/scripts/tabelaOrdens/delete-ordem.php'`
- `'scripts/tabelaMotos/delete-moto.php'` → `PROJECT_ROOT + '/scripts/tabelaMotos/delete-moto.php'` 