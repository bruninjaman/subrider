<?php

namespace Subrider\Utils;

/**
 * Classe de logging para o sistema
 */
class Logger
{
    /**
     * Loga uma mensagem de debug com contexto opcional
     * 
     * @param string $message Mensagem a ser logada
     * @param array $context Contexto adicional (opcional)
     * @return void
     */
    public static function debug(string $message, array $context = []): void {
        $timestamp = date('Y-m-d H:i:s');
        $logFile = dirname(dirname(__DIR__)) . '/logs/' . date('Y-m-d') . '-debug.log';
        $contextStr = !empty($context) ? ' | Contexto: ' . json_encode($context, JSON_UNESCAPED_UNICODE) : '';
        $logEntry = "[$timestamp] DEBUG: $message$contextStr" . PHP_EOL;
        
        if (!is_dir(dirname($logFile))) {
            mkdir(dirname($logFile), 0777, true);
        }
        
        file_put_contents($logFile, $logEntry, FILE_APPEND);
    }

    /**
     * Loga uma mensagem informativa com contexto opcional
     * 
     * @param string $message Mensagem a ser logada
     * @param array $context Contexto adicional (opcional)
     * @return void
     */
    public static function info(string $message, array $context = []): void {
        $timestamp = date('Y-m-d H:i:s');
        $logFile = dirname(dirname(__DIR__)) . '/logs/' . date('Y-m-d') . '-info.log';
        $contextStr = !empty($context) ? ' | Contexto: ' . json_encode($context, JSON_UNESCAPED_UNICODE) : '';
        $logEntry = "[$timestamp] INFO: $message$contextStr" . PHP_EOL;
        
        if (!is_dir(dirname($logFile))) {
            mkdir(dirname($logFile), 0777, true);
        }
        
        file_put_contents($logFile, $logEntry, FILE_APPEND);
    }

    /**
     * Loga um erro com contexto opcional
     * 
     * @param string $message Mensagem de erro
     * @param array $context Contexto adicional (opcional)
     * @return void
     */
    public static function error(string $message, array $context = []): void {
        $timestamp = date('Y-m-d H:i:s');
        $logFile = dirname(dirname(__DIR__)) . '/logs/' . date('Y-m-d') . '-error.log';
        $contextStr = !empty($context) ? ' | Contexto: ' . json_encode($context, JSON_UNESCAPED_UNICODE) : '';
        $logEntry = "[$timestamp] ERROR: $message$contextStr" . PHP_EOL;
        
        if (!is_dir(dirname($logFile))) {
            mkdir(dirname($logFile), 0777, true);
        }
        
        file_put_contents($logFile, $logEntry, FILE_APPEND);
    }
} 