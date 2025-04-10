<?php
// Incluir inicialização segura (sessão, config, db, security, permissions)
require_once __DIR__ . '/../../config/init.php';
require_once __DIR__ . '/../../src/Database/Database.php';
require_once __DIR__ . '/../../src/Security/Security.php';
require_once __DIR__ . '/../../src/Permissions/PermissionManager.php';
// require_once __DIR__ . '/../../classes/HistoricoMoto.php'; // Remover dependência direta?

use Subrider\Database\Database;
use Subrider\Security\Security;
use Subrider\Permissions\PermissionManager;

// Definição de $baseUrl (idealmente vindo de init.php)
$baseUrl = defined('BASE_URL') ? BASE_URL : '/subrider';

// --- Verificações Iniciais ---

// 1. Verificar Permissão (Ex: ADMIN)
// TODO: Definir a permissão correta
if (!PermissionManager::hasPermission(PERMISSION_ADMIN)) {
    $_SESSION['form_errors'] = ['Permissão negada para editar motocicletas.'];
    header("Location: {$baseUrl}/tabelaMotos.php?error=permission"); // Redireciona para a tabela
    exit;
}

// 2. Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['form_errors'] = ['Método de requisição inválido.'];
    header("Location: {$baseUrl}/tabelaMotos.php?error=invalid_method");
    exit;
}

// 3. Verificar ID da Moto na URL
if (!isset($_GET['motoID']) || !ctype_digit((string)$_GET['motoID'])) {
    $_SESSION['form_errors'] = ['ID da motocicleta inválido ou não fornecido na URL.'];
    header("Location: {$baseUrl}/tabelaMotos.php?error=invalid_id");
    exit;
}
$motoId = intval($_GET['motoID']);

// 4. Verificar Token CSRF (Assumindo que init.php configura e formulário envia)
if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    $_SESSION['form_errors'] = ['Token de segurança inválido ou ausente.'];
    header("Location: {$baseUrl}/editmotos.php?motoID={$motoId}&error=csrf");
    exit;
}

// --- Processamento do Formulário ---

// 1. Obter e Validar Dados do Formulário
$endereco = trim($_POST['endereco'] ?? '');
$ano = trim($_POST['ano'] ?? '');
$modelo = trim($_POST['modelo'] ?? '');
$marca = trim($_POST['marca'] ?? '');
$placa = strtoupper(trim($_POST['placa'] ?? ''));
$km = trim(str_replace(['.', ','], '', $_POST['km'] ?? '0')); // Usar 'km' minúsculo
$proprietarioNome = trim($_POST['proprietario'] ?? '');
$fotoFile = $_FILES['foto'] ?? null;

$errors = [];
$validation_errors = []; // Para erros específicos de campo

// Validações básicas e de formato
if (empty($endereco)) $validation_errors['endereco'] = "Campo obrigatório";
if (empty($ano)) $validation_errors['ano'] = "Campo obrigatório";
if (empty($modelo)) $validation_errors['modelo'] = "Campo obrigatório";
if (empty($marca)) $validation_errors['marca'] = "Campo obrigatório";
if (empty($placa)) $validation_errors['placa'] = "Campo obrigatório";
if (empty($proprietarioNome)) $validation_errors['proprietario'] = "Campo obrigatório";
if (!is_numeric($km)) $validation_errors['km'] = "KM deve ser um número.";

if (!empty($ano) && (!ctype_digit($ano) || $ano < 1900 || $ano > (date('Y') + 1))) {
    $validation_errors['ano'] = "Ano inválido (deve ser entre 1900 e " . (date('Y') + 1) . ").";
}
if (!empty($placa) && !preg_match('/^[A-Z]{3}-?[0-9][A-Z0-9][0-9]{2}$/', $placa)) {
    $validation_errors['placa'] = "Formato de placa inválido.";
}
if ($km < 0) {
     $validation_errors['km'] = "KM não pode ser negativo.";
}

// Validação do upload da foto (apenas se um novo arquivo foi enviado)
$fotoPathInDb = null; // Será definido se houver novo upload
$uploadedFilePath = null;
$oldFotoPath = null; // Será buscado do DB

if ($fotoFile && $fotoFile['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($fotoFile['error'] !== UPLOAD_ERR_OK) {
        $uploadErrors = [ /* ... erros de upload ... */ ];
        $errors[] = "Erro no upload da nova foto: " . ($uploadErrors[$fotoFile['error']] ?? "Erro desconhecido.");
    } else {
        $maxFileSize = 5 * 1024 * 1024; // 5MB
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $uploadDir = ROOT_DIR . '/upload/moto/';

        if ($fotoFile['size'] > $maxFileSize) {
            $errors[] = "A nova foto excede o tamanho máximo permitido de 5MB.";
        } else {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $fotoFile['tmp_name']);
            finfo_close($finfo);
            if (!in_array($mimeType, $allowedMimeTypes)) {
                $errors[] = "Tipo de arquivo inválido para a nova foto. Apenas JPG, PNG e GIF são permitidos.";
            }
            // Se passou nas validações de upload, o arquivo será movido depois, antes do update no DB
        }
    }
}

