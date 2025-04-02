<div class="form-container">
    <!-- Title -->
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="display-4 fw-light title-motor">Menu Motor</h1>
            <div class="title-underline"></div>
        </div>
    </div>

    <div class="container px-4">
        <!-- Cilindros e Curso -->
        <div class="row g-4 mb-5">
            <div class="col-md-6">
                <div class="floating-input">
                    <input type="number" 
                           name="nr_cilindros" 
                           class="form-control"
                           id="nr_cilindros"
                           placeholder=" ">
                    <label for="nr_cilindros">Nº Cilindros</label>
                    <div class="focus-border"></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="floating-input">
                    <input type="number" 
                           name="curso_pistao"
                           id="curso_pistao" 
                           class="form-control"
                           placeholder=" ">
                    <label for="curso_pistao">Curso Pistão (mm)</label>
                    <div class="focus-border"></div>
                </div>
            </div>
        </div>

        <!-- Diâmetros e Tolerâncias -->
        <div class="section-group mb-5">
            <h3 class="section-title">Diâmetros e Tolerâncias</h3>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="floating-input">
                        <input type="number" 
                               name="diametro_cilindro_max" 
                               class="form-control"
                               placeholder=" ">
                        <label>Diâmetro Cilindro Máx (mm)</label>
                        <div class="focus-border"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="floating-input">
                        <input type="number" 
                               name="conicidade_max" 
                               class="form-control"
                               placeholder=" ">
                        <label>Conicidade Máx (mm)</label>
                        <div class="focus-border"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="floating-input">
                        <input type="number" 
                               name="ovalizacao_max" 
                               class="form-control"
                               placeholder=" ">
                        <label>Ovalização Máx (mm)</label>
                        <div class="focus-border"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="floating-input">
                        <input type="number" 
                               name="diametro_pistao_min" 
                               class="form-control"
                               placeholder=" ">
                        <label>Diâmetro Pistão Min (mm)</label>
                        <div class="focus-border"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="floating-input">
                        <input type="number" 
                               name="folga_cil_pis_max" 
                               class="form-control"
                               placeholder=" ">
                        <label>Folga Cilindro/Pistão Máx (mm)</label>
                        <div class="focus-border"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="floating-input">
                        <input type="number" 
                               name="folga_pino_pis_max" 
                               class="form-control"
                               placeholder=" ">
                        <label>Folga Pino pis max (mm)</label>
                        <div class="focus-border"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Anéis -->
        <div class="section-group mb-5">
            <h3 class="section-title">Medidas dos Anéis</h3>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="floating-input">
                        <input type="number" 
                               name="aber_anel_1_max" 
                               class="form-control"
                               placeholder=" ">
                        <label>Abertura 1º Anel Livre Máx (mm)</label>
                        <div class="focus-border"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="floating-input">
                        <input type="number" 
                               name="aber_anel_2_max" 
                               class="form-control"
                               placeholder=" ">
                        <label>Abertura 2º Anel Livre Máx (mm)</label>
                        <div class="focus-border"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="floating-input">
                        <input type="number" 
                               name="aber_anel_1_pres_min" 
                               class="form-control"
                               placeholder=" ">
                        <label>Abertura 1º Anel Presa Min (mm)</label>
                        <div class="focus-border"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="floating-input">
                        <input type="number" 
                               name="aber_anel_2_pres_min" 
                               class="form-control"
                               placeholder=" ">
                        <label>Abertura 2º Anel Presa Min (mm)</label>
                        <div class="focus-border"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="floating-input">
                        <input type="number" 
                               name="larg_anel_1_min" 
                               class="form-control"
                               placeholder=" ">
                        <label>Largura 1º Anel Min (mm)</label>
                        <div class="focus-border"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="floating-input">
                        <input type="number" 
                               name="larg_anel_2_min" 
                               class="form-control"
                               placeholder=" ">
                        <label>Largura 2º Anel Min (mm)</label>
                        <div class="focus-border"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pino -->
        <div class="section-group mb-5">
            <h3 class="section-title">Medidas do Pino</h3>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="floating-input">
                        <input type="number" 
                               name="dia_furo_pis_min" 
                               class="form-control"
                               placeholder=" ">
                        <label>Diâmetro Furo Pistão Min (mm)</label>
                        <div class="focus-border"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="floating-input">
                        <input type="number" 
                               name="dia_pino_pis_min" 
                               class="form-control"
                               placeholder=" ">
                        <label>Diâmetro Pino Pistão Min (mm)</label>
                        <div class="focus-border"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="floating-input">
                        <input type="number" 
                               name="folga_pino_pis_max" 
                               class="form-control"
                               placeholder=" ">
                        <label>Folga Pino Pistão Máx (mm)</label>
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