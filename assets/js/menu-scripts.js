document.addEventListener('DOMContentLoaded', function() {
    // Funções para o menu do cabeçote
    const initCabecoteForm = () => {
        const form = document.getElementById('cabecoteForm');
        if (form) {
            form.addEventListener('submit', function(e) {
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
            tuchoSwitch.addEventListener('change', function() {
                console.log('Tucho Mecânico:', this.checked);
            });
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
        const sectionsToHide = [
            document.getElementById('folga_lateral_biela_section'),
            document.getElementById('folga_lateral_eixo_section'),
            document.getElementById('empenamento_max_section')
        ];

        const toggleSections = () => {
            if (rolamento2?.checked) {
                sectionsToHide.forEach(section => section?.classList.add('hidden'));
            } else {
                sectionsToHide.forEach(section => section?.classList.remove('hidden'));
            }
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

    // Adiciona evento para converter valores antes do submit
    document.addEventListener('submit', function(e) {
        const form = e.target;
        const decimalInputs = form.querySelectorAll('input[data-type="decimal"]');
        
        decimalInputs.forEach(input => {
            if (input.value) {
                input.value = convertCommaToDot(input.value);
            }
        });
    });

    // Adiciona evento para formatar valores ao exibir
    const decimalInputs = document.querySelectorAll('input[data-type="decimal"]');
    
    decimalInputs.forEach(input => {
        // Formata valor inicial se existir
        if (input.value) {
            input.value = formatNumberWithComma(input.value);
        }
        
        // Formata ao perder o foco
        input.addEventListener('blur', function() {
            if (this.value) {
                this.value = formatNumberWithComma(this.value);
            }
        });
    });

    // Inicializar todas as funções
    initCabecoteForm();
    initFieldsToggle();
    initVirabrequim();
}); 