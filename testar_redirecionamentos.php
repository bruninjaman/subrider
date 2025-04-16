<?php
// Script para testar redirecionamentos no projeto
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'config.php');

// Array com os redirecionamentos para testar
$redirecionamentos = [
    [
        'descricao' => 'Login → Index (com credenciais corretas)',
        'url_origem' => PROJECT_ROOT_URL . '/scripts/log-in.php',
        'metodo' => 'POST',
        'dados' => [
            'user' => 'admin', // Substitua pelo usuário real de teste
            'pass' => 'admin'    // Substitua pela senha real de teste
        ],
        'url_esperada' => PROJECT_ROOT_URL . '/index.php'
    ],
    [
        'descricao' => 'Login → Login (com credenciais incorretas)',
        'url_origem' => PROJECT_ROOT_URL . '/scripts/log-in.php',
        'metodo' => 'POST',
        'dados' => [
            'user' => 'usuario_invalido',
            'pass' => 'senha_invalida'
        ],
        'url_esperada' => PROJECT_ROOT_URL . '/login.php'
    ],
    [
        'descricao' => 'Adicionar Moto → Tabela Motos',
        'url_origem' => PROJECT_ROOT_URL . '/scripts/tabelaMotos/add-moto.php',
        'metodo' => 'POST',
        'dados' => [
            // Dados fictícios para adicionar moto - não executa realmente
            '_test_mode' => 'true'
        ],
        'url_esperada' => PROJECT_ROOT_URL . '/tabelaMotos.php'
    ],
    [
        'descricao' => 'Adicionar Peça → Tabela Peças',
        'url_origem' => PROJECT_ROOT_URL . '/scripts/tabelaPecasAdd/add-peca.php',
        'metodo' => 'POST',
        'dados' => [
            // Dados fictícios para adicionar peça - não executa realmente
            '_test_mode' => 'true'
        ],
        'url_esperada' => PROJECT_ROOT_URL . '/tabelaPecas.php'
    ],
    [
        'descricao' => 'Adicionar Serviço → Tabela Serviços',
        'url_origem' => PROJECT_ROOT_URL . '/scripts/tabelaServicosAdd/add-servico.php',
        'metodo' => 'POST',
        'dados' => [
            // Dados fictícios para adicionar serviço - não executa realmente
            '_test_mode' => 'true'
        ],
        'url_esperada' => PROJECT_ROOT_URL . '/tabelaServicos.php'
    ]
];

