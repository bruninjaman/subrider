<?php
session_start();

// PERM
require_once("../../scripts/perm.php");
// CONNECTION
require_once("../../connection/connection.php");
// FUNCTIONS
require_once("../../scripts/functions.php");

// Retrieve data from POST and GET
$motorinfo = isset($_POST['motorinfo']) ? $_POST['motorinfo'] : null;
$ordem = isset($_GET['ordem']) ? (string)$_GET['ordem'] : null;

// Parse motorinfo or use individual POST values
if (!empty($motorinfo)) {
    list($marca, $modelo, $ano) = explode(' - ', $motorinfo);
    $ano = intval($ano);
    $modelo = mysqli_real_escape_string($conn, $modelo);
    $marca = mysqli_real_escape_string($conn, $marca);
} else {
    $ano = intval($_POST['ano']);
    $modelo = mysqli_real_escape_string($conn, $_POST['modelo']);
    $marca = mysqli_real_escape_string($conn, $_POST['marca']);
}

// Flag to track if a duplicate ordem was found
$duplicateFound = false;

// Check if the ordem already exists
if (!empty($ordem)) {
    $check_query = "SELECT id FROM infomotor WHERE ordem = ?";
    $check_stmt = mysqli_prepare($conn, $check_query);

    if (!$check_stmt) {
        die("Prepare failed: " . mysqli_error($conn));
    }

    // Bind and execute the check query
    mysqli_stmt_bind_param($check_stmt, "s", $ordem);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);

    // If the ordem already exists, set the flag
    if (mysqli_stmt_num_rows($check_stmt) > 0) {
        $duplicateFound = true;
    }

    // Close the check statement
    mysqli_stmt_close($check_stmt);
}

// Only insert if no duplicate was found
if (!$duplicateFound) {
    // Prepare the SQL query for infomotor table
    $insert_query = "INSERT INTO infomotor (ano, modelo, marca, ordem) VALUES (?, ?, ?, ?)";
    $insert_stmt = mysqli_prepare($conn, $insert_query);

    if (!$insert_stmt) {
        die("Prepare failed: " . mysqli_error($conn));
    }

    // Bind and execute the insert query
    mysqli_stmt_bind_param($insert_stmt, "isss", $ano, $modelo, $marca, $ordem);
    mysqli_stmt_execute($insert_stmt);

    // Close the insert statement
    mysqli_stmt_close($insert_stmt);
}

$option = $_POST['selected_option'];

