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
        // Início do card
        echo "<div class='card'>";
        echo "<h2 class='card-title'>" . htmlspecialchars($tableTitle) . "</h2>";
        echo "<div class='card-content'>";

        while ($row = mysqli_fetch_assoc($result)) {
            foreach ($row as $key => $value) {
                $keyLower = strtolower($key);
                // Excluir 'id' e 'is_reference' e só mostrar se value não for null
                if ($keyLower !== 'id' && 
                    $keyLower !== 'ordem' && 
                    $keyLower !== 'is_reference' && 
                    $value !== null) {
                    echo "<div class='data-item'>";
                    echo "<span class='data-label'>" . 
                         htmlspecialchars(ucfirst(str_replace("_", " ", $key))) . 
                         ":</span>";
                    echo "<span class='data-value'>" . 
                         htmlspecialchars($value) . 
                         "</span>";
                    echo "</div>";
                }
            }
        }

        // Fim do card
        echo "</div>";
        echo "</div>";
    }

    mysqli_stmt_close($stmt);
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

<!-- CSS para estilizar os cards -->
<style>
.card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin: 20px 0;
    padding: 20px;
    max-width: 500px;
}

.card-title {
    color: #333;
    border-bottom: 2px solid #eee;
    padding-bottom: 10px;
    margin-bottom: 15px;
}

.card-content {
    padding: 10px;
}

.data-item {
    margin: 10px 0;
    display: flex;
    justify-content: space-between;
    gap: 10px;
}

.data-label {
    font-weight: bold;
    color: #555;
}

.data-value {
    color: #333;
}
</style>