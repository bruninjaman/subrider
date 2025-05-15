/**
 * Módulo de Geração de PDF
 * Gerencia a criação e download de PDFs de relatórios
 */

import { mostrarStatus } from './utils.js';

/**
 * Gera um PDF do relatório atual
 */
function gerarPDF() {
    // Mostrar loader
    document.getElementById('aguarde').style.display = 'flex';
    
    try {
        // Obter os dados para o nome do arquivo
        const numeroOrdem = document.getElementById('numero_ordem').value || 'relatorio';
        const fileName = `relatorio_${numeroOrdem.replace('/', '-')}.pdf`;
        
        // Criar um elemento temporário para renderizar o PDF
        const pdfContainer = document.createElement('div');
        pdfContainer.className = 'pdf-container';
        pdfContainer.style.position = 'fixed';
        pdfContainer.style.top = '-9999px';
        pdfContainer.style.left = '-9999px';
        pdfContainer.style.width = '210mm'; // Largura A4
        pdfContainer.style.background = '#fff';
        pdfContainer.style.padding = '20px';
        pdfContainer.style.color = '#000';
        pdfContainer.style.fontFamily = 'Arial, sans-serif';
        
        // Obter os dados principais
        const dataOrdem = document.getElementById('data_ordem').value || 'N/A';
        const kmOrdem = document.getElementById('km_ordem').value || 'N/A';
        const clienteOrdem = document.getElementById('cliente_ordem').value || 'N/A';
        const motoOrdem = document.getElementById('moto_ordem').value || 'N/A';
        const dataConclusao = formatarDataBr(document.getElementById('data-conclusao').value);
        const observacoesFinais = document.getElementById('observacoes_finais').value;
        const conteudoEditor = document.getElementById('editor-personalizado').innerHTML;
        
        // Montar o HTML do relatório
        pdfContainer.innerHTML = gerarHTMLRelatorio(
            numeroOrdem, dataOrdem, kmOrdem, clienteOrdem, 
            motoOrdem, dataConclusao, observacoesFinais, conteudoEditor
        );
        
        // Adicionar o elemento ao documento
        document.body.appendChild(pdfContainer);
        
        // Usar html2canvas para converter o HTML em imagem
        html2canvas(pdfContainer, {
            scale: 2,
            useCORS: true,
            logging: false,
            allowTaint: true
        }).then(function(canvas) {
            // Remover o elemento temporário
            document.body.removeChild(pdfContainer);
            
            // Calcular dimensões do PDF (A4 = 210mm x 297mm)
            const imgWidth = 210;
            const pageHeight = 297;
            const imgHeight = canvas.height * imgWidth / canvas.width;
            let heightLeft = imgHeight;
            let position = 0;
            
            // Criar instância jsPDF
            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF('p', 'mm', 'a4');
            
            // Adicionar a primeira página
            pdf.addImage(canvas, 'JPEG', 0, position, imgWidth, imgHeight, '', 'FAST');
            heightLeft -= pageHeight;
            
            // Adicionar páginas adicionais se necessário
            while (heightLeft >= 0) {
                position = heightLeft - imgHeight;
                pdf.addPage();
                pdf.addImage(canvas, 'JPEG', 0, position, imgWidth, imgHeight, '', 'FAST');
                heightLeft -= pageHeight;
            }
            
            // Fazer o download do PDF
            pdf.save(fileName);
            
            // Ocultar loader
            document.getElementById('aguarde').style.display = 'none';
            
            // Mostrar mensagem de sucesso
            mostrarStatus('PDF gerado com sucesso! O download começou automaticamente.', 'success');
        }).catch(function(error) {
            // Se falhar, remover o elemento temporário e tentar o método alternativo
            if (document.body.contains(pdfContainer)) {
                document.body.removeChild(pdfContainer);
            }
            tentarMetodoAlternativo();
        });
    } catch (error) {
        document.getElementById('aguarde').style.display = 'none';
        mostrarStatus('Erro ao gerar PDF: ' + error.message, 'error');
        tentarMetodoAlternativo();
    }
}

/**
 * Gera o HTML do relatório para o PDF
 * @param {string} numeroOrdem - Número da ordem de serviço
 * @param {string} dataOrdem - Data da ordem
 * @param {string} kmOrdem - Quilometragem
 * @param {string} clienteOrdem - Nome do cliente
 * @param {string} motoOrdem - Motocicleta
 * @param {string} dataConclusao - Data de conclusão
 * @param {string} observacoesFinais - Observações finais
 * @param {string} conteudoEditor - Conteúdo do editor
 * @returns {string} - HTML formatado
 */
