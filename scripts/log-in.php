<?php
// Define uma constante para indicar que estamos no processo de login
define('IS_LOGIN_PROCESS', true);

// Configuração da sessão para 30 dias
// (As configurações de sessão foram movidas para init.php, podem ser removidas aqui se não forem específicas para login)
// ini_set('session.gc_maxlifetime', 30 * 24 * 60 * 60); 
// ini_set('session.cookie_lifetime', 30 * 24 * 60 * 60);
// session_set_cookie_params(30 * 24 * 60 * 60);

require_once __DIR__ . '/../config/init.php';
require_once __DIR__ . '/system/audit.php';
require_once __DIR__ . '/system/login_attempts.php';
require_once __DIR__ . '/system/password_policy.php';
require_once __DIR__ . '/system/session_manager.php';

// Usar a classe Database (PDO) definida em init.php
use App\Database\Database;

// Obter a conexão PDO
try {
    $db = Database::getInstance();
    $conn = $db->getConnection(); // Obtém o objeto PDO
} catch (PDOException $e) {
    error_log("Erro crítico de conexão com o banco de dados no login: " . $e->getMessage());
    header("Location: /subrider/login.php?error=dberror");
    exit();
}

//CONNECTION
// require_once("../connection/connection.php"); // Comentado pois agora usamos Database
//FUNCTIONS
// require_once("functions.php"); // Comentado pois o arquivo não existe

// Inicializa classes de segurança com a conexão PDO
$loginAttempts = new LoginAttempts($conn);
$passwordPolicy = new PasswordPolicy($conn);
$sessionManager = SessionManager::getInstance();
$auditSystem = new AuditSystem($conn); // Instanciar AuditSystem aqui

// Limpa tentativas antigas
$loginAttempts->cleanOldAttempts();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validar token CSRF
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $auditSystem->logAction('login_failure', 'Falha de login: Token CSRF inválido.');
        header("Location: /subrider/login.php?error=csrf");
        exit();
    }

    // Sanitização moderna usando htmlspecialchars
    $username = htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8');
    $password = $_POST['password'] ?? '';  // Não sanitizamos a senha, pois será verificada com hash

    // Verifica se o usuário está bloqueado
    $blockStatus = $loginAttempts->isUserBlocked($username);
    if ($blockStatus['blocked']) {
        $minutes = ceil($blockStatus['timeRemaining'] / 60);
        $auditSystem->logAction('login_failure', "Tentativa de login bloqueada para usuário '$username'. Tempo restante: {$minutes} min.");
        header("Location: /subrider/login.php?error=blocked&time=$minutes");
        exit();
    }

    try {
        // Usa prepared statement PDO para prevenir injeção SQL
        $stmt = $conn->prepare("SELECT * FROM login WHERE username = :username");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();

        // Use fetch em vez de get_result
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Primeiro, verificar se a senha corresponde ao hash usando password_verify
            if (password_verify($password, $user['password'])) {
                // Login bem sucedido
                $sessionManager->startSession();
                $sessionManager->regenerateId(true); // Regenera ID da sessão para segurança
                $sessionManager->setUserSession($user['id'], $username, $user['userType']);

                // Registra tentativa bem sucedida
                $loginAttempts->recordAttempt($username, true);
                $auditSystem->logAction('login_success', "Login bem sucedido para usuário '$username'.");

                header("Location: /subrider/index.php");
                exit();
            }
            // Fallback temporário (REMOVER APÓS MIGRAÇÃO DE SENHAS)
            // Se password_verify falhar, verifica como texto puro (apenas para transição)
            // else if ($password === $user['password']) {
            //     // Login bem sucedido (senha antiga em texto puro)
            //     $sessionManager->startSession();
            //     $sessionManager->regenerateId(true);
            //     $sessionManager->setUserSession($user['id'], $username, $user['userType']);
            //     $loginAttempts->recordAttempt($username, true);
            //     $auditSystem->logAction('login_success_plaintext', "Login bem sucedido (senha antiga) para usuário '$username'. Hash necessário.");

            //     // Gerar hash da senha e atualizar no banco (IMPORTANTE)
            //     $newHash = password_hash($password, PASSWORD_DEFAULT);
            //     $updateStmt = $conn->prepare("UPDATE login SET password = :password WHERE id = :id");
            //     $updateStmt->bindParam(':password', $newHash);
            //     $updateStmt->bindParam(':id', $user['id']);
            //     $updateStmt->execute();
            //     $auditSystem->logAction('password_hashed', "Senha antiga do usuário '$username' atualizada para hash.");

            //     header("Location: /subrider/index.php");
            //     exit();
            // }
        }

        // Login falhou (usuário não encontrado ou senha incorreta)
        $loginAttempts->recordAttempt($username, false);
        $auditSystem->logAction('login_failure', "Tentativa de login falhou para usuário '$username'.");
        header("Location: /subrider/login.php?error=1");
        exit();

    } catch (PDOException $e) {
        error_log("Erro no processo de login: " . $e->getMessage());
        $auditSystem->logAction('login_error', "Erro de banco de dados durante o login para '$username': " . $e->getMessage());
        header("Location: /subrider/login.php?error=system");
        exit();
    } catch (Exception $e) { // Captura outras exceções gerais
        error_log("Erro inesperado no login: " . $e->getMessage());
        $auditSystem->logAction('login_error', "Erro inesperado durante o login para '$username': " . $e->getMessage());
        header("Location: /subrider/login.php?error=unknown");
        exit();
    }
} else {
    // Se não for POST, apenas redireciona (ou mostra a página de login, dependendo da estrutura)
    header("Location: /subrider/login.php");
    exit();
}
?>