<!DOCTYPE HTML>

<?php
session_start();

//PERM
require_once("./scripts/perm.php");
//CONNECTION
require_once("./connection/connection.php");
//FUNCTIONS
require_once("./scripts/functions.php");
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
    <link rel="stylesheet" href="assets/css/search.css" />
    <link rel="stylesheet" href="assets/css/fontawesome6.all.min.css" />  
    <noscript>
        <link rel="stylesheet" href="assets/css/noscript.css" />
    </noscript>
</head>

<body class="is-preload landing">
    <div id="page-wrapper">
        <!-- content -->
        <?php 
        require("./pages/tabelaOrdens/header/header.php");
        require("./pages/tabelaOrdens/sections/tabela.php");
        require("./pages/tabelaOrdens/footer/footer.php");
    
        ?>
    </div>
    <!-- Scripts for main theme -->
    <script src="assets/js/global/jquery.min.js"></script>
    <script src="assets/js/global/jquery.scrolly.min.js"></script>
    <script src="assets/js/global/jquery.dropotron.min.js"></script>
    <script src="assets/js/global/jquery.scrollex.min.js"></script>
    <script src="assets/js/global/browser.min.js"></script>
    <script src="assets/js/global/breakpoints.min.js"></script>
    <script src="assets/js/global/util.js"></script>
    <script src="assets/js/main.js"></script>
    

    <!-- Delete button -->
    <script src=".\pages\tabelaMotos\sections\delete_confirm.js"></script>
</body>

</html>

<?php
    mysqli_close($conn);
?>