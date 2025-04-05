<?php
require_once __DIR__ . '/../../config.php';

/**
 * Sistema de backup automático do banco de dados
 * 
 * @return bool True se o backup foi realizado com sucesso, False caso contrário
 */
function performBackup() {
    global $conn;
    
    try {
        // Configurações do backup
        $backupDir = __DIR__ . '/../../backups/';
        if (!file_exists($backupDir)) {
            mkdir($backupDir, 0755, true);
        }
        
        // Nome do arquivo de backup
        $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        $filepath = $backupDir . $filename;
        
        // Obtém todas as tabelas
        $tables = array();
        $result = mysqli_query($conn, "SHOW TABLES");
        while ($row = mysqli_fetch_row($result)) {
            $tables[] = $row[0];
        }
        
        // Inicia o arquivo de backup
        $output = "-- Backup gerado em " . date('Y-m-d H:i:s') . "\n\n";
        
        // Backup de cada tabela
        foreach ($tables as $table) {
            // Estrutura da tabela
            $result = mysqli_query($conn, "SHOW CREATE TABLE $table");
            $row = mysqli_fetch_row($result);
            $output .= "\n\n" . $row[1] . ";\n\n";
            
            // Dados da tabela
            $result = mysqli_query($conn, "SELECT * FROM $table");
            while ($row = mysqli_fetch_assoc($result)) {
                $output .= "INSERT INTO $table VALUES(";
                foreach ($row as $value) {
                    $value = addslashes($value);
                    $output .= "'$value',";
                }
                $output = rtrim($output, ',');
                $output .= ");\n";
            }
        }
        
        // Salva o arquivo de backup
        if (file_put_contents($filepath, $output)) {
            // Registra o backup no log
            logAction('BACKUP', "Backup realizado com sucesso: $filename");
            return true;
        }
        
        return false;
        
    } catch (Exception $e) {
        logAction('ERROR', "Erro ao realizar backup: " . $e->getMessage());
        return false;
    }
}

// Rotina de limpeza de backups antigos
function cleanOldBackups($daysToKeep = 30) {
    $backupDir = __DIR__ . '/../../backups/';
    if (!file_exists($backupDir)) return;
    
    $files = glob($backupDir . '*.sql');
    $now = time();
    
    foreach ($files as $file) {
        if (is_file($file)) {
            if ($now - filemtime($file) >= 60 * 60 * 24 * $daysToKeep) {
                unlink($file);
                logAction('BACKUP', "Backup antigo removido: " . basename($file));
            }
        }
    }
} 