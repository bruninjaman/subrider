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
    <link rel="stylesheet" href="assets/css/ordemservico.css" />
    <link rel="stylesheet" href="assets/css/main.css" />
    <noscript>
        <link rel="stylesheet" href="assets/css/noscript.css" />
    </noscript>

</head>

<body class="is-preload landing">
    <div id="page-wrapper">
        <!-- content -->
        <?php
        require("./pages/ordemservico/header.php");
        ?>
        <!-- <form action="scripts\ordemservico\register_medicoes.php?ordem=<?php echo (string)$_GET['ordem'] ?>" method="POST"> -->
            <input type="hidden" id="selected_option" name="selected_option" value="">
            <?php
            require("./pages/ordemservico/tabela.php")
            ?>
        <!-- </form> -->
        <?php
        require("./pages/ordemservico/info.php");
        require("./pages/ordemservico/footer.php");
        ?>
    </div>
    
    <!-- Select option script -->
    <script src="pages/ordemservico/option_selected.js"></script>
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
    <script src=".\pages\ordemservico\delete_confirm.js"></script>
    <!-- Jquery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Delete button -->
    <script src=".\pages\ordemservico\editable_data.js"></script>
    <script src=".\pages\ordemservico\editable_proprietario.js"></script>

</body>
</html>

<?php
mysqli_close($conn);
?>