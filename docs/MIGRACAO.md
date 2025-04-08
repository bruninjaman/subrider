# Guia de Migração - Sistema de Permissões

Este guia descreve as mudanças necessárias para migrar do sistema antigo de permissões para o novo sistema usando o `PermissionManager`.

## Mudanças Principais

1. **Inicialização do Sistema**
   - Antes: Verificações de permissão dispersas em vários arquivos
   - Agora: Centralizado em `config/init.php` e `src/Permissions/PermissionManager.php`

2. **Verificação de Permissões**
   - Antes:
     ```php
     if (!isset($_SESSION["user"]) || !isset($_SESSION["type"])) {
         header("Location: /subrider/login.php");
         exit();
     }
     ```
   - Agora:
     ```php
     use Subrider\Permissions\PermissionManager;
     
     // A verificação básica já é feita automaticamente em scripts/perm.php
     // Para verificações adicionais:
     if (!PermissionManager::hasPermission(PERMISSION_ADMIN)) {
         header("Location: /subrider/login.php?error=permission");
         exit();
     }
     ```

3. **Páginas Públicas**
   - Antes: Lista estática em `scripts/perm.php`
   - Agora: Gerenciado via `PermissionManager`
     ```php
     PermissionManager::addPublicPage('minha-pagina-publica.php');
     ```

4. **Logging**
   - Antes: Logging básico ou inexistente
   - Agora: Sistema completo de logging
     ```php
     logMessage('Ação importante realizada', [
         'user_id' => $userId,
         'action' => 'update',
         'details' => $details
     ]);
     
     // Para debug (apenas em ambiente local)
     logDebug('Debugging info', $context);
     ```

## Passos para Migração

1. **Atualização de Arquivos**
   - Adicione `require_once 'scripts/perm.php';` no início de cada arquivo que requer autenticação
   - Remova verificações antigas de sessão/permissão
   - Atualize chamadas de redirecionamento para usar as constantes

2. **Constantes de Permissão**
   - Substitua números mágicos pelos níveis de permissão definidos:
     - `0` → `PERMISSION_GUEST`
     - `1` → `PERMISSION_USER`
     - `2` → `PERMISSION_ADMIN`

3. **Formulários**
   - Adicione o token CSRF em todos os formulários POST:
     ```php
     <input type="hidden" name="csrf_token" value="<?php echo $_SESSION[CSRF_TOKEN_NAME]; ?>">
     ```

4. **Logging**
   - Identifique pontos críticos que necessitam logging
   - Adicione chamadas apropriadas de `logMessage()`
   - Use níveis adequados (info, error, debug)

## Exemplo de Arquivo Migrado

Antes:
```php
<?php
session_start();

if (!isset($_SESSION["user"])) {
    header("Location: /subrider/login.php");
    exit();
}

if ($_SESSION["type"] < 2) {
    header("Location: /subrider/index.php");
    exit();
}

// ... resto do código
```

Depois:
```php
<?php
require_once 'scripts/perm.php';

use Subrider\Permissions\PermissionManager;

if (!PermissionManager::hasPermission(PERMISSION_ADMIN)) {
    header("Location: /subrider/index.php");
    exit();
}

logMessage('Acesso à área administrativa', [
    'page' => basename(__FILE__)
]);

// ... resto do código
```

## Verificação da Migração

1. **Teste de Segurança**
   - Verifique se todas as rotas protegidas requerem autenticação
   - Confirme que os níveis de permissão estão corretos
   - Teste o CSRF em todos os formulários

2. **Logs**
   - Verifique se os logs estão sendo gerados corretamente
   - Confirme que informações sensíveis não estão sendo logadas
   - Verifique a rotação dos arquivos de log

3. **Performance**
   - Monitore o impacto das novas verificações
   - Verifique se o sistema de cache está funcionando
   - Ajuste configurações conforme necessário

## Suporte

Em caso de dúvidas ou problemas durante a migração:
1. Consulte a documentação em `docs/`
2. Verifique os logs em `logs/`
3. Execute os testes unitários
4. Entre em contato com a equipe de desenvolvimento 