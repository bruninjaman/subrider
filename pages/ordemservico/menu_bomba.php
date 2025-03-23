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
                        <input type="number" 
                               name="pressao_oleo_min"
                               id="pressao_oleo_min"
                               class="form-control"
                               step="0.01"
                               placeholder=" ">
                        <label>Pressão Mínima</label>
                        <div class="focus-border"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="floating-input">
                        <input type="number" 
                               name="pressao_oleo_max"
                               id="pressao_oleo_max"
                               class="form-control"
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
                        <input type="number" 
                               name="pressao_combustao"
                               id="pressao_combustao"
                               class="form-control"
                               step="0.01"
                               placeholder=" ">
                        <label>Pressão da Bomba de Combustão</label>
                        <div class="focus-border"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="floating-input">
                        <input type="number" 
                               name="vazao_combustao_min"
                               id="vazao_combustao_min"
                               class="form-control"
                               step="0.01"
                               placeholder=" ">
                        <label>Vazão Mínima</label>
                        <div class="focus-border"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="floating-input">
                        <input type="number" 
                               name="vazao_combustao_max"
                               id="vazao_combustao_max"
                               class="form-control"
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

.title-bomba {
    background-color: #e44c65;
    color: white;
    padding: 10px;
    border-radius: 8px;
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

.section-title {
    font-size: 1.2rem;
    color: #e5e5e5;
    margin-bottom: 1.5rem;
    font-weight: 300;
    text-align: center;
}

.section-group {
    background: rgba(255, 255, 255, 0.03);
    padding: 1.5rem;
    border-radius: 12px;
    margin-bottom: 1.5rem;
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
    text-decoration: none;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

@media (max-width: 768px) {
    .col-md-6 {
        width: 100%;
    }
}
</style>