# Sistema de Bloqueio de Login

Este documento descreve a implementação de um sistema de bloqueio de login após 5 tentativas incorretas.

## Recursos Implementados

1. **Bloqueio temporário**: Após 5 tentativas incorretas, a conta é bloqueada por 15 minutos.
2. **Contador de tentativas**: O usuário é informado de quantas tentativas restam antes do bloqueio.
3. **Timer de desbloqueio**: Quando bloqueado, exibe o tempo restante para desbloqueio.
4. **Redirecionamento automático**: A página se recarrega automaticamente após o término do bloqueio.
5. **Segurança aprimorada**: Uso de prepared statements para prevenir SQL injection.

## Instalação

Existem duas formas de implementar as alterações do banco de dados necessárias:

### Opção 1: Instalação Automática

As colunas serão criadas automaticamente na primeira tentativa de login após a atualização.
A função `login()` verifica se as colunas existem e as cria caso não existam.

### Opção 2: Instalação Manual

1. **Execute o script PHP específico**:
   Acesse `scripts/add_login_columns.php` no navegador para adicionar as colunas ao banco de dados.
   
   Exemplo: `http://localhost/scripts/add_login_columns.php`

   Este script irá:
   - Verificar se as colunas já existem
   - Adicionar as colunas necessárias se não existirem
   - Fornecer feedback sobre o processo

### Verificação

Certifique-se de que as seguintes modificações foram aplicadas:

- `scripts/functions.php`: Nova função de login com verificação de tentativas
- `pages/login/login.php`: Interface para exibir mensagens de erro e tempo de bloqueio
- `scripts/log-in.php`: Atualizações para compatibilidade com o novo sistema

## Funcionamento

1. Cada tentativa de login incorreta é registrada no banco de dados
2. Ao atingir 5 tentativas, o usuário é bloqueado por 15 minutos
3. Tentativas subsequentes durante o período de bloqueio mostrarão uma mensagem com o tempo restante
4. Após o período de bloqueio, as tentativas são reiniciadas

## Segurança

- Todas as consultas SQL agora usam prepared statements para evitar SQL injection
- Os parâmetros de entrada são sanitizados para evitar XSS
- O sistema de bloqueio impede ataques de força bruta

## Solução de Problemas

Se você encontrar erros relacionados às colunas do banco de dados:

1. Certifique-se de que seu usuário MySQL tem permissões para alterar tabelas
2. Execute o script de instalação manual em `scripts/add_login_columns.php`
3. Verifique se há erros no console do PHP ou logs do servidor 