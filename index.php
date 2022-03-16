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
        <!-- Header -->
        <header id="header">
            <h1 id="logo">
                <a href="index.php"><img src="./assets/css/images/logo-branco-crop.png" style="height:60px;width:180px;"></a>
            </h1>
            <nav id="nav">
                <ul>
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="index.php">Sobre nossos serviços</a></li>
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
                        echo '<a class="button primary" href="assets/php/scripts/log-out.php">Sair</a>';
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
                    <p>Oficina multimarcas incluindo HD.<br /> Entre em contato conosco e conheça nossos serviços.</p>
                </header>
                <span class="image"><img src="./assets/css/images/Close.jpg" alt="" /></span>
            </div>
            <a href="#one" class="goto-next scrolly">Proximo</a>
        </section>

        <!-- One -->
        <section id="one" class="spotlight style1 bottom">
            <span class="image fit main"><img src="./assets/css/images/V Strom.jpg" alt="" /></span>
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
            <a href="#two" class="goto-next scrolly">Proximo</a>
        </section>

        <!-- Two -->
        <section id="two" class="spotlight style2 right">
            <span class="image fit main"><img src="./assets/css/images/motor v-strom.jpg" alt="" /></span>
            <div class="content">
                <header>
                    <h2>Canal de videos no youtube</h2>
                    <a href="https://www.youtube.com/channel/UC_rUL6tWuwx-iACNG_uHZVA?sub_confirmation=1" target="_blank">Acesse o canal</a>
                </header>
                <p>Mostramos os nossos serviços de manutenção e estética em motocicletas com nosso trabalho, especialização, ferramentas e tecnologias.</p>
                <ul class="actions">
                    <li><a href="https://www.youtube.com/channel/UC_rUL6tWuwx-iACNG_uHZVA?sub_confirmation=1" target="_blank" class="button">Inscreva-se</a></li>
                </ul>
            </div>
            <a href="#three" class="goto-next scrolly">Proximo</a>
        </section>

        <!-- Three -->
        <section id="three" class="spotlight style3 left">
            <span class="image fit main bottom">
                <img src="./assets/css/images/insta-image.jpg" alt="" />
            </span>
            <div class="content">
                <header>
                    <!-- <div id="instafeed"></div> -->
                    <h2>Conheça nosso Instagram</h2>
                    <p>Fotos do nosso trabalho.</p>
                </header>
                <p>Nos siga no instagram e fique por dentro de todas novidades.</p>
                <ul class="actions">
                    <li><a href="https://www.instagram.com/xandov/" target="_blank" class="button">Seguir</a></li>
                </ul>
            </div>
            <a href="#subriderfeed" class="goto-next scrolly">Proximo</a>
        </section>

        <!-- Instagram feed -->
        <section id="subriderfeed" class="wrapper style2 special fade">
            <div class="container">
                <h2>Instagram Subrider</h2>
                <div id="instafeed" class="owl-carousel owl-theme owl-loaded owl-drag"></div>
            </div>
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
                            <span class="icon solid alt major fa-clock-rotate-left"></span>
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
                        <li><a href="contato.php" class="button">Entrar em contato</a></li>
                    </ul>
                </footer>
            </div>
        </section>

        <!-- Five -->
        <!--
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
        </section> -->

        <!-- Footer -->
        <footer id="footer">
            <ul class="icons">
                <li><a href="#" target="_blank" class="icon brands alt fa-twitter"><span class="label">Twitter</span></a></li>
                <li><a href="#" target="_blank" class="icon brands alt fa-facebook-f"><span class="label">Facebook</span></a></li>
                <li><a href="https://www.youtube.com/channel/UC_rUL6tWuwx-iACNG_uHZVA?sub_confirmation=1" target="_blank" class="icon brands alt fa-youtube"><span class="label">Youtube</span></a></li>
                <li><a href="https://www.instagram.com/xandov/" target="_blank" class="icon brands alt fa-instagram"><span class="label">Instagram</span></a></li>
                <li><a href="#" target="_blank" class="icon brands alt fa-github"><span class="label">GitHub</span></a></li>
                <li><a href="#" target="_blank" class="icon solid alt fa-envelope"><span class="label">Email</span></a></li>
            </ul>
            <ul class="copyright">
                <li>&copy; Whatsapp: (61) 98128-2136.</li>
                <li>Fale com: <a href="http://html5up.net">Robson Alexandre</a></li>
            </ul>
        </footer>

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
    <script type="text/javascript" src="assets/js/instafeed.min.js"></script>
    <script type="text/javascript">
        var feed = new Instafeed({
            accessToken: 'IGQVJYV25ROUFndzlXQWlkMDd0VDJmU2lLWW9YZADI2RzM0QW1NQjhBSU9ubTEtLUtyT21xSnhsWG5vbWh0U1BtSzcyODhQemtTN1NyM0tpTlpUUS15ckZAKR051emVRWjM1ckdCWS1UOWhWZAmpQUHJJXwZDZD',
            limit: 21,
            template: '<div class="item"><a href="{{link}}" target="_blank"><img title="{{caption}}" src="{{image}}" /></a></div>',
            after: function() {
                $(".owl-carousel").owlCarousel();
            }
        });
        feed.run();
    </script>

</body>

</html>