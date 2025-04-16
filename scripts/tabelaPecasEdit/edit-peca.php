<?php
// Adiciona config
// Caminho absoluto para config.php
require_once(dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'config.php'); 
session_start();

//PERM
// Caminhos corrigidos
require_once(PROJECT_ROOT_PATH . DS . "scripts" . DS . "perm.php");
//CONNECTION
require_once(PROJECT_ROOT_PATH . DS . "connection" . DS . "connection.php");
//FUNCTIONS
require_once(PROJECT_ROOT_PATH . DS . "scripts" . DS . "functions.php");

if (isset($_POST['grupo'])) {
    // Upload picture
    $fotoName = $_FILES["foto"]["name"];
    $fotoSize = $_FILES["foto"]["size"];
    $fotoTmpname = $_FILES["foto"]["tmp_name"];
    // Path de upload corrigido
    $file_path = PROJECT_ROOT_PATH . DS . "upload" . DS . "moto" . DS;

    // Upload
    $foto = uploadFoto($fotoName, $fotoSize, $fotoTmpname, $file_path);
    
    // Remove o path absoluto, deixando apenas o relativo à raiz do projeto
    // Ex: /upload/moto/imagem.jpg
    // Garante barras normais (/) para consistência entre OS
    if ($foto) { // Verifica se o upload foi bem-sucedido antes de processar
        $foto = str_replace(PROJECT_ROOT_PATH . DS, '', $foto);
        $foto = str_replace('\\', '/', $foto);
    } else {
        $foto = null; // ou tratar o erro de upload apropriadamente
    }

    $grupo = $_POST['grupo'];
    $item = $_POST['item'];
    $ordem = $_POST['ordem'];
    $parte = $_POST['parte'];
    $pecaId = $_GET['pecaID'];

    // Prepare the SQL statement using a prepared statement
    if (!empty($fotoName)) {
        $mysqli_query = "UPDATE pecas SET foto = ?, grupo = ?, item = ?, ordem = ?, parte = ? WHERE pecaId = ?";
        $stmt = $conn->prepare($mysqli_query);
        $stmt->bind_param("sssssi", $foto, $grupo, $item, $ordem, $parte, $pecaId);
    } else {
        $mysqli_query = "UPDATE pecas SET grupo = ?, item = ?, ordem = ?, parte = ? WHERE pecaId = ?";
        $stmt = $conn->prepare($mysqli_query);
        $stmt->bind_param("ssssi", $grupo, $item, $ordem, $parte, $pecaId);
    }
    
    // Execute the statement
    $stmt->execute();
    
    // Close the statement
    $stmt->close();
    
    // Close the connection
    mysqli_close($conn);
    
    // Redirecionamento corrigido
    header('Location: ' . PROJECT_ROOT_URL . '/tabelaPecas.php');
}
?>
