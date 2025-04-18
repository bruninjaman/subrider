<!DOCTYPE HTML>

<?php
session_start();

// Incluir arquivo de configuração global
require_once __DIR__ . '/config.php';

require_once './vendor/autoload.php';

// Load environment variables from .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Definir base address de forma dinâmica
$server_name = $_SERVER['SERVER_NAME'];
$is_localhost = (strpos($server_name, 'localhost') !== false || $server_name === '127.0.0.1');

// Se for localhost, usa o valor do .env, senão usa o caminho relativo ao root
if ($is_localhost) {
    $baseAddress = $_ENV['BASE_ADDRESS'] ?? '';
} else {
    // No servidor, será o path absoluto para o diretório do site
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $baseAddress = $protocol . '://' . $server_name . dirname($_SERVER['PHP_SELF']);
    
    // Remover '/pages/ordemservico' ou outros subdiretórios específicos
    $baseAddress = preg_replace('~/pages/[^/]+~', '', $baseAddress);
    
    // Remover qualquer '/' extra no final
    $baseAddress = rtrim($baseAddress, '/');
}

//PERM
require_once(__DIR__ . "/scripts/perm.php");
//CONNECTION
require_once(__DIR__ . "/connection/connection.php");
//FUNCTIONS
require_once(__DIR__ . "/scripts/functions.php");

// Detectar se é dispositivo mobile
$isMobile = false;
if (isset($_SERVER['HTTP_USER_AGENT'])) {
    $useragent = $_SERVER['HTTP_USER_AGENT'];
    $isMobile = (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4)));
}
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
    <link rel="stylesheet" href="./<?php echo $baseAddress; ?>/pages/ordemservico/ordemservico_mobile.css" />
    <link rel="stylesheet" href="./<?php echo $baseAddress; ?>/assets/css/main.css" />
    <noscript>
        <link rel="stylesheet" href="./<?php echo $baseAddress; ?>/assets/css/noscript.css" />
    </noscript>
    
    <!-- Script para aplicar tema escuro imediatamente em dispositivos mobile -->
    <script>
        (function() {
            // Detectar mobile pelo tamanho da tela
            if(window.innerWidth <= 768) {
                document.documentElement.classList.add('dark-theme');
                document.body.classList.add('is-mobile');
                document.body.classList.add('dark-theme');
                
                // Configurações para telas muito pequenas
                if(window.innerWidth <= 320) {
                    document.body.classList.add('is-very-small');
                }
            }
        })();
    </script>
    
    <style>
        /* Estilos minimalistas inline para mobile */
        @media screen and (max-width: 768px) {
            /* Reset & Base minimalista para móvel */
            body.is-mobile {
                font-size: 14px;
                line-height: 1.4;
                padding: 0;
                margin: 0;
                background-color: #121212;
                color: #e0e0e0;
                overflow-x: hidden;
            }
            
            /* Cabeçalho mais compacto */
            body.is-mobile #header {
                padding: 0.4em;
                height: auto;
                display: flex;
                justify-content: space-between;
                align-items: center;
                background-color: #181921 !important;
                position: relative;
            }
            
            body.is-mobile #header #logo img {
                height: 30px;
                width: auto;
                margin: 0;
            }
            
            /* Conteúdo principal */
            body.is-mobile #banner {
                padding: 0.5em !important;
            }
            
            body.is-mobile #banner .content {
                padding: 0.5em !important;
            }
            
            /* Tabelas responsivas */
            body.is-mobile .table-wrapper {
                overflow-x: visible;
                padding: 0;
                margin: 0.5em 0;
            }
            
            body.is-mobile table {
                border-collapse: collapse;
                width: 100%;
                margin: 0.5em 0;
                background-color: #1e1e1e;
                border-radius: 4px;
            }
            
            body.is-mobile .headers-tabela {
                font-size: 1em;
                padding: 0.5em;
                margin: 0.5em 0;
                background-color: #181921;
                color: #fff;
                border-radius: 4px;
                text-align: center;
            }
            
            /* Layout de informações da moto */
            body.is-mobile .motoinfobox {
                padding: 0.8em;
                margin: 0.5em 0;
                background-color: #1e1e1e;
                border-radius: 4px;
            }
            
            body.is-mobile .imagemoto img {
                width: 100%;
                height: auto;
                border-radius: 4px;
                margin-bottom: 0.5em;
            }
            
            body.is-mobile .motoinfo li {
                padding: 0.3em 0;
                border-bottom: 1px solid #333;
            }
            
            /* Botões */
            body.is-mobile button {
                padding: 0.4em 0.6em;
                margin: 0.3em;
                border-radius: 4px;
                background-color: #2e2e2e;
                border: 1px solid #444;
                color: #e0e0e0;
            }
            
            body.is-mobile button:hover {
                background-color: #3e3e3e;
            }
            
            body.is-mobile .button.primary {
                background-color: #3d4880;
            }
            
            body.is-mobile .button.primary:hover {
                background-color: #4d5890;
            }
            
            /* Ajustes para telas muito pequenas */
            @media screen and (max-width: 320px) {
                body.is-very-small {
                    font-size: 12px;
                }
                
                body.is-very-small #header #logo img {
                    height: 25px;
                }
                
                body.is-very-small button {
                    padding: 0.3em 0.5em;
                    font-size: 0.9em;
                }
                
                body.is-very-small .headers-tabela {
                    font-size: 0.9em;
                    padding: 0.3em;
                }
                
                body.is-very-small .table-wrapper {
                    margin: 0.3em 0;
                }
            }
        }
    </style>
