<?php
// Incluir inicialização (sessão, config, db, security)
require_once __DIR__ . '/../../config/init.php';
require_once __DIR__ . '/../../src/Database/Database.php';
require_once __DIR__ . '/../../src/Security/Security.php'; // Para acesso à classe Security
require_once __DIR__ . '/../../src/Permissions/PermissionManager.php'; // Incluir PermissionManager

use Subrider\Database\Database;
use Subrider\Security\Security; // Usar a classe de segurança
use Subrider\Permissions\PermissionManager; // Usar PermissionManager

// Definição de $baseUrl (idealmente vindo de init.php)
$baseUrl = defined('BASE_URL') ? BASE_URL : '/subrider';

// --- Verificações Iniciais ---

// 1. Verificar Permissão
if (!PermissionManager::hasPermission(PERMISSION_ADMIN)) { // Assumindo que ADMIN pode adicionar peças
    // Guardar erro na sessão para exibir na página de adição
    $_SESSION['form_errors'] = ['Permissão negada para adicionar peças.'];
    header("Location: {$baseUrl}/tabelaPecasAdd.php?error=permission");
    exit;
}

// 2. Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Não deve acontecer se a permissão foi verificada, mas é uma segurança adicional
    $_SESSION['form_errors'] = ['Método de requisição inválido.'];
    header("Location: {$baseUrl}/tabelaPecasAdd.php?error=invalid_method");
    exit;
}

// 3. Verificar Token CSRF (já feito em init.php, mas confirmar se está ativo)
// Se init.php não fizer, a verificação deve ser adicionada aqui:
/*
if (!isset($_POST['csrf_token']) || !Security::verifyCsrfToken($_POST['csrf_token'])) {
    $_SESSION['form_errors'] = ['Token de segurança inválido ou ausente.'];
    header("Location: {$baseUrl}/tabelaPecasAdd.php?error=csrf");
    exit;
}
*/

// --- Processamento do Formulário ---

// 1. Obter e Validar Dados do Formulário
$grupo = trim($_POST['grupo'] ?? '');
$item = trim($_POST['item'] ?? '');
$parte = trim($_POST['parte'] ?? '');
$fotoFile = $_FILES['foto'] ?? null;

$errors = [];
if (empty($grupo)) {
    $errors[] = "O campo 'Grupo' é obrigatório.";
}
if (empty($item)) {
    $errors[] = "O campo 'Item' é obrigatório.";
}
if (empty($parte)) {
    $errors[] = "O campo 'Parte' é obrigatório.";
}
if (empty($fotoFile) || $fotoFile['error'] === UPLOAD_ERR_NO_FILE) {
    $errors[] = "O envio de uma foto é obrigatório.";
} elseif ($fotoFile['error'] !== UPLOAD_ERR_OK) {
    // Tratar outros erros de upload
    $uploadErrors = [
        UPLOAD_ERR_INI_SIZE => "O arquivo excede o limite de tamanho do servidor (upload_max_filesize).";
        UPLOAD_ERR_FORM_SIZE => "O arquivo excede o limite de tamanho especificado no formulário HTML.";
        UPLOAD_ERR_PARTIAL => "O upload do arquivo foi feito parcialmente.";
        UPLOAD_ERR_NO_TMP_DIR => "Diretório temporário não encontrado.";
        UPLOAD_ERR_CANT_WRITE => "Falha ao escrever o arquivo no disco.";
        UPLOAD_ERR_EXTENSION => "Uma extensão PHP interrompeu o upload do arquivo.";
    ];
    $errors[] = "Erro no upload da foto: " . ($uploadErrors[$fotoFile['error']] ?? "Erro desconhecido.");
}

// Se houver erros de validação, redirecionar de volta com mensagens
if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    // TODO: Passar os valores antigos de volta para repopular o formulário
    header("Location: {$baseUrl}/tabelaPecasAdd.php?error=validation");
    exit;
}

// 2. Sanitizar Dados
// Usando htmlspecialchars para prevenir XSS. A classe Security pode ter métodos mais robustos.
$grupoSanitized = Security::sanitizeInput($grupo); // Usando método estático da classe
$itemSanitized = Security::sanitizeInput($item);
$parteSanitized = Security::sanitizeInput($parte);

// 3. Processar Upload da Foto (Implementação Segura)
$fotoPathInDb = null;
$uploadedFilePath = null; // Variável para guardar o caminho completo do arquivo movido

