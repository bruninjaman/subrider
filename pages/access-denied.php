<?php
require_once __DIR__ . '/../../config/init.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso Negado - Subrider</title>
    <link rel="stylesheet" href="../assets/css/main.css" />
    <style>
        .access-denied {
            text-align: center;
            padding: 50px;
            margin: 100px auto;
            max-width: 600px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .access-denied h1 {
            color: #e74c3c;
            margin-bottom: 20px;
            font-size: 2.5em;
        }
        .access-denied p {
            margin-bottom: 30px;
            font-size: 1.2em;
            line-height: 1.6;
            color: #555;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #3498db;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.3s ease;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .btn:hover {
            background: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        .error-details {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
            font-size: 0.9em;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="access-denied">
        <h1>Acesso Negado</h1>
        <p>Desculpe, mas o acesso direto a esta página não é permitido por questões de segurança.</p>
        <p>Por favor, acesse o sistema através da página principal.</p>
        
        <div class="error-details">
            <p>Detalhes do erro:</p>
            <p>Data/Hora: <?php echo date('d/m/Y H:i:s'); ?></p>
            <p>IP: <?php echo $_SERVER['REMOTE_ADDR']; ?></p>
        </div>
        
        <a href="/" class="btn">Voltar para a página principal</a>
    </div>
</body>
</html> 