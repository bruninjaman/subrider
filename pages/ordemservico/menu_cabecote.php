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

<style>
.form-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
    border-radius: 20px;
}

.title-underline {
    width: 80px;
    height: 4px;
    background: _palette(accent1);
    margin: 1rem auto;
    border-radius: 2px;
}

.floating-input {
    position: relative;
    margin-bottom: 1rem;
}

.floating-input input {
    width: 100%;
    padding: 1rem;
    border: 2px solid rgba(255, 255, 255, 0.1);
    background: rgba(255, 255, 255, 0.05);
    border-radius: 8px;
    font-size: 1rem;
    color: _palette(fg);
    transition: all 0.3s ease;
}

.floating-input label {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    background: transparent;
    padding: 0 0.5rem;
    color: _palette(fg-light);
    transition: all 0.3s ease;
    pointer-events: none;
}

.floating-input input:focus,
.floating-input input:not(:placeholder-shown) {
    border-color: _palette(accent1);
    background: rgba(255, 255, 255, 0.08);
}

.floating-input input:focus + label,
.floating-input input:not(:placeholder-shown) + label {
    top: 0;
    font-size: 0.85rem;
    color: _palette(accent1);
}

.section-title {
    font-size: 1.2rem;
    color: _palette(fg);
    margin-bottom: 1.5rem;
    font-weight: 300;
    text-align: center;
}

.custom-radio-group {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    justify-content: center;
    width: 100%;
}

.custom-radio-group input[type="radio"] {
    display: none;
}

.custom-radio-group label {
    padding: 0.8rem 1.5rem;
    border: 2px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    color: _palette(fg);
}

.custom-radio-group input[type="radio"]:checked + label {
    background: _palette(accent1);
    border-color: _palette(accent1);
    color: white;
}

.tucho-switch-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
}

.tucho-switch {
    position: relative;
    width: 60px;
    height: 34px;
}

.tucho-switch-input {
    display: none !important;
}

.tucho-switch-label {
    display: block;
    overflow: hidden;
    cursor: pointer;
    height: 34px;
    padding: 0;
    line-height: 34px;
    border: 2px solid #FFFFFF;
    border-radius: 34px;
    background-color: transparent;
    transition: background-color 0.3s ease-in;
}

.tucho-switch-label:before {
    content: "";
    display: block;
    width: 34px;
    height: 34px;
    margin: -2px;
    background: #FFFFFF;
    position: absolute;
    top: 0;
    bottom: 0;
    right: 28px;
    border-radius: 34px;
    transition: all 0.3s ease-in 0s; 
}

.tucho-switch-input:checked + .tucho-switch-label {
    background-color: #FFFFFF;
}

.tucho-switch-input:checked + .tucho-switch-label:before {
    right: -2px; 
    background-color: #e44c65;
}

.tucho-labels {
    display: flex;
    justify-content: space-between;
    width: 100px;
    color: #FFFFFF;
    font-size: 0.9rem;
}

.tucho-switch input[type="checkbox"] {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.tucho-switch-label * {
    box-sizing: border-box;
}

.tucho-switch-label:hover {
    opacity: 0.9;
}

.tucho-switch-input:focus + .tucho-switch-label {
    box-shadow: 0 0 1px #FFFFFF;
}

.measure-group {
    background: rgba(255, 255, 255, 0.03);
    padding: 1.5rem;
    border-radius: 12px;
    margin-bottom: 1rem;
}

.measure-label {
    display: block;
    color: _palette(fg);
    margin-bottom: 1rem;
    font-size: 0.9rem;
}

.measure-inputs {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.action-buttons {
    display: flex;
    justify-content: center;
    gap: 1rem;
    padding-top: 2rem;
}

.btn {
    padding: 1rem 2rem;
    border-radius: 8px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.btn-primary {
    background: _palette(accent1);
    border: none;
    color: white;
}

.btn-outline {
    border: 2px solid _palette(fg-light);
    color: _palette(fg);
    background: transparent;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.animated-section {
    transition: all 0.3s ease;
    opacity: 0;
    transform: translateY(20px);
}

.animated-section.active {
    opacity: 1;
    transform: translateY(0);
}

@media (max-width: 768px) {
    .measure-inputs {
        grid-template-columns: 1fr;
    }
    
    .custom-radio-group {
        flex-direction: column;
    }
    
    .custom-radio-group label {
        width: 100%;
        text-align: center;
    }
}
.title-cabecote {
    background-color: #e44c65 !important;
}
</style>

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