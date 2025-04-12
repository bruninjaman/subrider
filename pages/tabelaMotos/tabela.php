<section id="banner">
    <div class="content">
        <!-- search bar -->
        <?php
        include_once("./includes/searchbar_unified.php");
        ?>
        <div id="resultados-tabela">
            <?php
            // Carregar a tabela inicialmente
            include_once(__DIR__ . "/ajax/carregarMotos.php");
            ?>
        </div>
    </div>
</section>

<!-- Script para carregar a tabela sem recarregar a página -->
<script>
// Função global para carregar os dados via AJAX
window.carregarTabela = function(pagina = 1, pesquisa = '', orderby = '') {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', '/subrider/pages/tabelaMotos/ajax/carregarMotos.php?page=' + pagina + 
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
</script>