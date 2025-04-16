# Progresso de Implementação - Correção de Caminhos

## Status Geral
- [ ] Implementação das constantes globais em `config.php`
- [ ] Correção de caminhos em arquivos PHP de estrutura básica
- [ ] Correção de caminhos em scripts AJAX
- [ ] Correção de caminhos em scripts de processamento
- [ ] Correção de caminhos em páginas de tabelas
- [ ] Testes das implementações
- [ ] Validação final

## Detalhamento das Tarefas

### 1. Configuração Inicial
- [ ] Criar constantes em `config.php`:
  - [ ] `PROJECT_ROOT_PATH`: Caminho absoluto do sistema de arquivos
  - [ ] `PROJECT_ROOT_URL`: Caminho URL relativo ('/')
  - [ ] `DS`: Separador de diretório

### 2. Arquivos PHP - Estrutura Básica
- [ ] `/config.php`
- [ ] `/includes/template_base.php`

### 3. Scripts AJAX
- [ ] `/ajax/carregarMotos.php`
- [ ] `/ajax/carregarOrdens.php`
- [ ] `/ajax/update_date.php`
- [ ] `/ajax/update_proprietario.php`

### 4. Scripts de Processamento
- [ ] `/scripts/add_login_columns.php`
- [ ] `/scripts/log-in.php`
- [ ] `/scripts/tabelaOrdensAdd/create-service.php`
- [ ] `/scripts/tabelaOrdensDelete/delete-service.php`
- [ ] `/scripts/tabelaOrdensEdit/edit-ordem.php`
- [ ] `/scripts/tabelaPecasAdd/add-peca.php`
- [ ] `/scripts/tabelaPecasDelete/delete-peca.php`
- [ ] `/scripts/tabelaPecasEdit/edit-peca.php`
- [ ] `/scripts/tabelaServicos/delete-serv.php`
- [ ] `/scripts/tabelaServicosAdd/add-servico.php`
- [ ] `/scripts/tabelaServicosEdit/edit-servico.php`

### 5. Páginas de Tabelas
- [ ] `/pages/tabelaMotos/tabela.php`
- [ ] `/pages/tabelaMotos/ajax/carregarMotos.php`
- [ ] `/pages/tabelaOrdens/tabela.php`
- [ ] `/pages/tabelaPecas/tabela.php`
- [ ] `/pages/tabelaPecas/ajax/carregarPecas.php`
- [ ] `/pages/tabelaServicos/tabela.php`
- [ ] `/pages/tabelaServicos/ajax/carregarServicos.php`

## Tipos de Correções Implementadas

### Inclusão de arquivos
- [ ] Substituir includes/requires relativos por caminhos absolutos
  - Exemplo: `require_once("../../connection/connection.php")` → `require_once(PROJECT_ROOT_PATH . DS . "connection" . DS . "connection.php")`

### URLs e links
- [ ] Padronizar todos os links com `PROJECT_ROOT_URL`
  - Exemplo: `href="addmotos.php"` → `href="<?php echo PROJECT_ROOT_URL; ?>/addmotos.php"`

### Imagens e recursos estáticos
- [ ] Corrigir caminhos para assets
  - Exemplo: `src="assets/css/images/edit-new.png"` → `src="<?php echo PROJECT_ROOT_URL; ?>/assets/css/images/edit-new.png"`

### Chamadas AJAX
- [ ] Ajustar URLs em chamadas AJAX
  - Exemplo: `xhr.open('GET', './ajax/carregarOrdens.php'` → `xhr.open('GET', '<?php echo PROJECT_ROOT_URL; ?>/ajax/carregarOrdens.php'`

### Redirecionamentos
- [ ] Corrigir redirecionamentos para usar o caminho raiz
  - Exemplo: `header('location: ../../tabelaOrdens.php')` → `header('Location: ' . PROJECT_ROOT_URL . '/tabelaOrdens.php')`

### Caminhos de upload
- [ ] Ajustar caminhos para upload de arquivos
  - Exemplo: `$file_path = "../../upload/moto/"` → `$file_path = PROJECT_ROOT_PATH . DS . "upload" . DS . "moto" . DS`

## Notas de Implementação
- A raiz do projeto (`/subrider/`) corresponde ao diretório atual (`./`)
- Todos os caminhos absolutos devem ser construídos a partir de PROJECT_ROOT_PATH
- Todos os URLs devem usar PROJECT_ROOT_URL como base

## Testes Realizados
- [ ] Navegação entre páginas
- [ ] Carregamento de assets (CSS, JS, imagens)
- [ ] Operações de AJAX
- [ ] Uploads de arquivos
- [ ] Redirecionamentos

## Problemas Encontrados
<!-- Listar problemas encontrados durante a implementação -->

## Data de Conclusão
<!-- Data de finalização das tarefas --> 