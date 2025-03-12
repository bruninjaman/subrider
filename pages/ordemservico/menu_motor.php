<!-- Title -->
<div class="row">
    <h1>MENU MOTOR</h1>
</div>

<!-- Number of Cylinders -->
<div class="row">
    <div class="col-4">
        <label for="nr_cilindros">Nº CILINDROS</label>
    </div>
    <div class="col-3">
        <input type="number" name="nr_cilindros" value="">
    </div>
</div>

<!-- Course of Piston -->
<div class="row">
    <div class="col-4">
        <label for="curso_pistao">CURSO PISTÃO</label>
    </div>
    <div class="col-3">
        <input type="number" name="curso_pistao" value="">mm
    </div>
</div>

<!-- Diameters and tolerances -->
<div class="row">
    <div class="col-4">
        <label for="diametro_cilindro_max">DIÂMETRO CIL MÁX</label>
    </div>
    <div class="col-3">
        <input type="number" name="diametro_cilindro_max" value=""> mm
    </div>
</div>

<div class="row">
    <div class="col-4">
        <label for="conicidade_max">CONICIDADE MÁX</label>
    </div>
    <div class="col-3">
        <input type="number" name="conicidade_max" value=""> mm
    </div>
</div>

<div class="row">
    <div class="col-4">
        <label for="ovalizacao_max">OVALIZAÇÃO MÁX</label>
    </div>
    <div class="col-3">
        <input type="number" name="ovalizacao_max" value=""> mm
    </div>
</div>

<div class="row">
    <div class="col-4">
        <label for="diametro_pistao_min">DIÂMETRO PIS MIN</label>
    </div>
    <div class="col-3">
        <input type="number" name="diametro_pistao_min" value=""> mm
    </div>
</div>

<div class="row">
    <div class="col-4">
        <label for="folga_cil_pis_max">FOLGA CIL/PIS MÁX</label>
    </div>
    <div class="col-3">
        <input type="number" name="folga_cil_pis_max" value=""> mm
    </div>
</div>

<!-- Ring gaps -->
<div class="row">
    <div class="col-4">
        <label for="aber_anel_1_max">ABER 1º ANEL LIVRE MÁX</label>
    </div>
    <div class="col-3">
        <input type="number" name="aber_anel_1_max" value=""> mm
    </div>
</div>

<div class="row">
    <div class="col-4">
        <label for="aber_anel_2_max">ABER 2º ANEL LIVRE MÁX</label>
    </div>
    <div class="col-3">
        <input type="number" name="aber_anel_2_max" value=""> mm
    </div>
</div>

<div class="row">
    <div class="col-4">
        <label for="aber_anel_1_pres_min">ABER 1º ANEL PRESA MIN</label>
    </div>
    <div class="col-3">
        <input type="number" name="aber_anel_1_pres_min" value=""> mm
    </div>
</div>

<div class="row">
    <div class="col-4">
        <label for="aber_anel_2_pres_min">ABER 2º ANEL PRESA MIN</label>
    </div>
    <div class="col-3">
        <input type="number" name="aber_anel_2_pres_min" value=""> mm
    </div>
</div>

<!-- Ring widths -->
<div class="row">
    <div class="col-4">
        <label for="larg_anel_1_min">LARG 1º ANEL MIN</label>
    </div>
    <div class="col-3">
        <input type="number" name="larg_anel_1_min" value=""> mm
    </div>
</div>

<div class="row">
    <div class="col-4">
        <label for="larg_anel_2_min">LARG 2º ANEL MIN</label>
    </div>
    <div class="col-3">
        <input type="number" name="larg_anel_2_min" value=""> mm
    </div>
</div>

<!-- Pin measurements -->
<div class="row">
    <div class="col-4">
        <label for="dia_furo_pis_min">DIA FURO PIS MIN</label>
    </div>
    <div class="col-3">
        <input type="number" name="dia_furo_pis_min" value=""> mm
    </div>
</div>

<div class="row">
    <div class="col-4">
        <label for="dia_pino_pis_min">DIA PINO PIS MIN</label>
    </div>
    <div class="col-3">
        <input type="number" name="dia_pino_pis_min" value=""> mm
    </div>
</div>

<div class="row">
    <div class="col-4">
        <label for="folga_pino_pis_max">FOLGA PINO PIS MÁX</label>
    </div>
    <div class="col-3">
        <input type="number" name="folga_pino_pis_max" value=""> mm
    </div>
</div>

<!-- Buttons -->
<div class="row">
    <button type="submit" class="button primary">Salvar</button>
    <div class="col-3">
        <a href="#" class="button secondary" id="backToMenu" style="position: absolute; top: 10px; right: 10px;">Voltar</a>
    </div>
</div>
</form>
<style>
    input[type="number"] {
        height: 50px;
    }
</style>