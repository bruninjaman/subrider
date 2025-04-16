<!DOCTYPE HTML>

<?php
// Caminho absoluto para config.php
require_once(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config.php');
session_start();

//PERM
require_once(PROJECT_ROOT_PATH . DS . "scripts" . DS . "perm.php");
//CONNECTION
require_once(PROJECT_ROOT_PATH . DS . "connection" . DS . "connection.php");
//FUNCTIONS
require_once(PROJECT_ROOT_PATH . DS . "scripts" . DS . "functions.php");
?>
<!--
	Landed by HTML5 UP
	html5up.net | @ajlkn
	Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
-->
<html>

<head>
    <title>Subrider</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <link rel="stylesheet" href="<?php echo PROJECT_ROOT_URL; ?>/assets/css/main.css" />
    <link rel="stylesheet" href="<?php echo PROJECT_ROOT_URL; ?>/assets/css/search.css" />
    <link rel="stylesheet" href="<?php echo PROJECT_ROOT_URL; ?>/assets/css/sort.css" />
    <link rel="stylesheet" href="<?php echo PROJECT_ROOT_URL; ?>/assets/css/table.css" />
    <link rel="stylesheet" href="<?php echo PROJECT_ROOT_URL; ?>/assets/css/fontawesome6.all.min.css" />  
    <noscript>
        <link rel="stylesheet" href="<?php echo PROJECT_ROOT_URL; ?>/assets/css/noscript.css" />
    </noscript>
</head>

<body class="is-preload landing">
    <div id="page-wrapper">
        <!-- content -->
        <?php 
        require("./pages/{$pageType}/header.php");
        require("./pages/{$pageType}/tabela.php");
        require("./pages/{$pageType}/footer.php");
        ?>
    </div>
    <!-- Scripts for main theme -->
    <script src="<?php echo PROJECT_ROOT_URL; ?>/assets/js/global/jquery.min.js"></script>
    <script src="<?php echo PROJECT_ROOT_URL; ?>/assets/js/global/jquery.scrolly.min.js"></script>
    <script src="<?php echo PROJECT_ROOT_URL; ?>/assets/js/global/jquery.dropotron.min.js"></script>
    <script src="<?php echo PROJECT_ROOT_URL; ?>/assets/js/global/jquery.scrollex.min.js"></script>
    <script src="<?php echo PROJECT_ROOT_URL; ?>/assets/js/global/browser.min.js"></script>
    <script src="<?php echo PROJECT_ROOT_URL; ?>/assets/js/global/breakpoints.min.js"></script>
    <script src="<?php echo PROJECT_ROOT_URL; ?>/assets/js/global/util.js"></script>
    <script src="<?php echo PROJECT_ROOT_URL; ?>/assets/js/main.js"></script>
    <script src="<?php echo PROJECT_ROOT_URL; ?>/assets/js/delete_confirm.js"></script>
</body>

</html>

<?php
    mysqli_close($conn);
?> 