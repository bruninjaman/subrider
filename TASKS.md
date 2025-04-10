# Lista de Tarefas - Subrider System

Data: <?php echo date('d/m/Y'); ?>

## 🔍 Verificação de Páginas e Links

### Páginas Principais
- [x] Verificar e corrigir links na página inicial (index.php)
- [x] Verificar e corrigir a navegação no header.php
- [x] Revisar consistência entre links na barra de navegação
- [x] Corrigir redirecionamentos após login/logout
- [x] Corrigir problemas para logar no script log-in (Redirecionamentos e CSRF corrigidos; verificar hashes de senha se persistir)

### Páginas de Tabelas
- [x] Revisar e corrigir tabelaPecas.php e suas subpáginas
- [ ] Revisar e corrigir tabelaMotos.php e suas subpáginas (Edição Pendente)
- [x] Revisar e corrigir tabelaServicos.php e suas subpáginas
- [ ] Revisar e corrigir tabelaOrdens.php e suas subpáginas
- [ ] Verificar consistência nos formulários de adicionar/editar

### Área Administrativa
- [ ] Verificar permissões e acesso às páginas administrativas
- [ ] Revisar página de dashboard.php
- [ ] Corrigir página de notificacoes.php
- [ ] Verificar e corrigir relatórios em relatorio.php

## 🛠️ Correção de Funcionalidades

### Módulo de Ordens de Serviço
- [ ] Corrigir fluxo de criar_ordem.php
- [ ] Revisar e corrigir ordem_add_item.php
- [ ] Revisar e corrigir ordem_edit_item.php
- [ ] Verificar cálculos em CalculadoraOrdem.php
- [ ] Corrigir geração de PDF em OrdemServicoPDF.php

### Módulo de Proprietários e Motos
- [ ] Revisar e corrigir addproprietario.php
- [ ] Revisar e corrigir editproprietario.php
- [ ] Revisar e corrigir addmotos.php
- [ ] Revisar e corrigir editmotos.php
- [ ] Verificar histórico em HistoricoMoto.php

### Módulo de Avaliações
- [ ] Corrigir página avaliar.php
- [ ] Revisar exibição em avaliacoes.php
- [ ] Revisar funcionalidades em AvaliacaoManager.php

### Módulo de Relatórios
- [ ] Revisar classes Relatorio.php e RelatorioPersonalizado.php
- [ ] Corrigir geração de relatórios em relatorio.php
- [ ] Verificar páginas em pages/relatorios/

## 📱 Responsividade e UI/UX
- [ ] Testar e corrigir responsividade em todas as páginas
- [ ] Melhorar layout de formulários
- [ ] Verificar consistência visual entre páginas
- [ ] Corrigir problemas de usabilidade em dispositivos móveis

## 🔒 Segurança
- [ ] Revisar e corrigir validação de entrada em todos os formulários
- [ ] Verificar vulnerabilidades de SQL Injection
- [ ] Revisar permissionamento em PermissionManager.php
- [ ] Verificar e corrigir SessionManager.php

## 🧪 Testes
- [ ] Criar e executar testes para as funcionalidades corrigidas
- [ ] Testar fluxos completos de uso
- [ ] Verificar integração entre módulos

## 📚 Documentação
- [ ] Atualizar README.md com informações sobre correções
- [ ] Documentar melhorias realizadas
- [ ] Atualizar documentação de API (se existente)

## 🔄 Refatoração
- [ ] Organizar código seguindo as convenções em PLANNING.md
- [ ] Refatorar código procedural para orientado a objetos
- [ ] Melhorar estrutura de diretórios conforme PLANNING.md
- [ ] Implementar padrão MVC onde aplicável

## ✅ Tarefas Concluídas
- [x] Criação do arquivo TASKS.md

## 📋 Tarefas Descobertas Durante o Trabalho
- [ ] Refatorar manualmente `scripts/tabelaMotos/edit-moto.php` (falha na aplicação automática).
- [ ] Revisar `pages/editmotos/historico.php` e sua integração.
- [ ] Confirmar nomes de tabelas/colunas de proprietários e como são referenciados em `motocicletas`.
- [ ] Refatorar `addmotos.php` e `scripts/tabelaMotos/add-moto.php` para usar `proprietario_id` em vez de nome do proprietário (usar autocomplete para buscar ID).
<!-- Adicionar novas tarefas descobertas durante o desenvolvimento -->