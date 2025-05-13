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

    // Verificar se os elementos de assinatura existem antes de inicializá-los
    const canvasTecnico = document.getElementById('assinatura-pad');
    let signaturePadTecnico = null;
    
    if (canvasTecnico) {
        signaturePadTecnico = new SignaturePad(canvasTecnico, {
            backgroundColor: 'rgb(35, 37, 48)',
            penColor: 'rgb(255, 255, 255)'
        });
    }

    // Inicializar o pad de assinatura do cliente apenas se existir
    const canvasCliente = document.getElementById('assinatura-cliente-pad');
    let signaturePadCliente = null;
    
    if (canvasCliente) {
        signaturePadCliente = new SignaturePad(canvasCliente, {
            backgroundColor: 'rgb(35, 37, 48)',
            penColor: 'rgb(255, 255, 255)'
        });
    }

    // Ajustar tamanho dos canvas apenas se eles existirem
    function resizeCanvas() {
        if (canvasTecnico && signaturePadTecnico) {
            // Redimensionar canvas do técnico
            const ratioTecnico = Math.max(window.devicePixelRatio || 1, 1);
            canvasTecnico.width = canvasTecnico.offsetWidth * ratioTecnico;
            canvasTecnico.height = canvasTecnico.offsetHeight * ratioTecnico;
            canvasTecnico.getContext("2d").scale(ratioTecnico, ratioTecnico);
            signaturePadTecnico.clear();
        }
        
        if (canvasCliente && signaturePadCliente) {
            // Redimensionar canvas do cliente
            const ratioCliente = Math.max(window.devicePixelRatio || 1, 1);
            canvasCliente.width = canvasCliente.offsetWidth * ratioCliente;
            canvasCliente.height = canvasCliente.offsetHeight * ratioCliente;
            canvasCliente.getContext("2d").scale(ratioCliente, ratioCliente);
            signaturePadCliente.clear();
        }
    }

    // Chamar resizeCanvas() quando a janela for redimensionada
    window.addEventListener("resize", resizeCanvas);
    
    // Chamar resizeCanvas apenas se houver canvas de assinatura
    if (canvasTecnico || canvasCliente) {
        resizeCanvas();
    }

    // Botão para limpar assinatura do técnico, verificar se existe antes
    const btnLimparAssinatura = document.getElementById('limpar-assinatura');
    if (btnLimparAssinatura && signaturePadTecnico) {
        btnLimparAssinatura.addEventListener('click', function(e) {
            e.preventDefault(); // Prevenir envio do formulário
            signaturePadTecnico.clear();
        });
    }
    
    // Botão para limpar assinatura do cliente, verificar se existe antes
    const btnLimparAssinaturaCliente = document.getElementById('limpar-assinatura-cliente');
    if (btnLimparAssinaturaCliente && signaturePadCliente) {
        btnLimparAssinaturaCliente.addEventListener('click', function(e) {
            e.preventDefault(); // Prevenir envio do formulário
            signaturePadCliente.clear();
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
            gerarPDF(signaturePadTecnico, signaturePadCliente);
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
            'Accept': 'application/json'
        }
    })
        .then(response => {
            // Verificar se a resposta é bem-sucedida
            if (!response.ok) {
                throw new Error(`Erro HTTP: ${response.status}`);
            }
            
            // Verificar o tipo de conteúdo da resposta
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('A resposta não é um JSON válido');
            }
            
            return response.json();
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
            } else if (data.status === 'error' && data.message === 'Relatório não encontrado') {
                // Se não houver relatório, apenas continuar sem mostrar erro
                console.log('Nenhum relatório encontrado para esta ordem. Criando novo relatório.');
            } else {
                // Outros erros devem ser exibidos
                console.error('Erro ao carregar relatório:', data);
                mostrarStatus(data.message || 'Erro ao carregar relatório', 'error');
            }
        })
        .catch(error => {
            console.error('Erro ao carregar relatório:', error);
            mostrarStatus('Erro ao carregar relatório. Verifique o console para mais detalhes.', 'error');
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
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        // Esconder loader
        document.getElementById('aguarde').style.display = 'none';
        // Mostrar modal de confirmação
        document.getElementById('modal-confirmacao').style.display = 'flex';
        document.querySelector('.modal-message').textContent = data.message;
        // Mostrar status
        mostrarStatus(data.message, data.status);
    })
    .catch(error => {
        // Esconder loader
        document.getElementById('aguarde').style.display = 'none';
        console.error('Erro ao salvar relatório:', error);
        mostrarStatus('Erro ao salvar relatório', 'error');
    });
}

