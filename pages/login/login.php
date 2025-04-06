<?php
require_once(__DIR__ . '/../../config.php');
?>

<style>
  .spotlight .content-container {
    position: absolute;
    top: 75%;
    left: 15%;
    transform: translate(-50%, -50%);
    text-align: center;
  }

  @media screen and (max-width: 780px) {
    .image.fit {
      width: 210%;
    }
  }
  @media screen and (max-width: 736px) {
    .spotlight .content-container {
      position: absolute;
      z-index: 999;
      top: 20%;
      left: 50%;
      transform: translate(-50%, -50%);
      text-align: center;
    }
    #loginform label {
      margin: 0;
      font-size: 0.8rem;

    }
    #loginform .button.primary {
      width: 100%;
    }
    #loginform input {
      height: 2em;
    }
  }
</style>
<section id="two" class="spotlight style2 right">
  <span class="image fit main">
    <img src="/subrider/assets/css/images/race-moto.gif" alt="" />
  </span>
  <div class="content">
    <header>
      <h2>Acesso administrativo</h2>
    </header>
    <p>
    <ul>
      <li>
        Esta página de login é exclusivamente para administradores. No momento, estamos em processo de desenvolvimento e em breve haverá recursos e possibilidades para que os usuários possam acessar a página. Por enquanto, pedimos compreensão e paciência.
      </li>
      <br>
      <li>
        Se você precisar de mais informações, não hesite em entrar em contato conosco através do nosso e-mail, WhatsApp ou de alguma de nossas redes sociais. Estamos sempre à disposição para responder a qualquer dúvida ou sugestão.
      </li>
      <br>
      <li>
        Agradecemos a compreensão e esperamos em breve poder oferecer a vocês a melhor experiência em nossa página.
      </li>
    </ul>
    </p>
    <!-- <ul class="actions">
            <li><a href="#login" class="button">Entrar</a></li>
        </ul> -->
  </div>
  <div class="content-container">
    <header>
      <h2>Entre na sua conta</h2>
    </header>
    <form id="loginform" name="loginform" method="POST" action="/subrider/scripts/log-in.php">
      <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
      <div class="col-4 col-12-medium">
        <label for="fname">Login:</label>
        <input type="text" id="username" name="username" maxlength="25" required><br>
        <label for="lname">Senha:</label>
        <input type="password" id="password" name="password" maxlength="25" required><br>
      </div>
      <div class="col-4 col-12-medium">
        <button type="submit" class="button primary">Entrar</button>
      </div>
    </form>
  </div>
  <a href="#login" class="goto-next scrolly">Next</a>
</section>