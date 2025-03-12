<a class="button secondary" id="closeModal2" style="position: absolute; top: 10px; right: 10px;">Fechar</a>
<form action="scripts\ordemservico\bomba.php" method="POST">
    <h2>Menu Cadastro</h2>
    <label>Marca: </label>
    <input type="text" name="marca">
    <label>Modelo: </label>
    <input type="text" name="modelo">
    <label>Ano: </label>
    <input type="text" name="ano">
    <label for="motorInput">Selecionar motor:</label>
    <?php // Fetch data from the infomotor table
$query = "SELECT marca, modelo, ano FROM infomotor";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

// Generate the menu options dynamically
echo "<select name='motorinfo'>";
// Add a default empty option
echo "<option value='' selected disabled>Select an option</option>";

while ($row = mysqli_fetch_assoc($result)) {
    $name = htmlspecialchars($row['marca']);
    $modelo = htmlspecialchars($row['modelo']);
    $ano = htmlspecialchars($row['ano']);
    $optionValue = "$name - $modelo - $ano";
    echo "<option value='$optionValue'>$optionValue</option>";
}
echo "</select><br>"; ?>
    <a class="button primary" id="next2">Confirmar</a>
    <a class="button secondary" id="next3">Cadastrar</a>