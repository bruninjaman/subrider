<?php

function displayTableData($conn, $tableName, $tableTitle) {
    if (!isset($_GET['ordem'])) {
        echo "<p>Parâmetro 'ordem' inválido ou não fornecido.</p>";
        return;
    }

    $query = "SELECT * FROM " . mysqli_real_escape_string($conn, $tableName) . 
             " WHERE is_reference = 1 AND ordem = ?";
    
    $stmt = mysqli_prepare($conn, $query);
    if ($stmt === false) {
        die("Erro na preparação da query: " . mysqli_error($conn));
    }

    $ordem = (int)$_GET['ordem'];
    mysqli_stmt_bind_param($stmt, "i", $ordem);
    
    if (!mysqli_stmt_execute($stmt)) {
        die("Erro ao executar a query: " . mysqli_stmt_error($stmt));
    }

    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        echo "<h2>Tabela " . htmlspecialchars($tableTitle) . "</h2>";
        echo "<table border='1' cellpadding='10' cellspacing='0'>";
        echo "<thead><tr>";

        // Exibir cabeçalhos excluindo 'id' e 'is_reference'
        $fields = mysqli_fetch_fields($result);
        foreach ($fields as $field) {
            $fieldName = strtolower($field->name);
            if ($fieldName !== 'id' && $fieldName !== 'is_reference') {
                echo "<th>" . htmlspecialchars(ucfirst(str_replace("_", " ", $field->name))) . "</th>";
            }
        }

        echo "</tr></thead><tbody>";

        // Exibir dados excluindo 'id' e 'is_reference'
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            foreach ($row as $key => $value) {
                $keyLower = strtolower($key);
                if ($keyLower !== 'id' && $keyLower !== 'is_reference') {
                    echo "<td>" . htmlspecialchars($value ?? 'N/A') . "</td>";
                }
            }
            echo "</tr>";
        }

        echo "</tbody></table><br><br>";
    }

    mysqli_stmt_close($stmt);
}

if (!isset($conn) || !$conn) {
    die("Conexão com o banco de dados não estabelecida.");
}

// Exibir dados de todas as tabelas com suas respectivas colunas
displayTableData($conn, "embreagem", "Embreagem");    // disco_friccao, disco_friccao_espes_min, disco_separador, disco_separador_emp_max, ordem
displayTableData($conn, "cabecote", "Cabeçote");      // cames_adm_diam_max, cames_adm_diam_min, cames_diam_min, cilindros, compressao_max, 
                                                      // compressao_min, motor_tipo, ordem, tipo_val, tucho, val_adm, val_adm_limite_max, 
                                                      // val_adm_limite_min, val_esc, val_esc_limite_max, val_esc_limite_min
displayTableData($conn, "bomba", "Bomba");            // comb_pressao, pressao_oleo_max, pressao_oleo_min, vazao_max, vazao_min, ordem
displayTableData($conn, "motor", "Motor");            // aber_anel_1_max, aber_anel_1_pres_min, aber_anel_2_max, aber_anel_2_pres_min, conicidade_max, 
                                                      // created_at, curso_pistao, diametro_cilindro_max, diametro_pistao_min, dia_furo_pis_min, 
                                                      // dia_pino_pis_min, folga_cil_pis_max, folga_pino_pis_max, larg_anel_1_min, larg_anel_2_min, 
                                                      // nr_cilindros, ordem, ovalizacao_max, updated_at
displayTableData($conn, "virabrequim", "Virabrequim"); // empenamento, folga_bronzina, folga_lateral_biela, folga_lateral_eixo_max, 
                                                      // folga_lateral_eixo_min, folga_mancal, ordem, tipo

echo '<a class="button primary" id="closeModal3">Sair</a>';
?>