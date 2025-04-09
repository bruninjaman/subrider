## Tarefas - 2024-07-26

### Funcionalidade e Testes
- [ ] **Testar todas as páginas/funcionalidades:** Garantir que todas as rotas e interações da UI funcionem como esperado.
- [ ] **Implementar funcionalidade completa:** Finalizar a lógica para todas as páginas e recursos que estão incompletos.
- [ ] **Escrever testes unitários:** Criar testes unitários (Pytest) para todas as funções, classes e rotas críticas. Cobrir casos de uso esperados, casos de borda e falhas.

### Refatoração e Limpeza
- [ ] **Corrigir código quebrado:** Identificar e corrigir bugs ou lógica incorreta em todo o codebase. (Parcialmente bloqueado: Erros de inicialização corrigidos, mas não foi possível mover/criar `tests/Unit/AvaliacaoManagerTest.php` para executar os testes devido a erro de permissão da ferramenta de escrita de arquivo - 2024-07-26) (Progresso: Erro fatal de `mysqli`/PDO em `log-in.php` e classes relacionadas corrigido - 2024-07-26)
- [ ] **Remover código não utilizado:** Analisar o projeto e remover funções, variáveis, imports e arquivos que não estão sendo usados.
- [ ] **Revisar e refatorar:** Melhorar a clareza, eficiência e manutenibilidade do código existente, seguindo as convenções definidas em `PLANNING.md`.

### Descobertas Durante o Trabalho
*(Adicione aqui novas subtarefas ou TODOs encontrados)*
- Investigar erro "Access denied" da ferramenta `mcp_filesystem_write_file` ao tentar criar arquivos em subdiretórios de `tests/`.
