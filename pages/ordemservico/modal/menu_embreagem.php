<div class="form-container">
    <!-- Title -->
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="display-4 fw-light title-embreagem">Menu Embreagem</h1>
            <div class="title-underline"></div>
        </div>
    </div>

    <div class="container px-4">
        <!-- Discos -->
        <div class="row g-4 mb-5">
            <div class="col-md-6">
                <div class="floating-input">
                    <input type="number" 
                           name="nr_discos" 
                           class="form-control"
                           id="nr_discos"
                           step="1"
                           placeholder=" ">
                    <label for="nr_discos">Número de Discos</label>
                    <div class="focus-border"></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="floating-input">
                    <input type="number" 
                           name="nr_discos_sep"
                           id="nr_discos_sep" 
                           class="form-control"
                           step="1"
                           placeholder=" ">
                    <label for="nr_discos_sep">Número de Discos Separadores</label>
                    <div class="focus-border"></div>
                </div>
            </div>
        </div>

        <!-- Medidas -->
        <div class="section-group mb-5">
            <h3 class="section-title">Medidas</h3>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="floating-input">
                        <input type="number" 
                               name="disco_fric_esp_min" 
                               class="form-control"
                               step="0.1"
                               placeholder=" ">
                        <label>Disco Fricção ESP MIN</label>
                        <div class="focus-border"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="floating-input">
                        <input type="number" 
                               name="disco_sep_emp_max" 
                               class="form-control"
                               step="0.1"
                               placeholder=" ">
                        <label>Disco SEP EMPEN MAX</label>
                        <div class="focus-border"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botões -->
        <div class="action-buttons mt-5">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                <span>Salvar</span>
            </button>
            <a href="#" class="btn btn-outline button secondary" id="backToMenu">
                <i class="fas fa-arrow-left"></i>
                <span>Voltar</span>
            </a>
        </div>
    </div>
</div>

<link rel="stylesheet" href="<?php echo $baseAddress; ?>/assets/css/menu-styles.css">