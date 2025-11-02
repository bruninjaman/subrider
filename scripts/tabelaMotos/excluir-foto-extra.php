<?php
session_start();

//PERM
require_once("../perm.php");
//CONNECTION
require_once("../../connection/connection.php");
//FUNCTIONS
require_once("../functions.php");

if (isset($_POST['foto_id']) && !empty($_POST['foto_id'])) {
    $foto_id = $_POST['foto_id'];
    
    // Primeiro, obter o caminho da foto para excluir o arquivo
    $sql_select = "SELECT foto FROM moto_fotos_extras WHERE id = '$foto_id'";
    $result = mysqli_query($conn, $sql_select);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $foto = mysqli_fetch_assoc($result);
        $caminho_foto = "../../" . $foto['foto'];
        
        // Excluir o arquivo físico se existir
        if (file_exists($caminho_foto)) {
            unlink($caminho_foto);
        }
        
        // Excluir o registro do banco de dados
        $sql_delete = "DELETE FROM moto_fotos_extras WHERE id = '$foto_id'";
        mysqli_query($conn, $sql_delete);
        
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Foto não encontrada']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ID da foto não fornecido']);
}

// Fechar conexão
mysqli_close($conn);
?>