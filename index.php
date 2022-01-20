<!DOCTYPE html>
<?php session_start();
?>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <title>Sub-rider</title>
   <link rel='stylesheet' href='https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css'>
   <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css'>
   <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous" />
   <link rel="stylesheet" href="./style.css">
</head>

<body>
   <!-- partial:index.partial.html -->

   <body id="page-top" class="index" data-pinterest-extension-installed="cr1.3.4">
      <!-- Navigation -->
      <nav class="navbar navbar-default navbar-fixed-top navbar-shrink">
         <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header page-scroll">
               <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                  <span class="sr-only">Toggle navigation</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
               </button>
               <img src="./imgs/logo-branco.png" style="height:60px;width:180px;">
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
               <ul class="nav navbar-nav navbar-right">
                  <li class="hidden active">
                     <a href="#page-top"></a>
                  </li>
                  <?php
                  if (isset($_SESSION["user"])) {
                     echo '<li class="">
                            <a class="page-scroll" href="gerenciamento.php">Gerenciamento</a>
                            </li>';
                  }
                  ?>
                  <li class="">
                     <a class="page-scroll" href="#services">Serviços</a>
                  </li>
                  <li class="">
                     <a class="page-scroll" href="#portfolio">Feed Instagram</a>
                  </li>
                  <!--
                        <li class="">
                            <a class="page-scroll" href="#about">About</a>
                        </li>
                        <li class="">
                            <a class="page-scroll" href="#team">Team</a>
                        </li> !-->
                  <li class="">
                     <a class="page-scroll" href="#contact">Contato</a>
                  </li>
                  <li class="">
                     <?php
                     if (isset($_SESSION["user"])) {
                        echo '<a class="page-scroll loginicon" href="sairscript.php">Sair</a>';
                     } else {
                        echo '<a class="page-scroll loginicon" href="login.php">Login</a>';
                     }
                     ?>
                  </li>
               </ul>
            </div>
            <!-- /.navbar-collapse -->
         </div>
         <!-- /.container-fluid -->
      </nav>
      <!-- Header -->
      <header>
         <div class="container">
            <div class="intro-text">
               <!-- 
                     <div class="intro-lead-in">Mecanica de motocicletas</div>
                     -->
               <div class="intro-heading"></div>
            </div>
         </div>
      </header>
      <!-- Services Section -->
      <section id="services">
         <div class="container">
            <div class="row">
               <div class="col-lg-12 text-center">
                  <h2 class="section-heading">Serviços</h2>
                  <h3 class="section-subheading text-muted">Mecânica especializada de motocicletas.</h3>
               </div>
            </div>
            <div class="row text-center">
               <div class="col-md-4">
                  <span class="fa-stack fa-4x">
                     <i class="fa fa-circle fa-stack-2x text-primary"></i>
                     <i class="fas fa-truck-couch" style="position:relative;"></i>
                  </span>
                  <h4 class="service-heading">Reboque</h4>
                  <p class="text-muted">Transporte guincho para a oficina e para a residência do cliente.</p>
               </div>
               <div class="col-md-4">
                  <span class="fa-stack fa-4x">
                     <i class="fa fa-circle fa-stack-2x text-primary"></i>
                     <i class="glyphicon glyphicon-wrench"></i>
                  </span>
                  <h4 class="service-heading">Manutenção Preventiva</h4>
                  <p class="text-muted">Manutenção periódica conforme a tabela do fabricante ou por níveis: básico, intermediário e avançado.</p>
               </div>
               <div class="col-md-4">
                  <span class="fa-stack fa-4x">
                     <i class="fa fa-circle fa-stack-2x text-primary"></i>
                     <i class="glyphicon glyphicon-ok"></i>
                  </span>
                  <h4 class="service-heading">Manutenção Corretiva</h4>
                  <p class="text-muted">Reparo mecânico, elétrico, eletrônico e estético e motocicletas de 1 a 6 cilindros de todas as categorias e das marcas japonesas e europeias.</p>
               </div>
            </div>
         </div>
      </section>
      <!-- Portfolio Grid Section -->
      <section id="portfolio" class="bg-light-gray">
         <div class="container">
            <div class="row">
               <div class="col-lg-12 text-center">
                  <h2 class="section-heading">Feed Instagram</h2>
                  <script src="https://apps.elfsight.com/p/platform.js" defer></script>
                  <center>
                     <div class="elfsight-app-685f8634-90c1-4e64-9411-3e7ae2e33456"></div>
                  </center>
               </div>
            </div>
         </div>
      </section>
      <!-- About Section -->
      <!--
            <section id="about">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12 text-center">
                            <h2 class="section-heading">About</h2>
                            <h3 class="section-subheading text-muted">Ajmal, I need help to learn how to tweak this part. I don't want this timeline crap. Haha.</h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <ul class="timeline">
                                <li>
                                    <div class="timeline-image">
                                        <img class="img-circle img-responsive" src="img/about/1.jpg" alt="">
                                    </div>
                                    <div class="timeline-panel">
                                        <div class="timeline-heading">
                                            <h4>2009-2011</h4>
                                            <h4 class="subheading">Our Humble Beginnings</h4>
                                        </div>
                                        <div class="timeline-body">
                                            <p class="text-muted">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Sunt ut voluptatum eius sapiente, totam reiciendis temporibus qui quibusdam, recusandae sit vero unde, sed, incidunt et ea quo dolore laudantium consectetur!</p>
                                        </div>
                                    </div>
                                </li>
                                <li class="timeline-inverted">
                                    <div class="timeline-image">
                                        <img class="img-circle img-responsive" src="img/about/2.jpg" alt="">
                                    </div>
                                    <div class="timeline-panel">
                                        <div class="timeline-heading">
                                            <h4>March 2011</h4>
                                            <h4 class="subheading">An Agency is Born</h4>
                                        </div>
                                        <div class="timeline-body">
                                            <p class="text-muted">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Sunt ut voluptatum eius sapiente, totam reiciendis temporibus qui quibusdam, recusandae sit vero unde, sed, incidunt et ea quo dolore laudantium consectetur!</p>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="timeline-image">
                                        <img class="img-circle img-responsive" src="img/about/3.jpg" alt="">
                                    </div>
                                    <div class="timeline-panel">
                                        <div class="timeline-heading">
                                            <h4>December 2012</h4>
                                            <h4 class="subheading">Transition to Full Service</h4>
                                        </div>
                                        <div class="timeline-body">
                                            <p class="text-muted">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Sunt ut voluptatum eius sapiente, totam reiciendis temporibus qui quibusdam, recusandae sit vero unde, sed, incidunt et ea quo dolore laudantium consectetur!</p>
                                        </div>
                                    </div>
                                </li>
                                <li class="timeline-inverted">
                                    <div class="timeline-image">
                                        <img class="img-circle img-responsive" src="img/about/4.jpg" alt="">
                                    </div>
                                    <div class="timeline-panel">
                                        <div class="timeline-heading">
                                            <h4>July 2014</h4>
                                            <h4 class="subheading">Phase Two Expansion</h4>
                                        </div>
                                        <div class="timeline-body">
                                            <p class="text-muted">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Sunt ut voluptatum eius sapiente, totam reiciendis temporibus qui quibusdam, recusandae sit vero unde, sed, incidunt et ea quo dolore laudantium consectetur!</p>
                                        </div>
                                    </div>
                                </li>
                                <li class="timeline-inverted">
                                    <div class="timeline-image">
                                        <h4>Be Part
                                            <br>Of Our
                                            <br>Story!</h4>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>
            -->
      <!-- Team Section -->
      <!--
            <section id="team" class="bg-light-gray">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12 text-center">
                            <h2 class="section-heading">Our Amazing Team</h2>
                            <h3 class="section-subheading text-muted">Lorem ipsum dolor sit amet consectetur.</h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="team-member">
                                <img src="http://www.mycatspace.com/wp-content/uploads/2013/08/adopting-a-cat.jpg" class="img-responsive img-circle" alt="">
                                <h4>Kay Garland</h4>
                                <p class="text-muted">Lead Designer</p>
                                <ul class="list-inline social-buttons">
                                    <li><a href="#"><i class="fa fa-twitter"></i></a>
                                    </li>
                                    <li><a href="#"><i class="fa fa-facebook"></i></a>
                                    </li>
                                    <li><a href="#"><i class="fa fa-linkedin"></i></a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="team-member">
                                <img src="http://www.mycatspace.com/wp-content/uploads/2013/08/adopting-a-cat.jpg" class="img-responsive img-circle" alt="">
                                <h4>Larry Parker</h4>
                                <p class="text-muted">Lead Marketer</p>
                                <ul class="list-inline social-buttons">
                                    <li><a href="#"><i class="fa fa-twitter"></i></a>
                                    </li>
                                    <li><a href="#"><i class="fa fa-facebook"></i></a>
                                    </li>
                                    <li><a href="#"><i class="fa fa-linkedin"></i></a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="team-member">
                                <img src="http://www.mycatspace.com/wp-content/uploads/2013/08/adopting-a-cat.jpg" class="img-responsive img-circle" alt="">
                                <h4>Diana Pertersen</h4>
                                <p class="text-muted">Lead Developer</p>
                                <ul class="list-inline social-buttons">
                                    <li><a href="#"><i class="fa fa-twitter"></i></a>
                                    </li>
                                    <li><a href="#"><i class="fa fa-facebook"></i></a>
                                    </li>
                                    <li><a href="#"><i class="fa fa-linkedin"></i></a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-8 col-lg-offset-2 text-center">
                            <p class="large text-muted">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aut eaque, laboriosam veritatis, quos non quis ad perspiciatis, totam corporis ea, alias ut unde.</p>
                        </div>
                    </div>
                </div>
            </section>
            -->
      <!-- Clients Aside -->
      <section id="contact">
         <div class="container">
            <div class="row">
               <div class="col-lg-12 text-center">
                  <h2 class="section-heading"></h2>
                  <center>
                     <div class="mapouter">
                        <div class="gmap_canvas">
                           <iframe width="600" height="500" id="gmap_canvas" src="https://maps.google.com/maps?q=brazil%20subrider&t=&z=17&ie=UTF8&iwloc=&output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe><a href="https://123moviesz.nl"></a><br>
                           <style>
                              .mapouter {
                                 position: relative;
                                 text-align: right;
                                 height: 500px;
                                 width: 600px;
                              }
                           </style>
                           <a href="https://googlemapsembedcodegenerator.com
                                 "> how to add a map to wordpress </a>
                           <style>
                              .gmap_canvas {
                                 overflow: hidden;
                                 background: none !important;
                                 height: 500px;
                                 width: 600px;
                              }
                           </style>
                        </div>
                     </div>
                  </center>
               </div>
            </div>
         </div>
      </section>
      <footer>
         <div class="container">
            <div class="row">
               <div class="col-md-4">
                  <span class="copyright">
                     <h4 class="section-subheading"><i class="fab fa-whatsapp" style="position:relative;font-size: 25px;"></i> WhatsApp: (61) 98128-2136 </h4>
                  </span>
               </div>
               <div class="col-md-4">
                  <ul class="list-inline social-buttons">
                     <li><a href="#"><i class="fab fa-whatsapp"></i></a>
                     </li>
                     <li><a href="#"><i class="fab fa-instagram"></i></a>
                     </li>
                     <li><a href="#"><i class="fab fa-youtube"></i></a>
                     </li>
                  </ul>
               </div>
               <!--
                  <div class="col-md-4">
                     <ul class="list-inline quicklinks">
                        <li><a href="#">Politica de Privacidade</a>
                        </li>
                        <li><a href="#">Termos de uso</a>
                        </li>
                     </ul>
                  </div>
				  -->
            </div>
         </div>
      </footer>
      <!-- Portfolio Modals -->
      <!-- Use the modals below to showcase details about your portfolio projects! -->
      <!-- Portfolio Modal 1 -->
      <div class="portfolio-modal modal fade" id="portfolioModal1" tabindex="-1" role="dialog" aria-hidden="true">
         <div class="modal-content">
            <div class="close-modal" data-dismiss="modal">
               <div class="lr">
                  <div class="rl">
                  </div>
               </div>
            </div>
            <div class="container">
               <div class="row">
                  <div class="col-lg-8 col-lg-offset-2">
                     <div class="modal-body">
                        <!-- Project Details Go Here -->
                        <h2>Project Name</h2>
                        <p class="item-intro text-muted">Lorem ipsum dolor sit amet consectetur.</p>
                        <img class="img-responsive" src="https://unsplash.imgix.net/uploads%2F1411419068566071cef10%2F7562527b?q=75&w=1080&h=1080&fit=max&fm=jpg&auto=format&s=240c45655f09c546232a6f106688e502" alt="">
                        <p>Use this area to describe your project. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Est blanditiis dolorem culpa incidunt minus dignissimos deserunt repellat aperiam quasi sunt officia expedita beatae cupiditate, maiores repudiandae, nostrum, reiciendis facere nemo!</p>
                        <p>
                           <strong>Want these icons in this portfolio item sample?</strong>You can download 60 of them for free, courtesy of <a href="https://getdpd.com/cart/hoplink/18076?referrer=bvbo4kax5k8ogc">RoundIcons.com</a>, or you can purchase the 1500 icon set <a href="https://getdpd.com/cart/hoplink/18076?referrer=bvbo4kax5k8ogc">here</a>.
                        </p>
                        <ul class="list-inline">
                           <li>Date: July 2014</li>
                           <li>Client: Round Icons</li>
                           <li>Category: Graphic Design</li>
                        </ul>
                        <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-times"></i> Close Project</button>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- Portfolio Modal 2 -->
      <div class="portfolio-modal modal fade" id="portfolioModal2" tabindex="-1" role="dialog" aria-hidden="true">
         <div class="modal-content">
            <div class="close-modal" data-dismiss="modal">
               <div class="lr">
                  <div class="rl">
                  </div>
               </div>
            </div>
            <div class="container">
               <div class="row">
                  <div class="col-lg-8 col-lg-offset-2">
                     <div class="modal-body">
                        <h2>Project Heading</h2>
                        <p class="item-intro text-muted">Lorem ipsum dolor sit amet consectetur.</p>
                        <img class="img-responsive img-centered" src="https://unsplash.imgix.net/44/9s1lvXLlSbCX5l3ZaYWP_hdr-1.jpg?q=75&w=1080&h=1080&fit=max&fm=jpg&auto=format&s=f0a1db79752dbb04ec6d2aab7d17c7b0" alt="">
                        <p><a href="https://designmodo.com/startup/?u=787">Startup Framework</a> is a website builder for professionals. Startup Framework contains components and complex blocks (PSD+HTML Bootstrap themes and templates) which can easily be integrated into almost any design. All of these components are made in the same style, and can easily be integrated into projects, allowing you to create hundreds of solutions for your future projects.</p>
                        <p>You can preview Startup Framework <a href="https://designmodo.com/startup/?u=787">here</a>.</p>
                        <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-times"></i> Close Project</button>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- Portfolio Modal 3 -->
      <div class="portfolio-modal modal fade" id="portfolioModal3" tabindex="-1" role="dialog" aria-hidden="true">
         <div class="modal-content">
            <div class="close-modal" data-dismiss="modal">
               <div class="lr">
                  <div class="rl">
                  </div>
               </div>
            </div>
            <div class="container">
               <div class="row">
                  <div class="col-lg-8 col-lg-offset-2">
                     <div class="modal-body">
                        <!-- Project Details Go Here -->
                        <h2>Project Name</h2>
                        <p class="item-intro text-muted">Lorem ipsum dolor sit amet consectetur.</p>
                        <img class="img-responsive img-centered" src="https://unsplash.imgix.net/46/Ov6ZY1zLTWmhPC0wFysP_IMG_2896_edt.jpg?q=75&w=1080&h=1080&fit=max&fm=jpg&auto=format&s=6518e4df89659818f6c0392175a9c5e6" alt="">
                        <p>Treehouse is a free PSD web template built by <a href="https://www.behance.net/MathavanJaya">Mathavan Jaya</a>. This is bright and spacious design perfect for people or startup companies looking to showcase their apps or other projects.</p>
                        <p>You can download the PSD template in this portfolio sample item at <a href="http://freebiesxpress.com/gallery/treehouse-free-psd-web-template/">FreebiesXpress.com</a>.</p>
                        <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-times"></i> Close Project</button>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- Portfolio Modal 4 -->
      <div class="portfolio-modal modal fade" id="portfolioModal4" tabindex="-1" role="dialog" aria-hidden="true">
         <div class="modal-content">
            <div class="close-modal" data-dismiss="modal">
               <div class="lr">
                  <div class="rl">
                  </div>
               </div>
            </div>
            <div class="container">
               <div class="row">
                  <div class="col-lg-8 col-lg-offset-2">
                     <div class="modal-body">
                        <!-- Project Details Go Here -->
                        <h2>Project Name</h2>
                        <p class="item-intro text-muted">Lorem ipsum dolor sit amet consectetur.</p>
                        <img class="img-responsive img-centered" src="https://unsplash.imgix.net/44/9s1lvXLlSbCX5l3ZaYWP_hdr-1.jpg?q=75&w=1080&h=1080&fit=max&fm=jpg&auto=format&s=f0a1db79752dbb04ec6d2aab7d17c7b0" alt="">
                        <p>Start Bootstrap's Agency theme is based on Golden, a free PSD website template built by <a href="https://www.behance.net/MathavanJaya">Mathavan Jaya</a>. Golden is a modern and clean one page web template that was made exclusively for Best PSD Freebies. This template has a great portfolio, timeline, and meet your team sections that can be easily modified to fit your needs.</p>
                        <p>You can download the PSD template in this portfolio sample item at <a href="http://freebiesxpress.com/gallery/golden-free-one-page-web-template/">FreebiesXpress.com</a>.</p>
                        <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-times"></i> Close Project</button>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- Portfolio Modal 5 -->
      <div class="portfolio-modal modal fade" id="portfolioModal5" tabindex="-1" role="dialog" aria-hidden="true">
         <div class="modal-content">
            <div class="close-modal" data-dismiss="modal">
               <div class="lr">
                  <div class="rl">
                  </div>
               </div>
            </div>
            <div class="container">
               <div class="row">
                  <div class="col-lg-8 col-lg-offset-2">
                     <div class="modal-body">
                        <!-- Project Details Go Here -->
                        <h2>Project Name</h2>
                        <p class="item-intro text-muted">Lorem ipsum dolor sit amet consectetur.</p>
                        <img class="img-responsive img-centered" src="https://unsplash.imgix.net/46/Ov6ZY1zLTWmhPC0wFysP_IMG_2896_edt.jpg?q=75&w=1080&h=1080&fit=max&fm=jpg&auto=format&s=6518e4df89659818f6c0392175a9c5e6" alt="">
                        <p>Escape is a free PSD web template built by <a href="https://www.behance.net/MathavanJaya">Mathavan Jaya</a>. Escape is a one page web template that was designed with agencies in mind. This template is ideal for those looking for a simple one page solution to describe your business and offer your services.</p>
                        <p>You can download the PSD template in this portfolio sample item at <a href="http://freebiesxpress.com/gallery/escape-one-page-psd-web-template/">FreebiesXpress.com</a>.</p>
                        <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-times"></i> Close Project</button>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- Portfolio Modal 6 -->
      <div class="portfolio-modal modal fade" id="portfolioModal6" tabindex="-1" role="dialog" aria-hidden="true">
         <div class="modal-content">
            <div class="close-modal" data-dismiss="modal">
               <div class="lr">
                  <div class="rl">
                  </div>
               </div>
            </div>
            <div class="container">
               <div class="row">
                  <div class="col-lg-8 col-lg-offset-2">
                     <div class="modal-body">
                        <!-- Project Details Go Here -->
                        <h2>Project Name</h2>
                        <p class="item-intro text-muted">Lorem ipsum dolor sit amet consectetur.</p>
                        <img class="img-responsive img-centered" src="https://unsplash.imgix.net/uploads%2F1411419068566071cef10%2F7562527b?q=75&w=1080&h=1080&fit=max&fm=jpg&auto=format&s=240c45655f09c546232a6f106688e502" alt="">
                        <p>Dreams is a free PSD web template built by <a href="https://www.behance.net/MathavanJaya">Mathavan Jaya</a>. Dreams is a modern one page web template designed for almost any purpose. It’s a beautiful template that’s designed with the Bootstrap framework in mind.</p>
                        <p>You can download the PSD template in this portfolio sample item at <a href="http://freebiesxpress.com/gallery/dreams-free-one-page-web-template/">FreebiesXpress.com</a>.</p>
                        <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-times"></i> Close Project</button>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- jQuery Version 1.11.0 -->
      <script src="https://raw.githubusercontent.com/IronSummitMedia/startbootstrap/gh-pages/templates/agency/js/jquery-1.11.0.js"></script>
      <!-- Bootstrap Core JavaScript -->
      <script src="https://raw.githubusercontent.com/IronSummitMedia/startbootstrap/gh-pages/templates/agency/js/bootstrap.min.js"></script>
      <!-- Plugin JavaScript -->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>
      <script src="https://raw.githubusercontent.com/IronSummitMedia/startbootstrap/gh-pages/templates/agency/js/classie.js"></script>
      <script src="https://raw.githubusercontent.com/IronSummitMedia/startbootstrap/gh-pages/templates/agency/js/cbpAnimatedHeader.js"></script>
      <!-- Contact Form JavaScript -->
      <script src="https://raw.githubusercontent.com/IronSummitMedia/startbootstrap/gh-pages/templates/agency/js/jqBootstrapValidation.js"></script>
      <script src="https://raw.githubusercontent.com/IronSummitMedia/startbootstrap/gh-pages/templates/agency/js/contact_me.js"></script>
      <span style="height: 20px; width: 40px; min-height: 20px; min-width: 40px; position: absolute; opacity: 0.85; z-index: 8675309; display: none; cursor: pointer; background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACgAAAAUCAYAAAD/Rn+7AAADU0lEQVR42s2WXUhTYRjHz0VEVPRFUGmtVEaFUZFhHxBhsotCU5JwBWEf1EWEEVHQx4UfFWYkFa2biPJiXbUta33OXFtuUXMzJ4bK3Nqay7m5NeZq6h/tPQ+xU20zugjOxR/+7/O8539+5znnwMtNTExwJtMb3L/fiLv3botCSmUjeCaejTOb39AiFothfHxcFIrHY8RksZjBsckJcOIRMfFsHD/SsbExUYpnI8DR0dGUGjSb0byhEJp5Uqg5CTSzc2CQleJbMEj9/ywBcGRkJEk9DQqouEVQT1sK444yWI9UonmTjGqauVLEIlHa9x8lAMbj8SSpp0rwKGMVvg8P46vbg0C7na8z8JsMcgHe7jlEa+edRhiLy8n/TUMfu6EvLElk+U0WtGwrTrdfAGQf5J8iiK4LVzDU28t8JtMSocf8E+l68myaNFXm/6rXslLK7ay5TOunuRvZWpJuvwAYjUaTpOIWoquuAZ219RTaxKYp9BbjycoN5FvL9qH9TBX5rvoGdJythvXYSTxdtRnWylO/ZdqrLsGwszzhWQ593z2KlAwCYCQSSZJ6ehZ0W7bD9VBLgN0NCqr3qR7R2rBrL3pu3Sb/7nDlz2uy6cG0OXk0GTbZXzNp8trsPAQdTj6frlWzN2DcXZGKQQAMh8NJ6rpyHe+PnkCr/CAFdZyvpfpjuvkifLF9wIt1Wwlo0OHie1RvWrKa93RjzfzliTzPKz3ltB0/Tevmwp14wGUgHAzSOoUEwFAolFaaBSuhnslPRkJexUJtZ6v5HtUeLswl33n1BgEY5fvhs9sJ3FAiT+QYyyvoAQJuD0KBAFRTJNAuz5/s3gJgMBhMJwrVFRThM5tY5zUF/A4X1f2fvQTRLCuBreoim0YmAbqNJryvPEXeeq46kaNdkQ/1HCncbJKPs9ZSv2VHGfWsZ2hfkhKAfr8/pdxWKx4wwD69PmVfNSOL+lr2w+gYqHpWDtXt1xQ8AMlWU0e1lqLd/APRHoP8AJqWrQG9gYxcPMsvSJUvAA4MDKTUJ7MZLaVy8v+qT21tcDx/OemePr0RTkNrur4A6PP5xCgBsL+/X4wiQDpuuVxOeL1eMYmYeDY6sOp0z+B0OuHxeEQhxkJMFosJiSO/UinOI/8Pc+l7KKArAT8AAAAASUVORK5CYII=);"></span>
   </body>
   <!-- partial -->
   <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
   <script src='https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js'></script>
   <script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js'></script>
   <script src="./script.js"></script>
</body>

</html>