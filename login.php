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
</head>

<body class="is-preload landing">
    <div id="page-wrapper">
        <!-- Header -->
        <header id="header">
            <h1 id="logo">
                <a href="index.html"><img src="./assets/css/images/logo-branco-crop.png" style="height:60px;width:180px;"></a>
            </h1>
            <nav id="nav">
                <ul>
                    <li><a href="index.php">Página Inicial</a></li>
                    <li><a href="elements.html"><a class="button primary" href="#">Cadastrar</a></a></li>
                    <li><a class="button secondary" href="index.php">Voltar</a></li>
                </ul>
            </nav>
        </header>
        <!-- one -->
        <section id="two" class="spotlight style2 right">
            <span class="image fit main"><img src="./assets/css/images/race-moto.gif" alt="" /></span>
            <div class="content">
                <header>
                    <h2>Entre na sua conta</h2>
                </header>
                <p>
                <ul>
                    <li>
                        Cadastre suas motocicletas.
                    </li>
                    <li>
                        Veja todas opções de serviços disponiveis para você.
                    </li>

                    <li>
                        Entre em contato conosco para marcar novos serviços.
                    </li>

                    <li>
                        Veja como está progredindo e a etapa em que está seu serviço.
                    </li>
                </ul>
                </p>
                <ul class="actions">
                    <li><a href="#login" class="button">Entrar</a></li>
                </ul>
            </div>
            <a href="#login" class="goto-next scrolly">Next</a>
        </section>
        <!-- Two -->
        <section id="login" class="spotlight style1 bottom">
            <div class="content">
                <div class="container">
                    <div class="row">
                        <form id="loginform" name="loginform" method="POST" action="assets/php/scripts/log-in.php">
                            <div class="col-4 col-12-medium">
                                <h2>Entre com sua conta da sub-rider!</h2>
                                <label for="fname">Login:</label>
                                <input type="text" id="user" name="user"><br>
                                <label for="lname">Senha:</label>
                                <input type="password" id="pass" name="pass"><br>
                            </div>
                            <div class="col-4 col-12-medium">
                                <a class="button primary" href='javascript:loginform.submit()'>Entrar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer id="footer">
            <ul class="icons">
                <li><a href="#" class="icon brands alt fa-twitter"><span class="label">Twitter</span></a></li>
                <li><a href="#" class="icon brands alt fa-facebook-f"><span class="label">Facebook</span></a></li>
                <li><a href="#" class="icon brands alt fa-linkedin-in"><span class="label">LinkedIn</span></a></li>
                <li><a href="#" class="icon brands alt fa-instagram"><span class="label">Instagram</span></a></li>
                <li><a href="#" class="icon brands alt fa-github"><span class="label">GitHub</span></a></li>
                <li><a href="#" class="icon solid alt fa-envelope"><span class="label">Email</span></a></li>
            </ul>
            <ul class="copyright">
                <li>&copy; Whatsapp: (61) 98128-2136.</li>
                <li>Fale com: <a href="http://html5up.net">Robson Alexandre</a></li>
            </ul>
        </footer>

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