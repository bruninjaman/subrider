function calcularPastilha(input) {
    // Pegar os dados do elemento
    const cilindro = input.getAttribute('data-cilindro');
    const tipo = input.getAttribute('data-tipo');
    const lado = input.getAttribute('data-lado');

    if (!cilindro || !tipo || !lado) {
        console.error('Dados necessários não encontrados no input:', {cilindro, tipo, lado});
        return;
    }

    // Encontrar os inputs relacionados
    const folgaInput = document.querySelector(`input[name="medida[${tipo}_folga_${lado}][${cilindro}]"]`);
    const pastilhaInput = document.querySelector(`input[name="medida[${tipo}_pastilha_${lado}][${cilindro}]"]`);

    if (!folgaInput || !pastilhaInput) {
        console.error('Inputs não encontrados para:', { tipo, lado, cilindro });
        return;
    }

    // Obter os valores
    let folgaValue = folgaInput.value.trim().replace(',', '.');
    let pastilhaAtual = pastilhaInput.value.trim().replace(',', '.');
    
    // Verificar valores válidos
    if (!folgaValue || !pastilhaAtual || isNaN(folgaValue) || isNaN(pastilhaAtual)) {
        document.getElementById(`pc_${tipo}_${lado}_${cilindro}`).textContent = '-';
        return;
    }

    // Converter para números
    folgaValue = parseFloat(folgaValue);
    pastilhaAtual = parseFloat(pastilhaAtual);

    // Obter valores de referência da folga
    const referenciaMin = tipo === 'adm' ? 
        parseFloat(document.getElementById('val_adm_limite_min').value) :
        parseFloat(document.getElementById('val_esc_limite_min').value);
    const referenciaMax = tipo === 'adm' ? 
        parseFloat(document.getElementById('val_adm_limite_max').value) :
        parseFloat(document.getElementById('val_esc_limite_max').value);

    // Verificar se o valor está dentro da referência
    const estaDentroReferencia = folgaValue >= referenciaMin && folgaValue <= referenciaMax;
    
    // Calcular pastilha nova usando o valor de referência da folga
    const pastilhaCorrigida = (folgaValue - ((referenciaMax + referenciaMin ) /2)) + pastilhaAtual;
    
    // Atualizar o resultado
    const elementoResultado = document.getElementById(`pc_${tipo}_${lado}_${cilindro}`);
    if (elementoResultado) {
        // Só mostra a PC se o valor estiver fora da referência
        elementoResultado.textContent = estaDentroReferencia ? '-' : pastilhaCorrigida.toFixed(2).replace('.', ',');
    } else {
        console.error('Elemento de resultado não encontrado:', `pc_${tipo}_${lado}_${cilindro}`);
    }
}

// Adicionar função auxiliar para querySelector com texto
jQuery.expr[':'].contains = function(a, i, m) {
    return jQuery(a).text().toUpperCase()
        .indexOf(m[3].toUpperCase()) >= 0;
};

// Inicializar os eventos quando o documento estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    // Vincular eventos para todos os inputs de folga e pastilha
    const inputs = document.querySelectorAll('.folga-input, .pastilha-input');
    inputs.forEach(input => {
        input.addEventListener('change', function() {
            calcularPastilha(this);
        });
        input.addEventListener('input', function() {
            calcularPastilha(this);
        });
    });

    // Inicializar cálculos para inputs que já têm valores
    inputs.forEach(input => {
        if (input.value) {
            calcularPastilha(input);
        }
    });
}); 