<form action="#" method="POST">
    <!-- Title -->
    <div class="row">
        <h1>MENU MOTOR</h1>
    </div>

    <!-- Number of Cylinders -->
    <div class="row">
        <div class="col-6">
            <label for="num_cilindros">Nº CILINDROS</label>
            <span>= 4</span>
        </div>
    </div>

    <!-- Course of Piston -->
    <div class="row">
        <div class="col-4">
            <label for="curso_pistao">CURSO PISTÃO</label>
        </div>
        <div class="col-2">
            <input type="number" id="curso_pistao" name="curso_pistao" value="110" readonly> mm
        </div>
    </div>

    <!-- Diameters and tolerances -->
    <div class="row">
        <div class="col-4">
            <label for="diametro_cilindro_max">DIÂMETRO CIL MÁX</label>
        </div>
        <div class="col-2">
            <input type="number" id="diametro_cilindro_max" name="diametro_cilindro_max" value="108" readonly> mm
        </div>
    </div>

    <div class="row">
        <div class="col-4">
            <label for="conicidade_max">CONICIDADE MÁX</label>
        </div>
        <div class="col-2">
            <input type="number" id="conicidade_max" name="conicidade_max" value="0.2" readonly> mm
        </div>
    </div>

    <div class="row">
        <div class="col-4">
            <label for="ovalizacao_max">OVALIZAÇÃO MÁX</label>
        </div>
        <div class="col-2">
            <input type="number" id="ovalizacao_max" name="ovalizacao_max" value="0.1" readonly> mm
        </div>
    </div>

    <div class="row">
        <div class="col-4">
            <label for="diametro_pistao_min">DIÂMETRO PIS MIN</label>
        </div>
        <div class="col-2">
            <input type="number" id="diametro_pistao_min" name="diametro_pistao_min" value="107.3" readonly> mm
        </div>
    </div>

    <div class="row">
        <div class="col-4">
            <label for="folga_cil_pis_max">FOLGA CIL/PIS MÁX</label>
        </div>
        <div class="col-2">
            <input type="number" id="folga_cil_pis_max" name="folga_cil_pis_max" value="0.03" readonly> mm
        </div>
    </div>

    <!-- Ring gaps -->
    <div class="row">
        <div class="col-4">
            <label for="aber_anel_1_max">ABER 1º ANEL LIVRE MÁX</label>
        </div>
        <div class="col-2">
            <input type="number" id="aber_anel_1_max" name="aber_anel_1_max" value="8.9" readonly> mm
        </div>
    </div>

    <div class="row">
        <div class="col-4">
            <label for="aber_anel_2_max">ABER 2º ANEL LIVRE MÁX</label>
        </div>
        <div class="col-2">
            <input type="number" id="aber_anel_2_max" name="aber_anel_2_max" value="6.8" readonly> mm
        </div>
    </div>

    <div class="row">
        <div class="col-4">
            <label for="aber_anel_1_pres_min">ABER 1º ANEL PRESA MIN</label>
        </div>
        <div class="col-2">
            <input type="number" id="aber_anel_1_pres_min" name="aber_anel_1_pres_min" value="0.15" readonly> mm
        </div>
    </div>

    <div class="row">
        <div class="col-4">
            <label for="aber_anel_2_pres_min">ABER 2º ANEL PRESA MIN</label>
        </div>
        <div class="col-2">
            <input type="number" id="aber_anel_2_pres_min" name="aber_anel_2_pres_min" value="0.2" readonly> mm
        </div>
    </div>

    <!-- Ring widths -->
    <div class="row">
        <div class="col-4">
            <label for="larg_anel_1_min">LARG 1º ANEL MIN</label>
        </div>
        <div class="col-2">
            <input type="number" id="larg_anel_1_min" name="larg_anel_1_min" value="0.5" readonly> mm
        </div>
    </div>

    <div class="row">
        <div class="col-4">
            <label for="larg_anel_2_min">LARG 2º ANEL MIN</label>
        </div>
        <div class="col-2">
            <input type="number" id="larg_anel_2_min" name="larg_anel_2_min" value="0.5" readonly> mm
        </div>
    </div>

    <!-- Pin measurements -->
    <div class="row">
        <div class="col-4">
            <label for="dia_furo_pis_min">DIA FURO PIS MIN</label>
        </div>
        <div class="col-2">
            <input type="number" id="dia_furo_pis_min" name="dia_furo_pis_min" value="20.03" readonly> mm
        </div>
    </div>

    <div class="row">
        <div class="col-4">
            <label for="dia_pino_pis_min">DIA PINO PIS MIN</label>
        </div>
        <div class="col-2">
            <input type="number" id="dia_pino_pis_min" name="dia_pino_pis_min" value="19.98" readonly> mm
        </div>
    </div>

    <div class="row">
        <div class="col-4">
            <label for="folga_pino_pis_max">FOLGA PINO PIS MÁX</label>
        </div>
        <div class="col-2">
            <input type="number" id="folga_pino_pis_max" name="folga_pino_pis_max" value="0.05" readonly> mm
        </div>
    </div>

    <!-- Buttons -->
    <div class="row">
        <button type="submit" class="button primary">OK</button>
        <a class="button secondary" id="voltar">Volta ao Menu ANTERIOR</a>
    </div>
</form>
