<?php

function displayTableData($conn, $tableName, $tableTitle) {
    if (!isset($_GET['ordem'])) {
        echo "<p>Parâmetro 'ordem' inválido ou não fornecido.</p>";
        return;
    }

    $ordem = (int)$_GET['ordem'];

    // Handle form submission for updating values
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['table']) && $_POST['table'] === $tableName) {
        if (isset($_POST['update'])) {
            // Verificar se já existe medição
            $checkQuery = "SELECT COUNT(*) as count FROM $tableName WHERE is_reference = 0 AND ordem = ?";
            $checkStmt = mysqli_prepare($conn, $checkQuery);
            mysqli_stmt_bind_param($checkStmt, "i", $ordem);
            mysqli_stmt_execute($checkStmt);
            $checkResult = mysqli_stmt_get_result($checkStmt);
            $checkRow = mysqli_fetch_assoc($checkResult);
            
            if ($checkRow['count'] == 0) {
                // Não existe medição, vamos criar
                $refQuery = "SELECT * FROM $tableName WHERE is_reference = 1 AND ordem = ?";
                $refStmt = mysqli_prepare($conn, $refQuery);
                mysqli_stmt_bind_param($refStmt, "i", $ordem);
                mysqli_stmt_execute($refStmt);
                $refResult = mysqli_stmt_get_result($refStmt);
                $refRow = mysqli_fetch_assoc($refResult);
                
                if ($refRow) {
                    $fields = [];
                    $values = [];
                    $types = '';
                    foreach ($refRow as $key => $value) {
                        if (!in_array($key, ['id', 'is_reference']) && $value !== null) {
                            $fields[] = $key;
                            $values[] = $value;
                            $types .= 's';
                        }
                    }
                    
                    $fields[] = 'is_reference';
                    $values[] = 0;
                    $types .= 'i';
                    
                    $placeholders = array_fill(0, count($fields), '?');
                    $insertQuery = "INSERT INTO $tableName (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
                    $insertStmt = mysqli_prepare($conn, $insertQuery);
                    if ($insertStmt) {
                        mysqli_stmt_bind_param($insertStmt, $types, ...$values);
                        mysqli_stmt_execute($insertStmt);
                        mysqli_stmt_close($insertStmt);
                    }
                }
            }

            // Agora atualiza os valores
            foreach ($_POST['measured'] as $id => $fields) {
                $updates = [];
                $params = [];
                $types = '';
                foreach ($fields as $field => $value) {
                    $updates[] = "`$field` = ?";
                    $params[] = $value;
                    $types .= 's';
                }
                $params[] = $id;
                $types .= 'i';

                $updateQuery = "UPDATE " . $tableName . 
                              " SET " . implode(', ', $updates) . 
                              " WHERE id = ?";
                $stmt = mysqli_prepare($conn, $updateQuery);
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, $types, ...$params);
                    if (mysqli_stmt_execute($stmt)) {
                        echo "<p class='success-msg'>Alterações salvas com sucesso!</p>";
                    } else {
                        echo "<p class='error-msg'>Erro ao salvar alterações: " . mysqli_error($conn) . "</p>";
                    }
                    mysqli_stmt_close($stmt);
                }
            }
        }
    }

    // Query for reference values
    $refQuery = "SELECT * FROM $tableName WHERE is_reference = 1 AND ordem = ?";
    $refStmt = mysqli_prepare($conn, $refQuery);
    mysqli_stmt_bind_param($refStmt, "i", $ordem);
    mysqli_stmt_execute($refStmt);
    $refResult = mysqli_stmt_get_result($refStmt);

    // Query for measured values
    $measQuery = "SELECT * FROM $tableName WHERE is_reference = 0 AND ordem = ? ORDER BY id DESC";
    $measStmt = mysqli_prepare($conn, $measQuery);
    mysqli_stmt_bind_param($measStmt, "i", $ordem);
    mysqli_stmt_execute($measStmt);
    $measResult = mysqli_stmt_get_result($measStmt);

    if (mysqli_num_rows($refResult) > 0) {
        $refRow = mysqli_fetch_assoc($refResult);

        echo "<div class='card'>";
        echo "<h2 class='card-title'>" . htmlspecialchars($tableTitle) . "</h2>";
        echo "<div class='card-content'>";

        echo "<form method='POST' class='table-form'>";
        echo "<input type='hidden' name='table' value='" . htmlspecialchars($tableName) . "'>";
        echo "<input type='hidden' name='update' value='1'>";

        echo "<div class='table-container'>";
        echo "<table>";
        echo "<thead><tr><th>Parâmetro</th><th>Referência</th><th>Medidos</th></tr></thead>";
        echo "<tbody>";

        foreach ($refRow as $key => $value) {
            $keyLower = strtolower($key);
            if ($keyLower !== 'id' && 
                $keyLower !== 'ordem' && 
                $keyLower !== 'is_reference' && 
                $value !== null &&
                !($tableName === 'cabecote' && ($keyLower === 'motor_tipo' || $keyLower === 'tipo_val')) &&
                !($tableName === 'motor' && ($keyLower === 'created_at' || $keyLower === 'updated_at'))) {
                echo "<tr>";
                echo "<td class='data-label'>" . 
                     htmlspecialchars(ucfirst(str_replace("_", " ", $key))) . 
                     "</td>";
                echo "<td class='ref-value'>" . 
                     htmlspecialchars($value) . 
                     "</td>";
                
                echo "<td class='meas-values'>";
                mysqli_data_seek($measResult, 0);
                $first = true;
                while ($measRow = mysqli_fetch_assoc($measResult)) {
                    if (isset($measRow[$key]) && $measRow[$key] !== null) {
                        echo "<input type='text' " .
                             "name='measured[" . $measRow['id'] . "][" . htmlspecialchars($key) . "]' " .
                             "value='" . htmlspecialchars($measRow[$key]) . "' " .
                             "class='meas-input" . ($first ? " first" : "") . "'>";
                        $first = false;
                    }
                }
                if (mysqli_num_rows($measResult) === 0) {
                    echo "<input type='text' " .
                         "name='measured[new][" . htmlspecialchars($key) . "]' " .
                         "value='" . htmlspecialchars($value) . "' " .
                         "class='meas-input first'>";
                }
                echo "</td>";
                
                echo "</tr>";
            }
        }

        echo "</tbody></table>";
        echo "</div>";

        echo "<button type='submit' class='save-btn'>Salvar Alterações</button>";
        echo "</form>";

        echo "</div>";
        echo "</div>";
    }

    mysqli_stmt_close($refStmt);
    mysqli_stmt_close($measStmt);
}

