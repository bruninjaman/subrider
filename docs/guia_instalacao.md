# Guia de Instalação - SubRider

## Requisitos

### Servidor
- PHP >= 7.4
- MySQL >= 8.0
- Apache >= 2.4
- Composer

### Extensões PHP Necessárias
- PDO
- PDO_MYSQL
- GD
- mbstring
- json
- session

### Configurações PHP Recomendadas
```ini
memory_limit = 256M
post_max_size = 50M
upload_max_filesize = 20M
max_execution_time = 300
date.timezone = America/Sao_Paulo
```

## Instalação

### 1. Preparação do Ambiente

1. Instale o XAMPP (ou similar) com PHP 7.4+
2. Instale o Composer
3. Habilite as extensões PHP necessárias no php.ini
4. Configure o Apache para permitir .htaccess

### 2. Banco de Dados

1. Crie um banco de dados MySQL:
```sql
CREATE DATABASE subrider CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Crie um usuário para o banco:
```sql
CREATE USER 'subrider'@'localhost' IDENTIFIED BY 'sua_senha_segura';
GRANT ALL PRIVILEGES ON subrider.* TO 'subrider'@'localhost';
FLUSH PRIVILEGES;
```

3. Importe a estrutura do banco:
```bash
mysql -u subrider -p subrider < sql/schema.sql
```

### 3. Configuração do Sistema

1. Clone o repositório:
```bash
cd /xampp/htdocs
git clone https://github.com/seu-usuario/subrider.git
cd subrider
```

2. Instale as dependências:
```bash
composer install
```

3. Copie o arquivo de configuração:
```bash
cp .env.example .env
```

4. Configure o arquivo .env:
```ini
DB_HOST=localhost
DB_NAME=subrider
DB_USER=subrider
DB_PASS=sua_senha_segura

APP_URL=http://localhost/subrider
APP_ENV=production
APP_DEBUG=false

UPLOAD_DIR=/xampp/htdocs/subrider/uploads
LOG_DIR=/xampp/htdocs/subrider/logs
```

5. Crie os diretórios necessários:
```bash
mkdir -p uploads/motos
mkdir -p uploads/ordens
mkdir -p logs
chmod 755 uploads
chmod 755 logs
```

### 4. Configuração do Apache

1. Certifique-se que o mod_rewrite está habilitado:
```bash
a2enmod rewrite
```

2. Configure o VirtualHost (opcional):
```apache
<VirtualHost *:80>
    ServerName subrider.local
    DocumentRoot /xampp/htdocs/subrider
    
    <Directory /xampp/htdocs/subrider>
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/subrider-error.log
    CustomLog ${APACHE_LOG_DIR}/subrider-access.log combined
</VirtualHost>
```

### 5. Permissões de Arquivos

1. Configure as permissões:
```bash
chown -R www-data:www-data /xampp/htdocs/subrider
chmod -R 755 /xampp/htdocs/subrider
chmod -R 777 /xampp/htdocs/subrider/uploads
chmod -R 777 /xampp/htdocs/subrider/logs
```

### 6. Primeiro Acesso

1. Crie o usuário administrador:
```sql
INSERT INTO login (username, password, userType) 
VALUES ('admin', 'admin123', 'admin');
```

2. Acesse o sistema:
- URL: http://localhost/subrider
- Usuário: admin
- Senha: admin123

3. **IMPORTANTE**: Altere a senha do administrador após o primeiro acesso!

## Verificação da Instalação

### 1. Teste de Conexão
1. Acesse a página inicial
2. Tente fazer login
3. Verifique se os logs estão sendo gerados

### 2. Teste de Upload
1. Tente adicionar uma moto com foto
2. Verifique se o arquivo foi salvo em uploads/motos

### 3. Teste de Permissões
1. Crie uma ordem de serviço
2. Adicione medições
3. Gere um relatório

## Solução de Problemas

### Erro de Conexão com Banco
1. Verifique as credenciais no .env
2. Confirme se o MySQL está rodando
3. Teste a conexão manualmente

### Erro de Upload
1. Verifique as permissões dos diretórios
2. Confirme os limites no php.ini
3. Verifique os logs do Apache

### Erro de Rewrite
1. Confirme se mod_rewrite está ativo
2. Verifique se .htaccess está sendo lido
3. Teste as regras de rewrite

## Manutenção

### Backup
1. Configure backup diário do banco:
```bash
0 0 * * * /usr/bin/mysqldump -u subrider -p subrider > /backup/subrider_$(date +\%Y\%m\%d).sql
```

2. Configure backup dos arquivos:
```bash
0 0 * * 0 tar -czf /backup/subrider_files_$(date +\%Y\%m\%d).tar.gz /xampp/htdocs/subrider/uploads
```

### Logs
1. Configure rotação de logs:
```bash
/xampp/htdocs/subrider/logs/*.log {
    daily
    rotate 30
    compress
    delaycompress
    notifempty
    create 0640 www-data www-data
}
```

### Cache
1. Limpe periodicamente:
```bash
0 0 * * * /usr/bin/find /xampp/htdocs/subrider/uploads/cache -type f -mtime +7 -delete
```

## Atualização

### 1. Backup
```bash
mysqldump -u subrider -p subrider > backup_antes_atualizacao.sql
tar -czf uploads_backup.tar.gz uploads/
```

### 2. Atualização
```bash
git pull origin main
composer update
php scripts/update_database.php
```

### 3. Verificação
1. Teste todas as funcionalidades principais
2. Verifique os logs de erro
3. Confirme as permissões dos arquivos 