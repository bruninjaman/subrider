<!DOCTYPE HTML>

<?php
session_start();
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
    <link rel="stylesheet" href="assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="assets/css/owl.theme.default.min.css">
    <noscript>
        <link rel="stylesheet" href="assets/css/noscript.css" />
    </noscript>

</head>

<body class="is-preload landing">
    <div id="page-wrapper">
        <?php
        require("./assets/php/includes/pages/index/header/header.php");
        require("./assets/php/includes/pages/index/sections/banner.php");
        require("./assets/php/includes/pages/index/sections/mapa.php");
        require("./assets/php/includes/pages/index/sections/youtube.php");
        require("./assets/php/includes/pages/index/sections/instagram-1.php");
        require("./assets/php/includes/pages/index/sections/instagram-2-feed.php");
        require("./assets/php/includes/pages/index/sections/sobre.php");
        require("./assets/php/includes/main/footer.php");
        ?>

    </div>

    <!-- Scripts -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/owl.carousel.min.js"></script>
    <script src="assets/js/jquery.scrolly.min.js"></script>
    <script src="assets/js/jquery.dropotron.min.js"></script>
    <script src="assets/js/jquery.scrollex.min.js"></script>
    <script src="assets/js/browser.min.js"></script>
    <script src="assets/js/breakpoints.min.js"></script>
    <script src="assets/js/util.js"></script>
    <script src="assets/js/main.js"></script>
    <!-- Instagram feed script -->
    <script type="text/javascript" src="assets/js/index/instafeed.min.js"></script>
    <script type="text/javascript" src="assets/js/index/instafeed.js"></script>

</body>

</html>