if (!isset($conn) || !$conn) {
    die("Conexão com o banco de dados não estabelecida.");
}

displayTableData($conn, "embreagem", "Embreagem");
displayTableData($conn, "cabecote", "Cabeçote");
displayTableData($conn, "bomba", "Bomba");
displayTableData($conn, "motor", "Motor");
displayTableData($conn, "virabrequim", "Virabrequim");

echo '<a class="button primary" id="closeModal3">Sair</a>';
?>

<style>
.card {
    background: #1e2029;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
    margin: 15px 0;
    padding: 15px;
    width: 100%;
    max-width: 100%;
    color: #e5e5e5;
    border: 1px solid #333;
}

.card-title {
    color: #fff;
    border-bottom: 2px solid #333;
    padding-bottom: 10px;
    margin-bottom: 15px;
    font-size: 1.2em;
}

.card-content {
    padding: 10px;
}

.table-container {
    margin-bottom: 20px;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 8px;
    text-align: left;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

th {
    background: #2a2c35;
    color: #fff;
}

.data-label {
    font-weight: bold;
    color: #aaa;
}

.ref-value {
    color: #e5e5e5;
    text-align: right;
}

.meas-values {
    color: #4CAF50;
    text-align: right;
}

.meas-input {
    background: #2a2c35;
    border: 1px solid #4CAF50;
    border-radius: 4px;
    padding: 5px;
    color: #fff;
    text-align: right;
    width: 100%;
    margin: 2px 0;
}

.meas-input.first {
    margin-top: 0;
}

.table-form {
    margin-top: 15px;
}

.save-btn {
    background-color: #4CAF50;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
    margin-top: 10px;
    transition: background-color 0.2s ease-in-out;
}

.save-btn:hover {
    background-color: #45a049;
}

#closeModal3 {
    display: block;
    margin: 20px auto;
    padding: 0 1.75em;
    background-color: #ed4933;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    text-align: center;
    width: fit-content;
    text-transform: uppercase;
    letter-spacing: 0.25em;
    font-size: 0.8em;
    height: 3.5em;
    line-height: 3.5em;
    transition: background-color 0.2s ease-in-out;
    box-shadow: none;
}

#closeModal3:hover {
    background-color: #ef5e4a;
}
.success-msg {
    color: #4CAF50;
    margin: 10px 0;
    font-weight: bold;
}

.error-msg {
    color: #ed4933;
    margin: 10px 0;
    font-weight: bold;
}
</style>