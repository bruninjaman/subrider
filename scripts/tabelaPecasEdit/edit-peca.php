<?php
// Incluir inicialização segura (sessão, config, db, security, permissions)
require_once __DIR__ . '/../../config/init.php';
require_once __DIR__ . '/../../src/Database/Database.php';
require_once __DIR__ . '/../../src/Security/Security.php';
require_once __DIR__ . '/../../src/Permissions/PermissionManager.php';

use Subrider\Database\Database;
use Subrider\Security\Security;
use Subrider\Permissions\PermissionManager;

// Definição de $baseUrl (idealmente vindo de init.php)
$baseUrl = defined('BASE_URL') ? BASE_URL : '/subrider';

// --- Verificações Iniciais ---

// 1. Verificar Permissão (Ex: ADMIN pode editar peças)
if (!PermissionManager::hasPermission(PERMISSION_ADMIN)) {
    $_SESSION['form_errors'] = ['Permissão negada para editar peças.'];
    // Redireciona de volta para a tabela, pois o formulário pode não existir sem ID
    header("Location: {$baseUrl}/tabelaPecas.php?error=permission");
    exit;
}

// 2. Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['form_errors'] = ['Método de requisição inválido.'];
    header("Location: {$baseUrl}/tabelaPecas.php?error=invalid_method");
    exit;
}

// 3. Verificar ID da Peça na URL
if (!isset($_GET['pecaID']) || !ctype_digit((string)$_GET['pecaID'])) {
    $_SESSION['form_errors'] = ['ID da peça inválido ou não fornecido na URL.'];
    header("Location: {$baseUrl}/tabelaPecas.php?error=invalid_id");
    exit;
}
$pecaId = intval($_GET['pecaID']); // Sanitiza para inteiro

