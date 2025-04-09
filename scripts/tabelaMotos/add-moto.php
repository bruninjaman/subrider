<?php
session_start();
require_once(__DIR__ . "/../../config.php");

function setMessage($message, $type = 'danger') {
    $_SESSION['msg'] = $message;
    $_SESSION['msg_type'] = $type;
}

try {
    // Validar se é um POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método inválido');
    }

    // Validar campos obrigatórios
    $required_fields = ['endereco', 'ano', 'modelo', 'marca', 'placa', 'KM', 'proprietario'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
            throw new Exception("O campo {$field} é obrigatório");
        }
    }

    // Validar e processar a foto
    $foto = null;
    if (isset($_FILES["foto"]) && $_FILES["foto"]["error"] == 0) {
        $allowed = ["jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png"];
        $filename = $_FILES["foto"]["name"];
        $filetype = $_FILES["foto"]["type"];
        $filesize = $_FILES["foto"]["size"];

        // Verify file extension
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if (!array_key_exists($ext, $allowed)) {
            throw new Exception("Formato de arquivo inválido. Por favor, envie uma imagem nos formatos: " . implode(", ", array_keys($allowed)));
        }

        // Verify file size - 5MB maximum
        $maxsize = 5 * 1024 * 1024;
        if ($filesize > $maxsize) {
            throw new Exception("Tamanho do arquivo excede o limite de 5MB");
        }

        // Verify MYME type of the file
        if (!in_array($filetype, $allowed)) {
            throw new Exception("Tipo de arquivo inválido. Por favor, envie uma imagem válida.");
        }

        // Generate unique filename
        $new_filename = uniqid() . '.' . $ext;
        $upload_path = __DIR__ . "/../../upload/moto/";
        
        // Create directory if it doesn't exist
        if (!file_exists($upload_path)) {
            mkdir($upload_path, 0777, true);
        }

        if (move_uploaded_file($_FILES["foto"]["tmp_name"], $upload_path . $new_filename)) {
            $foto = "upload/moto/" . $new_filename;
        } else {
            throw new Exception("Erro ao fazer upload da imagem");
        }
    }

    // Sanitizar e validar inputs
    $endereco = filter_var($_POST['endereco'], FILTER_SANITIZE_STRING);
    $ano = filter_var($_POST['ano'], FILTER_SANITIZE_NUMBER_INT);
    $modelo = filter_var($_POST['modelo'], FILTER_SANITIZE_STRING);
    $marca = filter_var($_POST['marca'], FILTER_SANITIZE_STRING);
    $placa = strtoupper(filter_var($_POST['placa'], FILTER_SANITIZE_STRING));
    $km = filter_var(str_replace(['.', ','], '', $_POST['KM']), FILTER_SANITIZE_NUMBER_INT);
    $proprietario = filter_var($_POST['proprietario'], FILTER_SANITIZE_STRING);

    // Validações adicionais
    if ($ano < 1900 || $ano > (date('Y') + 1)) {
        throw new Exception("Ano inválido");
    }

    if (!preg_match('/^[A-Z]{3}-[0-9][A-Z0-9][0-9]{2}$/', $placa)) {
        throw new Exception("Formato de placa inválido");
    }

    // Verificar se a placa já existe
    $stmt = mysqli_prepare($conn, "SELECT id FROM motocicletas WHERE placa = ? AND id != ?");
    mysqli_stmt_bind_param($stmt, "si", $placa, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    if (mysqli_stmt_num_rows($stmt) > 0) {
        throw new Exception("Esta placa já está cadastrada");
    }
    mysqli_stmt_close($stmt);

    // Preparar a query
    $query = "INSERT INTO motocicletas (foto, endereco, ano, modelo, marca, placa, KM, proprietario) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);

    if (!$stmt) {
        throw new Exception("Erro ao preparar a query: " . mysqli_error($conn));
    }

    // Bind parameters
    mysqli_stmt_bind_param($stmt, "ssssssss", $foto, $endereco, $ano, $modelo, $marca, $placa, $km, $proprietario);

    // Executar a query
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Erro ao salvar os dados: " . mysqli_stmt_error($stmt));
    }

    // Sucesso
    setMessage("Moto cadastrada com sucesso!", "success");
    header('Location: ../../pages/addmotos/');
    exit;

} catch (Exception $e) {
    setMessage($e->getMessage());
    header('Location: ../../addmotos.php');
    exit;
} finally {
    if (isset($stmt)) {
        mysqli_stmt_close($stmt);
    }
    if (isset($conn)) {
        mysqli_close($conn);
    }
}
?>
