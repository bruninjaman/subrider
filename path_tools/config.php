<?php
/**
 * Path Tools - Arquivo de configuração
 * 
 * Este arquivo contém configurações para as ferramentas de caminhos.
 */

return [
    // Diretório raiz do projeto
    'project_root' => realpath(__DIR__ . '/..'),
    
    // Diretórios para ignorar durante buscas
    'ignore_directories' => [
        'vendor',
        'node_modules',
        '.git',
        '.svn',
        'cache',
        'dist',
        'build'
    ],
    
    // Limites e configurações de depuração
    'debug' => [
        'enabled' => false,       // Ativar depuração
        'log_to_file' => false,   // Registrar em arquivo
        'log_file' => __DIR__ . '/logs/path_tools.log'
    ],
    
    // Limites de busca
    'search_limits' => [
        'max_depth' => 5,         // Profundidade máxima de busca
        'max_results' => 100,     // Resultados máximos de busca
        'max_batch_size' => 50    // Tamanho máximo de lote para operações em massa
    ],
    
    // Configurações de compatibilidade
    'compatibility' => [
        'normalize_separators' => true,     // Normalizar separadores de caminho
        'case_sensitive' => false,          // Busca sensível a maiúsculas e minúsculas
        'prefer_relative_paths' => true     // Preferir caminhos relativos
    ]
];

// Configuração de manipulação de erros
function path_tools_error_handler($errno, $errstr, $errfile, $errline) {
    // Registrar o erro em um formato que podemos retornar como JSON
    $GLOBALS['error_occurred'] = true;
    $GLOBALS['error_message'] = $errstr;
    $GLOBALS['error_line'] = $errline;
    $GLOBALS['error_file'] = $errfile;
    
    // Retornar true para evitar manipulação de erro padrão
    return true;
}

// Função para garantir que toda saída seja JSON válido
function ensure_json_response() {
    // Capturar qualquer saída de buffer
    $output = ob_get_clean();
    
    // Se um erro ocorreu, enviar resposta de erro
    if (isset($GLOBALS['error_occurred']) && $GLOBALS['error_occurred']) {
        $response = [
            'success' => false,
            'message' => 'Erro PHP: ' . $GLOBALS['error_message'],
            'error_details' => [
                'file' => $GLOBALS['error_file'],
                'line' => $GLOBALS['error_line']
            ]
        ];
        echo json_encode($response);
        exit;
    }
    
    // Se tiver alguma saída não JSON, isso é um erro
    if (!empty($output) && substr($output, 0, 1) !== '{' && substr($output, 0, 1) !== '[') {
        $response = [
            'success' => false,
            'message' => 'Resposta inválida do servidor',
            'debug_output' => $output
        ];
        echo json_encode($response);
        exit;
    }
    
    // Se tudo estiver ok, apenas exibe a saída
    echo $output;
} 