function gerarHTMLRelatorio(
    numeroOrdem, dataOrdem, kmOrdem, clienteOrdem, 
    motoOrdem, dataConclusao, observacoesFinais, conteudoEditor
) {
    return `
        <div style="text-align:center; margin-bottom:20px; border-bottom:2px solid #5a4a96; padding-bottom:10px;">
            <h1 style="color:#5a4a96; font-size:18pt; margin:5px 0;">RELATÓRIO DE SERVIÇO</h1>
            <p style="margin:5px 0; font-size:14pt;">Ordem de Serviço: ${numeroOrdem}</p>
        </div>
        
        <div style="margin-bottom:20px;">
            <table style="width:100%; border-collapse:collapse; font-size:12pt;">
                <tr>
                    <td style="font-weight:bold; width:30%; padding:5px; border-bottom:1px solid #eee;">Cliente:</td>
                    <td style="padding:5px; border-bottom:1px solid #eee;">${clienteOrdem}</td>
                </tr>
                <tr>
                    <td style="font-weight:bold; width:30%; padding:5px; border-bottom:1px solid #eee;">Motocicleta:</td>
                    <td style="padding:5px; border-bottom:1px solid #eee;">${motoOrdem}</td>
                </tr>
                <tr>
                    <td style="font-weight:bold; width:30%; padding:5px; border-bottom:1px solid #eee;">Data:</td>
                    <td style="padding:5px; border-bottom:1px solid #eee;">${dataOrdem}</td>
                </tr>
                <tr>
                    <td style="font-weight:bold; width:30%; padding:5px; border-bottom:1px solid #eee;">Quilometragem:</td>
                    <td style="padding:5px; border-bottom:1px solid #eee;">${kmOrdem}</td>
                </tr>
            </table>
        </div>
        
        <div style="margin-top:20px;">
            <h2 style="color:#5a4a96; font-size:14pt; margin:10px 0 15px 0; border-bottom:1px solid #ccc; padding-bottom:5px;">DETALHES DO SERVIÇO</h2>
            <div style="font-size:11pt; line-height:1.4;">${conteudoEditor}</div>
        </div>
        
        ${observacoesFinais ? `
        <div style="margin-top:20px; border-top:1px dashed #ccc; padding-top:10px;">
            <h3 style="color:#5a4a96; font-size:12pt; margin:0 0 10px 0;">OBSERVAÇÕES FINAIS</h3>
            <p style="margin:5px 0;">${observacoesFinais}</p>
        </div>
        ` : ''}
        
        <div style="margin-top:30px; text-align:right;">
            <p><strong>Data de conclusão:</strong> ${dataConclusao}</p>
        </div>
    `;
}

/**
 * Método alternativo para geração de PDF usando impressão do navegador
 */
function tentarMetodoAlternativo() {
    try {
        // Obter número da ordem para nome do arquivo
        const numeroOrdem = document.getElementById('numero_ordem').value || 'relatorio';
        
        // Usar o método de impressão do navegador como fallback
        const printWindow = window.open('', '_blank');
        if (!printWindow) {
            throw new Error("Não foi possível abrir uma nova janela. Verifique se o bloqueador de pop-ups está desativado.");
        }
        
        // Obter os dados principais
        const dataOrdem = document.getElementById('data_ordem').value || 'N/A';
        const kmOrdem = document.getElementById('km_ordem').value || 'N/A';
        const clienteOrdem = document.getElementById('cliente_ordem').value || 'N/A';
        const motoOrdem = document.getElementById('moto_ordem').value || 'N/A';
        const dataConclusao = formatarDataBr(document.getElementById('data-conclusao').value);
        const observacoesFinais = document.getElementById('observacoes_finais').value;
        const conteudoEditor = document.getElementById('editor-personalizado').innerHTML;
        
        // Criar o conteúdo para impressão com estilos inline
        printWindow.document.write(gerarHTMLImpressao(
            numeroOrdem, dataOrdem, kmOrdem, clienteOrdem, 
            motoOrdem, dataConclusao, observacoesFinais, conteudoEditor
        ));
        
        // Ocultar loader
        document.getElementById('aguarde').style.display = 'none';
        
        // Atualizar mensagem de status
        mostrarStatus('Método alternativo: Relatório aberto em nova aba. Use Ctrl+P para salvar como PDF.', 'info');
        
    } catch (error) {
        mostrarStatus('Não foi possível gerar o PDF: ' + error.message, 'error');
    }
}

