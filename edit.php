
<?php
require_once("./connection/connection.php");
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $table = $_POST['table'];
    $id = $_POST['id'];

    // Aqui você pode buscar os dados do registro no banco de dados e exibir um formulário para edição.
    echo "<h2>Editando registro da tabela: $table</h2>";
    echo "<form method='POST' action='update.php'>
            <input type='hidden' name='table' value='$table'>
            <input type='hidden' name='id' value='$id'>
            <!-- Adicione os campos do formulário aqui -->
            <button type='submit'>Salvar</button>
          </form>";
}
?>