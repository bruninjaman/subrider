<?php
session_start();
require_once './vendor/autoload.php';

// Load environment variables from .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Usar variável de ambiente
$baseAddress = $_ENV['BASE_ADDRESS'] ?? '';

require_once('./config.php');

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . $baseAddress . '/login.php');
    exit;
}

// Se for um POST, processar a criação da ordem
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_moto = filter_input(INPUT_POST, 'id_moto', FILTER_SANITIZE_NUMBER_INT);
    $data_entrada = filter_input(INPUT_POST, 'data_entrada', FILTER_SANITIZE_STRING);
    $descricao = filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_STRING);
    $km = filter_input(INPUT_POST, 'km', FILTER_SANITIZE_NUMBER_INT);
    
    if (!$id_moto || !$data_entrada) {
        $erro = "Por favor, preencha todos os campos obrigatórios.";
    } else {
        // Criar a ordem de serviço
        $sql = "INSERT INTO ordem_servicos (motoID, Data, KM, Status) VALUES (?, ?, ?, 'Aberta')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isi", $id_moto, $data_entrada, $km);
        
        if ($stmt->execute()) {
            $ordem_id = $stmt->insert_id;
            header('Location: ' . $baseAddress . '/ordemservico.php?ordem=' . $ordem_id);
            exit;
        } else {
            $erro = "Erro ao criar ordem de serviço: " . $conn->error;
        }
    }
}

// Buscar lista de motos para o select
$sql_motos = "SELECT m.motoId, m.modelo, m.placa, p.nome as proprietario 
              FROM motocicletas m 
              JOIN proprietarios p ON m.id = p.id 
              ORDER BY p.nome, m.modelo";
$result_motos = $conn->query($sql_motos);
?>

<!DOCTYPE HTML>
<html>
<head>
    <title>Nova Ordem de Serviço - Subrider</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <link rel="stylesheet" href="<?php echo $baseAddress; ?>/assets/css/main.css" />
    <noscript><link rel="stylesheet" href="<?php echo $baseAddress; ?>/assets/css/noscript.css" /></noscript>
    <style>
        .error-message {
            color: #ff0000;
            margin-bottom: 1em;
            padding: 1em;
            background-color: #ffe6e6;
            border: 1px solid #ff9999;
            border-radius: 4px;
        }
        .form-section {
            margin-bottom: 2em;
        }
        .required-field::after {
            content: " *";
            color: red;
        }
    </style>
</head>

<body class="is-preload landing">
    <div id="page-wrapper">
        <!-- Header -->
        <?php include './pages/ordemservico/header.php'; ?>

        <!-- Main -->
        <div id="main" class="wrapper style1">
            <div class="container">
                <header class="major">
                    <h2>Nova Ordem de Serviço</h2>
                    <p>Preencha os dados abaixo para criar uma nova ordem de serviço</p>
                </header>

                <?php if (isset($erro)): ?>
                <div class="error-message">
                    <?php echo $erro; ?>
                </div>
                <?php endif; ?>

                <section>
                    <form method="post" action="">
                        <div class="row gtr-uniform gtr-50">
                            <div class="col-12 form-section">
                                <label class="required-field" for="id_moto">Motocicleta</label>
                                <select name="id_moto" id="id_moto" required>
                                    <option value="">Selecione uma moto</option>
                                    <?php while($moto = $result_motos->fetch_assoc()): ?>
                                    <option value="<?php echo $moto['motoId']; ?>">
                                        <?php echo $moto['proprietario'] . ' - ' . $moto['modelo'] . ' (' . $moto['placa'] . ')'; ?>
                                    </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="col-6 col-12-xsmall form-section">
                                <label class="required-field" for="data_entrada">Data de Entrada</label>
                                <input type="date" name="data_entrada" id="data_entrada" value="<?php echo date('Y-m-d'); ?>" required />
                            </div>

                            <div class="col-6 col-12-xsmall form-section">
                                <label for="km">Quilometragem</label>
                                <input type="number" name="km" id="km" placeholder="KM atual da moto" min="0" />
                            </div>

                            <div class="col-12 form-section">
                                <label for="descricao">Descrição do Serviço</label>
                                <textarea name="descricao" id="descricao" placeholder="Descreva o serviço a ser realizado" rows="4"></textarea>
                            </div>

                            <div class="col-12">
                                <ul class="actions">
                                    <li><input type="submit" value="Criar Ordem de Serviço" class="primary" /></li>
                                    <li><a href="<?php echo $baseAddress; ?>/ordemservico.php" class="button">Cancelar</a></li>
                                </ul>
                            </div>
                        </div>
                    </form>
                </section>
            </div>
        </div>

        <!-- Footer -->
        <?php include './pages/ordemservico/footer.php'; ?>
    </div>

    <!-- Scripts -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/jquery.scrolly.min.js"></script>
    <script src="assets/js/jquery.dropotron.min.js"></script>
    <script src="assets/js/jquery.scrollex.min.js"></script>
    <script src="assets/js/browser.min.js"></script>
    <script src="assets/js/breakpoints.min.js"></script>
    <script src="assets/js/util.js"></script>
    <script src="assets/js/main.js"></script>

</body>
</html>
<?php
mysqli_close($conn);
?>