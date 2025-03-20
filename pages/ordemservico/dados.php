<?php

function displayTableData($conn, $tableName, $tableTitle) {
    if (!isset($_GET['ordem'])) {
        echo "<p>Parâmetro 'ordem' inválido ou não fornecido.</p>";
        return;
    }

    $ordem = (int)$_GET['ordem'];

    // Handle form submission for adding/updating measured values
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['table']) && $_POST['table'] === $tableName) {
        $fields = array_keys($_POST);
        $updateQuery = "INSERT INTO " . mysqli_real_escape_string($conn, $tableName) . 
                       " (ordem, is_reference, " . implode(',', array_map('mysqli_real_escape_string', array($conn), $fields)) . ") 
                        VALUES (?, 0, " . str_repeat('?,', count($fields) - 1) . "?)
                        ON DUPLICATE KEY UPDATE " . implode(',', array_map(function($field) {
                            return "$field = VALUES($field)";
                        }, $fields));
        
        $stmt = mysqli_prepare($conn, $updateQuery);
        if ($stmt) {
            $params = array_merge([$ordem], array_values($_POST));
            $types = str_repeat('s', count($params)); // Assuming all fields are strings; adjust if needed
            mysqli_stmt_bind_param($stmt, $types, ...$params);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    // Query para valores de referência
    $refQuery = "SELECT * FROM " . mysqli_real_escape_string($conn, $tableName) . 
                " WHERE is_reference = 1 AND ordem = ?";
    $refStmt = mysqli_prepare($conn, $refQuery);
    mysqli_stmt_bind_param($refStmt, "i", $ordem);
    mysqli_stmt_execute($refStmt);
    $refResult = mysqli_stmt_get_result($refStmt);

    // Query para valores medidos
    $measQuery = "SELECT * FROM " . mysqli_real_escape_string($conn, $tableName) . 
                 " WHERE is_reference = 0 AND ordem = ?";
    $measStmt = mysqli_prepare($conn, $measQuery);
    mysqli_stmt_bind_param($measStmt, "i", $ordem);
    mysqli_stmt_execute($measStmt);
    $measResult = mysqli_stmt_get_result($measStmt);

    if (mysqli_num_rows($refResult) > 0) {
        $refRow = mysqli_fetch_assoc($refResult);
        $measRow = mysqli_fetch_assoc($measResult);

        // Início do card com formulário
        echo "<div class='card'>";
        echo "<h2 class='card-title'>" . htmlspecialchars($tableTitle) . "</h2>";
        echo "<form method='POST' class='card-content'>";
        echo "<input type='hidden' name='table' value='" . htmlspecialchars($tableName) . "'>";

        foreach ($refRow as $key => $value) {
            $keyLower = strtolower($key);
            if ($keyLower !== 'id' && 
                $keyLower !== 'ordem' && 
                $keyLower !== 'is_reference' && 
                $value !== null) {
                echo "<div class='data-item'>";
                echo "<span class='data-label'>" . 
                     htmlspecialchars(ucfirst(str_replace("_", " ", $key))) . 
                     ":</span>";
                echo "<span class='data-value ref-value'>" . 
                     htmlspecialchars($value) . 
                     "</span>";

                // Campo editável para valor medido
                $measValue = ($measRow && isset($measRow[$key])) ? $measRow[$key] : '';
                echo "<input type='text' class='meas-value' name='" . htmlspecialchars($key) . "' value='" . htmlspecialchars($measValue) . "'>";
                
                echo "</div>";
            }
        }

        // Botão de salvar
        echo "<button type='submit' class='save-btn'>Salvar</button>";
        echo "</form>";
        echo "</div>";
    }

    mysqli_stmt_close($refStmt);
    mysqli_stmt_close($measStmt);
}

if (!isset($conn) || !$conn) {
    die("Conexão com o banco de dados não estabelecida.");
}

// Exibir dados de todas as tabelas em formato de cards
displayTableData($conn, "embreagem", "Embreagem");
displayTableData($conn, "cabecote", "Cabeçote");
displayTableData($conn, "bomba", "Bomba");
displayTableData($conn, "motor", "Motor");
displayTableData($conn, "virabrequim", "Virabrequim");

echo '<a class="button primary" id="closeModal3">Sair</a>';
?>

<!-- CSS atualizado -->
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

.data-item {
    margin: 8px 0;
    display: grid;
    grid-template-columns: 2fr 1fr 1fr;
    gap: 10px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    padding-bottom: 5px;
    align-items: center;
}

.data-label {
    font-weight: bold;
    color: #aaa;
}

.data-value {
    text-align: right;
}

.ref-value {
    color: #e5e5e5;
}

.meas-value {
    background: #2a2c35;
    border: 1px solid #4CAF50;
    border-radius: 4px;
    padding: 5px;
    color: #fff;
    text-align: right;
    width: 100%;
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
</style>