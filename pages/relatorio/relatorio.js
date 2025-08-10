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