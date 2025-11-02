<?php
session_start();

//PERM
require_once("../../scripts/perm.php");
//CONNECTION
require_once("../../connection/connection.php");

require_once('../../config.php');

// Verificar se o ID da foto e da moto foram fornecidos
if (!isset($_GET['id']) || empty($_GET['id']) || !isset($_GET['motoID']) || empty($_GET['motoID'])) {
    echo "<script>alert('Parâmetros inválidos!'); window.location.href='../../tabelaMotos.php';</script>";
    exit;
}

$fotoID = $_GET['id'];
$motoID = $_GET['motoID'];

// Buscar informações da foto
$sql = "SELECT caminho_foto FROM moto_fotos WHERE id = ? AND motoId = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $fotoID, $motoID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    echo "<script>alert('Foto não encontrada!'); window.location.href='../../gerenciarfotos.php?motoID=$motoID';</script>";
    exit;
}

$foto = mysqli_fetch_assoc($result);
$caminhoFoto = $foto['caminho_foto'];

// Converter caminho relativo para absoluto
$caminhoAbsoluto = str_replace('./', '../../', $caminhoFoto);

// Excluir o arquivo físico
if (file_exists($caminhoAbsoluto) && is_file($caminhoAbsoluto)) {
    unlink($caminhoAbsoluto);
}

// Excluir registro do banco de dados
$sql = "DELETE FROM moto_fotos WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $fotoID);

if (mysqli_stmt_execute($stmt)) {
    echo "<script>alert('Foto excluída com sucesso!'); window.location.href='../../gerenciarfotos.php?motoID=$motoID';</script>";
} else {
    echo "<script>alert('Erro ao excluir foto!'); window.location.href='../../gerenciarfotos.php?motoID=$motoID';</script>";
}
?>