function calcularPastilha(input) {
    // Pegar os dados do elemento
    const cilindro = input.getAttribute('data-cilindro');
    const tipo = input.getAttribute('data-tipo');
    const lado = input.getAttribute('data-lado');

    console.log('Calculando pastilha para:', { cilindro, tipo, lado });

    if (!cilindro || !tipo || !lado) {
        console.error('Dados necessários não encontrados no input:', {cilindro, tipo, lado});
        return;
    }

    // Obter o valor da folga
    let folgaValue = input.value.trim().replace(',', '.');
    console.log('Valor da folga:', folgaValue);
    
    // Encontrar o input da pastilha atual usando o seletor correto
    let pastilhaInput = document.querySelector(`input[name="medida[${tipo}_pastilha_${lado}][${cilindro}]"]`);
    if (!pastilhaInput) {
        console.error('Input da pastilha não encontrado para:', { tipo, lado, cilindro });
        return;
    }
    
    let pastilhaAtual = pastilhaInput.value.trim().replace(',', '.');
    console.log('Valor da pastilha atual:', pastilhaAtual);
    
    // Verificar valores válidos
    if (!folgaValue || !pastilhaAtual || isNaN(folgaValue) || isNaN(pastilhaAtual)) {
        console.log('Valores inválidos:', { folgaValue, pastilhaAtual });
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

    console.log('Valores de referência:', { referenciaMin, referenciaMax });

    // Calcular valor médio de referência
    const valorReferencia = (referenciaMin + referenciaMax) / 2;
    console.log('Valor médio de referência:', valorReferencia);
    
    // Calcular pastilha nova
    const pastilhaCorrigida = (folgaValue - valorReferencia) + pastilhaAtual;
    console.log('Pastilha corrigida calculada:', pastilhaCorrigida);
    
    // Atualizar o resultado
    const elementoResultado = document.getElementById(`pc_${tipo}_${lado}_${cilindro}`);
    if (elementoResultado) {
        elementoResultado.textContent = pastilhaCorrigida.toFixed(2).replace('.', ',');
        console.log('Resultado atualizado:', elementoResultado.textContent);
    } else {
        console.error('Elemento de resultado não encontrado:', `pc_${tipo}_${lado}_${cilindro}`);
    }
}

// Adicionar função auxiliar para querySelector com texto
jQuery.expr[':'].contains = function(a, i, m) {
    return jQuery(a).text().toUpperCase()
        .indexOf(m[3].toUpperCase()) >= 0;
}; 