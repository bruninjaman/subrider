function calcularPastilha(input, tipo, lado, cilindro) {
    console.log('Calculando pastilha:', {tipo, lado, cilindro});

    // Obter o valor da folga
    let folgaValue = parseFloat(input.value.replace(',', '.'));
    console.log('Valor da folga:', folgaValue);

    if (isNaN(folgaValue)) {
        document.getElementById(`pastilha_${tipo}_${lado}_${cilindro}`).textContent = '-';
        return;
    }

    // Obter os valores de referência diretamente da tabela
    let referenciaCell;
    const rows = document.querySelectorAll('tr');
    for (let row of rows) {
        const firstCell = row.querySelector('td');
        if (firstCell) {
            const cellText = firstCell.textContent.trim();
            if (tipo === 'adm' && cellText === `Folga válvula admissão ${lado}`) {
                referenciaCell = row.querySelector('td:nth-child(2)');
                break;
            } else if (tipo === 'esc' && cellText === `Folga válvula escape ${lado}`) {
                referenciaCell = row.querySelector('td:nth-child(2)');
                break;
            }
        }
    }

    if (!referenciaCell) {
        console.error('Célula de referência não encontrada');
        return;
    }

    // Extrair os valores min e max da referência (formato: "0,15 a 0,25")
    let refText = referenciaCell.textContent.trim();
    let [min, max] = refText.split(' a ').map(val => parseFloat(val.replace(',', '.')));
    
    // Calcular o valor médio do intervalo (referência)
    let valorReferencia = (min + max) / 2;
    console.log('Valor referência:', valorReferencia);
    
    // Calcular a pastilha (PC = F - R + PA)
    let pastilhaAntiga = 3.00; // Valor fixo da pastilha antiga
    let pastilhaCorrigida = (folgaValue - valorReferencia) + pastilhaAntiga;
    console.log('Pastilha corrigida:', pastilhaCorrigida);
    
    // Atualizar a célula com o valor calculado
    let celulaPastilha = document.getElementById(`pastilha_${tipo}_${lado}_${cilindro}`);
    console.log('Elemento célula:', celulaPastilha);

    if (celulaPastilha) {
        let valorFormatado = pastilhaCorrigida.toFixed(2).replace('.', ',');
        celulaPastilha.textContent = valorFormatado;
        console.log('Valor atualizado:', valorFormatado);
    } else {
        console.error('Célula não encontrada:', `pastilha_${tipo}_${lado}_${cilindro}`);
    }
}

// Adicionar função auxiliar para querySelector com texto
jQuery.expr[':'].contains = function(a, i, m) {
    return jQuery(a).text().toUpperCase()
        .indexOf(m[3].toUpperCase()) >= 0;
}; 