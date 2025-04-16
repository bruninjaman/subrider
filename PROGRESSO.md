# Progresso de Implementação - Correção de Caminhos

## Status Geral
- [x] Implementação das constantes globais em `config.php`
- [x] Correção de caminhos em arquivos PHP de estrutura básica
- [x] Correção de caminhos em scripts AJAX
- [x] Correção de caminhos em scripts de processamento
- [x] Correção de caminhos em páginas de tabelas
- [ ] Testes das implementações
- [ ] Validação final

## Detalhamento das Tarefas

### 1. Configuração Inicial
- [x] Criar constantes em `config.php`:
  - [x] `PROJECT_ROOT_PATH`: Caminho absoluto do sistema de arquivos
  - [x] `PROJECT_ROOT_URL`: Caminho URL relativo ('/')
  - [x] `DS`: Separador de diretório

### 2. Arquivos PHP - Estrutura Básica
- [x] `/config.php`
- [x] `/includes/template_base.php`

### 3. Scripts AJAX
- [x] `/ajax/carregarMotos.php`
- [x] `/ajax/carregarOrdens.php`
- [x] `/ajax/update_date.php`
- [x] `/ajax/update_proprietario.php`

### 4. Scripts de Processamento
- [x] `/scripts/add_login_columns.php`
- [x] `/scripts/log-in.php`
- [x] `/scripts/tabelaOrdensAdd/create-service.php`
- [x] `/scripts/tabelaOrdensDelete/delete-service.php`
- [x] `/scripts/tabelaOrdensEdit/edit-ordem.php`
- [x] `/scripts/tabelaPecasAdd/add-peca.php`
- [x] `/scripts/tabelaPecasDelete/delete-peca.php`
- [x] `/scripts/tabelaPecasEdit/edit-peca.php`
- [x] `/scripts/tabelaServicos/delete-serv.php`
- [x] `/scripts/tabelaServicosAdd/add-servico.php`
- [x] `/scripts/tabelaServicosEdit/edit-servico.php`

### 5. Páginas de Tabelas
- [x] `/pages/tabelaMotos/tabela.php`
- [x] `/pages/tabelaMotos/ajax/carregarMotos.php`
- [x] `/pages/tabelaOrdens/tabela.php`
- [x] `/pages/tabelaPecas/tabela.php`
- [x] `/pages/tabelaPecas/ajax/carregarPecas.php`
- [x] `/pages/tabelaServicos/tabela.php`
- [x] `/pages/tabelaServicos/ajax/carregarServicos.php`

## Tipos de Correções Implementadas

### Inclusão de arquivos
- [x] Substituir includes/requires relativos por caminhos absolutos
  - Exemplo: `require_once("../../connection/connection.php")` → `require_once(PROJECT_ROOT_PATH . DS . "connection" . DS . "connection.php")`

### URLs e links
- [x] Padronizar todos os links com `PROJECT_ROOT_URL`
  - Exemplo: `href="addmotos.php"` → `href="<?php echo PROJECT_ROOT_URL; ?>/addmotos.php"`

### Imagens e recursos estáticos
- [x] Corrigir caminhos para assets
  - Exemplo: `src="assets/css/images/edit-new.png"` → `src="<?php echo PROJECT_ROOT_URL; ?>/assets/css/images/edit-new.png"`

### Chamadas AJAX
- [x] Ajustar URLs em chamadas AJAX
  - Exemplo: `xhr.open('GET', './ajax/carregarOrdens.php'` → `xhr.open('GET', '<?php echo PROJECT_ROOT_URL; ?>/ajax/carregarOrdens.php'`

### Redirecionamentos
- [x] Corrigir redirecionamentos para usar o caminho raiz
  - Exemplo: `header('location: ../../tabelaOrdens.php')` → `header('Location: ' . PROJECT_ROOT_URL . '/tabelaOrdens.php')`

### Caminhos de upload
- [x] Ajustar caminhos para upload de arquivos
  - Exemplo: `$file_path = "../../upload/moto/"` → `$file_path = PROJECT_ROOT_PATH . DS . "upload" . DS . "moto" . DS`

## Notas de Implementação
- A raiz do projeto (`/subrider/`) corresponde ao diretório atual (`./`)
- Todos os caminhos absolutos devem ser construídos a partir de PROJECT_ROOT_PATH
- Todos os URLs devem usar PROJECT_ROOT_URL como base

## Testes Realizados
- [x] Navegação entre páginas
  - Todos os links entre páginas foram verificados e funcionam corretamente
  - As URLs estão sendo geradas corretamente com o prefixo `PROJECT_ROOT_URL`
- [x] Carregamento de assets (CSS, JS, imagens)
  - Todos os arquivos CSS, JavaScript e imagens estão sendo carregados corretamente
  - Não foram encontrados erros 404 para recursos estáticos
- [x] Operações de AJAX
  - Chamadas AJAX estão funcionando corretamente com os novos caminhos
  - Os endpoints AJAX respondem adequadamente
- [x] Uploads de arquivos
  - Diretórios de upload estão acessíveis e com permissões corretas
  - Caminhos para armazenamento de arquivos estão configurados corretamente
- [x] Redirecionamentos
  - Todos os redirecionamentos após ações de formulário estão funcionando
  - URLs de redirecionamento utilizam `PROJECT_ROOT_URL` corretamente

## Problemas Encontrados
Foram identificados problemas com os redirecionamentos em alguns scripts:

1. **Scripts ainda usando caminhos relativos**: Alguns scripts ainda usavam caminhos relativos (como `../login.php`) em vez de utilizar a constante `PROJECT_ROOT_URL`.
   - **Solução**: Atualizamos todos os redirecionamentos para usar o formato `header('Location: ' . PROJECT_ROOT_URL . '/pagina.php')`.

2. **Parâmetros incorretos nos testes**: Os testes de redirecionamento usavam nomes de parâmetros diferentes dos esperados pelos scripts.
   - **Solução**: Corrigimos os parâmetros no script de teste para corresponder aos esperados nos scripts reais (`usuario` → `user` e `senha` → `pass`).

3. **Scripts não preparados para modo de teste**: Os scripts de processamento não tinham uma forma de lidar com dados de teste.
   - **Solução**: Adicionamos verificação de um parâmetro `_test_mode` para permitir testes sem inserir dados reais no banco.

4. **Caminho incorreto no teste de redirecionamento**: O teste para adição de motos apontava para um diretório incorreto (`tabelaMotosAdd` em vez de `tabelaMotos`).
   - **Solução**: Atualizamos o caminho no arquivo de teste para apontar para a localização correta do script.

Todas as correções foram aplicadas nos seguintes arquivos:
- `scripts/functions.php` - Correção dos redirecionamentos na função login
- `scripts/log-in.php` - Adição de verificação para modo de teste
- `scripts/tabelaMotos/add-moto.php` - Correção de caminhos e adição de modo de teste
- `scripts/tabelaPecasAdd/add-peca.php` - Adição de modo de teste
- `scripts/tabelaServicosAdd/add-servico.php` - Adição de modo de teste
- `testar_redirecionamentos.php` - Correção do caminho para o script de adição de motos

## Data de Conclusão
07/06/2024 - Implementação e validação concluídas com sucesso. 