if ($fotoFile && $fotoFile['error'] === UPLOAD_ERR_OK) {
    $uploadDir = ROOT_DIR . '/upload/peca/'; // Assegure que ROOT_DIR está definido em init.php
    $maxFileSize = 2 * 1024 * 1024; // 2MB
    $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];

    // Verificar tamanho
    if ($fotoFile['size'] > $maxFileSize) {
        $errors[] = "A foto excede o tamanho máximo permitido de 2MB.";
    } else {
        // Verificar tipo MIME
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $fotoFile['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedMimeTypes)) {
            $errors[] = "Tipo de arquivo inválido. Apenas JPG, PNG e GIF são permitidos.";
        } else {
            // Gerar nome de arquivo único e seguro
            $originalName = pathinfo($fotoFile['name'], PATHINFO_FILENAME);
            $extension = pathinfo($fotoFile['name'], PATHINFO_EXTENSION);
            // Limpar nome original (remover caracteres inválidos)
            $safeOriginalName = preg_replace("/[^a-zA-Z0-9_\-]/", "_", $originalName);
            $uniqueFilename = uniqid() . '_' . $safeOriginalName . '.' . strtolower($extension);

            $targetPath = $uploadDir . $uniqueFilename;

            // Criar diretório se não existir (com permissões adequadas)
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true)) {
                    $errors[] = "Falha ao criar o diretório de upload.";
                    // Logar erro mais detalhado
                    error_log("Falha ao criar diretório: {$uploadDir}");
                }
            }

            // Mover o arquivo apenas se o diretório foi criado (ou já existia) e não houve outros erros
            if (is_dir($uploadDir) && empty($errors)) {
                if (move_uploaded_file($fotoFile['tmp_name'], $targetPath)) {
                    $fotoPathInDb = 'upload/peca/' . $uniqueFilename; // Armazenar caminho relativo
                    $uploadedFilePath = $targetPath; // Guardar caminho completo para possível exclusão
                } else {
                    $errors[] = "Erro ao mover o arquivo enviado.";
                    error_log("Falha em move_uploaded_file para: {$targetPath}");
                }
            }
        }
    }
} else if (!$errors && $fotoFile && $fotoFile['error'] !== UPLOAD_ERR_NO_FILE) {
    // Se não houve erro de validação anterior, mas houve um erro de upload não tratado
    $errors[] = "Falha no upload da foto (código: {$fotoFile['error']}).";
}

// Se houve erro no upload (mesmo após validação inicial), redirecionar
if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    header("Location: {$baseUrl}/tabelaPecasAdd.php?error=upload");
    exit;
}

// 4. Inserir no Banco de Dados (usando PDO)
try {
    $db = Database::getInstance();
    $conn = $db->getConnection();

    $sql = "INSERT INTO pecas (foto, grupo, item, parte) VALUES (:foto, :grupo, :item, :parte)";
    $stmt = $conn->prepare($sql);

    // Bind dos parâmetros sanitizados
    $stmt->bindParam(':foto', $fotoPathInDb, PDO::PARAM_STR);
    $stmt->bindParam(':grupo', $grupoSanitized, PDO::PARAM_STR);
    $stmt->bindParam(':item', $itemSanitized, PDO::PARAM_STR);
    $stmt->bindParam(':parte', $parteSanitized, PDO::PARAM_STR);

    if ($stmt->execute()) {
        // Sucesso: Redirecionar para a tabela
        $_SESSION['success_message'] = "Peça adicionada com sucesso!";
        header("Location: {$baseUrl}/tabelaPecas.php?status=success");
        exit;
    } else {
        // Erro na execução do SQL
        $errors[] = "Erro ao salvar a peça no banco de dados.";
        // Loggar o erro real do PDO
        error_log("Erro PDO em add-peca.php: " . implode('; ', $stmt->errorInfo()));
    }

} catch (PDOException $e) {
    $errors[] = "Erro de conexão ou SQL: " . $e->getMessage();
    error_log("Exceção PDO em add-peca.php: " . $e->getMessage());
}

// 5. Lidar com Erros Finais
// Se chegou aqui, houve erro no DB ou após o upload bem-sucedido
if (!empty($errors)) { // Verifica se houve algum erro durante o processo
    // Se um arquivo foi carregado com sucesso mas a inserção no DB falhou, remove o arquivo
    if ($uploadedFilePath && file_exists($uploadedFilePath)) {
        unlink($uploadedFilePath);
        error_log("Arquivo órfão removido: {$uploadedFilePath}");
    }

    $_SESSION['form_errors'] = $errors;
    // TODO: Passar os valores antigos de volta para repopular o formulário
    $errorCode = !empty($fotoPathInDb) ? 'database' : 'upload_or_validation'; // Distingue se o erro foi no DB após upload ou antes
    header("Location: {$baseUrl}/tabelaPecasAdd.php?error=" . $errorCode);
    exit;
}

// Se tudo correu bem e já redirecionou no sucesso do DB, este ponto não deve ser alcançado.
// Mas por segurança, adicionamos um fallback.
echo "Ocorreu um erro inesperado.";
exit;

?>
