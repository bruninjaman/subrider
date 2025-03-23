<div class="form-container">
    <!-- Title -->
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="display-4 fw-light title-cabecote">Menu Cabeçote</h1>
            <div class="title-underline"></div>
        </div>
    </div>

    <form id="cabecoteForm" class="needs-validation custom-form" novalidate>
        <div class="container px-4">
            <!-- Cilindros e Válvulas -->
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="floating-input">
                        <input type="number" 
                               name="num_cilindros" 
                               class="form-control"
                               id="num_cilindros"
                               min="1"
                               max="16"
                               placeholder=" ">
                        <label for="num_cilindros">Nº Cilindros</label>
                        <div class="focus-border"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="floating-input">
                        <input type="number" 
                               name="num_val_adm"
                               id="num_val_adm" 
                               class="form-control"
                               min="1"
                               max="8"
                               placeholder=" ">
                        <label for="num_val_adm">Nº Válv. Admissão/Cil.</label>
                        <div class="focus-border"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="floating-input">
                        <input type="number" 
                               name="num_val_esc"
                               id="num_val_esc" 
                               class="form-control"
                               min="1"
                               max="8"
                               placeholder=" ">
                        <label for="num_val_esc">Nº Válv. Escape/Cil.</label>
                        <div class="focus-border"></div>
                    </div>
                </div>
            </div>

            <!-- Configuração do Motor -->
            <div class="section-group mb-5">
                <h3 class="section-title">Configuração do Motor</h3>
                <div class="custom-radio-group">
                    <input type="radio" name="engine_type" id="boxer" value="boxer">
                    <label for="boxer">Boxer</label>

                    <input type="radio" name="engine_type" id="emv" value="emv">
                    <label for="emv">Em V</label>

                    <input type="radio" name="engine_type" id="em_linha" value="em_linha">
                    <label for="em_linha">Em Linha</label>
                </div>
            </div>

            <!-- Tipo de Válvula -->
            <div class="row mb-5">
                <div class="col-md-8">
                    <div class="section-group">
                        <h3 class="section-title">Tipo de Válvula</h3>
                        <div class="custom-radio-group">
                            <input type="radio" name="valve_type" id="vareta" value="vareta" onchange="toggleFields()">
                            <label for="vareta">Vareta</label>

                            <input type="radio" name="valve_type" id="dohc" value="dohc" onchange="toggleFields()">
                            <label for="dohc">DOHC</label>

                            <input type="radio" name="valve_type" id="ohc" value="ohc" onchange="toggleFields()">
                            <label for="ohc">OHC</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="section-group">
                        <h3 class="section-title">Tucho Mecânico</h3>
                        <div class="tucho-switch-container">
                            <div class="tucho-switch">
                                <input type="checkbox" id="tucho" name="tucho" value="1" class="tucho-switch-input">
                                <label for="tucho" class="tucho-switch-label">
                                    <span class="tucho-switch-inner"></span>
                                    <span class="tucho-switch-switch"></span>
                                </label>
                            </div>
                            <div class="tucho-labels">
                                <span class="tucho-label-no">Não</span>
                                <span class="tucho-label-yes">Sim</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Campos Condicionais -->
            <div id="ohc_fields" class="animated-section" style="display: none;">
                <div class="floating-input">
                    <input type="number" 
                           step="0.01" 
                           class="form-control" 
                           name="came_diam_min"
                           id="came_diam_min"
                           placeholder=" ">
                    <label for="came_diam_min">Eixo Cames Diâm. Min</label>
                    <div class="focus-border"></div>
                </div>
            </div>

            <div id="dohc_fields" class="animated-section" style="display: none;">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="floating-input">
                            <input type="number" 
                                   step="0.01" 
                                   class="form-control" 
                                   name="cames_adm_diam_min"
                                   id="cames_adm_diam_min"
                                   placeholder=" ">
                            <label for="cames_adm_diam_min">Eixo Cames Adm. Diâm. Min</label>
                            <div class="focus-border"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="floating-input">
                            <input type="number" 
                                   step="0.01" 
                                   class="form-control" 
                                   name="cames_esc_diam_min"
                                   id="cames_esc_diam_min"
                                   placeholder=" ">
                            <label for="cames_esc_diam_min">Eixo Cames Esc. Diâm. Min</label>
                            <div class="focus-border"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Medidas -->
            <div class="measurements-section mt-5">
                <h3 class="section-title text-center mb-4">Medidas e Limites</h3>
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="measure-group">
                            <span class="measure-label">Válvula Adm. Limite (mm)</span>
                            <div class="measure-inputs">
                                <div class="floating-input">
                                    <input type="number" step="0.01" class="form-control" name="val_adm_limite_min" placeholder=" ">
                                    <label>Mínimo</label>
                                    <div class="focus-border"></div>
                                </div>
                                <div class="floating-input">
                                    <input type="number" step="0.01" class="form-control" name="val_adm_limite_max" placeholder=" ">
                                    <label>Máximo</label>
                                    <div class="focus-border"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="measure-group">
                            <span class="measure-label">Válvula Esc. Limite (mm)</span>
                            <div class="measure-inputs">
                                <div class="floating-input">
                                    <input type="number" step="0.01" class="form-control" name="val_esc_limite_min" placeholder=" ">
                                    <label>Mínimo</label>
                                    <div class="focus-border"></div>
                                </div>
                                <div class="floating-input">
                                    <input type="number" step="0.01" class="form-control" name="val_esc_limite_max" placeholder=" ">
                                    <label>Máximo</label>
                                    <div class="focus-border"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Compressão -->
            <div class="compression-section mt-5">
                <div class="measure-group">
                    <span class="measure-label">Compressão Cilindro</span>
                    <div class="measure-inputs">
                        <div class="floating-input">
                            <input type="number" class="form-control" name="compressao_min" placeholder=" ">
                            <label>Mínimo</label>
                            <div class="focus-border"></div>
                        </div>
                        <div class="floating-input">
                            <input type="number" class="form-control" name="compressao_max" placeholder=" ">
                            <label>Máximo</label>
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
    </form>
</div>

<script>
function toggleFields() {
    const ohcField = document.getElementById('ohc_fields');
    const dohcFields = document.getElementById('dohc_fields');
    const isOHC = document.getElementById('ohc').checked;
    const isDOHC = document.getElementById('dohc').checked;
    
    if (isOHC) {
        ohcField.style.display = 'block';
        setTimeout(() => ohcField.classList.add('active'), 10);
        dohcFields.style.display = 'none';
        dohcFields.classList.remove('active');
    } else if (isDOHC) {
        dohcFields.style.display = 'block';
        setTimeout(() => dohcFields.classList.add('active'), 10);
        ohcField.style.display = 'none';
        ohcField.classList.remove('active');
    } else {
        ohcField.style.display = 'none';
        dohcFields.style.display = 'none';
        ohcField.classList.remove('active');
        dohcFields.classList.remove('active');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('cabecoteForm');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(form);
        const data = {};
        
        for (let [key, value] of formData.entries()) {
            data[key] = value === '' ? null : value;
        }
        
        console.log(data);
    });

    const tuchoSwitch = document.getElementById('tucho');
    tuchoSwitch.addEventListener('change', function() {
        console.log('Tucho Mecânico:', this.checked);
    });
});
</script>