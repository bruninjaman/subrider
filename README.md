# SubRider - Sistema de Gerenciamento de Oficina

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

// ... existing code ...
