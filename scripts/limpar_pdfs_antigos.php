<?php
/**
 * Script para limpar PDFs antigos do diretório pdf/
 * Este script deve ser executado periodicamente via cron
 */

// Define o tempo máximo de vida dos arquivos (7 dias)
$maxAge = 7 * 24 * 60 * 60; // 7 dias em segundos

// Diretório dos PDFs
$pdfDir = __DIR__ . '/../pdf/';

// Verifica se o diretório existe
if (!is_dir($pdfDir)) {
    die("Diretório de PDFs não encontrado\n");
}

// Lista todos os arquivos PDF
$pdfs = glob($pdfDir . '*.pdf');

// Contador de arquivos removidos
$removidos = 0;

// Verifica cada arquivo
foreach ($pdfs as $pdf) {
    // Pega a última modificação do arquivo
    $lastModified = filemtime($pdf);
    
    // Se o arquivo for mais antigo que o tempo máximo
    if (time() - $lastModified > $maxAge) {
        // Tenta remover o arquivo
        if (unlink($pdf)) {
            $removidos++;
            echo "Arquivo removido: " . basename($pdf) . "\n";
        } else {
            echo "Erro ao remover arquivo: " . basename($pdf) . "\n";
        }
    }
}

echo "\nTotal de arquivos removidos: " . $removidos . "\n"; 