/**
 * Gera o HTML para o método de impressão alternativo
 * @param {string} numeroOrdem - Número da ordem de serviço
 * @param {string} dataOrdem - Data da ordem
 * @param {string} kmOrdem - Quilometragem
 * @param {string} clienteOrdem - Nome do cliente
 * @param {string} motoOrdem - Motocicleta
 * @param {string} dataConclusao - Data de conclusão
 * @param {string} observacoesFinais - Observações finais
 * @param {string} conteudoEditor - Conteúdo do editor
 * @returns {string} - HTML formatado
 */
function gerarHTMLImpressao(
    numeroOrdem, dataOrdem, kmOrdem, clienteOrdem, 
    motoOrdem, dataConclusao, observacoesFinais, conteudoEditor
) {
    return `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Relatório OS ${numeroOrdem}</title>
            <meta charset="UTF-8">
            <style>
                @page {
                    size: A4;
                    margin: 1cm;
                }
                body { 
                    font-family: Arial, sans-serif; 
                    color: #000; 
                    background: #fff; 
                    padding: 20px;
                    font-size: 12pt;
                    line-height: 1.5;
                }
                h1, h2, h3 { color: #5a4a96; }
                h1 { font-size: 18pt; margin: 0 0 10px 0; }
                h2 { font-size: 14pt; margin: 15px 0 10px 0; }
                h3 { font-size: 12pt; margin: 10px 0 5px 0; }
                p { margin: 5px 0; }
                table { width: 100%; border-collapse: collapse; margin: 15px 0; }
                td { padding: 8px; border-bottom: 1px solid #ddd; }
                .header { 
                    text-align: center; 
                    margin-bottom: 20px; 
                    padding-bottom: 15px;
                    border-bottom: 2px solid #5a4a96; 
                }
                .info-section { margin-bottom: 25px; }
                .content-section { margin-top: 25px; }
                .obs-section { 
                    margin-top: 20px; 
                    padding-top: 15px; 
                    border-top: 1px dashed #ccc; 
                }
                .conclusion { 
                    margin-top: 30px; 
                    text-align: right; 
                }
                ul, ol { margin: 5px 0 10px 20px; padding-left: 15px; }
                li { margin-bottom: 5px; }
                .pdf-instructions {
                    display: block;
                    position: fixed;
                    top: 0;
                    left: 0;
                    right: 0;
                    background: #ffff99;
                    color: #333;
                    padding: 10px;
                    border-bottom: 2px solid #ccc;
                    text-align: center;
                    font-size: 14px;
                    z-index: 9999;
                }
                .pdf-instructions b {
                    color: #5a4a96;
                }
                @media print {
                    .pdf-instructions {
                        display: none;
                    }
                }
            </style>
        </head>
        <body>
            <div class="pdf-instructions">
                Para salvar como PDF: <b>Pressione Ctrl+P</b> (ou Cmd+P no Mac), 
                selecione <b>"Salvar como PDF"</b> na opção de impressora/destino, 
                e depois clique em <b>"Salvar"</b>.
            </div>
            
            <div class="header">
                <h1>RELATÓRIO DE SERVIÇO</h1>
                <p>Ordem de Serviço: ${numeroOrdem}</p>
            </div>
            
            <div class="info-section">
                <table>
                    <tr>
                        <td style="font-weight: bold; width: 30%;">Cliente:</td>
                        <td>${clienteOrdem}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; width: 30%;">Motocicleta:</td>
                        <td>${motoOrdem}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; width: 30%;">Data:</td>
                        <td>${dataOrdem}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; width: 30%;">Quilometragem:</td>
                        <td>${kmOrdem}</td>
                    </tr>
                </table>
            </div>
            
            <div class="content-section">
                <h2>DETALHES DO SERVIÇO</h2>
                <div id="editor-content-print"></div>
            </div>
            
            ${observacoesFinais ? `
            <div class="obs-section">
                <h3>OBSERVAÇÕES FINAIS</h3>
                <p>${observacoesFinais}</p>
            </div>
            ` : ''}
            
            <div class="conclusion">
                <p><strong>Data de conclusão:</strong> ${dataConclusao}</p>
            </div>
            
            <script>
                window.onload = function() {
                    // Preencher conteúdo do editor
                    document.getElementById('editor-content-print').innerHTML = \`${conteudoEditor}\`;
                };
            </script>
        </body>
        </html>
    `;
}

/**
 * Formatar data em formato brasileiro (DD/MM/YYYY)
 * @param {string} dataString - String de data
 * @returns {string} - Data formatada
 */
function formatarDataBr(dataString) {
    if (!dataString) return '';
    const data = new Date(dataString);
    return data.toLocaleDateString('pt-BR');
}

export { gerarPDF }; 