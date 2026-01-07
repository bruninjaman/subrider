<div class="form-container">
    <!-- Título -->
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="display-4 fw-light title-virabrequim">Menu Virabrequim</h1>
            <div class="title-underline"></div>
        </div>
    </div>

    <div class="container px-4">
        <!-- Seleção do tipo de rolamento -->
        <div class="section-group mb-5">
            <h3 class="section-title">Tipo de Rolamento</h3>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="radio-input">
                        <input type="radio" id="rolamento_1" name="rolamento_type" value="rolamento">
                        <label for="rolamento_1">Rolamento</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="radio-input">
                        <input type="radio" id="rolamento_2" name="rolamento_type" value="bronzina">
                        <label for="rolamento_2">Bronzina</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Medições -->
        <div class="section-group mb-5">
            <h3 class="section-title">Medições</h3>
            <div class="row g-4">
                <div class="col-md-6" id="folga_lateral_biela_section">
                    <div class="floating-input">
                        <input type="text" name="folga_lateral_biela" class="form-control" data-type="decimal" placeholder=" ">
                        <label>Folga Lateral Biela Máx (mm)</label>
                        <div class="focus-border"></div>
                    </div>
                </div>

                <div class="col-md-6" id="folga_eixo_biela_section">
                    <div class="floating-input">
                        <input type="text" name="folga_eixo_biela" class="form-control" data-type="decimal" placeholder=" ">
                        <label>Folga Eixo-Biela Máx (mm)</label>
                        <div class="focus-border"></div>
                    </div>
                </div>

                <div class="col-md-6" id="folga_eixo_mancal_section">
                    <div class="floating-input">
                        <input type="text" name="folga_eixo_mancal" class="form-control" data-type="decimal" placeholder=" ">
                        <label>Folga Eixo-Mancal Máx (mm)</label>
                        <div class="focus-border"></div>
                    </div>
                </div>

                <div class="col-md-12" id="folga_lateral_eixo_section">
                    <div class="floating-input">
                        <div class="range-fields">
                            <input type="text" name="folga_lateral_eixo_min" class="form-control" data-type="decimal" placeholder=" ">
                            <span class="range-separator">a</span>
                            <div class="floating-input">
                                <input type="text" name="folga_lateral_eixo_max" class="form-control" data-type="decimal" placeholder=" ">
                                <label>maximo</label>
                            </div>
                        </div>
                        <label for="folga_lateral_eixo_min">Folga Lateral Eixo (mm)</label>
                        <div class="focus-border"></div>
                    </div>
                </div>

                <div class="col-md-6" id="empenamento_max_section">
                    <div class="floating-input">
                        <input type="text" name="empenamento_max" class="form-control" data-type="decimal" placeholder=" ">
                        <label>Empenamento Máx (mm)</label>
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

<link rel="stylesheet" href="<?php echo $baseAddress; ?>/pages/ordemservico/modal-menus.css">
<script src="<?php echo $baseAddress; ?>/assets/js/menu-scripts.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Adiciona classe active aos elementos com animated-section após um pequeno delay
    setTimeout(() => {
        document.querySelectorAll('.animated-section').forEach(section => {
            section.classList.add('active');
        });
    }, 100);
});
</script>
