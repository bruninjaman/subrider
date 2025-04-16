<?php

function pagination($conn, $sql_query, $results_per_page = 5)
{
    try {
        // Check for "page" parameter in query string
        if (isset($_GET['page'])) {
            $current_page = intval($_GET['page']);
        } else {
            $current_page = 1; // Default to first page
        }

        // Execute SQL query and get result
        $result = mysqli_query($conn, $sql_query);

        // Check for errors in database query
        if (!$result) {
            throw new Exception("Error executing database query: " . mysqli_error($conn));
        }

        // Calculate number of pages needed
        $num_rows = mysqli_num_rows($result);
        $num_pages = ceil($num_rows / $results_per_page);

        // Adjust current page if it's out of bounds
        if ($current_page < 1) $current_page = 1;
        if ($current_page > $num_pages) $current_page = $num_pages;

        // Limit results based on current page
        $offset = ($current_page - 1) * $results_per_page;
        $limited_sql_query = $sql_query . " LIMIT $offset, $results_per_page";
        $limited_result = mysqli_query($conn, $limited_sql_query);

        // Check for errors in database query (for limited results)
        if (!$limited_result) {
            throw new Exception("Error executing limited database query: " . mysqli_error($conn));
        }

        // Generate pagination interface
        ?>
        <div class="pagination-style">
            <?php if ($current_page > 1) : ?>
                <!-- Move to the first page -->
                <button type="button" class="paginacao-btn" data-page="1">« First</button>
                <button type="button" class="paginacao-btn" data-page="<?php echo $current_page - 1; ?>">‹ Prev</button>
            <?php endif; ?>

            <!-- Show page numbers -->
            <?php for ($i = max(1, $current_page - 2); $i <= min($num_pages, $current_page + 2); $i++) : ?>
                <button type="button" class="paginacao-btn <?php echo ($i == $current_page) ? 'paginacao-ativa' : ''; ?>" 
                    data-page="<?php echo $i; ?>"
                    <?php if ($i == $current_page) echo 'style="font-weight:bold;"'; ?>>
                    <?php echo $i; ?>
                </button>
            <?php endfor; ?>

            <?php if ($current_page < $num_pages) : ?>
                <!-- Move to the last page -->
                <button type="button" class="paginacao-btn" data-page="<?php echo $current_page + 1; ?>">Next ›</button>
                <button type="button" class="paginacao-btn" data-page="<?php echo $num_pages; ?>">Last »</button>
            <?php endif; ?>
        </div>
        <?php

    } catch (Exception $e) {
        echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    }
}





