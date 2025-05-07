document.addEventListener("DOMContentLoaded", function() {
    // Inicializar o Editor Quill
    const quill = new Quill('#editor-content', {
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'align': [] }],
                ['clean']
            ]
        },
        theme: 'snow'
    });

    // Inicializar o pad de assinatura do técnico
    const canvasTecnico = document.getElementById('assinatura-pad');
    const signaturePadTecnico = new SignaturePad(canvasTecnico, {
        backgroundColor: 'rgb(35, 37, 48)',
        penColor: 'rgb(255, 255, 255)'
    });

    // Inicializar o pad de assinatura do cliente
    const canvasCliente = document.getElementById('assinatura-cliente-pad');
    const signaturePadCliente = new SignaturePad(canvasCliente, {
        backgroundColor: 'rgb(35, 37, 48)',
        penColor: 'rgb(255, 255, 255)'
    });

    // Ajustar tamanho dos canvas
    function resizeCanvas() {
        // Redimensionar canvas do técnico
        const ratioTecnico = Math.max(window.devicePixelRatio || 1, 1);
        canvasTecnico.width = canvasTecnico.offsetWidth * ratioTecnico;
        canvasTecnico.height = canvasTecnico.offsetHeight * ratioTecnico;
        canvasTecnico.getContext("2d").scale(ratioTecnico, ratioTecnico);
        signaturePadTecnico.clear();
        
        // Redimensionar canvas do cliente
        const ratioCliente = Math.max(window.devicePixelRatio || 1, 1);
        canvasCliente.width = canvasCliente.offsetWidth * ratioCliente;
        canvasCliente.height = canvasCliente.offsetHeight * ratioCliente;
        canvasCliente.getContext("2d").scale(ratioCliente, ratioCliente);
        signaturePadCliente.clear();
    }

    // Chamar resizeCanvas() quando a janela for redimensionada
    window.addEventListener("resize", resizeCanvas);
    resizeCanvas();

    // Botão para limpar assinatura do técnico
    document.getElementById('limpar-assinatura').addEventListener('click', function(e) {
        e.preventDefault(); // Prevenir envio do formulário
        signaturePadTecnico.clear();
    });
    
    // Botão para limpar assinatura do cliente
    document.getElementById('limpar-assinatura-cliente').addEventListener('click', function(e) {
        e.preventDefault(); // Prevenir envio do formulário
        signaturePadCliente.clear();
    });

    // Carregar dados do relatório se existir
    const ordem_id = document.getElementById('ordem_id').value;
    carregarRelatorio(ordem_id, quill, signaturePadTecnico, signaturePadCliente);

    // Salvar relatório
    document.getElementById('btn-salvar').addEventListener('click', function(e) {
        e.preventDefault(); // Prevenir envio do formulário
        salvarRelatorio(quill, signaturePadTecnico, signaturePadCliente, ordem_id);
    });

    // Gerar PDF
    document.getElementById('btn-gerar-pdf').addEventListener('click', function(e) {
        e.preventDefault(); // Prevenir envio do formulário
        gerarPDF(quill, signaturePadTecnico, signaturePadCliente);
    });

    // Fechar modal
    document.getElementById('modal-fechar').addEventListener('click', function() {
        document.getElementById('modal-confirmacao').style.display = 'none';
    });
    
    // Prevenir envio do formulário pelo método tradicional
    document.getElementById('relatorio-form').addEventListener('submit', function(e) {
        e.preventDefault();
    });
});

