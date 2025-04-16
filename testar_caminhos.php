<?php
// Script para testar a correção de caminhos no projeto
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'config.php');

// Inicializa array para armazenar resultados
$resultados = [
    'arquivos_basicos' => [],
    'scripts_ajax' => [],
    'scripts_processamento' => [],
    'paginas_tabelas' => [],
    'assets' => [],
    'uploads' => []
];

// Função para verificar existência do arquivo
function verificarArquivo($caminho, $categoria) {
    global $resultados;
    
    $resultado = [
        'caminho' => $caminho,
        'status' => file_exists($caminho) ? 'OK' : 'ERRO',
        'mensagem' => file_exists($caminho) ? 'Arquivo encontrado' : 'Arquivo não encontrado'
    ];
    
    $resultados[$categoria][] = $resultado;
    return $resultado['status'] === 'OK';
}

// Função para exibir resultados formatados
function exibirResultados() {
    global $resultados;
    
    echo "<h1>Resultados dos Testes de Caminhos</h1>";
    
    foreach ($resultados as $categoria => $testes) {
        echo "<h2>" . ucfirst(str_replace('_', ' ', $categoria)) . "</h2>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background-color: #f2f2f2;'><th style='padding: 8px;'>Caminho</th><th style='padding: 8px;'>Status</th><th style='padding: 8px;'>Mensagem</th></tr>";
        
        foreach ($testes as $teste) {
            $corStatus = ($teste['status'] === 'OK') ? 'green' : 'red';
            echo "<tr>";
            echo "<td style='padding: 8px;'>" . htmlspecialchars($teste['caminho']) . "</td>";
            echo "<td style='padding: 8px; color: {$corStatus};'>" . htmlspecialchars($teste['status']) . "</td>";
            echo "<td style='padding: 8px;'>" . htmlspecialchars($teste['mensagem']) . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
    }
    
    // Exibir resumo
    $total = 0;
    $sucessos = 0;
    
    foreach ($resultados as $categoria => $testes) {
        foreach ($testes as $teste) {
            $total++;
            if ($teste['status'] === 'OK') $sucessos++;
        }
    }
    
    $porcentagemSucesso = ($total > 0) ? round(($sucessos / $total) * 100, 2) : 0;
    
    echo "<h2>Resumo</h2>";
    echo "<p>Total de testes: {$total}</p>";
    echo "<p>Testes bem-sucedidos: {$sucessos}</p>";
    echo "<p>Porcentagem de sucesso: {$porcentagemSucesso}%</p>";
}

// Teste 1: Verificar arquivos básicos
$arquivosBasicos = [
    PROJECT_ROOT_PATH . DS . 'config.php',
    PROJECT_ROOT_PATH . DS . 'includes' . DS . 'template_base.php'
];

foreach ($arquivosBasicos as $arquivo) {
    verificarArquivo($arquivo, 'arquivos_basicos');
}

// Teste 2: Verificar scripts AJAX
$scriptsAjax = [
    PROJECT_ROOT_PATH . DS . 'ajax' . DS . 'carregarMotos.php',
    PROJECT_ROOT_PATH . DS . 'ajax' . DS . 'carregarOrdens.php',
    PROJECT_ROOT_PATH . DS . 'ajax' . DS . 'update_date.php',
    PROJECT_ROOT_PATH . DS . 'ajax' . DS . 'update_proprietario.php'
];

foreach ($scriptsAjax as $arquivo) {
    verificarArquivo($arquivo, 'scripts_ajax');
}

// Teste 3: Verificar scripts de processamento
$scriptsProcessamento = [
    PROJECT_ROOT_PATH . DS . 'scripts' . DS . 'add_login_columns.php',
    PROJECT_ROOT_PATH . DS . 'scripts' . DS . 'log-in.php',
    PROJECT_ROOT_PATH . DS . 'scripts' . DS . 'tabelaOrdensAdd' . DS . 'create-service.php',
    PROJECT_ROOT_PATH . DS . 'scripts' . DS . 'tabelaOrdensDelete' . DS . 'delete-service.php',
    PROJECT_ROOT_PATH . DS . 'scripts' . DS . 'tabelaOrdensEdit' . DS . 'edit-ordem.php',
    PROJECT_ROOT_PATH . DS . 'scripts' . DS . 'tabelaPecasAdd' . DS . 'add-peca.php',
    PROJECT_ROOT_PATH . DS . 'scripts' . DS . 'tabelaPecasDelete' . DS . 'delete-peca.php',
    PROJECT_ROOT_PATH . DS . 'scripts' . DS . 'tabelaPecasEdit' . DS . 'edit-peca.php',
    PROJECT_ROOT_PATH . DS . 'scripts' . DS . 'tabelaServicos' . DS . 'delete-serv.php',
    PROJECT_ROOT_PATH . DS . 'scripts' . DS . 'tabelaServicosAdd' . DS . 'add-servico.php',
    PROJECT_ROOT_PATH . DS . 'scripts' . DS . 'tabelaServicosEdit' . DS . 'edit-servico.php'
];

foreach ($scriptsProcessamento as $arquivo) {
    verificarArquivo($arquivo, 'scripts_processamento');
}

// Teste 4: Verificar páginas de tabelas
$paginasTabelas = [
    PROJECT_ROOT_PATH . DS . 'pages' . DS . 'tabelaMotos' . DS . 'tabela.php',
    PROJECT_ROOT_PATH . DS . 'pages' . DS . 'tabelaMotos' . DS . 'ajax' . DS . 'carregarMotos.php',
    PROJECT_ROOT_PATH . DS . 'pages' . DS . 'tabelaOrdens' . DS . 'tabela.php',
    PROJECT_ROOT_PATH . DS . 'pages' . DS . 'tabelaPecas' . DS . 'tabela.php',
    PROJECT_ROOT_PATH . DS . 'pages' . DS . 'tabelaPecas' . DS . 'ajax' . DS . 'carregarPecas.php',
    PROJECT_ROOT_PATH . DS . 'pages' . DS . 'tabelaServicos' . DS . 'tabela.php',
    PROJECT_ROOT_PATH . DS . 'pages' . DS . 'tabelaServicos' . DS . 'ajax' . DS . 'carregarServicos.php'
];

foreach ($paginasTabelas as $arquivo) {
    verificarArquivo($arquivo, 'paginas_tabelas');
}

// Teste 5: Verificar assets essenciais
$assets = [
    PROJECT_ROOT_PATH . DS . 'assets' . DS . 'css' . DS . 'main.css',
    PROJECT_ROOT_PATH . DS . 'assets' . DS . 'css' . DS . 'search.css',
    PROJECT_ROOT_PATH . DS . 'assets' . DS . 'css' . DS . 'table.css',
    PROJECT_ROOT_PATH . DS . 'assets' . DS . 'js' . DS . 'main.js',
    PROJECT_ROOT_PATH . DS . 'assets' . DS . 'js' . DS . 'delete_confirm.js',
    PROJECT_ROOT_PATH . DS . 'assets' . DS . 'css' . DS . 'images' . DS . 'edit-new.png',
    PROJECT_ROOT_PATH . DS . 'assets' . DS . 'css' . DS . 'images' . DS . 'x-button.png'
];

foreach ($assets as $arquivo) {
    verificarArquivo($arquivo, 'assets');
}

// Teste 6: Verificar diretórios de upload
$uploads = [
    PROJECT_ROOT_PATH . DS . 'upload' . DS . 'moto',
    PROJECT_ROOT_PATH . DS . 'upload' . DS . 'peca'
];

foreach ($uploads as $diretorio) {
    $resultado = [
        'caminho' => $diretorio,
        'status' => is_dir($diretorio) ? 'OK' : 'ERRO',
        'mensagem' => is_dir($diretorio) ? 'Diretório encontrado' : 'Diretório não encontrado'
    ];
    
    if ($resultado['status'] === 'OK') {
        $resultado['mensagem'] .= ' e ' . (is_writable($diretorio) ? 'gravável' : 'não gravável');
    }
    
    $resultados['uploads'][] = $resultado;
}

// Exibir resultados
echo "<!DOCTYPE html>
<html>
<head>
    <title>Testes de Caminhos - SubRider</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #333; }
        h2 { color: #555; margin-top: 20px; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th { background-color: #f2f2f2; padding: 8px; text-align: left; }
        td { padding: 8px; border: 1px solid #ddd; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>";

exibirResultados();

echo "</body>
</html>"; 