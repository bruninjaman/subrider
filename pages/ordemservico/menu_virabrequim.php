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
    background: #e44c65;
    margin: 1rem auto;
    border-radius: 2px;
}

.title-virabrequim {
    background-color: #e44c65;
    color: white;
    padding: 10px;
    border-radius: 8px;
}

.section-group {
    background: rgba(255, 255, 255, 0.03);
    padding: 1.5rem;
    border-radius: 12px;
    margin-bottom: 1.5rem;
}

.section-title {
    font-size: 1.2rem;
    color: #e5e5e5;
    margin-bottom: 1.5rem;
    font-weight: 300;
    text-align: center;
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
    color: #e5e5e5;
    transition: all 0.3s ease;
}

.floating-input label {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    background: transparent;
    padding: 0 0.5rem;
    color: #aaa;
    transition: all 0.3s ease;
    pointer-events: none;
}

.floating-input input:focus,
.floating-input input:not(:placeholder-shown) {
    border-color: #e44c65;
    background: rgba(255, 255, 255, 0.08);
}

.floating-input input:focus + label,
.floating-input input:not(:placeholder-shown) + label {
    top: 0;
    font-size: 0.85rem;
    color: #e44c65;
}

.radio-input {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 8px;
    border: 2px solid rgba(255, 255, 255, 0.1);
}

.radio-input label {
    color: #e5e5e5;
    margin: 0;
    cursor: pointer;
}

.radio-input input[type="radio"] {
    width: 20px;
    height: 20px;
    cursor: pointer;
}

.range-input .range-fields {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.range-separator {
    color: #e5e5e5;
    font-size: 1rem;
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
    cursor: pointer;
    text-decoration: none;
}

.btn-primary {
    background: #e44c65;
    border: none;
    color: white;
}

.btn-outline {
    border: 2px solid #aaa;
    color: #e5e5e5;
    background: transparent;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.hidden {
    display: none;
}

@media (max-width: 768px) {
    .col-md-6 {
        width: 100%;
    }
}
</style>

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