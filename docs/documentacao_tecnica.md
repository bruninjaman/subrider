# Documentação Técnica - SubRider

## Arquitetura do Sistema

### Visão Geral
O SubRider é um sistema web desenvolvido em PHP seguindo uma arquitetura em camadas:

```
subrider/
├── assets/          # Recursos estáticos (CSS, JS, imagens)
├── classes/         # Classes de domínio
├── connection/      # Configuração de banco de dados
├── docs/           # Documentação
├── includes/        # Templates e componentes reutilizáveis
├── logs/           # Logs do sistema
├── pages/          # Páginas do sistema
├── repositories/   # Camada de acesso a dados
├── scripts/        # Scripts de segurança e utilitários
└── uploads/        # Arquivos enviados pelos usuários
```

### Tecnologias Utilizadas
- PHP 7.4+
- MySQL 8.0+
- HTML5
- CSS3 (Bootstrap 4)
- JavaScript (jQuery)
- Apache 2.4+

### Requisitos do Sistema
- PHP >= 7.4
- MySQL >= 8.0
- Apache >= 2.4
- Extensões PHP:
  - PDO
  - PDO_MYSQL
  - GD
  - mbstring
  - json
  - session

## Banco de Dados

### Diagrama ER
```
proprietarios
+---------------+--------------+------+-----+
| Field         | Type         | Null | Key |
+---------------+--------------+------+-----+
| id            | int         | NO   | PRI |
| nome          | varchar(100)| NO   |     |
| cpf           | varchar(11) | NO   | UNI |
| telefone      | varchar(15) | YES  |     |
| email         | varchar(100)| YES  |     |
| endereco      | text        | YES  |     |
| cidade        | varchar(50) | YES  |     |
| estado        | char(2)     | YES  |     |
| cep           | varchar(8)  | YES  |     |
| data_cadastro | datetime    | NO   |     |
+---------------+--------------+------+-----+

motocicletas
+----------------+--------------+------+-----+
| Field          | Type         | Null | Key |
+----------------+--------------+------+-----+
| id             | int         | NO   | PRI |
| placa          | varchar(8)  | NO   | UNI |
| marca          | varchar(50) | NO   |     |
| modelo         | varchar(50) | NO   |     |
| ano            | int         | NO   |     |
| proprietario_id| int         | YES  | MUL |
| km             | int         | YES  |     |
| cor            | varchar(30) | YES  |     |
| observacoes    | text        | YES  |     |
| foto           | varchar(255)| YES  |     |
+----------------+--------------+------+-----+

historico_proprietarios
+----------------+--------------+------+-----+
| Field          | Type         | Null | Key |
+----------------+--------------+------+-----+
| id             | int         | NO   | PRI |
| moto_id        | int         | NO   | MUL |
| proprietario_id| int         | NO   | MUL |
| data_inicio    | datetime    | NO   |     |
| data_fim       | datetime    | YES  |     |
| observacao     | text        | YES  |     |
| created_at     | timestamp   | NO   |     |
+----------------+--------------+------+-----+
```

## Segurança

### Autenticação
- Sistema de login com proteção contra força bruta
- Senhas armazenadas com hash seguro (bcrypt)
- Controle de sessão com regeneração periódica de ID
- Proteção contra CSRF em todos os formulários

### Validações
- Validação de CPF
- Validação de placa no formato Mercosul
- Sanitização de inputs
- Prepared statements para prevenção de SQL Injection

### Logs
- Log de ações do usuário
- Log de erros
- Log de sessões
- Log de tentativas de login

## APIs e Endpoints

### Proprietários
```
GET  /proprietarios/listar
POST /proprietarios/criar
GET  /proprietarios/buscar/{id}
PUT  /proprietarios/atualizar/{id}
DEL  /proprietarios/excluir/{id}
```

### Motos
```
GET  /motos/listar
POST /motos/criar
GET  /motos/buscar/{id}
PUT  /motos/atualizar/{id}
DEL  /motos/excluir/{id}
GET  /motos/{id}/historico
POST /motos/{id}/transferir
```

### Ordens de Serviço
```
GET  /ordens/listar
POST /ordens/criar
GET  /ordens/buscar/{id}
PUT  /ordens/atualizar/{id}
POST /ordens/{id}/itens/adicionar
PUT  /ordens/{id}/status
GET  /ordens/{id}/relatorio
```

## Classes Principais

### Repositories
- `ProprietarioRepository`: Gerencia operações CRUD de proprietários
- `MotocicletaRepository`: Gerencia operações CRUD de motos
- `HistoricoProprietarioRepository`: Gerencia histórico de proprietários
- `OrdemServicoRepository`: Gerencia ordens de serviço

### Classes de Domínio
- `CalculadoraOrdem`: Calcula valores de ordens de serviço
- `StatusOrdem`: Gerencia estados de ordens de serviço
- `Relatorio`: Gera relatórios em diferentes formatos

### Utilitários
- `SessionManager`: Gerencia sessões do sistema
- `Database`: Singleton para conexão com banco de dados
- `Security`: Funções de segurança e validação

## Manutenção

### Backup
- Backup diário do banco de dados
- Backup semanal dos arquivos enviados
- Rotação de logs a cada 30 dias

### Monitoramento
- Log de erros em `/logs/error.log`
- Log de segurança em `/logs/security.log`
- Log de sessão em `/logs/session.log`

### Cache
- Cache de consultas frequentes
- Cache de templates
- Cache de imagens redimensionadas

## Desenvolvimento

### Padrões de Código
- PSR-1: Basic Coding Standard
- PSR-12: Extended Coding Style
- Documentação em português Brasil
- Commits em português Brasil

### Fluxo de Desenvolvimento
1. Criar branch feature/fix
2. Desenvolver e testar localmente
3. Criar pull request
4. Code review
5. Merge para main

### Testes
- Testes unitários com PHPUnit
- Testes de integração
- Testes de aceitação 