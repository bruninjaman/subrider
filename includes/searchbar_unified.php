<!-- search bar -->
<form id="form-pesquisa" action="" method="get">
    <input type="hidden" name="page" value="1">
    <div class="row">
        <div class="col-12 search">
            <input type="text" name="pesquisa" id="input-pesquisa" placeholder="Pesquisar..">
            <button type="submit" id="btn-pesquisar"><i class="fa fa-search"></i></button>
        </div>
    </div>
</form>
<!-- fim da barra de pesquisa -->

<script>
// Função para verificar se carregarTabela está disponível
function checkCarregarTabela(callback, maxAttempts = 20, interval = 100) {
    console.log('Verificando carregarTabela...'); // Debug
    
    if (typeof window.carregarTabela === 'function') {
        console.log('carregarTabela encontrada!'); // Debug
        callback();
    } else if (maxAttempts > 0) {
        console.log('carregarTabela não encontrada, tentando novamente...'); // Debug
        setTimeout(() => checkCarregarTabela(callback, maxAttempts - 1, interval), interval);
    } else {
        console.error('Função carregarTabela não encontrada após várias tentativas.');
    }
}

// Função para inicializar a pesquisa
function initializePesquisa() {
    var formPesquisa = document.getElementById('form-pesquisa');
    if (formPesquisa) {
        formPesquisa.addEventListener('submit', function(event) {
            event.preventDefault();
            var pesquisa = document.getElementById('input-pesquisa').value;
            
            checkCarregarTabela(() => {
                window.carregarTabela(1, pesquisa);
            });
        });
    }
}

// Inicializar quando o DOM estiver pronto
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializePesquisa);
} else {
    initializePesquisa();
}
</script> 