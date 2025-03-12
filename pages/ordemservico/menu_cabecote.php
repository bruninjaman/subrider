    <!-- Title -->
    <div class="row">
        <div class="col-12 text-center">
            <h1>MENU CABEÇOTE</h1>
        </div>
    </div>

    <!-- Number of Cylinders -->
    <div class="row mb-3">
        <div class="col-4 text-right">
            <label for="num_cilindros">Nº CILINDROS</label>
        </div>
        <div class="col-2">
            <input type="number" name="num_cilindros" class="form-control" value="">
        </div>
        <div class="col-6"></div>
    </div>

    <!-- Number of Valves -->
    <div class="row mb-3">
        <div class="col-4 text-right">
            <label for="num_val_adm">Nº VÁLV ADM/CIL</label>
        </div>
        <div class="col-2">
            <input type="number" name="num_val_adm" class="form-control" value="">
        </div>
        <div class="col-4 text-right">
            <label for="num_val_esc">Nº VÁLV ESC/CIL</label>
        </div>
        <div class="col-2">
            <input type="number" name="num_val_esc" class="form-control" value="">
        </div>
    </div>

    <!-- Engine Type Radio Buttons -->
    <div class="row mb-3">
        <div class="col-4 text-right">
            <label>CONFIGURAÇÃO DO MOTOR</label>
        </div>
        <div class="col-2">
            <input type="radio" id="boxer" name="engine_type" value="boxer">
            <label for="boxer">BOXER</label>
        </div>
        <div class="col-2">
            <input type="radio" id="emv" name="engine_type" value="emv">
            <label for="emv">EM V</label>
        </div>
        <div class="col-2">
            <input type="radio" id="em_linha" name="engine_type" value="em_linha">
            <label for="em_linha">EM LINHA</label>
        </div>
        <div class="col-2"></div>
    </div>

    <!-- Valve Type Radio Buttons -->
    <div class="row mb-3">
        <div class="col-4 text-right">
            <label>TIPO DE VÁLVULA</label>
        </div>
        <div class="col-2">
            <input type="radio" id="vareta" name="valve_type" value="vareta" onchange="toggleFields()">
            <label for="vareta">VARETA</label>
        </div>
        <div class="col-2">
            <input type="radio" id="dohc" name="valve_type" value="dohc" onchange="toggleFields()">
            <label for="dohc">DOHC</label>
        </div>
        <div class="col-2">
            <input type="radio" id="ohc" name="valve_type" value="ohc" onchange="toggleFields()">
            <label for="ohc">OHC</label>
        </div>
        <div class="col-2"></div>
    </div>

    <!-- OHC Field -->
    <center>
        <br>
        <div class="row mb-3" id="ohc_fields" style="display: none;">
            <div class="col-2"></div>
            <div class="col-4 text-right">
                <label>EIXO CAMES DIAM MIN</label>
            </div>
            <div class="col-2">
                <input type="number" step="0.01" class="form-control" value="" name="came_diam_min">
            </div>
            <div class="col-4"></div>
        </div>
        <br>
    </center>

    <!-- DOHC Fields -->
    <center>
        <br>
        <div class="row mb-3" id="dohc_fields" style="display: none;">
            <div class="col-8 text-right">
                <label>EIXO CAMES ADM DIAM MIN</label>
            </div>
            <div class="col-4">
                <input type="number" step="0.01" class="form-control" value="" name="cames_adm_diam_min">
            </div>
            <div class="col-8 text-right">
                <label>EIXO CAMES ESC DIAM MIN</label>
            </div>
            <div class="col-4">
                <input type="number" step="0.01" class="form-control" value="" name="cames_adm_diam_max">
            </div>
        </div>
        <br>
    </center>

    <!-- Limits for Valve Measurements -->
    <div class="row mb-3">
        <div class="col-4 text-right">
            <label>VÁLVULA ADM LIMITE MIN</label>
        </div>
        <div class="col-2">
            <input type="number" step="0.01" class="form-control" value="" name="val_adm_limite_min">
        </div>
        <div class="col-2 text-right">
            <label>MAX</label>
        </div>
        <div class="col-2">
            <input type="number" step="0.01" class="form-control" value="" name="val_adm_limite_max">
        </div>
        <div class="col-2"></div>
    </div>

    <div class="row mb-3">
        <div class="col-4 text-right">
            <label>VÁLVULA ESC LIMITE MIN</label>
        </div>
        <div class="col-2">
            <input type="number" step="0.01" class="form-control" value="" name="val_esc_limite_min">
        </div>
        <div class="col-2 text-right">
            <label>MAX</label>
        </div>
        <div class="col-2">
            <input type="number" step="0.01" class="form-control" value="" name="val_esc_limite_max">
        </div>
        <div class="col-2"></div>
    </div>

    <!-- Cylinder Compression -->
    <div class="row mb-3">
        <div class="col-4 text-right">
            <label for="compressao_cilindro">COMPRESSÃO CILINDRO MIN/MAX</label>
        </div>
        <div class="col-2">
            <input type="number" name="compressao_min" class="form-control" value="">
        </div>
        <div class="col-2">
            <input type="number"  name="compressao_max" class="form-control" value="">
        </div>
        <div class="col-4"></div>
    </div>

    <br>
    <!-- Buttons -->

    <div class="row mb-3">
        <div class="col-4"></div>
        <div class="col-2">
            <button type="submit" class="button primary">Salvar</button>
        </div>
        <div class="col-2">
            <a href="#" class="button secondary" id="backToMenu" style="position: absolute; top: 10px; right: 10px;">Voltar</a>
        </div>
        <div class="col-4"></div>
    </div>

    <script>
        function toggleFields() {
            var ohcField = document.getElementById('ohc_fields');
            var dohcFields = document.getElementById('dohc_fields');
            var isOHC = document.getElementById('ohc').checked;
            var isDOHC = document.getElementById('dohc').checked;
            var isVareta = document.getElementById('vareta').checked;

            if (isVareta) {
                ohcField.style.display = 'none';
                dohcFields.style.display = 'none';
            } else if (isOHC) {
                ohcField.style.display = 'block';
                dohcFields.style.display = 'none';
            } else if (isDOHC) {
                dohcFields.style.display = 'block';
                ohcField.style.display = 'none';
            } else {
                ohcField.style.display = 'none';
                dohcFields.style.display = 'none';
            }
        }
    </script>