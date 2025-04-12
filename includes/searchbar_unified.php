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
document.addEventListener('DOMContentLoaded', function() {
    // Adicionar event listener para o formulário de pesquisa
    var formPesquisa = document.getElementById('form-pesquisa');
    if (formPesquisa) {
        formPesquisa.addEventListener('submit', function(event) {
            event.preventDefault();
            var pesquisa = document.getElementById('input-pesquisa').value;
            
            // Verifica se existe a função carregarTabela (definida em tabela.php)
            if (typeof window.carregarTabela === 'function') {
                window.carregarTabela(1, pesquisa);
            } else {
                console.error('Função carregarTabela não encontrada. Verifique se o script em tabela.php foi carregado corretamente.');
            }
        });
    }
});
</script> 