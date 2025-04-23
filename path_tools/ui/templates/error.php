<!DOCTYPE html>
<html>
<head>
    <title>Path Tools - Erro</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .error-container {
            padding: 20px;
            background-color: #f8d7da;
            border-left: 5px solid #dc3545;
            margin-bottom: 20px;
        }
        .error-title {
            color: #721c24;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .error-message {
            color: #721c24;
        }
        .back-button {
            background-color: #95a5a6;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            display: inline-block;
        }
        .path-tools-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        .path-tools-title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        .path-tools-actions a {
            margin-left: 15px;
            text-decoration: none;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="path-tools-header">
        <div class="path-tools-title">Path Tools - Erro</div>
        <div class="path-tools-actions">
            <a href="../path_checker.php" title="Verificador de caminhos">PathChecker</a>
            <a href="../path_editor.php" title="Editor de caminhos">PathEditor</a>
            <a href="../index.php" title="Voltar para a página inicial">Início</a>
        </div>
    </div>

    <div class="error-container">
        <div class="error-title">Erro</div>
        <div class="error-message"><?php echo htmlspecialchars($message); ?></div>
    </div>

    <a href="javascript:history.back()" class="back-button">Voltar</a>
</body>
</html> 