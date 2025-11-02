<?php
session_start();

//PERM
require_once("../perm.php");
//CONNECTION
require_once("../../connection/connection.php");
//FUNCTIONS
require_once("../functions.php");

if (isset($_POST['endereco'])) {
    $motoid = $_POST['motoID'];
    $endereco = $_POST['endereco'];
    $ano = $_POST['ano'];
    $modelo = $_POST['modelo'];
    $marca = $_POST['marca'];
    $placa = $_POST['placa'];
    $km = $_POST['KM'];
    $proprietario = $_POST['proprietario'];

    // Check if a new photo is uploaded
    if (!empty($_FILES["foto"]["name"])) {
        //Upload picture
        $fotoName = $_FILES["foto"]["name"];
        $fotoSize = $_FILES["foto"]["size"];
        $fotoTmpname = $_FILES["foto"]["tmp_name"];
        $file_path = "../../upload/moto/";

        //upload
        $foto = uploadFoto($fotoName,$fotoSize,$fotoTmpname,$file_path);
        //remove relative path
        $foto = trim($foto,"../../");

        // Update the photo along with other fields
        $mysqli_query = "UPDATE motocicletas ";
        $mysqli_query .= "SET foto = '{$foto}', endereco = '{$endereco}', ";
        $mysqli_query .= "ano = '{$ano}', modelo = '{$modelo}', marca = '{$marca}', placa = '{$placa}', km = '{$km}', proprietario = '{$proprietario}' ";
        $mysqli_query .= "WHERE motoID = '" . $motoid . "'";
    } else {
        // Update only other fields without changing the photo
        $mysqli_query = "UPDATE motocicletas ";
        $mysqli_query .= "SET endereco = '{$endereco}', ";
        $mysqli_query .= "ano = '{$ano}', modelo = '{$modelo}', marca = '{$marca}', placa = '{$placa}', km = '{$km}', proprietario = '{$proprietario}' ";
        $mysqli_query .= "WHERE motoID = '" . $motoid . "'";
    }

    // Execute the query
    mysqli_query($conn, $mysqli_query);
    
    // Processar fotos extras
    if (!empty($_FILES["fotos_extras"]["name"][0])) {
        $descricao = $_POST['descricao_fotos'];
        $file_path = "../../upload/moto/extras/";
        
        // Criar diretório se não existir
        if (!file_exists($file_path)) {
            mkdir($file_path, 0777, true);
        }
        
        // Processar cada arquivo
        $total_files = count($_FILES["fotos_extras"]["name"]);
        
        for ($i = 0; $i < $total_files; $i++) {
            $fotoName = $_FILES["fotos_extras"]["name"][$i];
            $fotoSize = $_FILES["fotos_extras"]["size"][$i];
            $fotoTmpname = $_FILES["fotos_extras"]["tmp_name"][$i];
            
            // Upload da foto
            $foto = uploadFoto($fotoName, $fotoSize, $fotoTmpname, $file_path);
            // Remove caminho relativo
            $foto = trim($foto, "../../");
            
            // Inserir na tabela de fotos extras
            $sql_insert_foto = "INSERT INTO moto_fotos_extras (motoId, foto, descricao, data_upload) 
                               VALUES ('$motoid', '$foto', '$descricao', NOW())";
            mysqli_query($conn, $sql_insert_foto);
        }
    }

    // Close the connection
    mysqli_close($conn);

    // Redirect to the desired page after the update
    header('Location: ../../tabelaMotos.php');
    exit(); // Ensure that script execution stops after redirection
}
?>
