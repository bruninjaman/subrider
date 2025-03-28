function calcularPastilha(input) {
    // Pegar os dados do elemento
    const cilindro = input.getAttribute('data-cilindro');
    const tipo = input.getAttribute('data-tipo');
    const lado = input.getAttribute('data-lado');

    if (!cilindro || !tipo || !lado) {
        console.error('Dados necessários não encontrados no input:', {cilindro, tipo, lado});
        return;
    }

    // Obter o valor da folga
    let folgaValue = input.value.trim().replace(',', '.');
    
    // Encontrar o input da pastilha atual usando o seletor correto
    let pastilhaInput = document.querySelector(`input[name="medida[${tipo}_pastilha_${lado}][${cilindro}]"]`);
    if (!pastilhaInput) {
        console.error('Input da pastilha não encontrado');
        return;
    }
    
    let pastilhaAtual = pastilhaInput.value.trim().replace(',', '.');
    
    // Verificar valores válidos
    if (!folgaValue || !pastilhaAtual || isNaN(folgaValue) || isNaN(pastilhaAtual)) {
        document.getElementById(`pc_${tipo}_${lado}_${cilindro}`).textContent = '-';
        return;
    }

    // Converter para números
    folgaValue = parseFloat(folgaValue);
    pastilhaAtual = parseFloat(pastilhaAtual);

    // Obter valores de referência
    const referenciaMin = tipo === 'adm' ? 
        parseFloat(document.getElementById('val_adm_limite_min').value) :
        parseFloat(document.getElementById('val_esc_limite_min').value);
    const referenciaMax = tipo === 'adm' ? 
        parseFloat(document.getElementById('val_adm_limite_max').value) :
        parseFloat(document.getElementById('val_esc_limite_max').value);

    // Calcular valor médio de referência
    const valorReferencia = (referenciaMin + referenciaMax) / 2;
    
    // Calcular pastilha nova
    const pastilhaCorrigida = (folgaValue - valorReferencia) + pastilhaAtual;
    
    // Atualizar o resultado
    const elementoResultado = document.getElementById(`pc_${tipo}_${lado}_${cilindro}`);
    if (elementoResultado) {
        elementoResultado.textContent = pastilhaCorrigida.toFixed(2).replace('.', ',');
    }
}

// Adicionar função auxiliar para querySelector com texto
jQuery.expr[':'].contains = function(a, i, m) {
    return jQuery(a).text().toUpperCase()
        .indexOf(m[3].toUpperCase()) >= 0;
}; 