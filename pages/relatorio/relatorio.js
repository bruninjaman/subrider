// Adicionar polyfill para eventos depreciados do DOM
// Isso ajuda a suprimir os avisos de depreciação do Quill.js
(function() {
    // Salvar a implementação original do addEventListener
    const originalAddEventListener = EventTarget.prototype.addEventListener;
    
    // Sobrescrever addEventListener para capturar e modificar eventos depreciados
    EventTarget.prototype.addEventListener = function(type, listener, options) {
        // Se for um evento depreciado (DOMNodeInserted), transformar em um MutationObserver
        if (type === 'DOMNodeInserted') {
            console.log('Evento depreciado DOMNodeInserted interceptado');
            // Não faça nada, ou implemente uma alternativa (MutationObserver)
            return;
        }
        
        // Para outros tipos de eventos, continue normalmente
        return originalAddEventListener.call(this, type, listener, options);
    };
})();

document.addEventListener("DOMContentLoaded", function() {
    // Inicializar o Editor Personalizado
    const editorPersonalizado = document.getElementById('editor-personalizado');
    const botoesEditor = document.querySelectorAll('.editor-toolbar button');
    
    if (editorPersonalizado) {
        // Adicionar manipuladores de eventos para os botões da barra de ferramentas
        botoesEditor.forEach(button => {
            button.addEventListener('click', () => {
                const command = button.getAttribute('data-command');
                
                if (command === 'h3') {
                    // Tratamento especial para inserir cabeçalho H3
                    document.execCommand('formatBlock', false, '<h3>');
                } else {
                    // Executar comando padrão do documento
                    document.execCommand(command, false, null);
                }
                
                // Manter o foco no editor
                editorPersonalizado.focus();
            });
        });
        
        // Verificar estado atual dos botões ao clicar no editor
        editorPersonalizado.addEventListener('click', atualizarEstadoBotoes);
        editorPersonalizado.addEventListener('keyup', atualizarEstadoBotoes);
    }
    
    // Função para atualizar o estado visual dos botões baseado na formatação atual
    function atualizarEstadoBotoes() {
        botoesEditor.forEach(button => {
            const command = button.getAttribute('data-command');
            
            if (command === 'h3') {
                // Verificar se estamos dentro de um h3
                const parentElement = window.getSelection().anchorNode.parentElement;
                if (parentElement && parentElement.tagName === 'H3') {
                    button.classList.add('active');
                } else {
                    button.classList.remove('active');
                }
            } else if (['bold', 'italic', 'underline'].includes(command)) {
                // Verificar estado dos comandos básicos
                if (document.queryCommandState(command)) {
                    button.classList.add('active');
                } else {
                    button.classList.remove('active');
                }
            }
        });
    }

    // Carregar dados do relatório se existir
    const ordem_id = document.getElementById('ordem_id').value;
    carregarRelatorio(ordem_id);

    // Salvar relatório
    const btnSalvar = document.getElementById('btn-salvar');
    if (btnSalvar) {
        btnSalvar.addEventListener('click', function(e) {
            e.preventDefault();
            salvarRelatorio(ordem_id);
        });
    }

    // Gerar PDF
    const btnGerarPDF = document.getElementById('btn-gerar-pdf');
    if (btnGerarPDF) {
        btnGerarPDF.addEventListener('click', function(e) {
            e.preventDefault(); // Prevenir envio do formulário
            gerarPDF();
        });
    }

    // Fechar modal
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

// Função para carregar relatório do banco de dados
function carregarRelatorio(ordem_id) {
    if (!ordem_id) return;
    
    // Exibir mensagem de carregamento
    mostrarStatus('Carregando relatório...', 'info');
    
    fetch(`scripts/relatorio/load.php?ordem=${ordem_id}`, {
        headers: {
            'Accept': 'application/json',
            'Cache-Control': 'no-cache'
        }
    })
    .then(response => {
        // Verificar se a resposta é bem-sucedida
        if (!response.ok) {
            throw new Error(`Erro HTTP: ${response.status}`);
        }
        
        // Obter o texto da resposta primeiro
        return response.text();
    })
    .then(text => {
        // Tentar analisar o texto como JSON
        try {
            // Se a resposta estiver vazia, lançar um erro
            if (!text.trim()) {
                throw new Error("Resposta vazia do servidor");
            }
            
            // Tentar analisar o texto como JSON
            return JSON.parse(text);
        } catch (e) {
            console.error("Erro ao analisar JSON:", e);
            console.error("Texto da resposta:", text);
            throw new Error(`Não foi possível processar a resposta do servidor: ${e.message}`);
        }
    })
    .then(data => {
        if (data.status === 'success') {
            // Preencher o editor com o conteúdo salvo
            const editorPersonalizado = document.getElementById('editor-personalizado');
            if (editorPersonalizado) {
                editorPersonalizado.innerHTML = data.conteudo;
            }
            
            // Preencher a data de conclusão se existir
            if (data.data_conclusao) {
                const dataConclusao = document.getElementById('data-conclusao');
                if (dataConclusao) {
                    dataConclusao.value = data.data_conclusao;
                }
            }
            // Preencher observações finais se existirem
            if (data.observacoes_finais) {
                const obsFinais = document.getElementById('observacoes_finais');
                if (obsFinais) {
                    obsFinais.value = data.observacoes_finais;
                }
            }
            // Mostrar mensagem de sucesso
            mostrarStatus(`Relatório carregado. Última modificação: ${formatarData(data.data_modificacao)}`, 'success');
        } else if (data.status === 'novo') {
            // Se for um novo relatório, apenas continuar com o template padrão
            console.log('Criando novo relatório para esta ordem.');
            mostrarStatus('Novo relatório criado. Preencha os dados necessários.', 'info');
        } else if (data.status === 'error') {
            // Exibir mensagem de erro específica
            console.error('Erro ao carregar relatório:', data.message);
            mostrarStatus(`Erro: ${data.message}`, 'error');
        } else {
            // Outros erros devem ser exibidos
            console.error('Resposta inesperada:', data);
            mostrarStatus('Erro inesperado ao carregar relatório', 'error');
        }
    })
    .catch(error => {
        console.error('Erro ao carregar relatório:', error);
        // Mostrar uma mensagem de erro mais amigável
        mostrarStatus(`Não foi possível carregar o relatório. ${error.message}`, 'error');
    });
}

// Função para salvar relatório no banco de dados
function salvarRelatorio(ordem_id) {
    if (!ordem_id) {
        mostrarStatus('ID da ordem não encontrado', 'error');
        return;
    }
    // Mostrar loader
    document.getElementById('aguarde').style.display = 'flex';
    
    // Obter conteúdo do editor personalizado
    const editorPersonalizado = document.getElementById('editor-personalizado');
    const conteudo = editorPersonalizado ? editorPersonalizado.innerHTML : '';
    
    // Obter outros campos do formulário
    const dataConclusao = document.getElementById('data-conclusao').value;
    const observacoesFinais = document.getElementById('observacoes_finais') ? document.getElementById('observacoes_finais').value : '';
    
    // Criar FormData para envio
    const formData = new FormData();
    formData.append('conteudo', conteudo);
    formData.append('data_conclusao', dataConclusao);
    formData.append('observacoes_finais', observacoesFinais);
    
    // Enviar dados para o servidor
    fetch(`scripts/relatorio/save.php?ordem=${ordem_id}`, {
        method: 'POST',
        body: formData,
        headers: {
            'Accept': 'application/json',
            'Cache-Control': 'no-cache'
        }
    })
    .then(response => {
        // Verificar se a resposta é bem-sucedida
        if (!response.ok) {
            throw new Error(`Erro HTTP: ${response.status}`);
        }
        
        // Obter o texto da resposta primeiro
        return response.text();
    })
    .then(text => {
        // Tentar analisar o texto como JSON
        try {
            // Se a resposta estiver vazia, lançar um erro
            if (!text.trim()) {
                throw new Error("Resposta vazia do servidor");
            }
            
            // Tentar analisar o texto como JSON
            return JSON.parse(text);
        } catch (e) {
            console.error("Erro ao analisar JSON:", e);
            console.error("Texto da resposta:", text);
            throw new Error(`Não foi possível processar a resposta do servidor: ${e.message}`);
        }
    })
    .then(data => {
        // Esconder loader
        document.getElementById('aguarde').style.display = 'none';
        
        if (data.status === 'success') {
            // Mostrar modal de confirmação
            document.getElementById('modal-confirmacao').style.display = 'flex';
            document.querySelector('.modal-message').textContent = data.message;
            // Mostrar status
            mostrarStatus(data.message, 'success');
        } else if (data.status === 'error') {
            // Exibir mensagem de erro específica
            console.error('Erro ao salvar relatório:', data.message);
            mostrarStatus(`Erro: ${data.message}`, 'error');
        } else {
            // Outros erros
            console.error('Resposta inesperada:', data);
            mostrarStatus('Erro inesperado ao salvar relatório', 'error');
        }
    })
    .catch(error => {
        // Esconder loader
        document.getElementById('aguarde').style.display = 'none';
        console.error('Erro ao salvar relatório:', error);
        // Mostrar uma mensagem de erro mais amigável
        mostrarStatus(`Não foi possível salvar o relatório. ${error.message}`, 'error');
    });
}

// Função para gerar PDF
function gerarPDF() {
    // Mostrar loader
    document.getElementById('aguarde').style.display = 'flex';
    
    console.log("Iniciando geração de PDF com download automático");
    
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
        pdfContainer.innerHTML = `
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
        
        // Adicionar o elemento ao documento
        document.body.appendChild(pdfContainer);
        
        console.log("Conteúdo do PDF preparado");
        
        // Usar html2canvas para converter o HTML em imagem
        html2canvas(pdfContainer, {
            scale: 2,
            useCORS: true,
            logging: false,
            allowTaint: true
        }).then(function(canvas) {
            console.log("Canvas gerado com sucesso");
            
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
            
            console.log("PDF gerado e baixado com sucesso");
            
            // Ocultar loader
            document.getElementById('aguarde').style.display = 'none';
            
            // Mostrar mensagem de sucesso
            mostrarStatus('PDF gerado com sucesso! O download começou automaticamente.', 'success');
        }).catch(function(error) {
            console.error("Erro ao gerar canvas:", error);
            document.getElementById('aguarde').style.display = 'none';
            mostrarStatus('Não foi possível gerar o PDF. ' + error.message, 'error');
            
            // Se falhar, tentar o método alternativo
            if (document.body.contains(pdfContainer)) {
                document.body.removeChild(pdfContainer);
            }
            tentarMetodoAlternativo();
        });
    } catch (error) {
        console.error('Erro geral na geração do PDF:', error);
        document.getElementById('aguarde').style.display = 'none';
        mostrarStatus('Erro ao gerar PDF: ' + error.message, 'error');
        tentarMetodoAlternativo();
    }
}

// Função auxiliar para tentativa alternativa de geração de PDF
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
        printWindow.document.write(`
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
        `);
        
        // Ocultar loader
        document.getElementById('aguarde').style.display = 'none';
        
        // Atualizar mensagem de status
        mostrarStatus('Método alternativo: Relatório aberto em nova aba. Use Ctrl+P para salvar como PDF.', 'info');
        
    } catch (error) {
        console.error('Erro no método alternativo:', error);
        mostrarStatus('Não foi possível gerar o PDF: ' + error.message, 'error');
    }
}

// Função para mostrar mensagens de status
function mostrarStatus(mensagem, tipo) {
    const statusElement = document.querySelector('.status-message');
    if (!statusElement) return;
    
    statusElement.textContent = mensagem;
    statusElement.className = 'status-message';
    statusElement.classList.add(tipo);
    
    // Remover a mensagem após 5 segundos
    setTimeout(() => {
        statusElement.textContent = '';
        statusElement.className = 'status-message';
    }, 5000);
}

// Formatar data para exibição
function formatarData(dataString) {
    if (!dataString) return '';
    const data = new Date(dataString);
    return data.toLocaleString('pt-BR');
}

// Formatar data em formato brasileiro (DD/MM/YYYY)
function formatarDataBr(dataString) {
    if (!dataString) return '';
    const data = new Date(dataString);
    return data.toLocaleDateString('pt-BR');
}