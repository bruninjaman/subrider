<style>
    #btnDados {
        background-color:rgb(214, 199, 65) !important;
        color: black !important;
    }

    #btnDados:hover {
        color: #da4961 !important;
    }
</style>
<h2>Selecione uma Opção</h2>
<a class="button secondary" id="btnDados" onclick="setFormSection('dados')">Dados</a>
<a class="button secondary" id="btnCabecote" onclick="setFormSection('cabecote')">Cabeçote</a>
<a class="button secondary" id="btnMotor" onclick="setFormSection('motor')">Motor</a>
<a class="button secondary" id="btnVirabrequim" onclick="setFormSection('virabrequim')">Virabrequim</a>
<a class="button secondary" id="btnEmbreagem" onclick="setFormSection('embreagem')">Embreagem</a>
<a class="button secondary" id="btnBombas" onclick="setFormSection('bomba')">Bombas</a>
<a class="button primary" id="closeModal3">Sair</a>