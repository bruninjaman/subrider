<!DOCTYPE HTML>
<?php
// Inicialização da sessão
session_start();

// Definição de constantes
define('BASE_PATH', __DIR__);
define('ASSETS_PATH', BASE_PATH . '/assets');
define('INCLUDES_PATH', BASE_PATH . '/includes');

// Carregamento de arquivos essenciais
require_once("./scripts/perm.php");
require_once("./connection/connection.php");
require_once("./scripts/functions.php");

// Configuração de cabeçalhos
header('Content-Type: text/html; charset=utf-8');
?>
<!--
	Landed by HTML5 UP
	html5up.net | @ajlkn
	Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
-->
<html>

<head>
    <title>Subrider - Sistema de Ordens de Serviço</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo ASSETS_PATH; ?>/css/main.css" />
    <link rel="stylesheet" href="<?php echo ASSETS_PATH; ?>/css/search.css" />
    <link rel="stylesheet" href="<?php echo ASSETS_PATH; ?>/css/sort.css" />
    <link rel="stylesheet" href="<?php echo ASSETS_PATH; ?>/css/table.css" />
    <link rel="stylesheet" href="<?php echo ASSETS_PATH; ?>/css/fontawesome6.all.min.css" />
    
    <noscript>
        <link rel="stylesheet" href="<?php echo ASSETS_PATH; ?>/css/noscript.css" />
    </noscript>
</head>

<body class="is-preload landing">
    <div id="page-wrapper">
        <!-- content -->
        <?php 
        require("./pages/tabelaOrdens/header.php");
        require("./pages/tabelaOrdens/tabela.php");
        require("./pages/tabelaOrdens/footer.php");
    
        ?>
    </div>
    <!-- Scripts -->
    <script src="<?php echo ASSETS_PATH; ?>/js/global/jquery.min.js"></script>
    <script src="<?php echo ASSETS_PATH; ?>/js/global/jquery.scrolly.min.js"></script>
    <script src="<?php echo ASSETS_PATH; ?>/js/global/jquery.dropotron.min.js"></script>
    <script src="<?php echo ASSETS_PATH; ?>/js/global/jquery.scrollex.min.js"></script>
    <script src="<?php echo ASSETS_PATH; ?>/js/global/browser.min.js"></script>
    <script src="<?php echo ASSETS_PATH; ?>/js/global/breakpoints.min.js"></script>
    <script src="<?php echo ASSETS_PATH; ?>/js/global/util.js"></script>
    <script src="<?php echo ASSETS_PATH; ?>/js/main.js"></script>
    <script src="<?php echo ASSETS_PATH; ?>/js/ordemservico/delete_confirm.js"></script>
</body>

</html>

<?php
    mysqli_close($conn);
?>