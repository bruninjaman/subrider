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
    <link rel="stylesheet" href="assets/css/mobile-navbar.css" />
    <noscript>
        <link rel="stylesheet" href="assets/css/noscript.css" />
    </noscript>
</head>

<body class="is-preload landing">
    <div id="page-wrapper">
        <?php
        session_start();
        require("./includes/mobile-navbar.php");
        require("./pages/login/header.php");
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
    <script src="assets/js/mobile-navbar.js"></script>

</body>

</html>