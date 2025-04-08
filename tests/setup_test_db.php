<?php
require_once __DIR__ . '/../config.php';

/**
 * Script para configurar o banco de dados de teste
 */

// Conecta ao MySQL sem selecionar banco
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Nome do banco de dados de teste
$testDb = DB_NAME . '_test';

// Cria banco de dados de teste se não existir
$sql = "CREATE DATABASE IF NOT EXISTS $testDb";
if (!$conn->query($sql)) {
    die("Erro ao criar banco de dados: " . $conn->error);
}

// Seleciona o banco de dados de teste
$conn->select_db($testDb);

// Cria tabelas necessárias para os testes
$tables = [
    // Tabela de login
    "CREATE TABLE IF NOT EXISTS login (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        userType ENUM('admin', 'user') DEFAULT 'user',
        password_changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX (username)
    )",
    
    // Tabela de tentativas de login
    "CREATE TABLE IF NOT EXISTS login_attempts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL,
        ip_address VARCHAR(45) NOT NULL,
        attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        success BOOLEAN DEFAULT FALSE,
        INDEX (username),
        INDEX (ip_address),
        INDEX (attempt_time)
    )",
    
    // Tabela de histórico de senhas
    "CREATE TABLE IF NOT EXISTS password_history (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id VARCHAR(50) NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX (user_id)
    )"
];

// Executa as queries de criação das tabelas
foreach ($tables as $sql) {
    if (!$conn->query($sql)) {
        die("Erro ao criar tabela: " . $conn->error);
    }
}

echo "Banco de dados de teste configurado com sucesso!\n";
$conn->close(); 