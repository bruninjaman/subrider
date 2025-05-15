/**
 * Módulo de Utilidades
 * Funções auxiliares usadas em diferentes partes do sistema
 */

/**
 * Mostra mensagens de status na interface
 * @param {string} mensagem - Mensagem a ser exibida
 * @param {string} tipo - Tipo de mensagem (info, success, error)
 */
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

export { mostrarStatus }; 