// Função para carregar relatório do banco de dados
function carregarRelatorio(ordem_id, quill, signaturePadTecnico, signaturePadCliente) {
    fetch(`scripts/relatorio/load.php?ordem=${ordem_id}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Preencher o editor com o conteúdo salvo
                quill.root.innerHTML = data.conteudo;
                
                // Preencher a assinatura do técnico se existir
                if (data.assinatura) {
                    signaturePadTecnico.fromDataURL(data.assinatura);
                }
                
                // Preencher a assinatura do cliente se existir
                if (data.assinatura_cliente) {
                    signaturePadCliente.fromDataURL(data.assinatura_cliente);
                }
                
                // Preencher a data de conclusão se existir
                if (data.data_conclusao) {
                    document.getElementById('data-conclusao').value = data.data_conclusao;
                }
                
                // Atualizar o valor da quilometragem se existir
                if (data.quilometragem) {
                    document.getElementById('km_ordem').value = data.quilometragem;
                }
                
                // Preencher o técnico responsável se existir
                if (data.tecnico_responsavel) {
                    document.getElementById('tecnico_responsavel').value = data.tecnico_responsavel;
                }
                
                // Preencher observações finais se existirem
                if (data.observacoes_finais) {
                    document.getElementById('observacoes_finais').value = data.observacoes_finais;
                }
                
                // Mostrar mensagem de sucesso
                mostrarStatus(`Relatório carregado. Última modificação: ${formatarData(data.data_modificacao)}`, 'success');
            }
        })
        .catch(error => {
            console.error('Erro ao carregar relatório:', error);
        });
}

// Função para salvar relatório no banco de dados
function salvarRelatorio(quill, signaturePadTecnico, signaturePadCliente, ordem_id) {
    // Mostrar loader
    document.getElementById('aguarde').style.display = 'flex';
    
    // Obter conteúdo do editor
    const conteudo = quill.root.innerHTML;
    
    // Obter assinatura do técnico (se existir)
    const assinatura = !signaturePadTecnico.isEmpty() ? signaturePadTecnico.toDataURL() : '';
    
    // Obter assinatura do cliente (se existir)
    const assinaturaCliente = !signaturePadCliente.isEmpty() ? signaturePadCliente.toDataURL() : '';
    
    // Obter outros campos do formulário
    const quilometragem = document.getElementById('km_ordem').value;
    const dataConclusao = document.getElementById('data-conclusao').value;
    const tecnicoResponsavel = document.getElementById('tecnico_responsavel').value;
    const observacoesFinais = document.getElementById('observacoes_finais').value;
    
    // Criar FormData para envio
    const formData = new FormData();
    formData.append('conteudo', conteudo);
    formData.append('assinatura', assinatura);
    formData.append('assinatura_cliente', assinaturaCliente);
    formData.append('quilometragem', quilometragem);
    formData.append('data_conclusao', dataConclusao);
    formData.append('tecnico_responsavel', tecnicoResponsavel);
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
        document.getElementById('modal-confirmacao').style.display = 'block';
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
function gerarPDF(quill, signaturePadTecnico, signaturePadCliente) {
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
    const cliente = document.getElementById('cliente_ordem').textContent || document.getElementById('cliente_ordem').value;
    const moto = document.getElementById('moto_ordem').textContent || document.getElementById('moto_ordem').value;
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
    if (endereco && (endereco.textContent || endereco.value)) {
        const enderecoTexto = (endereco.textContent || endereco.value).replace(/\n/g, ' ').trim();
        info.innerHTML += `<p><strong>Endereço:</strong> ${enderecoTexto}</p>`;
    }
    
    elemento.appendChild(info);
    
    // Adicionar conteúdo do relatório
    const conteudo = document.createElement('div');
    conteudo.classList.add('relatorio-body');
    conteudo.innerHTML = quill.root.innerHTML;
    elemento.appendChild(conteudo);
    
    // Adicionar observações finais se houver
    const observacoesFinais = document.getElementById('observacoes_finais').value;
    if (observacoesFinais) {
        const obsElement = document.createElement('div');
        obsElement.classList.add('relatorio-obs');
        obsElement.innerHTML = `
            <h3>Observações Finais</h3>
            <p>${observacoesFinais}</p>
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
    
    if (!signaturePadTecnico.isEmpty()) {
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
    
    if (!signaturePadCliente.isEmpty()) {
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
        filename: `relatorio-os-${document.getElementById('numero_ordem').value}.pdf`,
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
    const data = new Date(dataString);
    return data.toLocaleString('pt-BR');
}

// Formatar data em formato brasileiro (DD/MM/YYYY)
function formatarDataBr(dataString) {
    if (!dataString) return '';
    const data = new Date(dataString);
    return data.toLocaleDateString('pt-BR');
}