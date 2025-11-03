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
        pdfContainer.style.padding = '15px'; // Reduzido de 20px para 15px
        pdfContainer.style.color = '#000';
        pdfContainer.style.fontFamily = 'Arial, sans-serif';
        
        // Obter os dados principais
        const dataOrdem = document.getElementById('data_ordem').value || 'N/A';
        const kmOrdem = document.getElementById('km_ordem').value || 'N/A';
        const clienteOrdem = document.getElementById('cliente_ordem').value || 'N/A';
        const motoOrdem = document.getElementById('moto_ordem').value || 'N/A';
        const dataConclusao = formatarDataBr(document.getElementById('data-conclusao').value);
        const observacoesFinais = (function(){
            const el = document.getElementById('observacoes-finais-editor');
            return el ? el.innerHTML : '';
        })();
        const conteudoEditor = document.getElementById('editor-personalizado').innerHTML;
        
        // Criar elemento temporário para avaliar o tamanho do conteúdo
        const tempAvaliacao = document.createElement('div');
        tempAvaliacao.innerHTML = conteudoEditor;
        const nivelOtimizacao = avaliarTamanhoConteudo(tempAvaliacao);
        
        // Processar o conteúdo do editor para otimizar para PDF
        const conteudoOtimizado = otimizarConteudoParaPDF(conteudoEditor);
        
        // Montar o HTML do relatório
        pdfContainer.innerHTML = gerarHTMLRelatorio(
            numeroOrdem, dataOrdem, kmOrdem, clienteOrdem, 
            motoOrdem, dataConclusao, observacoesFinais, conteudoOtimizado
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
            
            // Criar instância jsPDF
            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF('p', 'mm', 'a4');
            
            // Calcular dimensões
            const imgWidth = 210; // largura A4 em mm
            const pageHeight = 297; // altura A4 em mm
            const imgHeight = canvas.height * imgWidth / canvas.width;
            
            // Se a altura for menor que a página, simplesmente adicione a imagem
            if (imgHeight <= pageHeight - 10) {
                pdf.addImage(canvas, 'JPEG', 0, 0, imgWidth, imgHeight, '', 'FAST');
            } else {
                // A imagem é maior que uma página, precisamos dividi-la
                
                // Proporção entre pixels da imagem e mm do PDF
                const mmToPx = canvas.width / imgWidth;
                
                // Altura máxima de cada página em pixels
                const pageHeightPx = pageHeight * mmToPx;
                
                // Número de páginas necessárias
                const totalPages = Math.ceil(canvas.height / pageHeightPx);
                
                // Para cada página
                for (let i = 0; i < totalPages; i++) {
                    // Se não for a primeira página, adicionar uma nova
                    if (i > 0) {
                        pdf.addPage();
                    }
                    
                    // Calcular posição de origem
                    const sourceY = i * pageHeightPx;
                    const sourceHeight = Math.min(pageHeightPx, canvas.height - sourceY);
                    
                    // Criar canvas temporário para esta página
                    const tempCanvas = document.createElement('canvas');
                    tempCanvas.width = canvas.width;
                    tempCanvas.height = sourceHeight;
                    
                    // Copiar parte da imagem original para o canvas temporário
                    const ctx = tempCanvas.getContext('2d');
                    ctx.drawImage(
                        canvas, 
                        0, sourceY, canvas.width, sourceHeight, 
                        0, 0, canvas.width, sourceHeight
                    );
                    
                    // Calcular altura proporcional no PDF
                    const destHeight = sourceHeight * imgWidth / canvas.width;
                    
                    // Adicionar imagem ao PDF
                    pdf.addImage(tempCanvas, 'JPEG', 0, 0, imgWidth, destHeight, '', 'FAST');
                }
            }
            
            // Fazer o download do PDF
            pdf.save(fileName);
            
            // Ocultar loader
            document.getElementById('aguarde').style.display = 'none';
            
            // Mostrar mensagem de sucesso com informações sobre a otimização
            let mensagemOtimizacao = '';
            if (nivelOtimizacao === 'compacto') {
                mensagemOtimizacao = 'Conteúdo denso detectado: aplicada otimização de tamanho. ';
            } else if (nivelOtimizacao === 'muitoCompacto') {
                mensagemOtimizacao = 'Conteúdo muito extenso: aplicada otimização máxima. ';
            }
            
            mostrarStatus(`${mensagemOtimizacao}PDF gerado com sucesso! O download começou automaticamente.`, 'success');
        }).catch(function(error) {
            // Se falhar, remover o elemento temporário e tentar o método alternativo
            if (document.body.contains(pdfContainer)) {
                document.body.removeChild(pdfContainer);
            }
            console.error("Erro ao gerar PDF:", error);
            tentarMetodoAlternativo();
        });
    } catch (error) {
        document.getElementById('aguarde').style.display = 'none';
        mostrarStatus('Erro ao gerar PDF: ' + error.message, 'error');
        console.error("Exceção ao gerar PDF:", error);
        tentarMetodoAlternativo();
    }
}

