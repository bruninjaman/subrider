document.addEventListener('DOMContentLoaded', function () {
    // Funções para o menu do cabeçote
    const initCabecoteForm = () => {
        const form = document.getElementById('cabecoteForm');
        if (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(form);
                const data = {};

                for (let [key, value] of formData.entries()) {
                    data[key] = value === '' ? null : value;
                }

                console.log(data);
            });
        }

        const tuchoSwitch = document.getElementById('tucho');
        if (tuchoSwitch) {
            tuchoSwitch.addEventListener('change', function () {
                console.log('Tucho Mecânico:', this.checked);
            });
        }

        // Toggle engine configuration section based on cylinder count
        const numCilindrosInput = document.getElementById('num_cilindros');
        const engineConfigSection = document.getElementById('engine_config_section');

        if (numCilindrosInput && engineConfigSection) {
            const toggleEngineConfig = () => {
                const cylinderCount = parseInt(numCilindrosInput.value) || 0;

                // Hide engine configuration if only 1 cylinder
                if (cylinderCount === 1) {
                    engineConfigSection.style.display = 'none';
                    // Clear any selected engine type
                    const engineTypeInputs = document.querySelectorAll('input[name="engine_type"]');
                    engineTypeInputs.forEach(input => input.checked = false);
                } else if (cylinderCount > 1) {
                    engineConfigSection.style.display = 'block';
                }
            };

            numCilindrosInput.addEventListener('input', toggleEngineConfig);
            numCilindrosInput.addEventListener('change', toggleEngineConfig);

            // Run on page load to handle pre-filled values
            toggleEngineConfig();
        }
    };

    // Funções para toggle de campos do cabeçote
    const initFieldsToggle = () => {
        const toggleFields = () => {
            const ohcField = document.getElementById('ohc_fields');
            const dohcFields = document.getElementById('dohc_fields');
            const isOHC = document.getElementById('ohc')?.checked;
            const isDOHC = document.getElementById('dohc')?.checked;

            if (ohcField && dohcFields) {
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
        };

        const valveTypeInputs = document.querySelectorAll('input[name="valve_type"]');
        valveTypeInputs.forEach(input => {
            input.addEventListener('change', toggleFields);
        });
    };

    // Funções para o virabrequim
    const initVirabrequim = () => {
        const rolamento1 = document.getElementById('rolamento_1');
        const rolamento2 = document.getElementById('rolamento_2');

        const rolamentoSections = [
            document.getElementById('folga_lateral_biela_section'),
            document.getElementById('folga_lateral_eixo_section'),
            document.getElementById('empenamento_max_section')
        ];

        const bronzinaSections = [
            document.getElementById('folga_eixo_bronzina_section'),
            document.getElementById('folga_eixo_mancal_section')
        ];

        const toggleSections = () => {
            const isBronzina = rolamento2?.checked;

            rolamentoSections.forEach(section => {
                if (section) {
                    if (isBronzina) section.classList.add('hidden');
                    else section.classList.remove('hidden');
                }
            });

            bronzinaSections.forEach(section => {
                if (section) {
                    if (isBronzina) section.classList.remove('hidden');
                    else section.classList.add('hidden');
                }
            });
        };

        if (rolamento1 && rolamento2) {
            rolamento1.addEventListener('change', toggleSections);
            rolamento2.addEventListener('change', toggleSections);
            toggleSections();
        }
    };

    // Função para converter vírgula em ponto decimal
    function convertCommaToDot(value) {
        if (typeof value === 'string') {
            return value.replace(',', '.');
        }
        return value;
    }

    // Função para formatar número com vírgula
    function formatNumberWithComma(value) {
        if (typeof value === 'number') {
            return value.toString().replace('.', ',');
        }
        return value;
    }

    // Função global para inicializar inputs decimais (útil para campos dinâmicos)
    window.initDecimalInputs = function () {
        const decimalInputs = document.querySelectorAll('input[data-type="decimal"]:not([data-initialized])');

        decimalInputs.forEach(input => {
            input.setAttribute('data-initialized', 'true');

            // Formata valor inicial se existir
            if (input.value) {
                input.value = formatNumberWithComma(input.value);
            }

            // Formata ao perder o foco
            input.addEventListener('blur', function () {
                if (this.value) {
                    this.value = formatNumberWithComma(this.value);
                }
            });
        });
    };

    // Adiciona evento para converter valores antes do submit
    document.addEventListener('submit', function (e) {
        const form = e.target;
        const decimalInputs = form.querySelectorAll('input[data-type="decimal"]');

        decimalInputs.forEach(input => {
            if (input.value) {
                input.value = convertCommaToDot(input.value);
            }
        });
    });

    // Inicializa inputs decimais existentes na página
    initDecimalInputs();

    // Inicializar todas as funções
    initCabecoteForm();
    initFieldsToggle();
    initVirabrequim();
}); 