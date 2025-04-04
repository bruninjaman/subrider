<link rel="stylesheet" href="<?php echo $baseAddress; ?>/assets/css/modal-menus.css">

<div class="form-container">
    <!-- Title -->
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="display-4 fw-light title-bomba">Menu Bombas</h1>
            <div class="title-underline"></div>
        </div>
    </div>

    <div class="container px-4">
        <!-- Pressão da Bomba de Óleo -->
        <div class="section-group mb-5">
            <h3 class="section-title">Pressão da Bomba de Óleo</h3>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="floating-input">
                        <input type="text" 
                               name="pressao_oleo_min"
                               id="pressao_oleo_min"
                               class="form-control"
                               data-type="decimal"
                               step="0.01"
                               placeholder=" ">
                        <label>Pressão Mínima</label>
                        <div class="focus-border"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="floating-input">
                        <input type="text" 
                               name="pressao_oleo_max"
                               id="pressao_oleo_max"
                               class="form-control"
                               data-type="decimal"
                               step="0.01"
                               placeholder=" ">
                        <label>Pressão Máxima</label>
                        <div class="focus-border"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pressão e Vazão da Bomba de Combustão -->
        <div class="section-group mb-5">
            <h3 class="section-title">Bomba de Combustão</h3>
            <div class="row g-4">
                <div class="col-md-12">
                    <div class="floating-input">
                        <input type="text" 
                               name="pressao_combustao"
                               id="pressao_combustao"
                               class="form-control"
                               data-type="decimal"
                               step="0.01"
                               placeholder=" ">
                        <label>Pressão da Bomba de Combustão</label>
                        <div class="focus-border"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="floating-input">
                        <input type="text" 
                               name="vazao_combustao_min"
                               id="vazao_combustao_min"
                               class="form-control"
                               data-type="decimal"
                               step="0.01"
                               placeholder=" ">
                        <label>Vazão Mínima</label>
                        <div class="focus-border"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="floating-input">
                        <input type="text" 
                               name="vazao_combustao_max"
                               id="vazao_combustao_max"
                               class="form-control"
                               data-type="decimal"
                               step="0.01"
                               placeholder=" ">
                        <label>Vazão Máxima</label>
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