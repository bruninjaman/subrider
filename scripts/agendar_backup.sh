#!/bin/bash

# Obtém o diretório do script
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )"

# Caminho para o script de backup
BACKUP_SCRIPT="$SCRIPT_DIR/backup.php"

# Verifica se o script existe
if [ ! -f "$BACKUP_SCRIPT" ]; then
    echo "Erro: Script de backup não encontrado em $BACKUP_SCRIPT"
    exit 1
fi

# Agenda o backup para rodar todos os dias às 3 da manhã
(crontab -l 2>/dev/null; echo "0 3 * * * php $BACKUP_SCRIPT") | crontab -

echo "Backup agendado com sucesso!"
echo "Será executado todos os dias às 3:00" 