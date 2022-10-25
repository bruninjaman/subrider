<?php

    function login($user, $password, $conn) {
        //check if password and name exist on db
        $mysqli_query = "SELECT * FROM login WHERE username = '{$user}' AND password = '{$password}'";
        $result = mysqli_query($conn, $mysqli_query);
        session_start();
        $rows = mysqli_num_rows($result);
        $result = mysqli_fetch_assoc($result);
        if($rows > 0) {
            //set sesions
            $_SESSION["user"] = $result["username"];
            $_SESSION["type"] = $result["userType"];
            header("location: ../index.php");
        } else {
            // NO LOGIN
            header("location: ../login.php");
        }
    }

    function uploadFoto($fotoName,$fotoSize,$fotoTmpname,$file_path) {
        //ADD UNIQUE ID
        $tmp_name = "";
        $tmp_name = uniqid($tmp_name, true);
        $tmp_name = $tmp_name . $fotoName;

        //GET FILE DESTINATION
        $file_destination = $file_path . "" . $tmp_name;

        if (file_exists($file_destination)) {
            echo "Sorry, file already exists.";
        } else if ($fotoSize > 500000) {
            echo "Sorry, your file is too large.";
        } else {
            //MOVE FILE TO DIRECTORY
            move_uploaded_file($fotoTmpname, $file_destination);
            return $file_destination;
        };

    }

    function realFormat($valor) { //Formato Real
        return 'R$' . number_format($valor, 2, ',', '.');
    }
?>