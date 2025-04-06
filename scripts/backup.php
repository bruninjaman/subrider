<?php
/**
 * Script de Backup Automático
 * 
 * Este script realiza o backup do banco de dados e arquivos do sistema.
 * Deve ser executado via cron job diariamente.
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/system/backup_manager.php';

// Configurações
$backupDir = __DIR__ . '/../backups';
$maxBackups = 30; // Mantém backups dos últimos 30 dias
$compressBackups = true;

// Cria diretório de backup se não existir
if (!file_exists($backupDir)) {
    mkdir($backupDir, 0755, true);
}

// Inicializa gerenciador de backup
$backupManager = new BackupManager($backupDir, $maxBackups);

try {
    // Realiza backup do banco de dados
    $dbBackupFile = $backupManager->backupDatabase();
    
    // Realiza backup dos arquivos
    $filesBackupFile = $backupManager->backupFiles();
    
    // Comprime os backups se configurado
    if ($compressBackups) {
        $backupManager->compressBackups([$dbBackupFile, $filesBackupFile]);
    }
    
    // Remove backups antigos
    $backupManager->cleanOldBackups();
    
    // Registra sucesso
    error_log("[Backup] Backup realizado com sucesso em " . date('Y-m-d H:i:s'));
    
} catch (Exception $e) {
    // Registra erro
    error_log("[Backup] Erro ao realizar backup: " . $e->getMessage());
    
    // Envia notificação por email se configurado
    if (defined('ADMIN_EMAIL')) {
        mail(
            ADMIN_EMAIL,
            "Erro no Backup - SubRider",
            "Ocorreu um erro ao realizar o backup:\n\n" . $e->getMessage()
        );
    }
} 