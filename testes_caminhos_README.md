# Testes de Caminhos - SubRider

Este diretório contém scripts para testar a implementação da correção de caminhos do projeto SubRider.

## Arquivos de Teste

1. **testar_caminhos.php** - Verifica a existência e acessibilidade dos arquivos físicos no sistema de arquivos.
2. **testar_urls.php** - Teste de URLs e acessibilidade das páginas via navegador.
3. **testar_redirecionamentos.php** - Testes de redirecionamentos entre páginas.

## Como Executar os Testes

### 1. Teste de Caminhos no Sistema de Arquivos

```
http://localhost/subrider/testar_caminhos.php
```

Este teste verifica se todos os arquivos e diretórios críticos existem no sistema de arquivos. São verificados:
- Arquivos básicos (config.php, template_base.php)
- Scripts AJAX
- Scripts de processamento
- Páginas de tabelas
- Assets principais (CSS, JS, imagens)
- Diretórios de upload

### 2. Teste de URLs

```
http://localhost/subrider/testar_urls.php
```

Este teste permite:
- Visualizar todas as URLs principais do sistema
- Testar a acessibilidade de cada URL
- Verificar se os links estão sendo gerados corretamente com `PROJECT_ROOT_URL`
- Exibir informações do ambiente, incluindo valores das constantes de caminho

### 3. Teste de Redirecionamentos

```
http://localhost/subrider/testar_redirecionamentos.php
```

Este teste simula envios de formulários para verificar se os redirecionamentos estão funcionando corretamente. Ele testa:
- Login → Index (com credenciais corretas)
- Login → Login (com credenciais incorretas)
- Adição → Tabela correspondente (para motos, peças, serviços)

## Procedimento de Teste Recomendado

1. Execute o script **testar_caminhos.php** e verifique se todos os arquivos existem.
   - Corrija quaisquer problemas de arquivos não encontrados.

2. Execute o script **testar_urls.php** e teste cada URL individualmente.
   - Clique em cada link para verificar se a página carrega corretamente.
   - Use o botão "Testar Todas as URLs" para verificar rapidamente todas as URLs.

3. Execute o script **testar_redirecionamentos.php** e teste os redirecionamentos.
   - Ajuste os dados de login no script se necessário.
   - Os testes de adição são simulados e não inserem dados no banco.

4. Teste manualmente algumas operações críticas:
   - Login e logout
   - Navegação entre páginas
   - Adicionar/editar/excluir registros
   - Upload de arquivos

## Problemas Comuns e Soluções

### URLs incorretas

Se as URLs não estão funcionando corretamente, verifique:
- O valor de `PROJECT_ROOT_URL` em config.php
- A configuração do servidor web (DocumentRoot)
- O tipo de barras usado nos caminhos (/ vs \)

### Problemas de redirecionamento

Se os redirecionamentos não funcionam:
- Verifique se a função header() está usando PROJECT_ROOT_URL
- Certifique-se de que não há saída antes dos redirecionamentos (header já enviado)
- Verifique se a URL de destino existe e está acessível

### Arquivos não encontrados

Se arquivos não são encontrados:
- Verifique o valor de PROJECT_ROOT_PATH em config.php
- Verifique se o valor de DS está sendo usado corretamente
- Confira se a estrutura de diretórios corresponde à esperada

## Registrando Resultados

Após executar todos os testes, atualize o arquivo PROGRESSO.md com os resultados na seção "Testes Realizados" e "Problemas Encontrados". 