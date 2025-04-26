<!-- search bar -->
<form id="form-pesquisa" onsubmit="return false;">
    <div class="row">
        <div class="col-8 search">
            <input type="text" name="pesquisa" id="input-pesquisa" placeholder="Pesquisar em todos os campos...">
            <button type="button" id="btn-pesquisar"><i class="fa fa-search"></i></button>
        </div>
    </div>
</form>
<!-- fim da barra de pesquisa -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Evento para o botão de pesquisa
    document.getElementById('btn-pesquisar').addEventListener('click', function() {
        var pesquisa = document.getElementById('input-pesquisa').value;
        var orderby = document.querySelector('.sort.active') ? 
                     document.querySelector('.sort.active').getAttribute('data-orderby') : '';
        
        // Chama a função carregarTabela com o termo de pesquisa, mas sem categoria específica
        window.carregarTabela(1, pesquisa, 'all', orderby);
    });

    // Evento para pressionar Enter no campo de pesquisa
    document.getElementById('input-pesquisa').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('btn-pesquisar').click();
        }
    });
});
</script>
