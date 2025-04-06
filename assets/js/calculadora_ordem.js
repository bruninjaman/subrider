function atualizarValorTotal(input) {
    const tr = input.closest('tr');
    const quantidade = parseFloat(tr.querySelector('[data-cell="Quantidade"]').textContent);
    const valorUnitario = parseFloat(tr.querySelector('[data-cell="Valor Unitário"]').textContent.replace('R$ ', '').replace('.', '').replace(',', '.'));
    const valorTotal = quantidade * valorUnitario;
    
    tr.querySelector('[data-cell="Valor Total"]').textContent = formatarMoeda(valorTotal);
    atualizarTotais();
}

function atualizarTotais() {
    fetch('scripts/calcular_totais.php?ordem=' + ordemId)
        .then(response => response.json())
        .then(totais => {
            document.querySelector('.total td:last-child').textContent = formatarMoeda(totais.total);
            document.querySelector('.totalbold:last-child').textContent = formatarMoeda(totais.saldo);
        });
}

function formatarMoeda(valor) {
    return 'R$ ' + valor.toFixed(2).replace('.', ',').replace(/(\d)(?=(\d{3})+\,)/g, "$1.");
}

// Adiciona listeners para os campos editáveis
document.querySelectorAll('[data-cell="Quantidade"], [data-cell="Valor Unitário"]').forEach(cell => {
    cell.addEventListener('blur', () => atualizarValorTotal(cell));
});