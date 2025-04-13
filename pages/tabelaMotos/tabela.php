<?php
// Se não estiver sendo incluído como script
if (!isset($isScript)) {
    // Define o caminho base caso não exista
    if (!isset($baseAddress)) {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domainName = $_SERVER['HTTP_HOST'];
        $scriptPath = dirname($_SERVER['SCRIPT_NAME']);
        $basePathParts = explode('/pages', $scriptPath);
        $baseAddress = $protocol . $domainName . $basePathParts[0];
    }
?>
<section id="banner">
    <div class="content">
        <!-- Script para carregar a tabela sem recarregar a página -->
        <script src="<?php echo $baseAddress; ?>/pages/tabelaMotos/js/tabela.js.php"></script>
        
        <!-- search bar -->
        <?php
        include_once($baseAddress ? realpath($_SERVER['DOCUMENT_ROOT'] . parse_url($baseAddress, PHP_URL_PATH)) . "/includes/searchbar_unified.php" : "./includes/searchbar_unified.php");
        ?>
        <div id="resultados-tabela">
            <?php
            // Carregar a tabela inicialmente
            include_once(realpath(__DIR__ . "/ajax/carregarMotos.php"));
            ?>
        </div>
    </div>
</section>
<?php
} else {
?>
// Recebe o baseAddress do PHP
const baseAddress = '<?php echo $baseAddress; ?>';

// Função global para carregar os dados via AJAX
window.carregarTabela = function(pagina = 1, pesquisa = '', orderby = '') {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', baseAddress + '/pages/tabelaMotos/ajax/carregarMotos.php?page=' + pagina + 
                   '&pesquisa=' + encodeURIComponent(pesquisa) + 
                   '&orderby=' + encodeURIComponent(orderby), true);
    
    xhr.onload = function() {
        if (this.status == 200) {
            document.getElementById('resultados-tabela').innerHTML = this.responseText;
            
            // Reaplica os eventos após o carregamento
            aplicarEventos();
        }
    };
    
    xhr.send();
};

// Função para aplicar eventos aos elementos carregados
function aplicarEventos() {
    // Adicionar eventos para os botões de ordenação
    document.querySelectorAll('.sort').forEach(function(button) {
        button.addEventListener('click', function() {
            // Remove a classe active de todos os botões
            document.querySelectorAll('.sort').forEach(function(btn) {
                btn.classList.remove('active');
            });
            
            // Adiciona a classe active ao botão clicado
            this.classList.add('active');
            
            var pesquisa = document.getElementById('input-pesquisa') ? 
                          document.getElementById('input-pesquisa').value : '';
            var orderby = this.getAttribute('data-orderby');
            
            window.carregarTabela(1, pesquisa, orderby);
        });
    });

    // Adicionar eventos para os botões de paginação
    document.querySelectorAll('.paginacao-btn').forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove a classe ativa de todos os botões
            document.querySelectorAll('.paginacao-btn').forEach(function(btn) {
                btn.classList.remove('paginacao-ativa');
                btn.style.fontWeight = 'normal';
            });
            
            // Adiciona a classe ativa ao botão clicado
            this.classList.add('paginacao-ativa');
            this.style.fontWeight = 'bold';
            
            var pagina = this.getAttribute('data-page');
            var pesquisa = document.getElementById('input-pesquisa') ? 
                          document.getElementById('input-pesquisa').value : '';
            var orderby = document.querySelector('.sort.active') ? 
                         document.querySelector('.sort.active').getAttribute('data-orderby') : '';
            
            window.carregarTabela(pagina, pesquisa, orderby);
        });
    });
}

// Aplicar eventos inicialmente
document.addEventListener('DOMContentLoaded', function() {
    aplicarEventos();
});
<?php
}
?>