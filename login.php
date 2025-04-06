<!DOCTYPE HTML>
<!--
	Landed by HTML5 UP
	html5up.net | @ajlkn
	Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
-->
<html>

<head>
    <title>login</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <link rel="stylesheet" href="assets/css/main.css" />
    <noscript>
        <link rel="stylesheet" href="assets/css/noscript.css" />
    </noscript>
    <style>
        .error-message {
            color: #ff3333;
            background-color: rgba(255, 51, 51, 0.1);
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>

<body class="is-preload landing">
    <div id="page-wrapper">
        <?php
        // Tratamento de erros de login
        $error_message = '';
        if (isset($_GET['error'])) {
            switch ($_GET['error']) {
                case '1':
                    $error_message = 'Usuário ou senha incorretos.';
                    break;
                case 'blocked':
                    $minutes = isset($_GET['time']) ? (int)$_GET['time'] : 30;
                    $error_message = "Conta temporariamente bloqueada. Tente novamente em {$minutes} minutos.";
                    break;
                case 'system':
                    $error_message = 'Erro no sistema. Por favor, tente novamente mais tarde.';
                    break;
            }
        }
        
        require("./pages/login/header.php");
        if ($error_message) {
            echo "<div class='error-message'>$error_message</div>";
        }
        require("./pages/login/login.php");
        require("./pages/login/footer.php");
        ?>

    </div>

    <!-- Scripts -->
    <script src="assets/js/global/jquery.min.js"></script>
    <script src="assets/js/global/jquery.scrolly.min.js"></script>
    <script src="assets/js/global/jquery.dropotron.min.js"></script>
    <script src="assets/js/global/jquery.scrollex.min.js"></script>
    <script src="assets/js/global/browser.min.js"></script>
    <script src="assets/js/global/breakpoints.min.js"></script>
    <script src="assets/js/global/util.js"></script>
    <script src="assets/js/main.js"></script>

</body>

</html>