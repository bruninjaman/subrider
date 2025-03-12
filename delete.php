<?php
require_once("./connection/connection.php");
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $table = $_POST['table'];
    $id = $_POST['id'];

    // Aqui você pode executar a query para deletar o registro.
    $query = "DELETE FROM $table WHERE id = ?";
    $stmt = $GLOBALS['conn']->prepare($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();

    echo "Registro deletado com sucesso!";
}
?>