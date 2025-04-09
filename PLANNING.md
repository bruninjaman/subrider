# Planejamento e Convenções do Projeto (PHP)

Este documento descreve as convenções de código, estrutura de diretórios e padrões de arquitetura a serem seguidos neste projeto PHP.

## 📜 Linguagem e Estilo de Código

- **Linguagem Principal:** PHP (versão a ser definida, idealmente a mais recente suportada pelo ambiente).
- **Padrão de Estilo:** [PSR-12 (Extended Coding Style)](https://www.php-fig.org/psr/psr-12/). Recomenda-se o uso de ferramentas como PHP-CS-Fixer ou PHP_CodeSniffer para garantir a conformidade.
- **Tipagem:** Utilizar type hints do PHP sempre que possível para melhorar a clareza e a detecção de erros.

## 📛 Convenções de Nomenclatura

- **Classes:** `PascalCase`. Ex: `UserRepository`, `OrderService`.
- **Traits:** `PascalCase`. Ex: `TimestampableTrait`.
- **Interfaces:** `PascalCase`. Ex: `LoggerInterface`.
- **Métodos e Funções:** `camelCase`. Ex: `getUserById`, `calculateTotal`.
- **Variáveis:** `camelCase`. Ex: `$userName`, `$orderItems`.
- **Constantes de Classe:** `UPPER_SNAKE_CASE`. Ex: `const MAX_USERS = 100;`.
- **Constantes Globais (define):** `UPPER_SNAKE_CASE`. Ex: `define('APP_ENV', 'development');`.
- **Arquivos PHP:**
    - Contendo Classes, Interfaces ou Traits: `PascalCase.php`. Ex: `UserService.php`.
    - Scripts ou Templates: `snake_case.php` ou seguir a convenção do framework (se aplicável). Ex: `process_order.php`.
- **Arquivos de Teste:** Sufixo `Test.php`. Ex: `UserServiceTest.php`.

## 📁 Estrutura de Diretórios Sugerida

A estrutura atual parece misturar responsabilidades. Uma estrutura mais organizada facilitaria a manutenção:

```
/
├── public/             # Raiz web, contém index.php, assets (CSS, JS, imagens)
│   ├── index.php
│   └── assets/
├── src/                # Código fonte principal da aplicação
│   ├── Controller/     # Controladores (ou Actions em ADR)
│   ├── Domain/         # Lógica de negócio, Entidades, Serviços, Repositórios
│   ├── Infrastructure/ # Implementações concretas (ex: acesso DB, APIs externas)
│   └── ...             # Outros diretórios conforme necessário (ex: Helpers, Middleware)
├── templates/          # Arquivos de template/view (ex: Twig, Blade, ou PHP puro)
├── config/             # Arquivos de configuração
├── tests/              # Testes (PHPUnit)
│   ├── Unit/
│   ├── Integration/
│   └── ...
├── vendor/             # Dependências do Composer (gerenciado pelo Composer)
├── scripts/            # Scripts utilitários (build, deploy, etc.)
├── .env                # Variáveis de ambiente
├── composer.json       # Definição do projeto e dependências
├── phpunit.xml         # Configuração do PHPUnit
└── README.md
└── TASKS.md
└── PLANNING.md
```

**Observação:** A migração para essa estrutura pode ser feita gradualmente durante a refatoração.

## 🏗️ Arquitetura

- **Padrão:** Considerar a adoção de um padrão como MVC (Model-View-Controller), ADR (Action-Domain-Responder) ou similar para separar responsabilidades. O código atual parece ser mais procedural.
- **Injeção de Dependência:** Utilizar um container de injeção de dependência (se aplicável/desejado) para gerenciar as dependências entre classes.
- **ORM/Banco de Dados:** Padronizar o acesso ao banco de dados. Usar PDO diretamente com boas práticas ou adotar um ORM (como Doctrine ou Eloquent) ou Query Builder. Evitar SQL diretamente nos controladores ou views.

## 📦 Gerenciamento de Dependências

- Usar `composer` para gerenciar todas as dependências do PHP.
- Manter o `composer.json` e `composer.lock` atualizados e versionados.

## 🧪 Testes

- **Framework:** Utilizar `PHPUnit` para testes unitários, de integração e funcionais.
- **Cobertura:** Escrever testes para novas funcionalidades e ao refatorar código existente. Focar em testar a lógica de negócios (`src/Domain`).
- **Localização:** Os testes devem residir no diretório `tests/`, espelhando a estrutura de `src/`.
- **Tipos de Teste:** Incluir testes para casos de sucesso, casos de borda (edge cases) e casos de falha.

## 📚 Documentação

- **DocBlocks:** Usar DocBlocks (PHPDoc) para todas as classes, métodos e funções. Seguir um padrão consistente (ex: [PHPDoc Standard](https://docs.phpdoc.org/latest/references/phpdoc/basic-syntax.html)).
- **README.md:** Manter atualizado com instruções de setup, descrição do projeto e como executar tarefas comuns (testes, etc.).
- **Comentários:** Comentar apenas o código que não for óbvio. Explicar o *porquê* de decisões complexas, não apenas o *o quê*.

## ✅ Gerenciamento de Tarefas

- Utilizar `TASKS.md` para rastrear tarefas a fazer, em andamento e concluídas.