/**
 * Gera o PDF via servidor (Dompdf) e inicia download
 */
async function gerarPDFServidor() {
    const loader = document.getElementById('aguarde');
    if (loader) loader.style.display = 'flex';

    try {
        const ordemId = document.getElementById('ordem_id')?.value || document.getElementById('numero_ordem')?.value || '';
        if (!ordemId) {
            throw new Error('ID da ordem não encontrado.');
        }

        const url = `scripts/relatorio/pdf.php?ordem=${encodeURIComponent(ordemId)}`;
        const response = await fetch(url, { method: 'GET', credentials: 'same-origin' });

        if (!response.ok) {
            if (response.status === 401) {
                throw new Error('Sessão expirada ou usuário não autenticado.');
            }
            const text = await response.text();
            throw new Error(text || `Falha ao gerar PDF (HTTP ${response.status}).`);
        }

        const contentType = response.headers.get('Content-Type') || '';
        if (!contentType.includes('application/pdf')) {
            const text = await response.text();
            throw new Error(text || 'Resposta do servidor não é um PDF válido.');
        }
        const blob = await response.blob();

        // Tentar extrair nome do arquivo do header
        let fileName = `relatorio_${String(ordemId).replace(/[\\/]+/g, '-')}.pdf`;
        const dispo = response.headers.get('Content-Disposition');
        if (dispo) {
            const match = dispo.match(/filename="?([^";]+)"?/i);
            if (match && match[1]) fileName = match[1];
        }

        const urlObj = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = urlObj;
        a.download = fileName;
        document.body.appendChild(a);
        a.click();
        a.remove();
        window.URL.revokeObjectURL(urlObj);

        mostrarStatus('PDF gerado pelo servidor com sucesso! Download iniciado.', 'success');
    } catch (err) {
        console.error('Erro ao baixar PDF do servidor:', err);
        mostrarStatus('Erro ao gerar PDF no servidor: ' + err.message, 'error');

        // Fallback: abrir em nova aba (se permitido)
        try {
            const ordemId = document.getElementById('ordem_id')?.value || document.getElementById('numero_ordem')?.value || '';
            if (ordemId) {
                window.open(`scripts/relatorio/pdf.php?ordem=${encodeURIComponent(ordemId)}`, '_blank');
            }
        } catch (_) {}
    } finally {
        if (loader) loader.style.display = 'none';
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
    // Processamos o conteúdo do editor para otimizar para PDF
    const conteudoOtimizado = otimizarConteudoParaPDF(conteudoEditor);
    const obsOtimizada = observacoesFinais ? otimizarConteudoParaPDF(observacoesFinais) : '';
    
    return `
        <div style="text-align:center; margin-bottom:15px; border-bottom:2px solid #5a4a96; padding-bottom:8px;">
            <h1 style="color:#5a4a96; font-size:16pt; margin:5px 0;">RELATÓRIO DE SERVIÇO</h1>
            <p style="margin:3px 0; font-size:12pt;">Ordem de Serviço: ${numeroOrdem}</p>
        </div>
        
        <div style="margin-bottom:15px;">
            <table style="width:100%; border-collapse:collapse; font-size:10pt;">
                <tr>
                    <td style="font-weight:bold; width:30%; padding:3px; border-bottom:1px solid #eee;">Cliente:</td>
                    <td style="padding:3px; border-bottom:1px solid #eee;">${clienteOrdem}</td>
                </tr>
                <tr>
                    <td style="font-weight:bold; width:30%; padding:3px; border-bottom:1px solid #eee;">Motocicleta:</td>
                    <td style="padding:3px; border-bottom:1px solid #eee;">${motoOrdem}</td>
                </tr>
                <tr>
                    <td style="font-weight:bold; width:30%; padding:3px; border-bottom:1px solid #eee;">Data:</td>
                    <td style="padding:3px; border-bottom:1px solid #eee;">${dataOrdem}</td>
                </tr>
                <tr>
                    <td style="font-weight:bold; width:30%; padding:3px; border-bottom:1px solid #eee;">Quilometragem:</td>
                    <td style="padding:3px; border-bottom:1px solid #eee;">${kmOrdem}</td>
                </tr>
            </table>
        </div>
        
        <div style="margin-top:15px;">
            <h2 style="color:#5a4a96; font-size:12pt; margin:8px 0 10px 0; border-bottom:1px solid #ccc; padding-bottom:3px;">DETALHES DO SERVIÇO</h2>
            <div style="font-size:10pt; line-height:1.3;">${conteudoOtimizado}</div>
        </div>
        
        ${obsOtimizada ? `
        <div style="margin-top:15px; border-top:1px dashed #ccc; padding-top:8px;">
            <h3 style="color:#5a4a96; font-size:11pt; margin:0 0 5px 0;">OBSERVAÇÕES FINAIS</h3>
            <div style="font-size:10pt; line-height:1.3; white-space: pre-line;">${obsOtimizada}</div>
        </div>
        ` : ''}
        
        <div style="margin-top:15px; text-align:right;">
            <p style="font-size:10pt;"><strong>Data de conclusão:</strong> ${dataConclusao}</p>
        </div>
    `;
}

