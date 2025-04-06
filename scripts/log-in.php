<?php
// Define uma constante para indicar que estamos no processo de login
define('IS_LOGIN_PROCESS', true);

// Configuração da sessão para 30 dias
ini_set('session.gc_maxlifetime', 30 * 24 * 60 * 60); // 30 dias em segundos
ini_set('session.cookie_lifetime', 30 * 24 * 60 * 60); // 30 dias em segundos
session_set_cookie_params(30 * 24 * 60 * 60); // 30 dias em segundos

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/system/audit.php';
require_once __DIR__ . '/system/login_attempts.php';
require_once __DIR__ . '/system/password_policy.php';
require_once __DIR__ . '/system/session_manager.php';

//CONNECTION
// require_once("../connection/connection.php");
//FUNCTIONS
// require_once("functions.php");

// Inicializa classes de segurança
$loginAttempts = new LoginAttempts($conn);
$passwordPolicy = new PasswordPolicy($conn);
$sessionManager = SessionManager::getInstance();

// Limpa tentativas antigas
$loginAttempts->cleanOldAttempts();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitização moderna usando htmlspecialchars
    $username = htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8');
    $password = $_POST['password'] ?? '';  // Não sanitizamos a senha, pois será verificada com hash
    
    // Verifica se o usuário está bloqueado
    $blockStatus = $loginAttempts->isUserBlocked($username);
    if ($blockStatus['blocked']) {
        $minutes = ceil($blockStatus['timeRemaining'] / 60);
        header("Location: /subrider/login.php?error=blocked&time=$minutes");
        exit();
    }
    
    try {
        // Usa prepared statement para prevenir injeção SQL
        $stmt = mysqli_prepare($conn, "SELECT * FROM login WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (!$result) {
            throw new Exception("Erro ao executar consulta: " . mysqli_error($conn));
        }
        
        if ($user = mysqli_fetch_assoc($result)) {
            // Verificação temporária para senhas em texto puro
            if ($password === $user['password']) {
                // Login bem sucedido
                $sessionManager->startSession();
                $sessionManager->setUserSession($user['id'], $username, $user['userType']);
                
                // Registra tentativa bem sucedida
                $loginAttempts->recordAttempt($username, true);
                
                header("Location: /subrider/index.php");
                exit();
            }
        }
        
        // Login falhou
        $loginAttempts->recordAttempt($username, false);
        header("Location: /subrider/login.php?error=1");
        exit();
        
    } catch (Exception $e) {
        error_log("Erro no login: " . $e->getMessage());
        header("Location: /subrider/login.php?error=system");
        exit();
    }
} else {
    header("Location: /subrider/login.php");
    exit();
}
?>