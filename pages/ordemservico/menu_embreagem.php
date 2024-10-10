<h1>Menu Embreagem</h1>
<form action="#" method="POST">
    <div class="row">
        <div class="col-4">
            <label>Número de Discos:</label>
        </div>
        <div class="col-3">
            <input type="number" name="nr_discos" step="1" required><br><br>
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <label>Número de Discos Separadores:</label>
        </div>
        <div class="col-3">
            <input type="number" name="nr_discos_sep" step="1" required><br><br>
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <label>Disco Fricção ESP MIN: </label>
        </div>
        <div class="col-3">
            <input type="number" name="disco_fric_esp_min" step="0.1" required><br><br>
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <label>Disco SEP EMPEN MAX:</label>
        </div>
        <div class="col-3">
            <input type="number" name="disco_sep_emp_max" step="0.1" required><br><br>
        </div>
    </div>

    <!-- Botão de envio -->
    <button type="submit" class="button primary">Salvar</button>
    <a class="button secondary" id="backToMenu">Voltar ao Menu</a>
</form>