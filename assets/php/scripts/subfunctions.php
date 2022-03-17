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

function pageCarousel($page,$table_count){
    $items_showing = ($page * 5);
    if ($items_showing > $table_count)
        $items_showing = $table_count;
    echo '<div class="hint-text">Mostrando <b style="color:red;">'.$items_showing.'</b> de <b style="color:red;">'.$table_count .'</b> resultados.</div>';
    echo '<ul class="pagination">';

    $table_current_page = $page;

    //previous next
    if ($page <= 1) {
        echo '<li class="page-item disabled"><a href="#">Anterior</a></li>';
    } else {
        echo '<li class="page-item"><a href="?page=' . ($page - 1) . '">Anterior</a></li>';
    }

    $i = 0;
    while ($i <= 8) {
        $table_page_index = ($i - 5);
        $table_page_index += $table_current_page;

        //continue loop if page is not positive number
        if ($table_page_index <= 0) {
            $i++;
            continue;
        }

        //determine the current active page
        if ($table_current_page == $table_page_index) {
            $addclass = ' active';
        } else {
            $addclass = '';
        }

        //disable pages that are empty
        if (ceil($table_count / 5) < $table_page_index) {
            $addclass .= ' disabled';
            echo '<li class="page-item ' . $addclass . '"><a href="?page=' . $page . '" class="page-link"> ' . $table_page_index . ' </a></li>';
        } else
            echo '<li class="page-item ' . $addclass . '"><a href="?page=' . $table_page_index . '" class="page-link"> ' . $table_page_index . ' </a></li>';

        $i++;
    }

    //disable next
    if ($page >= $table_count / 5) {
        echo '<li class="page-item disabled"><a href="#" class="page-link">Proximo</a></li>';
    } else {
        echo '<li class="page-item"><a href="?page=' . ($page + 1) . '" class="page-link">Proximo</a></li>';
    }
}
?>