// Função para exibir formulário de teste
function exibirFormularioTeste($redirecionamento, $index) {
    $id = "form_teste_" . $index;
    $formData = json_encode($redirecionamento['dados']);
    
    echo '<div class="redirect-test">';
    echo '<h3>' . htmlspecialchars($redirecionamento['descricao']) . '</h3>';
    echo '<div class="test-details">';
    echo '<p><strong>URL de Origem:</strong> ' . htmlspecialchars($redirecionamento['url_origem']) . '</p>';
    echo '<p><strong>Método:</strong> ' . htmlspecialchars($redirecionamento['metodo']) . '</p>';
    echo '<p><strong>Dados:</strong> <pre>' . htmlspecialchars(json_encode($redirecionamento['dados'], JSON_PRETTY_PRINT)) . '</pre></p>';
    echo '<p><strong>URL Esperada:</strong> ' . htmlspecialchars($redirecionamento['url_esperada']) . '</p>';
    echo '</div>';
    
    echo '<div class="test-actions">';
    echo '<button class="test-button" onclick="testarRedirecionamento(' . $index . ', ' . htmlspecialchars($formData) . ')">Testar Redirecionamento</button>';
    echo '<div id="resultado_' . $index . '" class="test-result"></div>';
    echo '</div>';
    echo '</div>';
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Teste de Redirecionamentos - SubRider</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px; 
            line-height: 1.6;
        }
        h1 { color: #333; }
        h2 { 
            color: #555; 
            margin-top: 20px; 
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        h3 {
            color: #444;
            margin-bottom: 10px;
        }
        .env-info {
            background-color: #f0f0f0;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .warning-box {
            background-color: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border-left: 5px solid #ffeeba;
        }
        .redirect-test {
            background-color: #f8f8f8;
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .test-details {
            margin-bottom: 15px;
        }
        pre {
            background-color: #f5f5f5;
            padding: 10px;
            border-radius: 4px;
            overflow-x: auto;
        }
        .test-actions {
            display: flex;
            flex-direction: column;
        }
        .test-button {
            padding: 8px 12px;
            margin-bottom: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            align-self: flex-start;
        }
        .test-button:hover {
            background-color: #45a049;
        }
        .test-result {
            padding: 10px;
            margin-top: 10px;
            border-radius: 4px;
            display: none;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        iframe {
            display: none;
        }
    </style>
</head>
<body>
    <h1>Teste de Redirecionamentos - SubRider</h1>
    
    <div class="warning-box">
        <p><strong>Atenção:</strong> Este é um teste simulado dos redirecionamentos. Os formulários são enviados com dados fictícios para verificar apenas a URL de destino após o redirecionamento, sem executar ações reais no banco de dados.</p>
        <p>Para testes completos, use credenciais reais e dados válidos quando necessário.</p>
    </div>
    
    <h2>Testes de Redirecionamento</h2>
    
    <div id="testes-container">
        <?php
        foreach ($redirecionamentos as $index => $redirecionamento) {
            exibirFormularioTeste($redirecionamento, $index);
        }
        ?>
    </div>
    
    <script>
        // Array global com informações dos redirecionamentos
        const redirecionamentos = <?php echo json_encode($redirecionamentos); ?>;
        
        // Função para testar um redirecionamento específico
        function testarRedirecionamento(index, dadosForm) {
            const redirecionamento = redirecionamentos[index];
            const resultadoDiv = document.getElementById('resultado_' + index);
            
            // Exibir resultados como "Em andamento..."
            resultadoDiv.className = 'test-result info';
            resultadoDiv.style.display = 'block';
            resultadoDiv.innerHTML = '<p>Testando redirecionamento, aguarde...</p>';
            
            // Para testes reais, você pode usar um iframe ou uma requisição real
            // Neste exemplo, apenas simulamos o resultado para demonstração
            if (redirecionamento.metodo === 'POST') {
                // Criar um iframe oculto para enviar o formulário
                const iframeId = 'iframe_' + index;
                let iframe = document.getElementById(iframeId);
                
                if (!iframe) {
                    iframe = document.createElement('iframe');
                    iframe.id = iframeId;
                    iframe.name = iframeId;
                    document.body.appendChild(iframe);
                }
                
                // Criar um formulário temporário
                const formId = 'temp_form_' + index;
                let form = document.getElementById(formId);
                
                if (!form) {
                    form = document.createElement('form');
                    form.id = formId;
                    form.method = redirecionamento.metodo;
                    form.action = redirecionamento.url_origem;
                    form.target = iframeId;
                    form.style.display = 'none';
                    document.body.appendChild(form);
                } else {
                    form.innerHTML = '';
                }
                
                // Adicionar os dados do formulário
                for (const key in dadosForm) {
                    if (dadosForm.hasOwnProperty(key)) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = key;
                        input.value = dadosForm[key];
                        form.appendChild(input);
                    }
                }
                
                // Adicionar campo de teste para evitar inserções reais no banco
                const testInput = document.createElement('input');
                testInput.type = 'hidden';
                testInput.name = '_test_mode';
                testInput.value = 'true';
                form.appendChild(testInput);
                
                // Configurar manipulador de eventos para o iframe
                iframe.onload = function() {
                    try {
                        // Tentar obter a URL final após redirecionamento
                        const finalUrl = iframe.contentWindow.location.href;
                        
                        // Verificar se a URL final corresponde à esperada
                        if (finalUrl.includes(redirecionamento.url_esperada)) {
                            resultadoDiv.className = 'test-result success';
                            resultadoDiv.innerHTML = '<p><strong>Sucesso!</strong> Redirecionado para: ' + finalUrl + '</p>';
                        } else {
                            resultadoDiv.className = 'test-result error';
                            resultadoDiv.innerHTML = '<p><strong>Falha!</strong> Redirecionado para: ' + finalUrl + '<br>Esperado: ' + redirecionamento.url_esperada + '</p>';
                        }
                    } catch (e) {
                        // Erro de segurança ao acessar iframe (política de mesma origem)
                        resultadoDiv.className = 'test-result error';
                        resultadoDiv.innerHTML = '<p><strong>Erro:</strong> Não foi possível verificar o redirecionamento devido a restrições de segurança do navegador.</p>';
                    }
                };
                
                // Enviar o formulário
                form.submit();
            } else {
                // Para método GET, podemos usar fetch
                fetch(redirecionamento.url_origem)
                    .then(response => {
                        if (response.redirected) {
                            if (response.url.includes(redirecionamento.url_esperada)) {
                                resultadoDiv.className = 'test-result success';
                                resultadoDiv.innerHTML = '<p><strong>Sucesso!</strong> Redirecionado para: ' + response.url + '</p>';
                            } else {
                                resultadoDiv.className = 'test-result error';
                                resultadoDiv.innerHTML = '<p><strong>Falha!</strong> Redirecionado para: ' + response.url + '<br>Esperado: ' + redirecionamento.url_esperada + '</p>';
                            }
                        } else {
                            resultadoDiv.className = 'test-result error';
                            resultadoDiv.innerHTML = '<p><strong>Falha!</strong> Não houve redirecionamento.</p>';
                        }
                    })
                    .catch(error => {
                        resultadoDiv.className = 'test-result error';
                        resultadoDiv.innerHTML = '<p><strong>Erro:</strong> ' + error.message + '</p>';
                    });
            }
        }
    </script>
</body>
</html> 