function login($user, $password, $conn)
{
    // Verificar se as colunas necessárias existem e criá-las se não existirem
    $check_columns_query = "SHOW COLUMNS FROM login LIKE 'login_attempts'";
    $check_columns_result = mysqli_query($conn, $check_columns_query);
    
    if (mysqli_num_rows($check_columns_result) == 0) {
        // As colunas não existem, vamos criá-las
        $alter_table_queries = [
            "ALTER TABLE login ADD COLUMN login_attempts INT DEFAULT 0",
            "ALTER TABLE login ADD COLUMN last_attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
            "ALTER TABLE login ADD COLUMN blocked_until TIMESTAMP NULL DEFAULT NULL"
        ];
        
        foreach ($alter_table_queries as $query) {
            if (!mysqli_query($conn, $query)) {
                // Se houver erro ao criar as colunas, continue com o login normal
                error_log("Erro ao adicionar coluna: " . mysqli_error($conn));
            }
        }
    }
    
    // Verificar se o usuário existe
    $check_user_query = "SELECT * FROM login WHERE username = ?";
    $stmt = mysqli_prepare($conn, $check_user_query);
    mysqli_stmt_bind_param($stmt, "s", $user);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    // Se o usuário existe
    if (mysqli_num_rows($result) > 0) {
        $user_data = mysqli_fetch_assoc($result);
        
        // Verificar se as colunas existem no resultado
        $has_block_column = isset($user_data['blocked_until']);
        $has_attempts_column = isset($user_data['login_attempts']);
        
        // Verificar se o usuário está bloqueado (apenas se a coluna existir)
        if ($has_block_column && $user_data['blocked_until'] !== NULL) {
            $current_time = time();
            $blocked_until = strtotime($user_data['blocked_until']);
            
            if ($current_time < $blocked_until) {
                // Calcular tempo restante em minutos
                $remaining_time = ceil(($blocked_until - $current_time) / 60);
                header("Location: " . PROJECT_ROOT_URL . "/login.php?error=blocked&time=" . $remaining_time);
                exit();
            } else {
                // Se o bloqueio expirou, resetar as tentativas
                if ($has_attempts_column) {
                    $reset_query = "UPDATE login SET login_attempts = 0, blocked_until = NULL WHERE username = ?";
                    $reset_stmt = mysqli_prepare($conn, $reset_query);
                    mysqli_stmt_bind_param($reset_stmt, "s", $user);
                    mysqli_stmt_execute($reset_stmt);
                    mysqli_stmt_close($reset_stmt);
                }
            }
        }
        
        // Verificar senha
        if ($user_data['password'] === $password) {
            // Login bem-sucedido, resetar tentativas
            if ($has_attempts_column) {
                $reset_query = "UPDATE login SET login_attempts = 0, last_attempt_time = CURRENT_TIMESTAMP WHERE username = ?";
                $reset_stmt = mysqli_prepare($conn, $reset_query);
                mysqli_stmt_bind_param($reset_stmt, "s", $user);
                mysqli_stmt_execute($reset_stmt);
                mysqli_stmt_close($reset_stmt);
            }
            
            // Iniciar sessão
            session_start();
            $_SESSION["user"] = $user_data["username"];
            $_SESSION["type"] = $user_data["userType"];
            header("Location: " . PROJECT_ROOT_URL . "/index.php");
            exit();
        } else {
            // Senha incorreta
            if ($has_attempts_column) {
                // Incrementar tentativas
                $attempts = $user_data['login_attempts'] + 1;
                
                // Se atingiu 5 tentativas, bloquear por 15 minutos
                if ($attempts >= 5) {
                    $block_time = date('Y-m-d H:i:s', strtotime('+15 minutes'));
                    $update_query = "UPDATE login SET login_attempts = ?, last_attempt_time = CURRENT_TIMESTAMP, blocked_until = ? WHERE username = ?";
                    $update_stmt = mysqli_prepare($conn, $update_query);
                    mysqli_stmt_bind_param($update_stmt, "iss", $attempts, $block_time, $user);
                    mysqli_stmt_execute($update_stmt);
                    mysqli_stmt_close($update_stmt);
                    
                    header("Location: " . PROJECT_ROOT_URL . "/login.php?error=blocked&time=15");
                    exit();
                } else {
                    // Atualizar número de tentativas
                    $update_query = "UPDATE login SET login_attempts = ?, last_attempt_time = CURRENT_TIMESTAMP WHERE username = ?";
                    $update_stmt = mysqli_prepare($conn, $update_query);
                    mysqli_stmt_bind_param($update_stmt, "is", $attempts, $user);
                    mysqli_stmt_execute($update_stmt);
                    mysqli_stmt_close($update_stmt);
                    
                    $remaining_attempts = 5 - $attempts;
                    header("Location: " . PROJECT_ROOT_URL . "/login.php?error=wrong&attempts=" . $remaining_attempts);
                    exit();
                }
            } else {
                // Se não temos as colunas, apenas redirecionar com erro simples
                header("Location: " . PROJECT_ROOT_URL . "/login.php?error=wrong");
                exit();
            }
        }
    } else {
        // Usuário não existe
        header("Location: " . PROJECT_ROOT_URL . "/login.php?error=nouser");
        exit();
    }
}

function uploadFoto($fotoName, $fotoSize, $fotoTmpname, $file_path) {
    // Ensure file size is not too large
    if ($fotoSize > 800000) {
        echo "Sorry, your file is too large.";
        return false;
    }

    // Ensure file name is unique by adding a unique ID
    $tmp_name = "";
    $tmp_name = uniqid($tmp_name, true);
    $tmp_name = $tmp_name . $fotoName;

    // Get file destination
    $file_destination = $file_path . "" . $tmp_name;

    // Check if file already exists at destination
    if (file_exists($file_destination)) {
        echo "Sorry, file already exists.";
        return false;
    }

    // Attempt to move uploaded file to destination
    if (move_uploaded_file($fotoTmpname, $file_destination)) {
        return $file_destination;
    } else {
        echo "Sorry, there was an error uploading your file.";
        return false;
    }
}

function realFormat($valor)
{ //Formato Real
    return 'R$' . number_format($valor, 2, ',', '.');
}
function KMFormat($valor)
{ //Formato Real
    return number_format($valor, 0, ',', '.') . "km";
}
?>