if ($option == 'bomba') {
    // Sanitize input data
    $pressao_oleo_min = floatval($_POST['pressao_oleo_min']);
    $pressao_oleo_max = floatval($_POST['pressao_oleo_max']);
    $pressao_combustao = floatval($_POST['pressao_combustao']);
    $vazao_combustao_min = floatval($_POST['vazao_combustao_min']);
    $vazao_combustao_max = floatval($_POST['vazao_combustao_max']);
    $ordem = $_GET['ordem'];

    // Set is_reference to 1
    $is_reference = 1;

    // Check if a record with is_reference = 1 already exists
    $check_query = "SELECT id FROM bomba WHERE is_reference = 1 LIMIT 1";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        // If a record exists, update it
        $row = mysqli_fetch_assoc($check_result);
        $id = $row['id'];

        $update_query = "UPDATE bomba SET comb_pressao = ?, pressao_oleo_min = ?, pressao_oleo_max = ?, vazao_min = ?, vazao_max = ?, ordem = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $update_query);

        if (!$stmt) {
            die("Prepare failed: " . mysqli_error($conn));
        }

        // Bind parameters
        mysqli_stmt_bind_param(
            $stmt,
            "dddddss",
            $pressao_combustao,
            $pressao_oleo_min,
            $pressao_oleo_max,
            $vazao_combustao_min,
            $vazao_combustao_max,
            $ordem,
            $id
        );

        // Execute the query
        if (mysqli_stmt_execute($stmt)) {
            echo "Data updated successfully!";
        } else {
            echo "Error: " . mysqli_stmt_error($stmt);
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        // If no record exists, insert a new one
        $insert_query = "INSERT INTO bomba (comb_pressao, pressao_oleo_min, pressao_oleo_max, vazao_min, vazao_max, ordem, is_reference) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insert_query);

        if (!$stmt) {
            die("Prepare failed: " . mysqli_error($conn));
        }

        // Bind parameters
        mysqli_stmt_bind_param(
            $stmt,
            "dddddss",
            $pressao_combustao,
            $pressao_oleo_min,
            $pressao_oleo_max,
            $vazao_combustao_min,
            $vazao_combustao_max,
            $ordem,
            $is_reference
        );

        // Execute the query
        if (mysqli_stmt_execute($stmt)) {
            echo "Data inserted successfully!";
        } else {
            echo "Error: " . mysqli_stmt_error($stmt);
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    }
} else if ($option == 'cabecote') {
    // Retrieve and sanitize input data
    $selected_option = intval($_POST['selected_option'] ?? 0);
    $engine_type = $_POST['engine_type'] ?? '';
    $valve_type = $_POST['valve_type'] ?? '';
    $num_cilindros = intval($_POST['num_cilindros'] ?? 0);
    $num_val_adm = intval($_POST['num_val_adm'] ?? 0);
    $num_val_esc = intval($_POST['num_val_esc'] ?? 0);
    $val_adm_limite_min = floatval($_POST['val_adm_limite_min'] ?? 0);
    $val_adm_limite_max = floatval($_POST['val_adm_limite_max'] ?? 0);
    $val_esc_limite_min = floatval($_POST['val_esc_limite_min'] ?? 0);
    $val_esc_limite_max = floatval($_POST['val_esc_limite_max'] ?? 0);
    $compressao_min = floatval($_POST['compressao_min'] ?? 0);
    $compressao_max = floatval($_POST['compressao_max'] ?? 0);
    $tucho = isset($_POST['tucho']) ? 1 : 0; // Handle the tucho field
    $is_reference = 1; // Hardcode is_reference as true
    $ordem = intval($_POST['ordem'] ?? 0); // Assuming ordem is passed in the POST request

    // Handle nullable fields based on valve type
    $cames_adm_diam_min = $cames_adm_diam_max = $came_diam_min = null;

    if ($valve_type === 'dohc') { // Now checking for string 'dohc'
        $cames_adm_diam_min = !empty($_POST['cames_adm_diam_min']) ? floatval($_POST['cames_adm_diam_min']) : null;
        $cames_adm_diam_max = !empty($_POST['cames_adm_diam_max']) ? floatval($_POST['cames_adm_diam_max']) : null;
    } else if ($valve_type === 'ohc') { // Now checking for string 'ohc'
        $came_diam_min = !empty($_POST['came_diam_min']) ? floatval($_POST['came_diam_min']) : null;
    }

    // Check if a record with is_reference = 1 and the given ordem already exists
    $check_query = "SELECT id FROM cabecote WHERE ordem = ? AND is_reference = 1";
    $check_stmt = mysqli_prepare($conn, $check_query);

    if (!$check_stmt) {
        die("Prepare failed: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($check_stmt, "i", $ordem);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);

    if (mysqli_stmt_num_rows($check_stmt) > 0) {
        // Record exists, perform an update
        $update_query = "UPDATE cabecote SET 
            motor_tipo = ?, 
            tipo_val = ?, 
            cilindros = ?, 
            val_adm = ?, 
            val_esc = ?, 
            cames_diam_min = ?, 
            cames_adm_diam_min = ?, 
            cames_adm_diam_max = ?, 
            val_adm_limite_min = ?, 
            val_adm_limite_max = ?, 
            val_esc_limite_min = ?, 
            val_esc_limite_max = ?, 
            compressao_min = ?, 
            compressao_max = ?, 
            tucho = ?
        WHERE ordem = ? AND is_reference = 1";

        $update_stmt = mysqli_prepare($conn, $update_query);

        if (!$update_stmt) {
            die("Prepare failed: " . mysqli_error($conn));
        }

        mysqli_stmt_bind_param(
            $update_stmt,
            "ssiiidddddddddsi",
            $engine_type,
            $valve_type,
            $num_cilindros,
            $num_val_adm,
            $num_val_esc,
            $came_diam_min,
            $cames_adm_diam_min,
            $cames_adm_diam_max,
            $val_adm_limite_min,
            $val_adm_limite_max,
            $val_esc_limite_min,
            $val_esc_limite_max,
            $compressao_min,
            $compressao_max,
            $tucho,
            $ordem
        );

        if (mysqli_stmt_execute($update_stmt)) {
            echo "Data updated successfully!";
        } else {
            echo "Error: " . mysqli_stmt_error($update_stmt);
        }

        mysqli_stmt_close($update_stmt);
    } else {
        // Record does not exist, perform an insert
        $insert_query = "INSERT INTO cabecote (
            motor_tipo, tipo_val, cilindros, val_adm, val_esc, cames_diam_min, cames_adm_diam_min, 
            cames_adm_diam_max, val_adm_limite_min, val_adm_limite_max, val_esc_limite_min, 
            val_esc_limite_max, compressao_min, compressao_max, ordem, is_reference, tucho
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $insert_stmt = mysqli_prepare($conn, $insert_query);

        if (!$insert_stmt) {
            die("Prepare failed: " . mysqli_error($conn));
        }

        mysqli_stmt_bind_param(
            $insert_stmt,
            "ssiiidddddddddsii",
            $engine_type,
            $valve_type,
            $num_cilindros,
            $num_val_adm,
            $num_val_esc,
            $came_diam_min,
            $cames_adm_diam_min,
            $cames_adm_diam_max,
            $val_adm_limite_min,
            $val_adm_limite_max,
            $val_esc_limite_min,
            $val_esc_limite_max,
            $compressao_min,
            $compressao_max,
            $ordem,
            $is_reference,
            $tucho
        );

        if (mysqli_stmt_execute($insert_stmt)) {
            echo "Data inserted successfully!";
        } else {
            echo "Error: " . mysqli_stmt_error($insert_stmt);
        }

        mysqli_stmt_close($insert_stmt);
    }

    // Close the check statement
    mysqli_stmt_close($check_stmt);
} else if ($option == 'virabrequim') {
    // Sanitize input data
    $rolamento_type = $_POST['rolamento_type'] ?? '';
    $folga_lateral_biela = intval($_POST['folga_lateral_biela'] ?? 0);
    $folga_eixo_bronzina = intval($_POST['folga_eixo_bronzina'] ?? 0);
    $folga_eixo_mancal = intval($_POST['folga_eixo_mancal'] ?? 0);
    $folga_lateral_eixo_min = intval($_POST['folga_lateral_eixo_min'] ?? 0);
    $folga_lateral_eixo_max = intval($_POST['folga_lateral_eixo_max'] ?? 0);
    $empenamento_max = intval($_POST['empenamento_max'] ?? 0);
    $is_reference = true; // Always true as per requirement

    // Check if the type is 'bronzina' and set specific values to null
    if ($rolamento_type == 'bronzina') {
        $folga_lateral_biela_max = null;
        $folga_lateral_eixo_max = null;
        $empenamento_max = null;
    }

    // Check if there is already a record with is_reference = true
    $checkQuery = "SELECT id FROM virabrequim WHERE is_reference = true LIMIT 1";
    $checkResult = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        // Update existing record
        $row = mysqli_fetch_assoc($checkResult);
        $id = $row['id'];

        $updateQuery = "UPDATE virabrequim SET 
            tipo = ?, 
            folga_lateral_biela = ?, 
            folga_bronzina = ?, 
            folga_mancal = ?, 
            folga_lateral_eixo_min = ?, 
            folga_lateral_eixo_max = ?, 
            empenamento = ?, 
            ordem = ? 
            WHERE id = ?";

        $stmt = mysqli_prepare($conn, $updateQuery);

        if (!$stmt) {
            die("Prepare failed: " . mysqli_error($conn));
        }

        mysqli_stmt_bind_param(
            $stmt,
            "siiiiiisi",
            $rolamento_type,
            $folga_lateral_biela,
            $folga_eixo_bronzina,
            $folga_eixo_mancal,
            $folga_lateral_eixo_min,
            $folga_lateral_eixo_max,
            $empenamento_max,
            $ordem,
            $id
        );

        if (mysqli_stmt_execute($stmt)) {
            echo "Data updated successfully!";
        } else {
            echo "Error: " . mysqli_stmt_error($stmt);
        }

        mysqli_stmt_close($stmt);
    } else {
        // Insert new record
        $insertQuery = "INSERT INTO virabrequim (
            tipo, folga_lateral_biela, folga_bronzina, folga_mancal, 
            folga_lateral_eixo_min, folga_lateral_eixo_max, empenamento, ordem, is_reference
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $insertQuery);

        if (!$stmt) {
            die("Prepare failed: " . mysqli_error($conn));
        }

        mysqli_stmt_bind_param(
            $stmt,
            "siiiiiisi",
            $rolamento_type,
            $folga_lateral_biela,
            $folga_eixo_bronzina,
            $folga_eixo_mancal,
            $folga_lateral_eixo_min,
            $folga_lateral_eixo_max,
            $empenamento_max,
            $ordem,
            $is_reference
        );

        if (mysqli_stmt_execute($stmt)) {
            echo "Data inserted successfully!";
        } else {
            echo "Error: " . mysqli_stmt_error($stmt);
        }

        mysqli_stmt_close($stmt);
    }
} else if ($option == 'embreagem') {
    // Sanitize input data
    $nr_discos = intval($_POST['nr_discos'] ?? 0);
    $nr_discos_sep = intval($_POST['nr_discos_sep'] ?? 0);
    $disco_fric_esp_min = floatval($_POST['disco_fric_esp_min'] ?? 0);
    $disco_sep_emp_max = floatval($_POST['disco_sep_emp_max'] ?? 0);
    $is_reference = true; // Always true in this case

    // Check if a row with the same ordem and is_reference = 1 exists
    $check_query = "SELECT id FROM embreagem WHERE ordem = ? AND is_reference = 1";
    $check_stmt = mysqli_prepare($conn, $check_query);

    if (!$check_stmt) {
        die("Prepare failed: " . mysqli_error($conn));
    }

    // Bind the ordem parameter
    mysqli_stmt_bind_param($check_stmt, "s", $ordem);

    // Execute the check query
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);

    if (mysqli_stmt_num_rows($check_stmt) > 0) {
        // Row exists: Perform an UPDATE
        $update_query = "UPDATE embreagem SET
            disco_friccao = ?,
            disco_separador = ?,
            disco_friccao_espes_min = ?,
            disco_separador_emp_max = ?
            WHERE ordem = ? AND is_reference = 1";

        $update_stmt = mysqli_prepare($conn, $update_query);

        if (!$update_stmt) {
            die("Prepare failed: " . mysqli_error($conn));
        }

        // Bind parameters for UPDATE
        mysqli_stmt_bind_param(
            $update_stmt,
            "iidds",
            $nr_discos,
            $nr_discos_sep,
            $disco_fric_esp_min,
            $disco_sep_emp_max,
            $ordem
        );

        // Execute the UPDATE
        if (mysqli_stmt_execute($update_stmt)) {
            echo "Data updated successfully!";
        } else {
            echo "Error: " . mysqli_stmt_error($update_stmt);
        }

        // Close the UPDATE statement
        mysqli_stmt_close($update_stmt);
    } else {
        // Row does not exist: Perform an INSERT
        $insert_query = "INSERT INTO embreagem (
            disco_friccao, disco_separador, disco_friccao_espes_min, disco_separador_emp_max, ordem, is_reference
        ) VALUES (?, ?, ?, ?, ?, ?)";

        $insert_stmt = mysqli_prepare($conn, $insert_query);

        if (!$insert_stmt) {
            die("Prepare failed: " . mysqli_error($conn));
        }

        // Bind parameters for INSERT
        mysqli_stmt_bind_param(
            $insert_stmt,
            "iiddsi",
            $nr_discos,
            $nr_discos_sep,
            $disco_fric_esp_min,
            $disco_sep_emp_max,
            $ordem,
            $is_reference
        );

        // Execute the INSERT
        if (mysqli_stmt_execute($insert_stmt)) {
            echo "Data inserted successfully!";
        } else {
            echo "Error: " . mysqli_stmt_error($insert_stmt);
        }

        // Close the INSERT statement
        mysqli_stmt_close($insert_stmt);
    }

    // Close the check statement
    mysqli_stmt_close($check_stmt);
} else if ($option == 'motor') {
    // Sanitize input data
    $nr_cilindros = intval($_POST['nr_cilindros'] ?? 0);
    $curso_pistao = floatval($_POST['curso_pistao'] ?? 0);
    $diametro_cilindro_max = floatval($_POST['diametro_cilindro_max'] ?? 0);
    $conicidade_max = floatval($_POST['conicidade_max'] ?? 0);
    $ovalizacao_max = floatval($_POST['ovalizacao_max'] ?? 0);
    $diametro_pistao_min = floatval($_POST['diametro_pistao_min'] ?? 0);
    $folga_cil_pis_max = floatval($_POST['folga_cil_pis_max'] ?? 0);
    $aber_anel_1_max = floatval($_POST['aber_anel_1_max'] ?? 0);
    $aber_anel_2_max = floatval($_POST['aber_anel_2_max'] ?? 0);
    $aber_anel_1_pres_min = floatval($_POST['aber_anel_1_pres_min'] ?? 0);
    $aber_anel_2_pres_min = floatval($_POST['aber_anel_2_pres_min'] ?? 0);
    $larg_anel_1_min = floatval($_POST['larg_anel_1_min'] ?? 0);
    $larg_anel_2_min = floatval($_POST['larg_anel_2_min'] ?? 0);
    $dia_furo_pis_min = floatval($_POST['dia_furo_pis_min'] ?? 0);
    $dia_pino_pis_min = floatval($_POST['dia_pino_pis_min'] ?? 0);
    $folga_pino_pis_max = floatval($_POST['folga_pino_pis_max'] ?? 0);
    $ordem = $_GET['ordem'];

    // Set is_reference to 1
    $is_reference = 1;

    // Check if a record with is_reference = 1 already exists
    $check_query = "SELECT id FROM motor WHERE is_reference = 1 LIMIT 1";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        // If a record exists, update it
        $row = mysqli_fetch_assoc($check_result);
        $id = $row['id'];

        $insert_query = "INSERT INTO motor (
            nr_cilindros, curso_pistao, diametro_cilindro_max, conicidade_max, ovalizacao_max, 
            diametro_pistao_min, folga_cil_pis_max, aber_anel_1_max, aber_anel_2_max, 
            aber_anel_1_pres_min, aber_anel_2_pres_min, larg_anel_1_min, larg_anel_2_min, 
            dia_furo_pis_min, dia_pino_pis_min, folga_pino_pis_max, ordem, is_reference
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $update_query = "UPDATE motor SET 
        nr_cilindros = ?, 
        curso_pistao = ?, 
        diametro_cilindro_max = ?, 
        conicidade_max = ?, 
        ovalizacao_max = ?, 
        diametro_pistao_min = ?, 
        folga_cil_pis_max = ?, 
        aber_anel_1_max = ?, 
        aber_anel_2_max = ?, 
        aber_anel_1_pres_min = ?, 
        aber_anel_2_pres_min = ?, 
        larg_anel_1_min = ?, 
        larg_anel_2_min = ?, 
        dia_furo_pis_min = ?, 
        dia_pino_pis_min = ?, 
        folga_pino_pis_max = ?, 
        ordem = ?, 
        is_reference = ? 
        WHERE id = ?"; // 19 placeholders

        $stmt = mysqli_prepare($conn, $update_query);

        if (!$stmt) {
            die("Prepare failed: " . mysqli_error($conn));
        }

        mysqli_stmt_bind_param(
            $stmt,
            "idddddddddddddddssi", // 19 type specifiers
            $nr_cilindros,
            $curso_pistao,
            $diametro_cilindro_max,
            $conicidade_max,
            $ovalizacao_max,
            $diametro_pistao_min,
            $folga_cil_pis_max,
            $aber_anel_1_max,
            $aber_anel_2_max,
            $aber_anel_1_pres_min,
            $aber_anel_2_pres_min,
            $larg_anel_1_min,
            $larg_anel_2_min,
            $dia_furo_pis_min,
            $dia_pino_pis_min,
            $folga_pino_pis_max,
            $ordem,
            $is_reference,
            $id // Added for the WHERE clause
        );

        // Execute the query
        if (mysqli_stmt_execute($stmt)) {
            echo "Data updated successfully!";
        } else {
            echo "Error: " . mysqli_stmt_error($stmt);
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        // If no record exists, insert a new one
        $insert_query = "INSERT INTO motor (
            nr_cilindros, curso_pistao, diametro_cilindro_max, conicidade_max, ovalizacao_max, 
            diametro_pistao_min, folga_cil_pis_max, aber_anel_1_max, aber_anel_2_max, 
            aber_anel_1_pres_min, aber_anel_2_pres_min, larg_anel_1_min, larg_anel_2_min, 
            dia_furo_pis_min, dia_pino_pis_min, folga_pino_pis_max, ordem, is_reference
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $insert_query);

        if (!$stmt) {
            die("Prepare failed: " . mysqli_error($conn));
        }

        // Bind parameters
        mysqli_stmt_bind_param(
            $stmt,
            "idddddddddddddddds", // 19 characters in the type definition string
            $nr_cilindros,
            $curso_pistao,
            $diametro_cilindro_max,
            $conicidade_max,
            $ovalizacao_max,
            $diametro_pistao_min,
            $folga_cil_pis_max,
            $aber_anel_1_max,
            $aber_anel_2_max,
            $aber_anel_1_pres_min,
            $aber_anel_2_pres_min,
            $larg_anel_1_min,
            $larg_anel_2_min,
            $dia_furo_pis_min,
            $dia_pino_pis_min,
            $folga_pino_pis_max,
            $ordem,
            $is_reference
        );

        // Execute the query
        if (mysqli_stmt_execute($stmt)) {
            echo "Data inserted successfully!";
        } else {
            echo "Error: " . mysqli_stmt_error($stmt);
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    }
} else {
    echo "<br>option not set: " . var_export($_POST['selected_option'], true) . PHP_EOL;
}

// Redirect after processing
header('Location: ../../ordemservico.php?ordem=' . (string)$_GET['ordem']);
exit;
