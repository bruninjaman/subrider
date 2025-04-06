<?php
/**
 * Gerenciador de Backups
 * 
 * Classe responsável por gerenciar backups do banco de dados e arquivos do sistema.
 */
class BackupManager {
    private string $backupDir;
    private int $maxBackups;
    
    /**
     * Construtor
     * 
     * @param string $backupDir Diretório onde os backups serão armazenados
     * @param int $maxBackups Número máximo de backups a manter
     */
    public function __construct(string $backupDir, int $maxBackups) {
        $this->backupDir = rtrim($backupDir, '/');
        $this->maxBackups = $maxBackups;
    }
    
    /**
     * Realiza backup do banco de dados
     * 
     * @return string Caminho do arquivo de backup gerado
     * @throws Exception Se ocorrer erro no backup
     */
    public function backupDatabase(): string {
        $filename = $this->backupDir . '/db_' . date('Y-m-d_His') . '.sql';
        
        // Obtém credenciais do banco
        $host = DB_HOST;
        $user = DB_USER;
        $pass = DB_PASS;
        $name = DB_NAME;
        
        // Comando mysqldump
        $command = sprintf(
            'mysqldump -h %s -u %s -p%s %s > %s',
            escapeshellarg($host),
            escapeshellarg($user),
            escapeshellarg($pass),
            escapeshellarg($name),
            escapeshellarg($filename)
        );
        
        exec($command, $output, $return);
        
        if ($return !== 0) {
            throw new Exception('Erro ao realizar backup do banco de dados');
        }
        
        return $filename;
    }
    
    /**
     * Realiza backup dos arquivos do sistema
     * 
     * @return string Caminho do arquivo de backup gerado
     * @throws Exception Se ocorrer erro no backup
     */
    public function backupFiles(): string {
        $filename = $this->backupDir . '/files_' . date('Y-m-d_His') . '.tar';
        
        // Diretórios a incluir no backup
        $dirs = [
            'uploads',
            'docs',
            'config'
        ];
        
        // Cria arquivo tar
        $command = sprintf(
            'tar -czf %s %s',
            escapeshellarg($filename),
            implode(' ', array_map('escapeshellarg', $dirs))
        );
        
        exec($command, $output, $return);
        
        if ($return !== 0) {
            throw new Exception('Erro ao realizar backup dos arquivos');
        }
        
        return $filename;
    }
    
    /**
     * Comprime arquivos de backup usando gzip
     * 
     * @param array $files Lista de arquivos a comprimir
     * @throws Exception Se ocorrer erro na compressão
     */
    public function compressBackups(array $files): void {
        foreach ($files as $file) {
            if (!file_exists($file)) {
                continue;
            }
            
            $command = sprintf('gzip %s', escapeshellarg($file));
            exec($command, $output, $return);
            
            if ($return !== 0) {
                throw new Exception("Erro ao comprimir arquivo: $file");
            }
        }
    }
    
    /**
     * Remove backups antigos mantendo apenas os mais recentes
     * conforme configuração maxBackups
     */
    public function cleanOldBackups(): void {
        // Lista todos os arquivos de backup
        $files = glob($this->backupDir . '/*.*');
        
        // Ordena por data (mais antigo primeiro)
        usort($files, function($a, $b) {
            return filemtime($a) - filemtime($b);
        });
        
        // Remove arquivos excedentes
        $count = count($files);
        if ($count > $this->maxBackups) {
            $toRemove = array_slice($files, 0, $count - $this->maxBackups);
            foreach ($toRemove as $file) {
                unlink($file);
            }
        }
    }
} 