/**
 * Otimiza o conteúdo do editor para caber melhor no PDF
 * @param {string} conteudo - O conteúdo HTML do editor
 * @returns {string} - O conteúdo otimizado
 */
function otimizarConteudoParaPDF(conteudo) {
    // Criar um elemento temporário para manipular o HTML
    const temp = document.createElement('div');
    temp.innerHTML = conteudo;
    
    // Avaliar o tamanho do conteúdo para determinar o quão agressivo deve ser a otimização
    const nivelOtimizacao = avaliarTamanhoConteudo(temp);
    
    // Definir tamanhos com base no nível de otimização
    const tamanhos = {
        normal: {
            titulo: '11pt',
            paragrafo: '10pt',
            margemTitulo: '10px 0 5px 0',
            margemParagrafo: '3px 0',
            margemLista: '3px 0 3px 15px',
            paddingLista: '0 0 0 10px',
            margemItem: '2px 0',
            lineHeight: '1.3'
        },
        compacto: {
            titulo: '10pt',
            paragrafo: '9pt',
            margemTitulo: '8px 0 4px 0',
            margemParagrafo: '2px 0',
            margemLista: '2px 0 2px 12px',
            paddingLista: '0 0 0 8px',
            margemItem: '1px 0',
            lineHeight: '1.2'
        },
        muitoCompacto: {
            titulo: '9pt',
            paragrafo: '8pt',
            margemTitulo: '6px 0 3px 0',
            margemParagrafo: '1px 0',
            margemLista: '1px 0 1px 10px',
            paddingLista: '0 0 0 6px',
            margemItem: '1px 0',
            lineHeight: '1.1'
        }
    };
    
    // Escolher o conjunto de tamanhos apropriado
    const tamanhoAtual = tamanhos[nivelOtimizacao];
    
    // Ajustar estilos dos títulos
    const titulos = temp.querySelectorAll('h1, h2, h3, h4, h5, h6');
    titulos.forEach(titulo => {
        titulo.style.fontSize = tamanhoAtual.titulo;
        titulo.style.margin = tamanhoAtual.margemTitulo;
        titulo.style.padding = '0';
    });
    
    // Ajustar parágrafos
    const paragrafos = temp.querySelectorAll('p');
    paragrafos.forEach(p => {
        p.style.margin = tamanhoAtual.margemParagrafo;
        p.style.lineHeight = tamanhoAtual.lineHeight;
        p.style.fontSize = tamanhoAtual.paragrafo;
    });
    
    // Ajustar listas
    const listas = temp.querySelectorAll('ul, ol');
    listas.forEach(lista => {
        lista.style.margin = tamanhoAtual.margemLista;
        lista.style.padding = tamanhoAtual.paddingLista;
    });
    
    const itens = temp.querySelectorAll('li');
    itens.forEach(item => {
        item.style.margin = tamanhoAtual.margemItem;
        item.style.fontSize = tamanhoAtual.paragrafo;
    });
    
    return temp.innerHTML;
}

/**
 * Avalia o tamanho do conteúdo para determinar o nível de otimização necessário
 * @param {HTMLElement} elemento - O elemento contendo o conteúdo
 * @returns {string} - Nível de otimização: 'normal', 'compacto' ou 'muitoCompacto'
 */
