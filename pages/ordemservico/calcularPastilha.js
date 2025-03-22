function calcularPastilha(input) {
    // Pegar os dados do elemento usando getAttribute para garantir
    const cilindro = input.getAttribute('data-cilindro');
    const tipo = input.getAttribute('data-tipo');
    const lado = input.getAttribute('data-lado');
    
    // Log para debug
    console.log('Dados do input:', {
        cilindro: cilindro,
        tipo: tipo,
        lado: lado,
        valor: input.value
    });

    // Verificar se todos os dados necessários estão presentes
    if (!cilindro || !tipo || !lado) {
        console.error('Dados necessários não encontrados no input:', {cilindro, tipo, lado});
        return;
    }

    // Obter o valor da folga
    let folgaValue = input.value.trim().replace(',', '.');
    
    // Encontrar o input da pastilha atual
    let pastilhaInput = document.querySelector(`input[name="medida[${tipo}_pastilha_${lado}][${cilindro}]"]`);
    if (!pastilhaInput) {
        console.error('Input da pastilha não encontrado');
        return;
    }
    
    let pastilhaAtual = pastilhaInput.value.trim().replace(',', '.');
    
    // Verificar se os valores são numéricos válidos
    if (!folgaValue || !pastilhaAtual || isNaN(folgaValue) || isNaN(pastilhaAtual)) {
        document.getElementById(`pc_${tipo}_${lado}_${cilindro}`).textContent = '-';
        return;
    }
    
    // Converter para números
    folgaValue = parseFloat(folgaValue);
    pastilhaAtual = parseFloat(pastilhaAtual);

    // Buscar a referência
    let textoReferencia = `Folga válvula ${tipo === 'adm' ? 'admissão' : 'escape'} ${lado}`;
    let referenciaCell = null;

    // Encontrar a célula de referência na mesma linha do input
    let linha = input.closest('tr');
    if (linha) {
        referenciaCell = linha.querySelector('td:nth-child(2)');
    }

    if (!referenciaCell) {
        console.error('Célula de referência não encontrada');
        return;
    }

    // Extrair os valores min e max da referência (formato: "0,15 a 0,25")
    let refText = referenciaCell.textContent.trim();
    let [min, max] = refText.split(' a ').map(val => parseFloat(val.replace(',', '.')));
    
    if (isNaN(min) || isNaN(max)) {
        console.error('Valores de referência inválidos:', refText);
        return;
    }
    
    // Calcular o valor médio do intervalo (referência)
    let valorReferencia = (min + max) / 2;
    
    // Calcular a pastilha nova: (folga - referencia) + pastilha atual
    let pastilhaCorrigida = (folgaValue - valorReferencia) + pastilhaAtual;
    
    // Atualizar o valor calculado
    let celulaPastilhaCorrigida = document.getElementById(`pc_${tipo}_${lado}_${cilindro}`);
    if (celulaPastilhaCorrigida) {
        let valorFormatado = pastilhaCorrigida.toFixed(2).replace('.', ',');
        celulaPastilhaCorrigida.textContent = valorFormatado;
        
        console.log('Cálculo realizado:', {
            folga: folgaValue,
            pastilhaAtual: pastilhaAtual,
            valorReferencia: valorReferencia,
            pastilhaCorrigida: pastilhaCorrigida,
            valorFormatado: valorFormatado
        });
    } else {
        console.error(`Elemento de resultado não encontrado: pc_${tipo}_${lado}_${cilindro}`);
    }
}

// Adicionar função auxiliar para querySelector com texto
jQuery.expr[':'].contains = function(a, i, m) {
    return jQuery(a).text().toUpperCase()
        .indexOf(m[3].toUpperCase()) >= 0;
}; 