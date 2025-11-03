<?php
session_start();

//PERM
require_once("../../scripts/perm.php");
//CONNECTION
require_once("../../connection/connection.php");

require_once('../../config.php');

// Verificar se o ID da moto foi fornecido
if (!isset($_POST['motoID']) || empty($_POST['motoID'])) {
    echo "<script>alert('ID da moto não fornecido!'); window.location.href='../../tabelaMotos.php';</script>";
    exit;
}

$motoID = $_POST['motoID'];
$isAjax = isset($_POST['ajax']);

$uploadedFiles = 0;
$errors = [];
$principalUpdated = false;

// Verificar se a foto principal foi enviada
if (isset($_FILES['foto_principal']) && !empty($_FILES['foto_principal']['name'])) {
    // Processar a foto principal
    $uploadDir = '../../upload/moto/';
    
    // Verificar se o diretório existe, se não, criar
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $fileName = $_FILES['foto_principal']['name'];
    $fileSize = $_FILES['foto_principal']['size'];
    $fileType = $_FILES['foto_principal']['type'];
    $tmpName = $_FILES['foto_principal']['tmp_name'];
    
    // Verificar se é uma imagem
    if (strpos($fileType, 'image/') === 0 && $fileSize <= 5 * 1024 * 1024) {
        // Gerar nome único para o arquivo
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        $newFileName = 'moto_principal_' . $motoID . '_' . uniqid() . '.' . $fileExtension;
        $filePath = $uploadDir . $newFileName;
        
        if (move_uploaded_file($tmpName, $filePath)) {
            // Atualizar o caminho da foto principal na tabela motocicletas
            $relativePath = 'upload/moto/' . $newFileName;
            $sql = "UPDATE motocicletas SET foto = '$relativePath' WHERE motoId = $motoID";
            mysqli_query($conn, $sql);
            $principalUpdated = true;
        }
    }
}

// Verificar se foram enviados arquivos adicionais
if (!isset($_FILES['fotos']) || empty($_FILES['fotos']['name'][0])) {
    if ($isAjax) {
        header('Content-Type: application/json');
        if ($principalUpdated) {
            echo json_encode([
                'success' => true,
                'uploadedFiles' => 0,
                'principalUpdated' => true,
                'message' => 'Foto principal atualizada com sucesso!'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'uploadedFiles' => 0,
                'principalUpdated' => false,
                'message' => 'Nenhuma foto selecionada'
            ]);
        }
        exit;
    } else {
        // Se não houver fotos adicionais mas atualizou a principal, redirecionar
        if (isset($_FILES['foto_principal']) && !empty($_FILES['foto_principal']['name'])) {
            echo "<script>alert('Foto principal atualizada com sucesso!'); window.location.href='../../gerenciarfotos.php?motoID=$motoID';</script>";
            exit;
        } else {
            echo "<script>alert('Nenhuma foto selecionada!'); window.location.href='../../gerenciarfotos.php?motoID=$motoID';</script>";
            exit;
        }
    }
}

// Diretório para salvar as fotos
$uploadDir = '../../upload/moto/';

// Verificar se o diretório existe, se não, criar
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Processar cada arquivo

foreach ($_FILES['fotos']['tmp_name'] as $key => $tmp_name) {
    if ($_FILES['fotos']['error'][$key] === 0) {
        $fileName = $_FILES['fotos']['name'][$key];
        $fileSize = $_FILES['fotos']['size'][$key];
        $fileType = $_FILES['fotos']['type'][$key];
        
        // Verificar se é uma imagem
        if (strpos($fileType, 'image/') !== 0) {
            $errors[] = "O arquivo '$fileName' não é uma imagem válida.";
            continue;
        }
        
        // Verificar tamanho (limite de 5MB)
        if ($fileSize > 5 * 1024 * 1024) {
            $errors[] = "O arquivo '$fileName' excede o tamanho máximo permitido (5MB).";
            continue;
        }
        
        // Gerar nome único para o arquivo
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        $newFileName = 'moto_' . $motoID . '_' . uniqid() . '.' . $fileExtension;
        $destination = $uploadDir . $newFileName;
        
        // Mover o arquivo para o diretório de destino
        if (move_uploaded_file($tmp_name, $destination)) {
            // Salvar informações no banco de dados
            $caminhoFoto = str_replace('../../', './', $destination);
            
            $sql = "INSERT INTO moto_fotos (motoId, caminho_foto, data_upload) VALUES (?, ?, NOW())";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "is", $motoID, $caminhoFoto);
            
            if (mysqli_stmt_execute($stmt)) {
                $uploadedFiles++;
            } else {
                $errors[] = "Erro ao salvar informações da foto '$fileName' no banco de dados.";
            }
        } else {
            $errors[] = "Erro ao fazer upload do arquivo '$fileName'.";
        }
    } else {
        $errors[] = "Erro no upload do arquivo #$key: " . $_FILES['fotos']['error'][$key];
    }
}

// Resposta apropriada
if ($isAjax) {
    header('Content-Type: application/json');
    if ($uploadedFiles > 0 || $principalUpdated) {
        $message = '';
        if ($uploadedFiles > 0) {
            $message = $uploadedFiles . ' foto(s) adicionada(s) com sucesso!';
        }
        if ($principalUpdated) {
            $message = trim($message . ' ' . 'Foto principal atualizada com sucesso!');
        }
        if (!empty($errors)) {
            $message .= ' Porém, ocorreram os seguintes erros: ' . implode(', ', $errors);
        }
        echo json_encode([
            'success' => true,
            'uploadedFiles' => $uploadedFiles,
            'principalUpdated' => $principalUpdated,
            'errors' => $errors,
            'message' => $message
        ]);
    } else {
        $message = 'Nenhuma foto foi adicionada.';
        if (!empty($errors)) {
            $message .= ' Erros: ' . implode(', ', $errors);
        }
        echo json_encode([
            'success' => false,
            'uploadedFiles' => 0,
            'principalUpdated' => $principalUpdated,
            'errors' => $errors,
            'message' => $message
        ]);
    }
    exit;
} else {
    // Comportamento antigo via redirecionamento
    if ($uploadedFiles > 0) {
        $message = "$uploadedFiles foto(s) adicionada(s) com sucesso!";
        if (!empty($errors)) {
            $message .= " Porém, ocorreram os seguintes erros: " . implode(", ", $errors);
        }
        echo "<script>alert('$message'); window.location.href='../../gerenciarfotos.php?motoID=$motoID';</script>";
    } else {
        $message = "Nenhuma foto foi adicionada. Erros: " . implode(", ", $errors);
        echo "<script>alert('$message'); window.location.href='../../gerenciarfotos.php?motoID=$motoID';</script>";
    }
}
?>