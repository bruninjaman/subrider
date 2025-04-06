<?php
session_start();

require_once(__DIR__ . "/../config.php");

// Funções de validação
function validateMotoData($data) {
    $errors = [];
    
    // Validação do ano
    if (!preg_match('/^(19|20)\d{2}$/', $data['ano'])) {
        $errors['ano'] = "Ano inválido. Use o formato AAAA entre 1900 e " . date('Y');
    }
    
    // Validação da placa (formato Mercosul)
    if (!validateMercosulPlate($data['placa'])) {
        $errors['placa'] = "Placa inválida. Use o formato Mercosul (ABC1D23 ou ABC-1D23)";
    }
    
    // Validação do KM
    if (!is_numeric($data['KM']) || $data['KM'] < 0) {
        $errors['KM'] = "Quilometragem inválida";
    }
    
    // Validação de campos obrigatórios
    $requiredFields = ['endereco', 'modelo', 'marca', 'proprietario'];
    foreach ($requiredFields as $field) {
        if (empty(trim($data[$field]))) {
            $errors[$field] = "Campo obrigatório";
        }
    }
    
    return $errors;
}

function validateMercosulPlate($plate) {
    $plate = str_replace('-', '', $plate);
    return preg_match('/^[A-Z]{3}[0-9][A-Z][0-9]{2}$/', $plate);
}

function sanitizeMotoData($data, $conn) {
    $clean = [];
    foreach ($data as $key => $value) {
        $clean[$key] = mysqli_real_escape_string($conn, trim($value));
    }
    return $clean;
}

function logAudit($conn, $motoId, $oldData, $newData) {
    $userId = $_SESSION['userId'];
    $changes = [];
    
    foreach ($newData as $key => $value) {
        if ($oldData[$key] != $value) {
            $changes[] = "$key: {$oldData[$key]} -> $value";
        }
    }
    
    if (!empty($changes)) {
        $changes = mysqli_real_escape_string($conn, implode(", ", $changes));
        $query = "INSERT INTO auditoria (userId, acao, tabela, registro, alteracoes, data) 
                 VALUES ('$userId', 'UPDATE', 'motocicletas', '$motoId', '$changes', NOW())";
        mysqli_query($conn, $query);
    }
}

if (isset($_POST['endereco'])) {
    $errors = validateMotoData($_POST);
    
    if (empty($errors)) {
        // Buscar dados antigos para auditoria
        $motoId = $_POST['motoID'];
        $oldDataQuery = "SELECT * FROM motocicletas WHERE motoId = '$motoId'";
        $oldDataResult = mysqli_query($conn, $oldDataQuery);
        $oldData = mysqli_fetch_assoc($oldDataResult);
        
        // Sanitizar dados
        $data = sanitizeMotoData($_POST, $conn);
        
        // Preparar query base
        $updateFields = [
            "endereco = '{$data['endereco']}'",
            "ano = '{$data['ano']}'",
            "modelo = '{$data['modelo']}'",
            "marca = '{$data['marca']}'",
            "placa = '{$data['placa']}'",
            "km = '{$data['KM']}'",
            "proprietario = '{$data['proprietario']}'"
        ];
        
        // Verificar se há nova foto
        if (!empty($_FILES["foto"]["name"])) {
            $fotoName = $_FILES["foto"]["name"];
            $fotoSize = $_FILES["foto"]["size"];
            $fotoTmpname = $_FILES["foto"]["tmp_name"];
            $file_path = "../../upload/moto/";
            
            $foto = uploadFoto($fotoName, $fotoSize, $fotoTmpname, $file_path);
            $foto = trim($foto, "../../");
            
            $updateFields[] = "foto = '$foto'";
        }
        
        // Construir e executar query
        $mysqli_query = "UPDATE motocicletas SET " . implode(", ", $updateFields) . 
                       " WHERE motoId = '$motoId'";
        
        if (mysqli_query($conn, $mysqli_query)) {
            // Registrar auditoria
            $newData = array_merge($data, ['foto' => $foto ?? $oldData['foto']]);
            logAudit($conn, $motoId, $oldData, $newData);
            
            $_SESSION['success_message'] = "Moto atualizada com sucesso!";
        } else {
            $_SESSION['error_message'] = "Erro ao atualizar moto: " . mysqli_error($conn);
        }
    } else {
        $_SESSION['validation_errors'] = $errors;
    }
    
    mysqli_close($conn);
    header('Location: ../../editmotos.php?motoID=' . $motoId);
    exit();
}
?>