</head>

<body class="is-peload landing <?php echo $isMobile ? 'is-mobile dark-theme' : ''; ?>">
    <div id="page-wrapper">
        <!-- content -->
        <?php
        require("./pages/ordemservico/header.php");
        require("./pages/ordemservico/tabela.php");
        require("./pages/ordemservico/info.php");
        require("./pages/ordemservico/footer.php");
        ?>
    </div>
    
    <!-- Select option script -->
    <script src="pages/ordemservico/modal/option_selected.js"></script>
    <!-- Scripts for main theme -->
    <script src="assets/js/global/jquery.min.js"></script>
    <script src="assets/js/global/jquery.scrolly.min.js"></script>
    <script src="assets/js/global/jquery.dropotron.min.js"></script>
    <script src="assets/js/global/jquery.scrollex.min.js"></script>
    <script src="assets/js/global/browser.min.js"></script>
    <script src="assets/js/global/breakpoints.min.js"></script>
    <script src="assets/js/global/util.js"></script>
    <script src="assets/js/main.js"></script>

    <!-- Define baseAddress for JavaScript -->
    <script>
        const baseAddress = '<?php echo $baseAddress; ?>';
    </script>

    <!-- Delete button -->
    <script src=".\pages\ordemservico\delete_confirm.js"></script>
    <!-- Jquery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Delete button -->
    <script src=".\pages\ordemservico\editable_data.js"></script>
    <script src=".\pages\ordemservico\editable_proprietario.js"></script>
    <!-- Mobile Responsive Script -->
    <script src=".\pages\ordemservico\mobile_responsive.js"></script>
    
    <!-- Script de melhorias para mobile -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.innerWidth <= 768) {
                // Otimizar scroll para mobile
                window.scrollTo(0, 0);
                
                // Ajustar tamanho de elementos com base no viewport
                const viewportHeight = window.innerHeight;
                const viewportWidth = window.innerWidth;
                
                // Garantir que modais e popups não ultrapassem o viewport
                const modals = document.querySelectorAll('.modal-content');
                modals.forEach(modal => {
                    modal.style.maxHeight = (viewportHeight * 0.8) + 'px';
                    modal.style.maxWidth = (viewportWidth * 0.9) + 'px';
                    modal.style.overflow = 'auto';
                });
                
                // Otimizar carregamento de imagens para mobile
                const images = document.querySelectorAll('.imagemoto img');
                images.forEach(img => {
                    // Adicionar lazy loading para melhorar performance
                    img.loading = 'lazy';
                    
                    // Otimizar tamanho baseado no viewport
                    img.style.maxWidth = '100%';
                    img.style.height = 'auto';
                });
                
                // Melhorar usabilidade das tabelas em tela pequena
                const tables = document.querySelectorAll('table');
                tables.forEach(table => {
                    // Adicionar swipe indicator para tabelas maiores que a tela
                    if (table.scrollWidth > viewportWidth) {
                        const tableWrapper = table.closest('.table-wrapper');
                        if (tableWrapper) {
                            tableWrapper.classList.add('swipe-enabled');
                            
                            // Adicionar indicador de swipe se ainda não existir
                            if (!tableWrapper.querySelector('.swipe-indicator')) {
                                const indicator = document.createElement('div');
                                indicator.className = 'swipe-indicator';
                                indicator.innerHTML = '← Deslize →';
                                indicator.style.textAlign = 'center';
                                indicator.style.fontSize = '0.8em';
                                indicator.style.color = '#aaa';
                                indicator.style.padding = '0.3em';
                                tableWrapper.appendChild(indicator);
                            }
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>

<?php
mysqli_close($conn);
?>