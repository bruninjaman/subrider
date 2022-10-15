<!-- Header -->
<header id="header">
    <h1 id="logo">
        <a href="index.php"><img src="./assets/css/images/logo-branco-crop.png" style="height:60px;width:180px;"></a>
    </h1>
    <nav id="nav">
        <ul>
            <li><a href="tabelaPecas.php">Tabela de peças</a></li>
            <li><a href="tabelaMotos.php">Tabela de motocicletas</a></li>
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