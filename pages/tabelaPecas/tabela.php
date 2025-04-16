<?php
// Caminho absoluto para config.php
require_once(dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'config.php'); // Adiciona config
?>
<section id="banner">
    <div class="content">
        <!-- search bar -->
        <?php
        // Caminho corrigido para include
        include_once(PROJECT_ROOT_PATH . DS . "includes" . DS . "searchbar_unified.php");
        ?>
        <div id="resultados-tabela">
            <?php
            // Carregar a tabela inicialmente (Mantido __DIR__ por simplicidade neste caso)
            include_once(__DIR__ . "/ajax/carregarPecas.php");
            ?>
        </div>
    </div>
</section>

<!-- Script para carregar a tabela sem recarregar a página -->
<script>
// Função global para carregar os dados via AJAX
window.carregarTabela = function(pagina = 1, pesquisa = '', orderby = '') {
    var xhr = new XMLHttpRequest();
    // URL AJAX corrigida
    xhr.open('GET', '<?php echo PROJECT_ROOT_URL; ?>/pages/tabelaPecas/ajax/carregarPecas.php?page=' + pagina + 
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

    // Adicionar eventos para os links de paginação
    document.querySelectorAll('.paginacao-btn').forEach(function(botao) { 
        botao.addEventListener('click', function(e) {
            e.preventDefault();
            
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
aplicarEventos();
</script>
<!-- Scripts com caminhos corrigidos -->
<script src="<?php echo PROJECT_ROOT_URL; ?>/assets/js/delete_confirm.js"></script>
<script src="<?php echo PROJECT_ROOT_URL; ?>/pages/tabelaPecas/js/deletePeca.js"></script>
</section>