<?php
// Conecta ao MySQL sem selecionar um banco de dados
$pdo = new PDO(
    "mysql:host=localhost",
    "root",
    ""
);

// Cria o banco de dados de teste se não existir
$pdo->exec("CREATE DATABASE IF NOT EXISTS subrider_test");
echo "Banco de dados de teste criado com sucesso!\n"; 