// Função para gerar PDF
function gerarPDF(signaturePadTecnico, signaturePadCliente) {
    // Mostrar loader
    document.getElementById('aguarde').style.display = 'flex';
    
    // Criar um elemento para o PDF
    const elemento = document.createElement('div');
    elemento.classList.add('relatorio-pdf');
    
    // Adicionar cabeçalho
    const header = document.createElement('div');
    header.classList.add('relatorio-header');
    header.innerHTML = `
        <img src="./assets/css/images/logo-branco-crop.png">
        <h1>Relatório de Ordem de Serviço</h1>
    `;
    elemento.appendChild(header);
    
    // Adicionar informações básicas
    const info = document.createElement('div');
    info.classList.add('relatorio-info');
    
    // Obter dados dos elementos, textareas usam textContent
    const numeroOrdem = document.getElementById('numero_ordem').value;
    const cliente = document.getElementById('cliente_ordem').value || document.getElementById('cliente_ordem').textContent;
    const moto = document.getElementById('moto_ordem').value || document.getElementById('moto_ordem').textContent;
    const data = document.getElementById('data_ordem').value;
    const km = document.getElementById('km_ordem').value;
    const dataConclusao = formatarDataBr(document.getElementById('data-conclusao').value);
    const tecnico = document.getElementById('tecnico_responsavel').value || 'Não informado';
    
    info.innerHTML = `
        <p><strong>Ordem de Serviço Nº:</strong> ${numeroOrdem}</p>
        <p><strong>Cliente:</strong> ${cliente}</p>
        <p><strong>Motocicleta:</strong> ${moto}</p>
        <p><strong>Data:</strong> ${data}</p>
        <p><strong>Quilometragem:</strong> ${km}</p>
        <p><strong>Data de Conclusão:</strong> ${dataConclusao}</p>
        <p><strong>Técnico Responsável:</strong> ${tecnico}</p>
    `;
    
    // Adicionar endereço se existir
    const endereco = document.getElementById('endereco_cliente');
    if (endereco && (endereco.value || endereco.textContent)) {
        const enderecoTexto = (endereco.value || endereco.textContent).replace(/\n/g, ' ').trim();
        info.innerHTML += `<p><strong>Endereço:</strong> ${enderecoTexto}</p>`;
    }
    
    elemento.appendChild(info);
    
    // Adicionar conteúdo do relatório
    const conteudo = document.createElement('div');
    conteudo.classList.add('relatorio-body');
    const editorPersonalizado = document.getElementById('editor-personalizado');
    conteudo.innerHTML = editorPersonalizado ? editorPersonalizado.innerHTML : '';
    elemento.appendChild(conteudo);
    
    // Adicionar observações finais se houver
    const observacoesElement = document.getElementById('observacoes_finais');
    if (observacoesElement && observacoesElement.value) {
        const obsElement = document.createElement('div');
        obsElement.classList.add('relatorio-obs');
        obsElement.innerHTML = `
            <h3>Observações Finais</h3>
            <p>${observacoesElement.value}</p>
        `;
        elemento.appendChild(obsElement);
    }
    
    // Adicionar assinaturas
    const assinaturas = document.createElement('div');
    assinaturas.classList.add('relatorio-assinaturas');
    assinaturas.style.display = 'flex';
    assinaturas.style.justifyContent = 'space-between';
    assinaturas.style.marginTop = '30px';
    
    // Assinatura do técnico
    const assinaturaTecnico = document.createElement('div');
    assinaturaTecnico.style.width = '45%';
    assinaturaTecnico.style.textAlign = 'center';
    
    if (signaturePadTecnico && !signaturePadTecnico.isEmpty()) {
        assinaturaTecnico.innerHTML = `
            <img src="${signaturePadTecnico.toDataURL()}" style="max-width: 100%; height: auto; border-bottom: 1px solid #000;">
            <p>Assinatura do Técnico</p>
        `;
    } else {
        assinaturaTecnico.innerHTML = `
            <div style="height: 70px; border-bottom: 1px solid #000;"></div>
            <p>Assinatura do Técnico</p>
        `;
    }
    
    // Assinatura do cliente
    const assinaturaCliente = document.createElement('div');
    assinaturaCliente.style.width = '45%';
    assinaturaCliente.style.textAlign = 'center';
    
    if (signaturePadCliente && !signaturePadCliente.isEmpty()) {
        assinaturaCliente.innerHTML = `
            <img src="${signaturePadCliente.toDataURL()}" style="max-width: 100%; height: auto; border-bottom: 1px solid #000;">
            <p>Assinatura do Cliente</p>
        `;
    } else {
        assinaturaCliente.innerHTML = `
            <div style="height: 70px; border-bottom: 1px solid #000;"></div>
            <p>Assinatura do Cliente</p>
        `;
    }
    
    assinaturas.appendChild(assinaturaTecnico);
    assinaturas.appendChild(assinaturaCliente);
    elemento.appendChild(assinaturas);
    
    // Configuração do html2pdf
    const opt = {
        margin: [15, 15],
        filename: `relatorio-os-${numeroOrdem}.pdf`,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };
    
    // Gerar PDF
    html2pdf().set(opt).from(elemento).save().then(() => {
        // Esconder loader após gerar PDF
        document.getElementById('aguarde').style.display = 'none';
        mostrarStatus('PDF gerado com sucesso!', 'success');
    }).catch(error => {
        document.getElementById('aguarde').style.display = 'none';
        console.error('Erro ao gerar PDF:', error);
        mostrarStatus('Erro ao gerar PDF', 'error');
    });
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