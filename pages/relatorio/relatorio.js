/**
 * Arquivo principal do Relatório
 * Inicializa o sistema e integra os diferentes módulos
 */

import { inicializarEditor } from './modules/editor.js';
import { carregarRelatorio, salvarRelatorio } from './modules/relatorioApi.js';
import { gerarPDF } from './modules/pdfGenerator.js';
import { mostrarStatus } from './modules/utils.js';

// Inicializar quando o DOM estiver pronto
document.addEventListener("DOMContentLoaded", function() {
    // Inicializar o editor personalizado
    inicializarEditor();
    
    // Carregar dados do relatório se existir
    const ordem_id = document.getElementById('ordem_id').value;
    carregarRelatorio(ordem_id);

    // Configurar evento de salvar relatório
    const btnSalvar = document.getElementById('btn-salvar');
    if (btnSalvar) {
        btnSalvar.addEventListener('click', function(e) {
            e.preventDefault();
            salvarRelatorio(ordem_id);
        });
    }

    // Configurar evento de gerar PDF
    const btnGerarPDF = document.getElementById('btn-gerar-pdf');
    if (btnGerarPDF) {
        btnGerarPDF.addEventListener('click', function(e) {
            e.preventDefault();
            gerarPDF();
        });
    }

    // Adicionar botão para visualizar prévia do PDF no formulário
    const formActions = document.querySelector('.form-actions');
    const previewBtn = document.createElement('button');
    previewBtn.type = 'button';
    previewBtn.id = 'btn-preview-pdf';
    previewBtn.className = 'button';
    previewBtn.innerHTML = 'Alternar Prévia do PDF';
    
    if (formActions) {
        formActions.insertBefore(previewBtn, btnGerarPDF);
        
        // Configurar evento para alternar visualização de prévia
        previewBtn.addEventListener('click', function(e) {
            e.preventDefault();
            alternarPreviewPDF();
        });
    }

    // Configurar evento de fechar modal
    const btnFecharModal = document.getElementById('modal-fechar');
    if (btnFecharModal) {
        btnFecharModal.addEventListener('click', function() {
            document.getElementById('modal-confirmacao').style.display = 'none';
        });
    }
    
    // Prevenir envio do formulário pelo método tradicional
    const form = document.getElementById('relatorio-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
        });
    }
});

/**
 * Alterna entre visualização normal e prévia do PDF
 */
function alternarPreviewPDF() {
    const container = document.querySelector('.relatorio-container');
    const editor = document.getElementById('editor-personalizado');
    const previewBtn = document.getElementById('btn-preview-pdf');
    
    if (container.classList.contains('pdf-preview-mode')) {
        // Voltar ao modo normal
        container.classList.remove('pdf-preview-mode');
        previewBtn.textContent = 'Alternar Prévia do PDF';
        mostrarStatus('Modo de edição normal ativado', 'info');
    } else {
        // Ativar modo prévia de PDF
        container.classList.add('pdf-preview-mode');
        previewBtn.textContent = 'Voltar ao Modo Normal';
        mostrarStatus('Prévia do PDF ativada. Esta visualização é similar ao resultado final do PDF.', 'info');
        
        // Otimizar o conteúdo do editor para melhor visualização
        const titulos = editor.querySelectorAll('h1, h2, h3, h4, h5, h6');
        const paragrafos = editor.querySelectorAll('p');
        const listas = editor.querySelectorAll('ul, ol');
        const itens = editor.querySelectorAll('li');
        
        // Não precisamos adicionar estilos inline aqui pois estamos usando 
        // classes CSS para controlar a aparência no modo de prévia
    }
}