// 4. Verificar Token CSRF (Assumindo que init.php configura e formulário envia)
/*
if (!isset($_POST['csrf_token']) || !Security::verifyCsrfToken($_POST['csrf_token'])) {
    $_SESSION['form_errors'] = ['Token de segurança inválido ou ausente.'];
    header("Location: {$baseUrl}/tabelaPecasEdit.php?pecaID={$pecaId}&error=csrf");
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

// Validação do upload da foto (apenas se um novo arquivo foi enviado)
if ($fotoFile && $fotoFile['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($fotoFile['error'] !== UPLOAD_ERR_OK) {
        // Tratar outros erros de upload
        $uploadErrors = [
            UPLOAD_ERR_INI_SIZE => "O arquivo excede o limite de tamanho do servidor (upload_max_filesize).",
            UPLOAD_ERR_FORM_SIZE => "O arquivo excede o limite de tamanho especificado no formulário HTML.",
            UPLOAD_ERR_PARTIAL => "O upload do arquivo foi feito parcialmente.",
            UPLOAD_ERR_NO_TMP_DIR => "Diretório temporário não encontrado.",
            UPLOAD_ERR_CANT_WRITE => "Falha ao escrever o arquivo no disco.",
            UPLOAD_ERR_EXTENSION => "Uma extensão PHP interrompeu o upload do arquivo."
        ];
        $errors[] = "Erro no upload da nova foto: " . ($uploadErrors[$fotoFile['error']] ?? "Erro desconhecido.");
    } else {
        // Validação de tamanho e tipo para a nova foto
        $maxFileSize = 2 * 1024 * 1024; // 2MB
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];

        if ($fotoFile['size'] > $maxFileSize) {
            $errors[] = "A nova foto excede o tamanho máximo permitido de 2MB.";
        } else {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $fotoFile['tmp_name']);
            finfo_close($finfo);
            if (!in_array($mimeType, $allowedMimeTypes)) {
                $errors[] = "Tipo de arquivo inválido para a nova foto. Apenas JPG, PNG e GIF são permitidos.";
            }
        }
    }
}

// Se houver erros de validação, redirecionar de volta com mensagens
if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    // TODO: Passar os valores antigos de volta para repopular o formulário
    header("Location: {$baseUrl}/tabelaPecasEdit.php?pecaID={$pecaId}&error=validation");
    exit;
}

// 2. Sanitizar Dados (após validação básica)
$grupoSanitized = Security::sanitizeString($grupo);
$itemSanitized = Security::sanitizeString($item);
$parteSanitized = Security::sanitizeString($parte);

// 3. Buscar Peça Atual (para obter caminho da foto antiga, se necessário)
$oldFotoPath = null;
try {
    $db = Database::getInstance();
    $conn = $db->getConnection();

    $stmtFetch = $conn->prepare("SELECT foto FROM pecas WHERE pecaId = :pecaId");
    $stmtFetch->bindParam(':pecaId', $pecaId, PDO::PARAM_INT);
    $stmtFetch->execute();
    $pecaAtual = $stmtFetch->fetch(PDO::FETCH_ASSOC);

    if (!$pecaAtual) {
        // Segurança: Se a peça não existe mais, não tenta atualizar.
        $_SESSION['form_errors'] = ["Peça com ID {$pecaId} não encontrada para atualização."];
        header("Location: {$baseUrl}/tabelaPecas.php?error=not_found");
        exit;
    }
    $oldFotoPath = $pecaAtual['foto']; // Caminho relativo no DB

} catch (PDOException $e) {
    $errors[] = "Erro ao buscar dados da peça atual: " . $e->getMessage();
    error_log("Exceção PDO em edit-peca.php (fetch old): " . $e->getMessage());
    // Redirecionar mesmo em caso de erro de fetch, pois não podemos prosseguir
    $_SESSION['form_errors'] = $errors;
    header("Location: {$baseUrl}/tabelaPecasEdit.php?pecaID={$pecaId}&error=database_fetch");
    exit;
}

// 4. Processar Upload da Nova Foto (se houver) e Excluir a Antiga
$fotoPathInDb = $oldFotoPath; // Mantém a foto antiga por padrão
$uploadedFilePath = null; // Caminho completo do novo arquivo carregado
$oldFileFullPath = ROOT_DIR . '/' . ltrim($oldFotoPath ?? '', '/'); // Caminho completo do arquivo antigo

// Verifica se um novo arquivo foi enviado E se não houve erros de validação anteriores
if ($fotoFile && $fotoFile['error'] === UPLOAD_ERR_OK && empty($errors)) {
    $uploadDir = ROOT_DIR . '/upload/peca/'; // Diretório correto

    // Gerar nome de arquivo único e seguro para a nova foto
    $originalName = pathinfo($fotoFile['name'], PATHINFO_FILENAME);
    $extension = pathinfo($fotoFile['name'], PATHINFO_EXTENSION);
    $safeOriginalName = preg_replace("/[^a-zA-Z0-9_\-]/", "_", $originalName);
    $uniqueFilename = uniqid() . '_' . $safeOriginalName . '.' . strtolower($extension);
    $targetPath = $uploadDir . $uniqueFilename;

    // Criar diretório se não existir
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            $errors[] = "Falha ao criar o diretório de upload.";
            error_log("Falha ao criar diretório: {$uploadDir}");
        }
    }

    // Mover o novo arquivo apenas se o diretório existe e não houve erros
    if (is_dir($uploadDir) && empty($errors)) {
        if (move_uploaded_file($fotoFile['tmp_name'], $targetPath)) {
            $fotoPathInDb = 'upload/peca/' . $uniqueFilename; // Novo caminho relativo para o DB
            $uploadedFilePath = $targetPath; // Guarda o caminho completo do novo arquivo

            // Tentar excluir a foto antiga APENAS se o upload da nova foi bem-sucedido
            if (!empty($oldFotoPath) && file_exists($oldFileFullPath) && is_file($oldFileFullPath)) {
                if (!unlink($oldFileFullPath)) {
                    // Logar falha na exclusão da foto antiga, mas não impedir o fluxo principal
                    error_log("Falha ao excluir foto antiga: {$oldFileFullPath} durante edição da peça ID {$pecaId}");
                }
            }
        } else {
            $errors[] = "Erro ao mover o novo arquivo enviado.";
            error_log("Falha em move_uploaded_file para: {$targetPath}");
        }
    }
} elseif ($fotoFile && $fotoFile['error'] !== UPLOAD_ERR_NO_FILE && empty($errors)) {
    // Caso raro: Erro de upload não pego na validação inicial
     $errors[] = "Falha no upload da nova foto (código: {$fotoFile['error']}).";
}


// Se houve erro no upload (mesmo após validação inicial), redirecionar
if (!empty($errors)) {
    // Se um novo arquivo foi carregado mas houve erro depois, remove-o
    if ($uploadedFilePath && file_exists($uploadedFilePath)) {
        unlink($uploadedFilePath);
    }
    $_SESSION['form_errors'] = $errors;
    header("Location: {$baseUrl}/tabelaPecasEdit.php?pecaID={$pecaId}&error=upload");
    exit;
}

// 5. Atualizar no Banco de Dados (usando PDO)
try {
    // Reutilizar a conexão se possível, ou obter nova instância
    if (!$conn) {
        $db = Database::getInstance();
        $conn = $db->getConnection();
    }

    $sql = "UPDATE pecas SET foto = :foto, grupo = :grupo, item = :item, parte = :parte WHERE pecaId = :pecaId";
    $stmt = $conn->prepare($sql);

    // Bind dos parâmetros (usa $fotoPathInDb, que contém o caminho novo ou o antigo)
    $stmt->bindParam(':foto', $fotoPathInDb, PDO::PARAM_STR);
    $stmt->bindParam(':grupo', $grupoSanitized, PDO::PARAM_STR);
    $stmt->bindParam(':item', $itemSanitized, PDO::PARAM_STR);
    $stmt->bindParam(':parte', $parteSanitized, PDO::PARAM_STR);
    $stmt->bindParam(':pecaId', $pecaId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        // Sucesso: Redirecionar para a tabela
        $_SESSION['success_message'] = "Peça atualizada com sucesso!";
        header("Location: {$baseUrl}/tabelaPecas.php?status=updated&id={$pecaId}");
        exit;
    } else {
        // Erro na execução do SQL
        $errors[] = "Erro ao atualizar a peça no banco de dados.";
        // Loggar o erro real do PDO
        error_log("Erro PDO em edit-peca.php (update): " . implode('; ', $stmt->errorInfo()));
    }

} catch (PDOException $e) {
    $errors[] = "Erro de conexão ou SQL na atualização: " . $e->getMessage();
    error_log("Exceção PDO em edit-peca.php (update): " . $e->getMessage());
}

// 6. Lidar com Erros Finais (Se chegou aqui, houve erro no DB)
if (!empty($errors)) {
    // Se um novo arquivo foi carregado com sucesso mas a atualização no DB falhou, remove o novo arquivo
    if ($uploadedFilePath && file_exists($uploadedFilePath)) {
        unlink($uploadedFilePath);
        error_log("Novo arquivo órfão removido devido à falha no DB: {$uploadedFilePath}");
        // Nota: Não tentamos restaurar o arquivo antigo aqui, pois seria complexo e propenso a erros.
    }

    $_SESSION['form_errors'] = $errors;
    header("Location: {$baseUrl}/tabelaPecasEdit.php?pecaID={$pecaId}&error=database_update");
    exit;
}

// Fallback caso algo muito inesperado aconteça
echo "Ocorreu um erro inesperado durante a atualização.";
exit;

?>
