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
                        <input type="number" name="folga_lateral_biela" class="form-control" placeholder=" ">
                        <label>Folga Lateral Biela Máx (mm)</label>
                        <div class="focus-border"></div>
                    </div>
                </div>

                <div class="col-md-6" id="folga_eixo_bronzina_section">
                    <div class="floating-input">
                        <input type="number" name="folga_eixo_bronzina" class="form-control" placeholder=" ">
                        <label>Folga Eixo-Bronzina Máx (mm)</label>
                        <div class="focus-border"></div>
                    </div>
                </div>

                <div class="col-md-6" id="folga_eixo_mancal_section">
                    <div class="floating-input">
                        <input type="number" name="folga_eixo_mancal" class="form-control" placeholder=" ">
                        <label>Folga Eixo-Mancal Máx (mm)</label>
                        <div class="focus-border"></div>
                    </div>
                </div>

                <div class="col-md-12" id="folga_lateral_eixo_section">
                    <div class="floating-input range-input">
                        <div class="range-fields">
                            <input type="number" name="folga_lateral_eixo_min" class="form-control" placeholder=" ">
                            <span class="range-separator">a</span>
                            <input type="number" name="folga_lateral_eixo_max" class="form-control" placeholder=" ">
                        </div>
                        <label>Folga Lateral Eixo (mm)</label>
                        <div class="focus-border"></div>
                    </div>
                </div>

                <div class="col-md-6" id="empenamento_max_section">
                    <div class="floating-input">
                        <input type="number" name="empenamento_max" class="form-control" placeholder=" ">
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
            <a href="#" class="btn btn-outline" id="backToMenu">
                <i class="fas fa-arrow-left"></i>
                <span>Voltar</span>
            </a>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const rolamento1 = document.getElementById('rolamento_1');
        const rolamento2 = document.getElementById('rolamento_2');
        const sectionsToHide = [
            document.getElementById('folga_lateral_biela_section'),
            document.getElementById('folga_lateral_eixo_section'),
            document.getElementById('empenamento_max_section')
        ];

        function toggleSections() {
            if (rolamento2.checked) {
                sectionsToHide.forEach(section => section.classList.add('hidden'));
            } else {
                sectionsToHide.forEach(section => section.classList.remove('hidden'));
            }
        }

        rolamento1.addEventListener('change', toggleSections);
        rolamento2.addEventListener('change', toggleSections);

        toggleSections();
    });
</script>