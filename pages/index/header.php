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
                            <li><a href="tabelaPecas.php">Peças</a></li>
                            <li><a href="tabelaMotos.php">Motocicletas</a></li>
                            <li><a href="tabelaServicos.php">Serviços</a></li>
                            <li><a href="tabelaOrdens.php">Ordens de serviço</a></li>
                        </ul>
                    </li>';
            }
            ?>
            <li><a href="#footer">Contato</a></li>
            <?php
            if (isset($_SESSION["user"])) {
                echo '<li>';
                echo '<a class="button primary" href="scripts/log-out.php">Sair</a>';
                echo '</li>';
            } else {
                echo '<li>';
                echo '<a class="button primary" href="login.php">Entrar</a>';
                echo '</li>';
            }
            ?>
        </ul>
    </nav>
</header>