function avaliarTamanhoConteudo(elemento) {
    // Contar elementos para estimar o tamanho
    const paragrafos = elemento.querySelectorAll('p').length;
    const listas = elemento.querySelectorAll('ul, ol').length;
    const itens = elemento.querySelectorAll('li').length;
    const titulos = elemento.querySelectorAll('h1, h2, h3, h4, h5, h6').length;
    
    // Calcular texto aproximado
    const textoTotal = elemento.textContent.trim();
    const caracteres = textoTotal.length;
    
    // Calcular pontuação de 'densidade'
    let densidade = caracteres / 1200; // 1200 caracteres é um bom tamanho para uma página
    
    // Adicionar peso para elementos estruturais
    densidade += paragrafos * 0.1;
    densidade += listas * 0.2;
    densidade += itens * 0.1;
    densidade += titulos * 0.3;
    
    // Detectar imagens e adicionar peso
    const imagens = elemento.querySelectorAll('img').length;
    densidade += imagens * 1.5;
    
    // Decidir nível de otimização
    if (densidade <= 1.2) {
        return 'normal';
    } else if (densidade <= 2.0) {
        return 'compacto';
    } else {
        return 'muitoCompacto';
    }
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
        // Observações finais agora vêm de um editor rich-text
        const obsEl = document.getElementById('observacoes-finais-editor');
        const observacoesFinais = obsEl ? obsEl.innerHTML : '';
        const conteudoEditor = document.getElementById('editor-personalizado').innerHTML;
        
        // Avaliar o tamanho do conteúdo
        const tempAvaliacao = document.createElement('div');
        tempAvaliacao.innerHTML = conteudoEditor;
        const nivelOtimizacao = avaliarTamanhoConteudo(tempAvaliacao);
        
        // Processar o conteúdo do editor para otimizar para impressão
        const conteudoOtimizado = otimizarConteudoParaPDF(conteudoEditor);
        
        // Criar o conteúdo para impressão com estilos inline
        printWindow.document.write(gerarHTMLImpressao(
            numeroOrdem, dataOrdem, kmOrdem, clienteOrdem, 
            motoOrdem, dataConclusao, observacoesFinais, conteudoOtimizado
        ));
        
        // Ocultar loader
        document.getElementById('aguarde').style.display = 'none';
        
        // Atualizar mensagem de status com informação de otimização
        let mensagemOtimizacao = '';
        if (nivelOtimizacao === 'compacto') {
            mensagemOtimizacao = ' Conteúdo denso detectado: aplicada otimização automática.';
        } else if (nivelOtimizacao === 'muitoCompacto') {
            mensagemOtimizacao = ' Conteúdo muito extenso: aplicada otimização máxima.';
        }
        
        mostrarStatus('Método alternativo: Relatório aberto em nova aba. Use Ctrl+P para salvar como PDF.' + mensagemOtimizacao, 'info');
        
    } catch (error) {
        mostrarStatus('Não foi possível gerar o PDF: ' + error.message, 'error');
        console.error("Erro no método alternativo:", error);
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
                    padding: 15px;
                    font-size: 11pt;
                    line-height: 1.3;
                }
                h1, h2, h3 { color: #5a4a96; }
                h1 { font-size: 16pt; margin: 0 0 8px 0; }
                h2 { font-size: 14pt; margin: 12px 0 8px 0; }
                h3 { font-size: 12pt; margin: 8px 0 4px 0; }
                p { margin: 4px 0; }
                table { width: 100%; border-collapse: collapse; margin: 10px 0; }
                td { padding: 5px; border-bottom: 1px solid #ddd; }
                .header { 
                    text-align: center; 
                    margin-bottom: 15px; 
                    padding-bottom: 10px;
                    border-bottom: 2px solid #5a4a96; 
                }
                .info-section { margin-bottom: 15px; }
                .content-section { margin-top: 15px; }
                .obs-section { 
                    margin-top: 15px; 
                    padding-top: 10px; 
                    border-top: 1px dashed #ccc; 
                }
                #obs-content-print { white-space: pre-line; }
                .conclusion { 
                    margin-top: 15px; 
                    text-align: right; 
                }
                ul, ol { margin: 4px 0 8px 20px; padding-left: 10px; }
                li { margin-bottom: 3px; }
                .pdf-instructions {
                    display: block;
                    position: fixed;
                    top: 0;
                    left: 0;
                    right: 0;
                    background: #ffff99;
                    color: #333;
                    padding: 8px;
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
                /* Estilos para otimizar impressão */
                @media print {
                    h1 { font-size: 16pt; }
                    h2 { font-size: 14pt; }
                    h3 { font-size: 12pt; }
                    p, li, td { font-size: 10pt; }
                    
                    /* Evitar quebras de página dentro destes elementos */
                    h1, h2, h3 { page-break-after: avoid; }
                    .info-section, .header { page-break-inside: avoid; }
                    table { page-break-inside: avoid; }
                    ul, ol { page-break-inside: avoid; }
                    
                    /* Forçar quebras de página se necessário */
                    .content-section { page-break-before: auto; }
                    .obs-section { page-break-before: auto; }
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
                <div id="obs-content-print"></div>
            </div>
            ` : ''}
            
            <div class="conclusion">
                <p><strong>Data de conclusão:</strong> ${dataConclusao}</p>
            </div>
            
            <script>
                window.onload = function() {
                    // Preencher conteúdo dos editores
                    const contenedor = document.getElementById('editor-content-print');
                    contenedor.innerHTML = \`${conteudoEditor}\`;
                    const obsPrint = document.getElementById('obs-content-print');
                    if (obsPrint) { obsPrint.innerHTML = \`${observacoesFinais}\`; }
                    
                    // Iniciar impressão automática após carregar
                    setTimeout(function() {
                        window.print();
                    }, 1000);
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
export { gerarPDFServidor };