# Planejamento do Projeto

## Arquitetura do Sistema de Autenticação e Permissões

### Estrutura Atual
- Scripts PHP individuais
- Verificação de permissões via `scripts/perm.php`
- Sistema de sessões PHP para autenticação
- Arquivos de configuração dispersos

### Melhorias Planejadas

#### Correção Imediata (14/03/2024)
1. Criar arquivo de configuração central para inicialização de sessão
   - Localização: `config/init.php`
   - Responsabilidade: Inicializar sessão e configurações básicas
   - Será incluído em todos os scripts que necessitam de autenticação
   - Implementar verificações de segurança:
     - Validação de HTTPS
     - Configuração de cookies seguros
     - Proteção contra CSRF
     - Headers de segurança

2. Refatoração do Sistema de Permissões
   - Mover verificação de permissões para após inicialização da sessão
   - Implementar verificações mais robustas
   - Melhorar tratamento de erros
   - Adicionar sistema de logging
   - Implementar cache de permissões

3. Padrões de Código
   - Usar constantes para níveis de permissão
   - Implementar logging para debugging
   - Documentar todas as funções e configurações
   - PSR-4 para autoloading
   - Composer para gerenciamento de dependências

### Convenções
- Todos os arquivos devem iniciar com inclusão de `config/init.php`
- Usar comentários explicativos para lógica complexa
- Manter consistência na nomenclatura de variáveis e funções
- Seguir PSR-12 para estilo de código
- Documentação PHPDoc em todas as classes e métodos

### Estrutura de Diretórios
```
subrider/
├── config/
│   ├── init.php
│   └── constants.php
├── src/
│   ├── Auth/
│   ├── Permissions/
│   └── Services/
├── tests/
├── public/
└── logs/
```

### Segurança
- Implementar CSRF tokens em todos os formulários
- Validar todas as entradas de usuário
- Usar prepared statements para queries
- Implementar rate limiting
- Logging de ações sensíveis

### Próximos Passos
1. Migração gradual para nova estrutura
2. Implementação de testes automatizados
3. Documentação completa do sistema
4. Monitoramento e logging
