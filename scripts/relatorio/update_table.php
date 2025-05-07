<?php
require_once("../../connection/connection.php");

// Verifica se as colunas já existem
$result = mysqli_query($conn, "SHOW COLUMNS FROM relatorios LIKE 'assinatura_cliente_img'");
$exists = (mysqli_num_rows($result)) ? TRUE : FALSE;
if (!$exists) {
    // Adiciona coluna assinatura_cliente_img
    mysqli_query($conn, "ALTER TABLE relatorios ADD COLUMN assinatura_cliente_img LONGTEXT NULL AFTER assinatura_img");
    echo "Coluna assinatura_cliente_img adicionada<br>";
}

// Verifica se as colunas já existem
$result = mysqli_query($conn, "SHOW COLUMNS FROM relatorios LIKE 'tecnico_responsavel'");
$exists = (mysqli_num_rows($result)) ? TRUE : FALSE;
if (!$exists) {
    // Adiciona coluna tecnico_responsavel
    mysqli_query($conn, "ALTER TABLE relatorios ADD COLUMN tecnico_responsavel VARCHAR(255) NULL AFTER data_conclusao");
    echo "Coluna tecnico_responsavel adicionada<br>";
}

// Verifica se as colunas já existem
$result = mysqli_query($conn, "SHOW COLUMNS FROM relatorios LIKE 'observacoes_finais'");
$exists = (mysqli_num_rows($result)) ? TRUE : FALSE;
if (!$exists) {
    // Adiciona coluna observacoes_finais
    mysqli_query($conn, "ALTER TABLE relatorios ADD COLUMN observacoes_finais TEXT NULL AFTER tecnico_responsavel");
    echo "Coluna observacoes_finais adicionada<br>";
}

echo "Atualização concluída!";
?>