// Combinar erros gerais e de validação
if (!empty($validation_errors)) {
    $errors = array_merge($errors, array_values($validation_errors));
}

// Se houver erros até aqui (antes do DB), redirecionar
if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['validation_errors'] = $validation_errors; // Guarda erros específicos para destacar campos
    // TODO: Passar os valores antigos de volta
    header("Location: {$baseUrl}/editmotos.php?motoID={$motoId}&error=validation");
    exit;
}

// 2. Sanitizar Dados (após validação básica)
$enderecoSanitized = Security::sanitizeString($endereco);
$anoSanitized = intval($ano);
$modeloSanitized = Security::sanitizeString($modelo);
$marcaSanitized = Security::sanitizeString($marca);
$placaSanitized = Security::sanitizeString($placa);
$kmSanitized = intval($km);
$proprietarioNomeSanitized = Security::sanitizeString($proprietarioNome);

// --- Operações com Banco de Dados ---
try {
    $db = Database::getInstance();
    $conn = $db->getConnection();

    // 3. Buscar Moto Atual (para obter foto antiga e verificar existência)
    $stmtFetch = $conn->prepare("SELECT foto, proprietario FROM motocicletas WHERE motoId = :motoId"); // Pega proprietario também para histórico?
    $stmtFetch->bindParam(':motoId', $motoId, PDO::PARAM_INT);
    $stmtFetch->execute();
    $motoAtual = $stmtFetch->fetch(PDO::FETCH_ASSOC);

    if (!$motoAtual) {
        $_SESSION['form_errors'] = ["Motocicleta com ID {$motoId} não encontrada para atualização."];
        header("Location: {$baseUrl}/tabelaMotos.php?error=not_found");
        exit;
    }
    $oldFotoPath = $motoAtual['foto']; // Caminho relativo no DB
    // $oldProprietario = $motoAtual['proprietario']; // Para histórico

    // 4. Validar Placa (se mudou) e Proprietário no DB
    // Verificar se a placa JÁ EXISTE em OUTRA moto
    if ($placaSanitized !== $motoAtual['placa']) { // Só verifica se a placa mudou
        $stmtCheckPlaca = $conn->prepare("SELECT motoId FROM motocicletas WHERE placa = :placa AND motoId != :motoId");
        $stmtCheckPlaca->bindParam(':placa', $placaSanitized, PDO::PARAM_STR);
        $stmtCheckPlaca->bindParam(':motoId', $motoId, PDO::PARAM_INT);
        $stmtCheckPlaca->execute();
        if ($stmtCheckPlaca->fetch()) {
            $validation_errors['placa'] = "Esta placa já está cadastrada em outra motocicleta.";
        }
    }

    // Verificar se o proprietário existe
    // TODO: Confirmar nome da tabela e coluna de proprietários
    $stmtCheckProp = $conn->prepare("SELECT proprietarioId FROM proprietarios WHERE nome = :nome");
    $stmtCheckProp->bindParam(':nome', $proprietarioNomeSanitized, PDO::PARAM_STR);
    $stmtCheckProp->execute();
    $proprietarioResult = $stmtCheckProp->fetch(PDO::FETCH_ASSOC);
    if (!$proprietarioResult) {
        $validation_errors['proprietario'] = "Proprietário '" . htmlspecialchars($proprietarioNomeSanitized) . "' não encontrado. Verifique o nome ou cadastre-o primeiro.";
    } else {
        // TODO: Verificar se a tabela 'motocicletas' usa 'proprietarioId' ou 'proprietario' (nome)
        $proprietarioIdParaUpdate = $proprietarioResult['proprietarioId'];
    }

    // Se houve erros de validação de DB, redirecionar
    if (!empty($validation_errors)) {
        $_SESSION['form_errors'] = array_merge($errors, array_values($validation_errors));
        $_SESSION['validation_errors'] = $validation_errors;
        header("Location: {$baseUrl}/editmotos.php?motoID={$motoId}&error=db_validation");
        exit;
    }

    // 5. Processar Upload da Nova Foto (se houver) e Excluir a Antiga
    $updateFoto = false;
    if ($fotoFile && $fotoFile['error'] === UPLOAD_ERR_OK) {
        $uploadDir = ROOT_DIR . '/upload/moto/';
        $originalName = pathinfo($fotoFile['name'], PATHINFO_FILENAME);
        $extension = pathinfo($fotoFile['name'], PATHINFO_EXTENSION);
        $safeOriginalName = preg_replace("/[^a-zA-Z0-9_\-]/", "_", $originalName);
        $uniqueFilename = uniqid('moto_') . '_' . $safeOriginalName . '.' . strtolower($extension);
        $targetPath = $uploadDir . $uniqueFilename;

        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                 $errors[] = "Falha ao criar o diretório de upload.";
                 error_log("Falha ao criar diretório: {$uploadDir}");
            }
        }

        if (is_dir($uploadDir) && empty($errors)) {
            if (move_uploaded_file($fotoFile['tmp_name'], $targetPath)) {
                $fotoPathInDb = 'upload/moto/' . $uniqueFilename; // Define o novo caminho
                $uploadedFilePath = $targetPath;
                $updateFoto = true; // Marca que a foto será atualizada no DB

                // Excluir a foto antiga do servidor
                $oldFileFullPath = ROOT_DIR . '/' . ltrim($oldFotoPath ?? '', '/');
                if (!empty($oldFotoPath) && file_exists($oldFileFullPath) && is_file($oldFileFullPath)) {
                    if (!unlink($oldFileFullPath)) {
                        error_log("Falha ao excluir foto antiga: {$oldFileFullPath} durante edição da moto ID {$motoId}");
                    }
                }
            } else {
                $errors[] = "Erro ao mover o novo arquivo enviado.";
                error_log("Falha em move_uploaded_file para: {$targetPath}");
            }
        }
    } // Fim processamento nova foto

    // Se houve erro durante o processamento da foto (após validações iniciais)
    if (!empty($errors)) {
        $_SESSION['form_errors'] = $errors;
        header("Location: {$baseUrl}/editmotos.php?motoID={$motoId}&error=upload");
        exit;
    }

    // 6. Atualizar no Banco de Dados (PDO)
    $fieldsToUpdate = [
        'endereco = :endereco',
        'ano = :ano',
        'modelo = :modelo',
        'marca = :marca',
        'placa = :placa',
        'km = :km',
        'proprietario_id = :proprietarioId'
    ];
    $params = [
        ':endereco' => $enderecoSanitized,
        ':ano' => $anoSanitized,
        ':modelo' => $modeloSanitized,
        ':marca' => $marcaSanitized,
        ':placa' => $placaSanitized,
        ':km' => $kmSanitized,
        ':proprietarioId' => $proprietarioIdParaUpdate,
        ':motoId' => $motoId
    ];

    // Adiciona a foto ao update apenas se uma nova foi carregada
    if ($updateFoto) {
        $fieldsToUpdate[] = 'foto = :foto';
        $params[':foto'] = $fotoPathInDb;
    }

    $sql = "UPDATE motocicletas SET " . implode(", ", $fieldsToUpdate) . " WHERE motoId = :motoId";
    $stmt = $conn->prepare($sql);

    if ($stmt->execute($params)) {
         // TODO: Chamar lógica de histórico aqui, se necessário, passando dados antigos e novos
         /*
         if (class_exists('HistoricoMoto') && isset($_SESSION['userId'])) {
             $historico = new HistoricoMoto($conn, $motoId, $_SESSION['userId']);
             // Comparar $motoAtual com os dados sanitizados ($params) e registrar alterações
             // ... lógica de comparação e chamada a $historico->registrarAlteracao(...)
         }
         */

        $_SESSION['success_message'] = "Motocicleta atualizada com sucesso!";
        header("Location: {$baseUrl}/editmotos.php?motoID={$motoId}&status=success"); // Volta para edição com msg sucesso
        exit;
    } else {
        $errors[] = "Erro ao atualizar a motocicleta no banco de dados.";
        error_log("Erro PDO em edit-moto.php (update): " . implode('; ', $stmt->errorInfo()));
        // Se o upload foi feito, mas o DB falhou, remover a nova foto?
         if ($uploadedFilePath && file_exists($uploadedFilePath)) {
             unlink($uploadedFilePath);
             error_log("Nova foto órfã removida devido à falha no DB update: {$uploadedFilePath}");
         }
    }

} catch (PDOException $e) {
    $errors[] = "Erro de conexão ou SQL: " . $e->getMessage();
    error_log("Exceção PDO em edit-moto.php: " . $e->getMessage());
    // Se o upload foi feito antes da exceção, remover a nova foto?
    if ($uploadedFilePath && file_exists($uploadedFilePath)) {
        unlink($uploadedFilePath);
         error_log("Nova foto órfã removida devido à exceção PDO: {$uploadedFilePath}");
    }
}

// 7. Lidar com Erros Finais (Se chegou aqui, houve erro no DB ou Exception)
if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['validation_errors'] = $validation_errors; // Passa erros de validação específicos de volta
    header("Location: {$baseUrl}/editmotos.php?motoID={$motoId}&error=final");
    exit;
}

// Fallback
echo "Ocorreu um erro inesperado durante a atualização.";
exit;

?>
