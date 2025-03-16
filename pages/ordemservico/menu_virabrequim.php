<style>
    input[type="number"] {
        height: 50px;
    }
    .hidden {
        display: none;
    }
</style>
<div class="container menuvirabrequim">
    <h1>MENU VIRABREQUIM</h1>

    <div class="section checkbox-container">
        <input type="radio" id="rolamento_1" name="rolamento_type" value="rolamento">
        <label for="rolamento_1">Rolamento</label>

        <input type="radio" id="rolamento_2" name="rolamento_type" value="bronzina">
        <label for="rolamento_2">Bronzina</label>
    </div>

    <br>
    <div class="section" id="folga_lateral_biela_section">
        <label for="folga_lateral_biela">FOLGA LATERAL BIELA MAX:</label>
        <input type="number" name="folga_lateral_biela" value=""> mm
    </div>
    <br>
    <div class="section" id="folga_eixo_bronzina_section">
        <label for="folga_eixo_bronzina">FOLGA EIXO-BRONZINA MAX:</label>
        <input type="number" name="folga_eixo_bronzina" value=""> mm
    </div>
    <br>
    <div class="section" id="folga_eixo_mancal_section">
        <label for="folga_eixo_mancal">FOLGA EIXO-MANCAL MAX:</label>
        <input type="number" name="folga_eixo_mancal" value=""> mm
    </div>
    <br>
    <div class="section" id="folga_lateral_eixo_section">
        <label for="folga_lateral_eixo">FOLGA LATERAL EIXO:</label>
        <input type="number" name="folga_lateral_eixo_min" value=""> a
        <input type="number" name="folga_lateral_eixo_max" value=""> mm
    </div>
    <br>
    <div class="section" id="empenamento_max_section">
        <label for="empenamento_max">EMPENAMENTO MAX:</label>
        <input type="number" name="empenamento_max" value=""> mm
    </div>

    <br>
    <button type="submit" class="button primary">OK</button>
    <div class="col-2">
        <a href="#" class="button secondary" id="backToMenu" style="position: absolute; top: 10px; right: 10px;">Voltar</a>
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

        // Inicializa o estado correto ao carregar a página
        toggleSections();
    });
</script>