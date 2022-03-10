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
    <noscript>
        <link rel="stylesheet" href="assets/css/noscript.css" />
    </noscript>
</head>

<body class="is-preload landing">
    <div id="page-wrapper">
        <!-- Header -->
        <header id="header">
            <h1 id="logo">
                <a href="index.html"><img src="./images/logo-branco-crop.png" style="height:60px;width:180px;"></a>
            </h1>
            <nav id="nav">
                <ul>
                    <li><a href="index.html">Inicio</a></li>
                    <li><a href="index.html">Sobre nossos serviços</a></li>
                    <?php

                    if (isset($_SESSION["user"])) {
                        echo '
                    <li>
                        <a href="#">Tabelas</a>
                        <ul>
                            <li><a href="tabelaPecas.php">Tabela de peças</a></li>
                            <li><a href="tabelaMotos.php">Tabela de motocicletas</a></li>
                            <li><a href="tabelaServicos.php">Tabela de serviços</a></li>
                            <li><a href="#">Tabela de clientes</a></li>
                        </ul>
                    </li>';
                    }
                    ?>
                    <li><a href="#">Contato</a></li>
                    <?php
                    if (isset($_SESSION["user"])) {
                        echo '<li>';
                        echo '<a class="button primary" href="scripts/php/sair.php">Sair</a>';
                        echo '</li>';
                    } else {
                        echo '<li>';
                        echo '<a class="button primary" href="login.php">Entrar</a>';
                        echo '</li><li>';
                        echo '<a class="button secondary" href="cadastrar.php">Criar conta</a>';
                        echo '</li>';
                    }
                    ?>
                </ul>
            </nav>
        </header>

        <!-- Banner -->
        <section id="banner">
            <div class="content">
                <header>
                    <h2>Mecânica Especializada em motocicletas</h2>
                    <p>Oficina de reparo, manutenção e pintura.<br /> Entre em contato conosco e conheça nossos serviços.</p>
                </header>
                <span class="image"><img src="images/Close.jpg" alt="" /></span>
            </div>
            <a href="#one" class="goto-next scrolly">Next</a>
        </section>

        <!-- One -->
        <section id="one" class="spotlight style1 bottom">
            <span class="image fit main"><img src="images/V Strom.jpg" alt="" /></span>
            <div class="content">
                <div class="container">
                    <div class="row">
                        <div class="col-4 col-12-medium" style="margin-right: 300px;">
                            <div class="mapouter">
                                <div class="gmap_canvas">
                                    <iframe width="600" height="500" id="gmap_canvas" src="https://maps.google.com/maps?q=brazil%20subrider&t=&z=17&ie=UTF8&iwloc=&output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
                                    <a href="https://123moviesz.nl"></a><br>
                                    <style>
                                        .mapouter {
                                            position: relative;
                                            text-align: right;
                                            height: 300px;
                                            width: 550px;
                                        }
                                    </style>
                                    <a href="https://googlemapsembedcodegenerator.com
											"> how to add a map to wordpress </a>
                                    <style>
                                        .gmap_canvas {
                                            overflow: hidden;
                                            background: none !important;
                                            height: 300px;
                                            width: 550px;
                                        }
                                    </style>
                                </div>
                            </div>
                        </div>
                        <div class="col-4 col-12-medium">
                            <p>Endereço da Sub-Rider, Quadra 12, conjunto L, St. Sul, Brasília - DF. CEP 72415-612. Whatsapp: (61) 98128-2136 </p>
                        </div>
                    </div>
                </div>
            </div>
            <a href="#two" class="goto-next scrolly">Next</a>
        </section>

        <!-- Two -->
        <section id="two" class="spotlight style2 right">
            <span class="image fit main"><img src="images/motor v-strom.jpg" alt="" /></span>
            <div class="content">
                <header>
                    <h2>Canal de videos no youtube</h2>
                    <p>O link será disponibilizado em breve</p>
                </header>
                <p>Mostramos os nossos serviços de manutenção e estética em motocicletas. E também nosso trabalho especialização/ferramentas e tecnologias.</p>
                <ul class="actions">
                    <li><a href="#" class="button">Inscreva-se</a></li>
                </ul>
            </div>
            <a href="#three" class="goto-next scrolly">Next</a>
        </section>

        <!-- Three -->
        <section id="three" class="spotlight style3 left">
            <span class="image fit main bottom"><img src="images/pic04.jpg" alt="" /></span>
            <div class="content">
                <header>
                    <h2>Conheça nosso instagram</h2>
                    <p>fotos e resultados de clientes satisfeitos com nosso trabalho</p>
                </header>
                <p>Nos siga no instagram e fique por dentro de todas novidades.</p>
                <ul class="actions">
                    <li><a href="#" class="button">Seguir</a></li>
                </ul>
            </div>
            <a href="#four" class="goto-next scrolly">Next</a>
        </section>

        <!-- Four -->


        <section id="four" class="wrapper style1 special fade-up">
            <div class="container">
                <header class="major">
                    <h2>Oferecemos serviço de manutenção em várias marcas de motocicletas</h2>
                    <p>Veja as variadas opções de serviços</p>
                </header>
                <div class="box alt">
                    <div class="row gtr-uniform">
                        <section class="col-4 col-6-medium col-12-xsmall">
                            <span class="icon solid alt major fa-trailer"></span>
                            <h3>Trasnsporte/Reboque</h3>
                            <p>Transporte guincho para a oficina e para a residência do cliente.</p>
                        </section>
                        <section class="col-4 col-6-medium col-12-xsmall">
                            <span class="icon solid alt major fa-hammer"></span>
                            <h3>Manutenção Preventiva</h3>
                            <p>Manutenção periódica conforme a tabela do fabricante ou por níveis: básico, intermediário e avançado.</p>
                        </section>
                        <section class="col-4 col-6-medium col-12-xsmall">
                            <span class="icon solid alt major fa-gear"></span>
                            <h3>Manutenção Corretiva</h3>
                            <p>Reparo mecânico, elétrico, eletrônico e estético e motocicletas de 1 a 6 cilindros.</p>
                        </section>
                        <section class="col-4 col-6-medium col-12-xsmall">
                            <span class="icon solid alt major fa-broom-ball"></span>
                            <h3>Manutenção Estética</h3>
                            <p>Pintura, polimento, recuperação de peças cromadas, soldas, etc.</p>
                        </section>
                        <section class="col-4 col-6-medium col-12-xsmall">
                            <span class="icon solid alt major fa-kit-medical"></span>
                            <h3>Manutenção Emergencial</h3>
                            <p>Atendimento de emergência para motocicletas imobilizadas.</p>
                        </section>
                        <section class="col-4 col-6-medium col-12-xsmall">
                            <span class="icon solid alt major fa-coins"></span>
                            <h3>Manutenção Econômica</h3>
                            <p>Busca por alternativas seguras de redução de custos de manutenção.</p>
                        </section>
                    </div>
                </div>
                <footer class="major">
                    <ul class="actions special">
                        <li><a href="#" class="button">Entrar em contato</a></li>
                    </ul>
                </footer>
            </div>
        </section>

        <!-- Five -->
        <section id="five" class="wrapper style2 special fade">
            <div class="container">
                <header>
                    <h2>Começe o seu cadastro em e veja mais opções de serviços e manutenções</h2>
                    <p>Criando sua conta em nosso site</p>
                </header>
                <form method="post" action="#" class="cta">
                    <div class="row gtr-uniform gtr-50">
                        <div class="col-8 col-12-xsmall"><input type="email" name="email" id="email" placeholder="Digite seu endereço de email" /></div>
                        <div class="col-4 col-12-xsmall"><input type="submit" value="Começar" class="fit primary" /></div>
                    </div>
                </form>
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