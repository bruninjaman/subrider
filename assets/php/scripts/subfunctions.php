<?php
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

?>
