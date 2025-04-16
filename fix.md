# Correção de Caminhos - Commit a13d643a5fa0480627b1f5a1c5e96272324467c6

## Objetivo

O principal objetivo destas alterações é modificar todos os caminhos do projeto para usar um diretório raiz padrão (`/subrider/`), permitindo que a aplicação funcione corretamente em qualquer plataforma ou ambiente, independente do sistema operacional ou configuração do servidor.

## Abordagem

1. Criação de constantes globais no arquivo `config.php`:
   - `PROJECT_ROOT_PATH`: Caminho absoluto do sistema de arquivos para a raiz do projeto
   - `PROJECT_ROOT_URL`: Caminho URL relativo à raiz do domínio do servidor web
   - `DS`: Separador de diretório (DIRECTORY_SEPARATOR) para compatibilidade entre sistemas

2. Substituição de todos os caminhos relativos por chamadas às constantes:
   - Arquivos PHP: `require_once(PROJECT_ROOT_PATH . DS . "pasta" . DS . "arquivo.php")`
   - Links e URLs: `href="<?php echo PROJECT_ROOT_URL; ?>/caminho/arquivo.php"`
   - Imagens e recursos: `src="<?php echo PROJECT_ROOT_URL; ?>/assets/imagens/imagem.png"`
   - JavaScript (URLs em AJAX): `xhr.open('GET', '<?php echo PROJECT_ROOT_URL; ?>/ajax/arquivo.php')`

3. Padronização da detecção de caminho absoluto através de `dirname(__DIR__)` 

## Arquivos Modificados

### Arquivos PHP - Estrutura Básica
- `/config.php` - Adicionadas constantes para caminhos e lógica de detecção
- `/includes/template_base.php` - Corrigidos includes e caminhos de assets

### Scripts AJAX
- `/ajax/carregarMotos.php`
- `/ajax/carregarOrdens.php`
- `/ajax/update_date.php`
- `/ajax/update_proprietario.php`

### Scripts de Processamento
- `/scripts/add_login_columns.php`
- `/scripts/log-in.php`
- `/scripts/tabelaOrdensAdd/create-service.php`
- `/scripts/tabelaOrdensDelete/delete-service.php` 
- `/scripts/tabelaOrdensEdit/edit-ordem.php`
- `/scripts/tabelaPecasAdd/add-peca.php`
- `/scripts/tabelaPecasDelete/delete-peca.php`
- `/scripts/tabelaPecasEdit/edit-peca.php`
- `/scripts/tabelaServicos/delete-serv.php`
- `/scripts/tabelaServicosAdd/add-servico.php`
- `/scripts/tabelaServicosEdit/edit-servico.php`

### Páginas de Tabelas
- `/pages/tabelaMotos/tabela.php`
- `/pages/tabelaMotos/ajax/carregarMotos.php`
- `/pages/tabelaOrdens/tabela.php`
- `/pages/tabelaPecas/tabela.php`
- `/pages/tabelaPecas/ajax/carregarPecas.php`
- `/pages/tabelaServicos/tabela.php`
- `/pages/tabelaServicos/ajax/carregarServicos.php`

## Principais Alterações

1. **Inclusão de arquivos**
   - Substituição de include/require relativos por caminhos absolutos usando constantes
   - Exemplo: `require_once("../../connection/connection.php")` → `require_once(PROJECT_ROOT_PATH . DS . "connection" . DS . "connection.php")`

2. **URLs e links**
   - Padronização de todos os links usando a constante `PROJECT_ROOT_URL`
   - Exemplo: `href="addmotos.php"` → `href="<?php echo PROJECT_ROOT_URL; ?>/addmotos.php"`

3. **Imagens e recursos estáticos**
   - Correção dos caminhos para imagens e outros arquivos estáticos
   - Exemplo: `src="assets/css/images/edit-new.png"` → `src="<?php echo PROJECT_ROOT_URL; ?>/assets/css/images/edit-new.png"`

4. **Chamadas AJAX**
   - Ajuste nas URLs usadas em chamadas AJAX
   - Exemplo: `xhr.open('GET', './ajax/carregarOrdens.php'` → `xhr.open('GET', '<?php echo PROJECT_ROOT_URL; ?>/ajax/carregarOrdens.php'`

5. **Redirecionamentos**
   - Correção dos redirecionamentos para usar o caminho raiz
   - Exemplo: `header('location: ../../tabelaOrdens.php')` → `header('Location: ' . PROJECT_ROOT_URL . '/tabelaOrdens.php')`

6. **Caminhos de upload**
   - Ajustes nos caminhos de upload de arquivos
   - Exemplo: `$file_path = "../../upload/moto/"` → `$file_path = PROJECT_ROOT_PATH . DS . "upload" . DS . "moto" . DS`

## Vantagens da Nova Estrutura

1. **Portabilidade entre ambientes** - O sistema funciona igualmente em qualquer servidor, independente da estrutura de diretórios
2. **Compatibilidade entre sistemas operacionais** - Uso de `DIRECTORY_SEPARATOR` para compatibilidade entre Windows (\\) e Unix (/)
3. **Manutenção simplificada** - Centralização das configurações de caminhos no arquivo `config.php`
4. **Flexibilidade de implantação** - O sistema pode ser instalado em qualquer subdiretório sem modificações no código
5. **Prevenção de erros** - Eliminação de erros de caminho relativo que podem ocorrer em diferentes contextos de inclusão

## Recomendações Futuras

1. **Validação de entrada** - Implementar validação e sanitização adequada dos dados de entrada
2. **Prepared Statements** - Substituir concatenação direta de SQL por prepared statements para evitar injeção SQL
3. **Revisão adicional** - Verificar quaisquer arquivos que possam não ter sido incluídos neste commit
4. **Documentação** - Manter este documento atualizado com quaisquer alterações futuras na estrutura de diretórios 