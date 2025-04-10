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

// 1. Verificar Permissão (Ex: ADMIN ou outra permissão específica)
// TODO: Definir a permissão correta para adicionar motos
if (!PermissionManager::hasPermission(PERMISSION_ADMIN)) { // Usando ADMIN como placeholder
    $_SESSION['form_errors'] = ['Permissão negada para adicionar motocicletas.'];
    header("Location: {$baseUrl}/addmotos.php?error=permission");
    exit;
}

// 2. Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['form_errors'] = ['Método de requisição inválido.'];
    header("Location: {$baseUrl}/addmotos.php?error=invalid_method");
    exit;
}

// 3. Verificar Token CSRF (Assumindo que init.php configura e formulário envia)
/*
if (!isset($_POST['csrf_token']) || !Security::verifyCsrfToken($_POST['csrf_token'])) {
    $_SESSION['form_errors'] = ['Token de segurança inválido ou ausente.'];
    header("Location: {$baseUrl}/addmotos.php?error=csrf");
    exit;
}
*/

// --- Processamento do Formulário ---

// 1. Obter e Validar Dados do Formulário
$endereco = trim($_POST['endereco'] ?? '');
$ano = trim($_POST['ano'] ?? '');
$modelo = trim($_POST['modelo'] ?? '');
$marca = trim($_POST['marca'] ?? '');
$placa = strtoupper(trim($_POST['placa'] ?? ''));
$km = trim(str_replace(['.', ','], '', $_POST['km'] ?? '0')); // Corrigido para 'km' minúsculo e remove formatação
$proprietarioNome = trim($_POST['proprietario'] ?? ''); // Nome do proprietário vindo do autocomplete
$fotoFile = $_FILES['foto'] ?? null;

$errors = [];

// Validações básicas
if (empty($endereco)) $errors[] = "O campo 'Endereço' é obrigatório.";
if (empty($ano)) $errors[] = "O campo 'Ano' é obrigatório.";
if (empty($modelo)) $errors[] = "O campo 'Modelo' é obrigatório.";
if (empty($marca)) $errors[] = "O campo 'Marca' é obrigatório.";
if (empty($placa)) $errors[] = "O campo 'Placa' é obrigatório.";
if (empty($proprietarioNome)) $errors[] = "O campo 'Proprietário' é obrigatório.";
// km pode ser 0, então verificamos se é numérico
if (!is_numeric($km)) $errors[] = "O campo 'KM' deve ser um número.";

// Validação de formato/intervalo
if (!empty($ano) && (!ctype_digit($ano) || $ano < 1900 || $ano > (date('Y') + 1))) {
    $errors[] = "Ano inválido (deve ser entre 1900 e " . (date('Y') + 1) . ").";
}
if (!empty($placa) && !preg_match('/^[A-Z]{3}-?[0-9][A-Z0-9][0-9]{2}$/', $placa)) {
     // Permite formato antigo (ABC-1234) ou Mercosul (ABC1D23) - Ajustar regex se necessário
    $errors[] = "Formato de placa inválido.";
}
if ($km < 0) {
     $errors[] = "KM não pode ser negativo.";
}

// Validação do upload da foto (se um arquivo foi enviado)
$fotoPathInDb = null;
$uploadedFilePath = null;
if ($fotoFile && $fotoFile['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($fotoFile['error'] !== UPLOAD_ERR_OK) {
        $uploadErrors = [
             UPLOAD_ERR_INI_SIZE => "O arquivo excede o limite de tamanho do servidor (upload_max_filesize).",
             UPLOAD_ERR_FORM_SIZE => "O arquivo excede o limite de tamanho especificado no formulário HTML.",
             UPLOAD_ERR_PARTIAL => "O upload do arquivo foi feito parcialmente.",
             UPLOAD_ERR_NO_TMP_DIR => "Diretório temporário não encontrado.",
             UPLOAD_ERR_CANT_WRITE => "Falha ao escrever o arquivo no disco.",
             UPLOAD_ERR_EXTENSION => "Uma extensão PHP interrompeu o upload do arquivo."
         ];
        $errors[] = "Erro no upload da foto: " . ($uploadErrors[$fotoFile['error']] ?? "Erro desconhecido.");
    } else {
        $maxFileSize = 5 * 1024 * 1024; // 5MB
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $uploadDir = ROOT_DIR . '/upload/moto/'; // Padronizar diretório?

        if ($fotoFile['size'] > $maxFileSize) {
            $errors[] = "A foto excede o tamanho máximo permitido de 5MB.";
        } else {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $fotoFile['tmp_name']);
            finfo_close($finfo);
            if (!in_array($mimeType, $allowedMimeTypes)) {
                $errors[] = "Tipo de arquivo inválido. Apenas JPG, PNG e GIF são permitidos.";
            } else {
                 // Gerar nome único e seguro
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
                // Move apenas se o diretório existe e não houve erros anteriores
                if (is_dir($uploadDir) && empty($errors)) {
                    if (move_uploaded_file($fotoFile['tmp_name'], $targetPath)) {
                        $fotoPathInDb = 'upload/moto/' . $uniqueFilename;
                        $uploadedFilePath = $targetPath;
                    } else {
                        $errors[] = "Erro ao mover o arquivo enviado.";
                        error_log("Falha em move_uploaded_file para: {$targetPath}");
                    }
                }
            }
        }
    }
} // Fim validação foto

// Se houver erros até aqui (antes do DB), redirecionar
if (!empty($errors)) {
    // Se um arquivo foi carregado mas houve erro de validação depois, remove o arquivo
    if ($uploadedFilePath && file_exists($uploadedFilePath)) {
        unlink($uploadedFilePath);
    }
    $_SESSION['form_errors'] = $errors;
    // TODO: Passar os valores antigos de volta para repopular o formulário
    header("Location: {$baseUrl}/addmotos.php?error=validation");
    exit;
}

