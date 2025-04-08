# SubRider - Sistema de Gerenciamento de Oficina Mecânica

## Funcionalidades

// ... existing code ...

### Sistema de Impressão de OS

O sistema permite a impressão de Ordens de Serviço em formato PDF. Os arquivos são gerados automaticamente e armazenados no diretório `pdf/`. Principais características:

- Geração de PDF com layout profissional
- Inclusão de informações detalhadas do cliente e da moto
- Lista de serviços realizados com valores
- Seção para assinaturas do cliente e responsável
- Limpeza automática de arquivos antigos após 7 dias
- Proteção de acesso aos arquivos via .htaccess

Para imprimir uma OS:
1. Acesse a página de detalhes da OS
2. Clique no botão "Imprimir OS"
3. O PDF será gerado e aberto em uma nova aba

### Configuração do Cron Job

Para manter o sistema organizado, configure um cron job para executar o script de limpeza de PDFs antigos:

```bash
# Executar todos os dias à meia-noite
0 0 * * * php /caminho/para/scripts/limpar_pdfs_antigos.php
```

### Sistema de Permissões

O SubRider utiliza um sistema de permissões baseado em recursos para controlar o acesso às funcionalidades. As permissões são gerenciadas pela classe `PermissionManager`.

### Permissões Disponíveis

- **Notificações**
  - `notifications.delete`: Excluir notificações
  - `notifications.update`: Marcar notificações como lidas
  - `notifications.view`: Visualizar notificações

- **Avaliações**
  - `avaliacoes.view`: Visualizar avaliações
  - `avaliacoes.create`: Criar avaliações

- **Dashboard**
  - `dashboard.view`: Visualizar dashboard

- **Histórico**
  - `historico.proprietarios.view`: Visualizar histórico de proprietários
  - `historico.status.view`: Visualizar histórico de status

### Implementação

O sistema utiliza a classe `PermissionManager` para verificar as permissões do usuário:

```php
$permManager = \Security\PermissionManager::getInstance();
$permManager->loadUserPermissions($userId);

if ($permManager->hasPermission('notifications.delete')) {
    // Usuário tem permissão para excluir notificações
}
```

## Executando os Testes

O sistema possui testes unitários para garantir o funcionamento correto das funcionalidades. Para executar os testes, siga os passos abaixo:

### Pré-requisitos
- PHP 7.4 ou superior
- Composer instalado
- Extensões PHP: PDO, JSON

### Instalação das Dependências
```bash
composer install
```

### Executando os Testes
No Windows:
```bash
vendor\bin\phpunit --testdox
```

No Linux/Mac:
```bash
./run_tests.sh
```

### Gerando Relatório de Cobertura
Para gerar um relatório detalhado de cobertura de código:
```bash
vendor\bin\phpunit --coverage-html coverage
```
O relatório será gerado na pasta `coverage` e pode ser visualizado abrindo o arquivo `coverage/index.html` no navegador.

### Estrutura dos Testes
Os testes estão organizados na pasta `tests/` seguindo a mesma estrutura do código fonte:
- `tests/AvaliacaoManagerTest.php`: Testes do sistema de avaliações
- `tests/Permissions/PermissionManagerTest.php`: Testes do sistema de permissões
  - Verificação de páginas públicas
  - Autenticação de usuários
  - Níveis de permissão
  - Gestão de sessão

### Executando Testes Específicos
Para executar apenas um conjunto específico de testes:
```bash
vendor\bin\phpunit --filter NomeDoTeste
```

Exemplo:
```bash
vendor\bin\phpunit --filter testCriarAvaliacaoComSucesso
```

// ... existing code ...
