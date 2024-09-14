<form action="#" method="POST">
    <!-- Title -->
    <div class="row">
        <h1>MENU CABEÇOTE</h1>
    </div>

    <!-- Number of Cylinders -->
    <div class="row">
        <div class="col-4">
            <label for="num_cilindros">Nº CILINDROS</label>
        </div>
        <div class="col-2">
            <input type="number" id="num_cilindros" name="num_cilindros" value="4" required>
        </div>
    </div>

    <!-- Number of Valves -->
    <div class="row">
        <div class="col-4">
            <label for="num_val_adm">Nº VÁLV ADM/CIL</label>
        </div>
        <div class="col-2">
            <input type="number" id="num_val_adm" name="num_val_adm" value="2" required>
        </div>
        <div class="col-4">
            <label for="num_val_esc">Nº VÁLV ESC/CIL</label>
        </div>
        <div class="col-2">
            <input type="number" id="num_val_esc" name="num_val_esc" value="2" required>
        </div>
    </div>

    <!-- Checkboxes for engine configuration -->
    <div class="row">
        <div class="col-2">
            <input type="checkbox" id="boxer" name="boxer">
            <label for="boxer">BOXER</label>
        </div>
        <div class="col-2">
            <input type="checkbox" id="emv" name="emv">
            <label for="emv">EM V</label>
        </div>
        <div class="col-2">
            <input type="checkbox" id="em_linha" name="em_linha">
            <label for="em_linha">EM LINHA</label>
        </div>
        <div class="col-2">
            <input type="checkbox" id="tucho_mecanico" name="tucho_mecanico">
            <label for="tucho_mecanico">TUCHO MECÂNICO</label>
        </div>
        <div class="col-2">
            <input type="checkbox" id="vareta" name="vareta">
            <label for="vareta">VARETA</label>
        </div>
    </div>

    <div class="row">
        <div class="col-2">
            <input type="checkbox" id="dohc" name="dohc">
            <label for="dohc">DOHC</label>
        </div>
        <div class="col-2">
            <input type="checkbox" id="ohc" name="ohc">
            <label for="ohc">OHC</label>
        </div>
    </div>

    <!-- Limits for Valve Measurements -->
    <div class="row">
        <div class="col-4">
            <label>VÁLVULA ADM LIMITE MIN</label>
        </div>
        <div class="col-2">
            <input type="number" step="0.01" value="0.12" readonly>
        </div>
        <div class="col-2">
            <label>MAX</label>
        </div>
        <div class="col-2">
            <input type="number" step="0.01" value="0.18" readonly>
        </div>
    </div>

    <div class="row">
        <div class="col-4">
            <label>VÁLVULA ESC LIMITE MIN</label>
        </div>
        <div class="col-2">
            <input type="number" step="0.01" value="0.15" readonly>
        </div>
        <div class="col-2">
            <label>MAX</label>
        </div>
        <div class="col-2">
            <input type="number" step="0.01" value="0.21" readonly>
        </div>
    </div>

    <!-- Cylinder Compression -->
    <div class="row">
        <div class="col-4">
            <label for="compressao_cilindro">COMPRESSÃO CILINDRO MIN/MAX</label>
        </div>
        <div class="col-2">
            <input type="number" id="compressao_min" name="compressao_min" value="155" readonly>
        </div>
        <div class="col-2">
            <input type="number" id="compressao_max" name="compressao_max" value="195" readonly>
        </div>
    </div>

    <!-- Buttons -->
    <div class="row">
        <!-- Botão de envio -->
        <button type="submit" class="button primary">Salvar</button>
        <a class="button secondary" id="backToMenu">Voltar ao Menu</a>
    </div>
</form>