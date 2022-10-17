<!DOCTYPE HTML>

<?php
session_start();

//PERM
require("./assets/php/scripts/perm.php");
//CONNECTION
require("./connection/connection.php");
//FUNCTIONS
require("./assets/php/scripts/functions.php");
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
    <link rel="stylesheet" href="assets/css/main.css" />
    <noscript>
        <link rel="stylesheet" href="assets/css/noscript.css" />
    </noscript>

</head>

<body class="is-prload landing">
    <div id="page-wrapper">
        <!-- content -->
        <?php 
        include("assets/php/includes/pages/services/header/header.php");
        include("assets/php/includes/pages/services/sections/tabela.php");
        include("assets/php/includes/pages/services/sections/info.php");
        include("assets/php/includes/pages/services/footer/footer.php");
        ?>
    </div>

    <!-- Scripts -->
    <script src="assets/js/global/jquery.min.js"></script>
    <script src="assets/js/global/browser.min.js"></script>
    <script src="assets/js/global/breakpoints.min.js"></script>
    <script src="assets/js/global/util.js"></script>
    <script src="assets/js/main.js"></script>
</body>

</html>