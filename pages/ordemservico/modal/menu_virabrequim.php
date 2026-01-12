<?php
// Lógica para pré-carregar dados do banco de dados
$qtdCilindrosPredefinido = '';
$virabrequimRefData = [];

if (isset($conn)) {
    $ordemParaBusca = isset($ordem) ? $ordem : (isset($_GET['ordem']) ? $_GET['ordem'] : null);

    if ($ordemParaBusca) {
        // Tenta buscar nr_cilindros do motor
        $stmtCilindros = $conn->prepare("SELECT nr_cilindros FROM motor WHERE ordem = ? ORDER BY is_reference DESC, id DESC LIMIT 1");
        if ($stmtCilindros) {
            $stmtCilindros->bind_param("s", $ordemParaBusca);
            $stmtCilindros->execute();
            $resCilindros = $stmtCilindros->get_result();
            if ($rowCilindros = $resCilindros->fetch_assoc()) {
                if ($rowCilindros['nr_cilindros'] > 0) {
                    $qtdCilindrosPredefinido = $rowCilindros['nr_cilindros'];
                }
            }
            $stmtCilindros->close();
        }

        // Se não encontrou no motor, tenta buscar do cabeçote
        if (empty($qtdCilindrosPredefinido)) {
            $stmtCab = $conn->prepare("SELECT cilindros FROM cabecote WHERE ordem = ? ORDER BY is_reference DESC, id DESC LIMIT 1");
            if ($stmtCab) {
                $stmtCab->bind_param("s", $ordemParaBusca);
                $stmtCab->execute();
                $resCab = $stmtCab->get_result();
                if ($rowCab = $resCab->fetch_assoc()) {
                    if (isset($rowCab['cilindros']) && $rowCab['cilindros'] > 0) {
                        $qtdCilindrosPredefinido = $rowCab['cilindros'];
                    }
                }
                $stmtCab->close();
            }
        }

        // Carregar dados existentes do Virabrequim (referência)
        $stmtVira = $conn->prepare("SELECT * FROM virabrequim WHERE ordem = ? AND is_reference = 1 LIMIT 1");
        if ($stmtVira) {
            $stmtVira->bind_param("s", $ordemParaBusca);
            $stmtVira->execute();
            $resVira = $stmtVira->get_result();
            $virabrequimRefData = $resVira->fetch_assoc() ?: [];
            $stmtVira->close();
        }

        // Priorizar qtd_cilindros do virabrequim se existir
        if (!empty($virabrequimRefData['qtd_cilindros'])) {
            $qtdCilindrosPredefinido = $virabrequimRefData['qtd_cilindros'];
        }
    }
}

