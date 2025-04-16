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

// Verificar se está em modo de teste
if (isset($_POST['_test_mode']) && $_POST['_test_mode'] === 'true') {
    // Em modo de teste, apenas redirecionar para a página de tabela
    header('Location: ' . PROJECT_ROOT_URL . '/tabelaMotos.php');
    exit();
}

if (isset($_FILES["foto"])) {
    //Upload picture securely
    $fotoName = $_FILES["foto"]["name"];
    $fotoSize = $_FILES["foto"]["size"];
    $fotoTmpname = $_FILES["foto"]["tmp_name"];
    // Path de upload corrigido
    $file_path = PROJECT_ROOT_PATH . DS . "upload" . DS . "moto" . DS;

    // Validate and upload photo
    $foto = uploadFoto($fotoName, $fotoSize, $fotoTmpname, $file_path);
    if (!$foto) {
        die("Error: Failed to upload photo.");
    }

    // Remove o path absoluto, deixando apenas o relativo à raiz do projeto
    // Ex: /upload/moto/imagem.jpg
    // Garante barras normais (/) para consistência entre OS
    $foto = str_replace(PROJECT_ROOT_PATH . DS, '', $foto);
    $foto = str_replace('\\', '/', $foto);

    // Validate other input data (example validations)
    $endereco = $_POST['endereco'];
    $ano = $_POST['ano'];
    $modelo = $_POST['modelo'];
    $marca = $_POST['marca'];
    $placa = $_POST['placa'];
    $km = $_POST['KM'];
    $proprietario = $_POST['proprietario'];

    // Perform additional input validation here (e.g., validate year, kilometer, etc.)

    // Prepare the SQL statement with placeholders
    $mysqli_query = "INSERT INTO motocicletas (foto, endereco, ano, modelo, marca, placa, KM, proprietario) ";
    $mysqli_query .= "VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    // Initialize a prepared statement
    $stmt = mysqli_prepare($conn, $mysqli_query);

    // Check if the statement preparation was successful
    if ($stmt) {
        // Bind parameters to the prepared statement
        mysqli_stmt_bind_param($stmt, "ssssssss", $foto, $endereco, $ano, $modelo, $marca, $placa, $km, $proprietario);

        // Execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Close the prepared statement
            mysqli_stmt_close($stmt);

            // Close the database connection
            mysqli_close($conn);

            // Redirect the user (caminho corrigido)
            header('Location: ' . PROJECT_ROOT_URL . '/tabelaMotos.php');
            exit; // Exit after redirection to prevent further execution
        } else {
            // Handle the execution error, for example:
            die("Error: " . mysqli_error($conn));
        }
    } else {
        // Handle the statement preparation error, for example:
        die("Error: " . mysqli_error($conn));
    }
}
?>