// 2. Sanitizar Dados (usando classe Security)
$enderecoSanitized = Security::sanitizeString($endereco);
$anoSanitized = intval($ano); // Já validado como digit e range
$modeloSanitized = Security::sanitizeString($modelo);
$marcaSanitized = Security::sanitizeString($marca);
$placaSanitized = Security::sanitizeString($placa); // Formato já validado
$kmSanitized = intval($km); // Já validado como numeric e >= 0
$proprietarioNomeSanitized = Security::sanitizeString($proprietarioNome);

// --- Operações com Banco de Dados ---
try {
    $db = Database::getInstance();
    $conn = $db->getConnection();

    // 3. Validar Placa e Proprietário no DB
    // Verificar se a placa já existe
    $stmtCheckPlaca = $conn->prepare("SELECT motoId FROM motocicletas WHERE placa = :placa");
    $stmtCheckPlaca->bindParam(':placa', $placaSanitized, PDO::PARAM_STR);
    $stmtCheckPlaca->execute();
    if ($stmtCheckPlaca->fetch()) {
        $errors[] = "Esta placa já está cadastrada.";
    }

    // Verificar se o proprietário existe (assumindo uma tabela 'proprietarios' com coluna 'nome')
    // TODO: Confirmar nome da tabela e coluna de proprietários
    $stmtCheckProp = $conn->prepare("SELECT proprietarioId FROM proprietarios WHERE nome = :nome");
    $stmtCheckProp->bindParam(':nome', $proprietarioNomeSanitized, PDO::PARAM_STR);
    $stmtCheckProp->execute();
    $proprietarioResult = $stmtCheckProp->fetch(PDO::FETCH_ASSOC);
    if (!$proprietarioResult) {
        $errors[] = "Proprietário '" . htmlspecialchars($proprietarioNomeSanitized) . "' não encontrado. Verifique o nome ou cadastre-o primeiro.";
    } else {
        // Guardar o ID do proprietário para usar na inserção (se a tabela motocicletas usa ID)
        // TODO: Verificar se a tabela 'motocicletas' usa 'proprietarioId' ou 'proprietario' (nome)
        $proprietarioIdParaInserir = $proprietarioResult['proprietarioId'];
        // Se a tabela usa o nome, $proprietarioNomeSanitized já está correto.
    }

    // Se houve erros de DB (placa/proprietário), redirecionar
    if (!empty($errors)) {
        // Se um arquivo foi carregado mas houve erro de validação de DB, remove o arquivo
        if ($uploadedFilePath && file_exists($uploadedFilePath)) {
            unlink($uploadedFilePath);
        }
        $_SESSION['form_errors'] = $errors;
        header("Location: {$baseUrl}/addmotos.php?error=db_validation");
        exit;
    }

    // 4. Inserir no Banco de Dados (PDO)
    // TODO: Confirmar se a coluna é 'proprietario' (nome) ou 'proprietarioId'
    $sql = "INSERT INTO motocicletas (foto, endereco, ano, modelo, marca, placa, km, proprietario) VALUES (:foto, :endereco, :ano, :modelo, :marca, :placa, :km, :proprietario)";
    // Se usar ID: $sql = "INSERT INTO motocicletas (foto, endereco, ano, modelo, marca, placa, km, proprietarioId) VALUES (:foto, :endereco, :ano, :modelo, :marca, :placa, :km, :proprietarioId)";
    $stmt = $conn->prepare($sql);

    $stmt->bindParam(':foto', $fotoPathInDb, PDO::PARAM_STR);
    $stmt->bindParam(':endereco', $enderecoSanitized, PDO::PARAM_STR);
    $stmt->bindParam(':ano', $anoSanitized, PDO::PARAM_INT);
    $stmt->bindParam(':modelo', $modeloSanitized, PDO::PARAM_STR);
    $stmt->bindParam(':marca', $marcaSanitized, PDO::PARAM_STR);
    $stmt->bindParam(':placa', $placaSanitized, PDO::PARAM_STR);
    $stmt->bindParam(':km', $kmSanitized, PDO::PARAM_INT);
    $stmt->bindParam(':proprietario', $proprietarioNomeSanitized, PDO::PARAM_STR); // Ou :proprietarioId com $proprietarioIdParaInserir

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Motocicleta adicionada com sucesso!";
        header("Location: {$baseUrl}/tabelaMotos.php?status=success");
        exit;
    } else {
        $errors[] = "Erro ao salvar a motocicleta no banco de dados.";
        error_log("Erro PDO em add-moto.php: " . implode('; ', $stmt->errorInfo()));
    }

} catch (PDOException $e) {
    $errors[] = "Erro de conexão ou SQL: " . $e->getMessage();
    error_log("Exceção PDO em add-moto.php: " . $e->getMessage());
}

// 5. Lidar com Erros Finais (Se chegou aqui, houve erro no DB ou Exception)
if (!empty($errors)) {
    // Se um arquivo foi carregado com sucesso mas a inserção no DB falhou, remove o arquivo
    if ($uploadedFilePath && file_exists($uploadedFilePath)) {
        unlink($uploadedFilePath);
        error_log("Arquivo órfão removido devido à falha no DB: {$uploadedFilePath}");
    }
    $_SESSION['form_errors'] = $errors;
    // TODO: Passar os valores antigos de volta para repopular o formulário
    header("Location: {$baseUrl}/addmotos.php?error=final");
    exit;
}

// Fallback
echo "Ocorreu um erro inesperado.";
exit;

?>