// Função auxiliar para exibir valor ou vazio
function vVal($data, $key)
{
    return isset($data[$key]) && $data[$key] != 0 ? $data[$key] : '';
}
?>
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
                        <input type="radio" id="rolamento_1" name="rolamento_type" value="rolamento" <?php echo (isset($virabrequimRefData['tipo']) && $virabrequimRefData['tipo'] == 'rolamento') ? 'checked' : ''; ?>>
                        <label for="rolamento_1">Rolamento</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="radio-input">
                        <input type="radio" id="rolamento_2" name="rolamento_type" value="bronzina" <?php echo (isset($virabrequimRefData['tipo']) && $virabrequimRefData['tipo'] == 'bronzina') ? 'checked' : ''; ?>>
                        <label for="rolamento_2">Bronzina</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Medições -->
        <div class="section-group mb-5">
            <h3 class="section-title">Medições</h3>
            <div class="row g-4">
                <div class="col-md-4" id="folga_lateral_biela_section">
                    <div class="floating-input">
                        <input type="text" name="folga_lateral_biela" class="form-control" data-type="decimal"
                            placeholder=" " value="<?php echo vVal($virabrequimRefData, 'folga_lateral_biela'); ?>">
                        <label>Folga Lateral Biela Máx (mm)</label>
                        <div class="focus-border"></div>
                    </div>
                </div>

                <div class="col-md-4" id="folga_biela_section">
                    <div class="floating-input">
                        <input type="text" name="folga_biela" class="form-control" data-type="decimal" placeholder=" "
                            value="<?php echo vVal($virabrequimRefData, 'folga_biela'); ?>">
                        <label id="label_folga_biela">Folga Biela (mm)</label>
                        <div class="focus-border"></div>
                    </div>
                </div>

                <div class="col-md-4" id="folga_mancal_section">
                    <div class="floating-input">
                        <input type="text" name="folga_mancal" class="form-control" data-type="decimal" placeholder=" "
                            value="<?php echo vVal($virabrequimRefData, 'folga_mancal'); ?>">
                        <label id="label_folga_mancal">Folga Mancal (mm)</label>
                        <div class="focus-border"></div>
                    </div>
                </div>

                <div class="col-md-8" id="folga_lateral_eixo_section">
                    <div class="floating-input group-input">
                        <div class="range-fields">
                            <div class="floating-input">
                                <input type="text" name="folga_lateral_eixo_min" class="form-control"
                                    data-type="decimal" placeholder=" "
                                    value="<?php echo vVal($virabrequimRefData, 'folga_lateral_eixo_min'); ?>">
                                <label>mínimo</label>
                            </div>
                            <span class="range-separator">a</span>
                            <div class="floating-input">
                                <input type="text" name="folga_lateral_eixo_max" class="form-control"
                                    data-type="decimal" placeholder=" "
                                    value="<?php echo vVal($virabrequimRefData, 'folga_lateral_eixo_max'); ?>">
                                <label>máximo</label>
                            </div>
                        </div>
                        <label class="outer-label">Folga Lateral Eixo (mm)</label>
                        <div class="focus-border"></div>
                    </div>
                </div>

                <div class="col-md-4" id="empenamento_section">
                    <div class="floating-input">
                        <input type="text" name="empenamento" class="form-control" data-type="decimal" placeholder=" "
                            value="<?php echo vVal($virabrequimRefData, 'empenamento'); ?>">
                        <label>Empenamento (mm)</label>
                        <div class="focus-border"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Referência de Diâmetros (Bronzina) -->
        <div class="section-group mb-5" id="referencia_diametros_section" style="display: none;">
            <h3 class="section-title">Referência de Diâmetro</h3>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="floating-input">
                        <input type="text" name="diametro_moente" class="form-control" data-type="decimal"
                            placeholder=" " value="<?php echo vVal($virabrequimRefData, 'diametro_moente'); ?>">
                        <label>Diâmetro Moente Referência (mm)</label>
                        <div class="focus-border"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="floating-input">
                        <input type="text" name="diametro_munhao" class="form-control" data-type="decimal"
                            placeholder=" " value="<?php echo vVal($virabrequimRefData, 'diametro_munhao'); ?>">
                        <label>Diâmetro Munhão Referência (mm)</label>
                        <div class="focus-border"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Diâmetros Moente (por Cilindro) -->
        <div class="section-group mb-5" id="diametro_moente_section">
            <h3 class="section-title">Diâmetro Moente</h3>
            <div class="row g-4 mb-3">
                <div class="col-md-4">
                    <div class="floating-input">
                        <select name="qtd_cilindros" id="qtd_cilindros" class="form-control form-select" required>
                            <option value="">Selecione...</option>
                            <?php
                            $opcoesCilindros = [1, 2, 3, 4, 5, 6, 8, 10, 12];
                            foreach ($opcoesCilindros as $qtd) {
                                $selected = ($qtdCilindrosPredefinido == $qtd) ? 'selected' : '';
                                $label = $qtd . ($qtd == 1 ? ' Cilindro' : ' Cilindros');
                                echo "<option value='$qtd' $selected>$label</option>";
                            }
                            ?>
                        </select>
                        <label>Nº de Cilindros</label>
                        <div class="focus-border"></div>
                    </div>
                </div>
            </div>
            <div class="row g-4" id="moente_fields_container" style="display: none;">
                <!-- Ocultado pois as medições agora são feitas na aba de Dados -->
            </div>
        </div>

        <!-- Diâmetros Munhões -->
        <div class="section-group mb-5" id="diametro_munhoes_section">
            <h3 class="section-title">Diâmetro Munhões</h3>
            <div class="row g-4 mb-3">
                <div class="col-md-4">
                    <div class="floating-input">
                        <select name="qtd_munhoes" id="qtd_munhoes" class="form-control form-select" required>
                            <option value="">Selecione...</option>
                            <?php
                            for ($i = 1; $i <= 8; $i++) {
                                $selected = (isset($virabrequimRefData['qtd_munhoes']) && $virabrequimRefData['qtd_munhoes'] == $i) ? 'selected' : '';
                                $label = $i . ($i == 1 ? ' Munhão' : ' Munhões');
                                echo "<option value='$i' $selected>$label</option>";
                            }
                            ?>
                        </select>
                        <label>Nº de Munhões</label>
                        <div class="focus-border"></div>
                    </div>
                </div>
            </div>
            <div class="row g-4" id="munhoes_fields_container" style="display: none;">
                <!-- Ocultado pois as medições agora são feitas na aba de Dados -->
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
    document.addEventListener('DOMContentLoaded', function () {
        // Adiciona classe active aos elementos com animated-section após um pequeno delay
        setTimeout(() => {
            document.querySelectorAll('.animated-section').forEach(section => {
                section.classList.add('active');
            });
        }, 100);

        // O menu agora foca apenas na configuração de quantidades e referências.
        const qtdCilindrosSelect = document.getElementById('qtd_cilindros');
        const qtdMunhoesSelect = document.getElementById('qtd_munhoes');

        // Disparar evento change se já houver valor selecionado (vindo do PHP)
        if (qtdCilindrosSelect && qtdCilindrosSelect.value) {
            qtdCilindrosSelect.dispatchEvent(new Event('change'));
        }

        // Controle de visibilidade das seções
        function toggleMoenteMunhoes() {
            const bronzinaRadio = document.querySelector('input[name="rolamento_type"][value="bronzina"]');
            const isBronzina = bronzinaRadio ? bronzinaRadio.checked : false;

            // Seções para Bronzina
            const moenteSection = document.getElementById('diametro_moente_section');
            const munhoesSection = document.getElementById('diametro_munhoes_section');
            const refSection = document.getElementById('referencia_diametros_section');
            const lateralBielaSection = document.getElementById('folga_lateral_biela_section');

            // Campos e Labels
            const labelBiela = document.getElementById('label_folga_biela');
            const labelMancal = document.getElementById('label_folga_mancal');

            if (isBronzina) {
                // Layout Bronzina
                if (moenteSection) moenteSection.style.display = 'block';
                if (munhoesSection) munhoesSection.style.display = 'block';
                if (refSection) refSection.style.display = 'block';
                if (lateralBielaSection) lateralBielaSection.style.display = 'block';

                if (labelBiela) labelBiela.innerText = 'Folga Eixo-Bronzina Máx (mm)';
                if (labelMancal) labelMancal.innerText = 'Folga Eixo-Mancal Máx (mm)';
            } else {
                // Layout Rolamento
                if (moenteSection) moenteSection.style.display = 'none';
                if (munhoesSection) munhoesSection.style.display = 'none';
                if (refSection) refSection.style.display = 'none';
                if (lateralBielaSection) lateralBielaSection.style.display = 'none';

                if (labelBiela) labelBiela.innerText = 'Folga Biela (mm)';
                if (labelMancal) labelMancal.innerText = 'Folga Eixo Mancal (mm)';
            }

            // Ajustar obrigatoriedade
            if (qtdCilindrosSelect) qtdCilindrosSelect.required = isBronzina;
            if (qtdMunhoesSelect) qtdMunhoesSelect.required = isBronzina;
        }

        // Listeners para os radio buttons
        const radiosRolamento = document.querySelectorAll('input[name="rolamento_type"]');
        radiosRolamento.forEach(radio => {
            radio.addEventListener('change', toggleMoenteMunhoes);
        });

        // Estado inicial - executa para garantir o estado correto ao carregar
        toggleMoenteMunhoes();
    });
</script>