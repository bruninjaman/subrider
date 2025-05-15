/**
 * Módulo de API do Relatório
 * Gerencia as operações de carregamento e salvamento do relatório
 */

import { mostrarStatus } from './utils.js';

/**
 * Carrega um relatório do banco de dados
 * @param {string} ordem_id - ID da ordem de serviço
 * @returns {Promise} - Promise resolvida quando o relatório for carregado
 */
async function carregarRelatorio(ordem_id) {
    if (!ordem_id) return;
    
    // Exibir mensagem de carregamento
    mostrarStatus('Carregando relatório...', 'info');
    
    try {
        const response = await fetch(`scripts/relatorio/load.php?ordem=${ordem_id}`, {
            headers: {
                'Accept': 'application/json',
                'Cache-Control': 'no-cache'
            }
        });
        
        // Verificar se a resposta é bem-sucedida
        if (!response.ok) {
            throw new Error(`Erro HTTP: ${response.status}`);
        }
        
        // Obter o texto da resposta primeiro
        const text = await response.text();
        
        // Verificar se a resposta está vazia
        if (!text.trim()) {
            throw new Error("Resposta vazia do servidor");
        }
            
        // Tentar analisar o texto como JSON
        const data = JSON.parse(text);
        
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
            mostrarStatus('Novo relatório criado. Preencha os dados necessários.', 'info');
        } else if (data.status === 'error') {
            // Exibir mensagem de erro específica
            mostrarStatus(`Erro: ${data.message}`, 'error');
        } else {
            // Outros erros
            mostrarStatus('Erro inesperado ao carregar relatório', 'error');
        }
    } catch (error) {
        // Mostrar uma mensagem de erro mais amigável
        mostrarStatus(`Não foi possível carregar o relatório. ${error.message}`, 'error');
    }
}

/**
 * Salva o relatório no banco de dados
 * @param {string} ordem_id - ID da ordem de serviço
 * @returns {Promise} - Promise resolvida quando o relatório for salvo
 */
async function salvarRelatorio(ordem_id) {
    if (!ordem_id) {
        mostrarStatus('ID da ordem não encontrado', 'error');
        return;
    }
    
    // Mostrar loader
    document.getElementById('aguarde').style.display = 'flex';
    
    try {
        // Obter conteúdo do editor personalizado
        const editorPersonalizado = document.getElementById('editor-personalizado');
        const conteudo = editorPersonalizado ? editorPersonalizado.innerHTML : '';
        
        // Obter outros campos do formulário
        const dataConclusao = document.getElementById('data-conclusao').value;
        const observacoesFinais = document.getElementById('observacoes_finais') ? 
            document.getElementById('observacoes_finais').value : '';
        
        // Criar FormData para envio
        const formData = new FormData();
        formData.append('conteudo', conteudo);
        formData.append('data_conclusao', dataConclusao);
        formData.append('observacoes_finais', observacoesFinais);
        
        // Enviar dados para o servidor
        const response = await fetch(`scripts/relatorio/save.php?ordem=${ordem_id}`, {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json',
                'Cache-Control': 'no-cache'
            }
        });
        
        // Verificar se a resposta é bem-sucedida
        if (!response.ok) {
            throw new Error(`Erro HTTP: ${response.status}`);
        }
        
        // Obter o texto da resposta primeiro
        const text = await response.text();
        
        // Verificar se a resposta está vazia
        if (!text.trim()) {
            throw new Error("Resposta vazia do servidor");
        }
        
        // Tentar analisar o texto como JSON
        const data = JSON.parse(text);
        
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
            mostrarStatus(`Erro: ${data.message}`, 'error');
        } else {
            // Outros erros
            mostrarStatus('Erro inesperado ao salvar relatório', 'error');
        }
    } catch (error) {
        // Esconder loader
        document.getElementById('aguarde').style.display = 'none';
        // Mostrar uma mensagem de erro mais amigável
        mostrarStatus(`Não foi possível salvar o relatório. ${error.message}`, 'error');
    }
}

/**
 * Formatar data para exibição
 * @param {string} dataString - String de data
 * @returns {string} - Data formatada
 */
function formatarData(dataString) {
    if (!dataString) return '';
    const data = new Date(dataString);
    return data.toLocaleString('pt-BR');
}

export { carregarRelatorio, salvarRelatorio, formatarData }; 