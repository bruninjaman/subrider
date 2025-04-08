# Tarefas do Projeto SubRider

### Modernização do Sistema de Segurança
- [x] Remover código legado de autenticação:
  - [x] Remover includes e requires do perm.php em todos os arquivos
  - [x] Remover variáveis globais relacionadas a permissões antigas
  - [x] Remover funções de verificação de permissão obsoletas
  - [x] Limpar sessões que usam variáveis de permissão antigas

- [x] Atualizar API de Notificações:
  - [x] /api/notificacoes/excluir.php:
    - [x] Remover include do perm.php
    - [x] Implementar novo PermissionManager
    - [x] Adicionar verificação de permissão 'notifications.delete'
    - [x] Implementar validação CSRF
    - [x] Adicionar headers de segurança
  - [x] /api/notificacoes/marcar_todas_lidas.php:
    - [x] Remover include do perm.php
    - [x] Implementar novo PermissionManager
    - [x] Adicionar verificação de permissão 'notifications.update'
    - [x] Implementar validação CSRF
    - [x] Adicionar headers de segurança
  - [x] /api/notificacoes/marcar_lida.php:
    - [x] Remover include do perm.php
    - [x] Implementar novo PermissionManager
    - [x] Adicionar verificação de permissão 'notifications.update'
    - [x] Implementar validação CSRF
    - [x] Adicionar headers de segurança

- [x] Atualizar Páginas:
  - [x] /pages/avaliacoes.php:
    - [x] Remover include do perm.php
    - [x] Implementar novo PermissionManager
    - [x] Adicionar verificação de permissão 'avaliacoes.view'
  - [x] /pages/avaliar.php:
    - [x] Remover include do perm.php
    - [x] Implementar novo PermissionManager
    - [x] Adicionar verificação de permissão 'avaliacoes.create'
  - [x] /pages/notificacoes.php:
    - [x] Remover include do perm.php
    - [x] Implementar novo PermissionManager
    - [x] Adicionar verificação de permissão 'notifications.view'
  - [x] /pages/dashboard.php:
    - [x] Remover include do perm.php
    - [x] Implementar novo PermissionManager
    - [x] Adicionar verificação de permissão 'dashboard.view'
  - [x] /pages/historico_proprietarios.php:
    - [x] Remover include do perm.php
    - [x] Implementar novo PermissionManager
    - [x] Adicionar verificação de permissão 'historico.proprietarios.view'
  - [x] /pages/historico_status.php:
    - [x] Remover include do perm.php
    - [x] Implementar novo PermissionManager
    - [x] Adicionar verificação de permissão 'historico.status.view'

- [x] Documentação:
  - [x] Criar arquivo permissions.md documentando todas as permissões do sistema
  - [x] Atualizar README.md com as novas mudanças no sistema de segurança
  - [x] Documentar processo de migração para novos desenvolvedores

- [x] Testes:
  - [x] Criar testes unitários para o novo sistema de permissões
  - [x] Testar cada endpoint da API com diferentes níveis de permissão
  - [x] Validar headers de segurança em todas as respostas
  - [x] Testar proteção CSRF em todas as rotas POST/PUT/DELETE

- [x] Monitoramento:
  - [x] Implementar logging de tentativas de acesso não autorizado
  - [x] Configurar alertas para múltiplas falhas de autenticação
  - [x] Implementar auditoria de ações sensíveis

## Tarefas Concluídas
- [x] Remoção do arquivo perm.php do diretório /scripts
  - Data: 08/04/2024
- [x] Atualização da API de Notificações com novo sistema de permissões
  - Data: 08/04/2024
- [x] Atualização das páginas com novo sistema de permissões
  - Data: 08/04/2024
- [x] Documentação do novo sistema de permissões
  - Data: 08/04/2024
- [x] Implementação dos testes unitários
  - Data: 08/04/2024
- [x] Implementação do sistema de monitoramento
  - Data: 08/04/2024
- [x] Remoção do código legado de autenticação
  - Data: 08/04/2024
---
*Última atualização: 08/04/2024*
