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
        require("./pages/index/header.php");
        require("./pages/index/banner.php");
        require("./pages/index/mapa.php");
        require("./pages/index/youtube.php");
        require("./pages/index/instagram-1.php");
        require("./pages/index/instagram-2-feed.php");
        require("./pages/index/sobre.php");
        require("./pages/index/footer.php");
        ?>

    </div>

    <!-- Scripts -->
    <script src="assets/js/global/jquery.min.js"></script>
    <script src="assets/js/index/owl.carousel.min.js"></script>
    <script src="assets/js/global/jquery.scrolly.min.js"></script>
    <script src="assets/js/global/jquery.dropotron.min.js"></script>
    <script src="assets/js/global/jquery.scrollex.min.js"></script>
    <script src="assets/js/global/browser.min.js"></script>
    <script src="assets/js/global/breakpoints.min.js"></script>
    <script src="assets/js/global/util.js"></script>
    <script src="assets/js/main.js"></script>
    <!-- Instagram feed script -->
    <script type="text/javascript" src="assets/js/index/instafeed.min.js"></script>
    <!-- <script type="text/javascript" src="assets/js/index/instafeed.js"></script> -->
    <!-- fancybox script -->
    <script type="text/javascript" src="assets/js/index/jquery.fancybox.min.js"></script>

</body>

</html>