<?php
/**
 * Path Checker - Ferramenta para verificação de caminhos em arquivos
 * 
 * Este arquivo é o ponto de entrada para o verificador de caminhos.
 * Ele apresenta uma interface para selecionar arquivos e verifica os caminhos encontrados.
 */

// Incluir autoloader
require_once __DIR__ . '/autoload.php';

use PathTools\Core\PathCheckerCore;
use PathTools\UI\PathCheckerUI;

// Iniciar sessão
session_start();

// Criar instâncias
$pathChecker = new PathCheckerCore();
$ui = new PathCheckerUI($pathChecker);

// Processar solicitação
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['scan'])) {
    if (empty($_POST['paths']) || !is_array($_POST['paths'])) {
        $ui->displayError('Nenhum arquivo ou diretório selecionado');
        exit;
    }
    
    $selectedPaths = $_POST['paths'];
    
    // Contar arquivos para mostrar progresso
    $totalFiles = $pathChecker->countScanFiles($selectedPaths);
    
    if ($totalFiles === 0) {
        $ui->displayError('Nenhum arquivo encontrado nos caminhos selecionados');
        exit;
    }
    
    // Escanear caminhos selecionados
    $results = $pathChecker->scanSelectedPaths($selectedPaths);
    
    // Armazenar resultados na sessão para uso posterior
    $_SESSION['path_checker_results'] = $results;
    
    // Exibir resultados
    $ui->displayResults($results);
} else {
    // Exibir seletor de arquivos
    $ui->displaySelector();
} 