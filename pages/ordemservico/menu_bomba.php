<h1>Menu Bombas</h1>
<div class="row">
    <div class="col-4">
        <!-- Pressão da Bomba de Óleo -->
        <label>Pressão da Bomba de Óleo MIN/MAX:</label>
    </div>
    <div class="col-2">
        <label>Min</label>
        <input type="number" id="pressao_oleo_min" name="pressao_oleo_min" step="0.01"><br><br>
    </div>
    <div class="col-2">
        <label>Max</label>
        <input type="number" id="pressao_oleo_max" name="pressao_oleo_max" step="0.01"><br><br>
    </div>
</div>
<div class="row">
    <!-- Pressão da Bomba de Combustão -->
    <div class="col-4">
        <label>Pressão da Bomba de Combustão:</label>
    </div>
    <div class="col-1">
    </div>
    <div class="col-2">
        <input type="number" id="pressao_combustao" name="pressao_combustao" step="0.01"><br><br>
    </div>
</div>
<div class="row">
    <div class="col-4">
        <!-- Vazão da Bomba de Combustão -->
        <label for="vazao_combustao_min">Vazão da Bomba de Combustão MIN/MAX:</label>
    </div>
    <div class="col-2">
        <label>Min</label>
        <input type="number" id="vazao_combustao_min" name="vazao_combustao_min" step="0.01"><br><br>
    </div>
    <div class="col-2">
        <label>Max</label>
        <input type="number" id="vazao_combustao_max" name="vazao_combustao_max" step="0.01"><br><br>
    </div>
</div>
<!-- Botão de envio -->
<button type="submit" class="button primary">Salvar</button>
<div class="col-2">
    <a href="#" class="button secondary" id="backToMenu" style="position: absolute; top: 10px; right: 10px;">Voltar</a>
</div>