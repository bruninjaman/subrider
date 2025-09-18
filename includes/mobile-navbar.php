<!-- Mobile Navbar -->
<nav class="mobile-navbar">
    <div class="logo">
        <a href="index.php">
            <img src="./assets/css/images/logo-branco-crop.png" alt="Subrider Logo">
        </a>
    </div>
    
    <button class="hamburger-menu" aria-label="Menu">
        <span class="bar"></span>
        <span class="bar"></span>
        <span class="bar"></span>
    </button>
</nav>

<!-- Mobile Menu Overlay -->
<div class="mobile-menu-overlay">
    <ul class="mobile-menu-items">
        <li>
            <a href="index.php">Início</a>
        </li>
        
        <li>
            <a href="#four">Sobre nossos serviços</a>
        </li>
        
        <?php if (isset($_SESSION["user"])): ?>
        <li class="has-submenu">
            <a href="#" class="submenu-parent">
                Tabelas
            </a>
            <button class="submenu-toggle" aria-label="Abrir submenu">▼</button>
            <ul class="mobile-submenu">
                <li><a href="tabelaPecas.php">Peças</a></li>
                <li><a href="tabelaMotos.php">Motocicletas</a></li>
                <li><a href="tabelaServicos.php">Serviços</a></li>
                <li><a href="tabelaOrdens.php">Ordens de serviço</a></li>
            </ul>
        </li>
        <?php endif; ?>
        
        <li>
            <a href="#footer">Contato</a>
        </li>
        
        <li>
            <?php if (isset($_SESSION["user"])): ?>
                <a href="scripts/log-out.php" class="button">Sair</a>
            <?php else: ?>
                <a href="login.php" class="button">Entrar</a>
            <?php endif; ?>
        </li>
    </ul>
</div>