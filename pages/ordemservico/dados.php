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
    display: flex;
    justify-content: space-between;
    gap: 10px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    padding-bottom: 5px;
}

.data-label {
    font-weight: bold;
    color: #aaa;
    flex: 1;
}

.data-value {
    color: #e5e5e5;
    flex: 2;
    text-align: right;
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