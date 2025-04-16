<?php
// Adiciona config
// Caminho absoluto para config.php
require_once(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config.php'); 

// Conexão com o banco de dados
// Caminho corrigido
require_once(PROJECT_ROOT_PATH . DS . "connection" . DS . "connection.php");

// Verificar se as colunas já existem
$check_columns_query = "SHOW COLUMNS FROM login LIKE 'login_attempts'";
$check_columns_result = mysqli_query($conn, $check_columns_query);

if (mysqli_num_rows($check_columns_result) == 0) {
    echo "<h2>Adicionando colunas para o sistema de bloqueio de login</h2>";
    
    // As colunas não existem, vamos criá-las
    $alter_table_queries = [
        "ALTER TABLE login ADD COLUMN login_attempts INT DEFAULT 0",
        "ALTER TABLE login ADD COLUMN last_attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
        "ALTER TABLE login ADD COLUMN blocked_until TIMESTAMP NULL DEFAULT NULL"
    ];
    
    $all_success = true;
    foreach ($alter_table_queries as $query) {
        echo "Executando: $query<br>";
        if (mysqli_query($conn, $query)) {
            echo "<span style='color:green'>✓ Sucesso!</span><br><br>";
        } else {
            echo "<span style='color:red'>✗ Erro: " . mysqli_error($conn) . "</span><br><br>";
            $all_success = false;
        }
    }
    
    if ($all_success) {
        echo "<h3 style='color:green'>Todas as colunas foram adicionadas com sucesso!</h3>";
        echo "<p>O sistema de bloqueio de login agora está completamente configurado.</p>";
    } else {
        echo "<h3 style='color:red'>Houve erros ao adicionar algumas colunas.</h3>";
        echo "<p>Por favor, verifique os erros acima e tente corrigir manualmente ou contate o administrador do sistema.</p>";
    }
} else {
    echo "<h2>As colunas para o sistema de bloqueio de login já existem</h2>";
    echo "<p>Não é necessário fazer nenhuma alteração adicional.</p>";
}

// Fechar conexão
mysqli_close($conn);

// Link corrigido
echo "<p><a href='" . PROJECT_ROOT_URL . "/login.php'>Voltar para a página de login</a></p>";
?> 