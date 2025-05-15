/**
 * Módulo de Editor Personalizado
 * Gerencia todas as funcionalidades relacionadas ao editor de texto
 */

// Polyfill para eventos depreciados do DOM (Quill.js)
(function() {
    // Salvar a implementação original do addEventListener
    const originalAddEventListener = EventTarget.prototype.addEventListener;
    
    // Sobrescrever addEventListener para capturar e modificar eventos depreciados
    EventTarget.prototype.addEventListener = function(type, listener, options) {
        // Se for um evento depreciado (DOMNodeInserted), ignorar
        if (type === 'DOMNodeInserted') {
            return;
        }
        
        // Para outros tipos de eventos, continue normalmente
        return originalAddEventListener.call(this, type, listener, options);
    };
})();

/**
 * Inicializa o editor personalizado
 */
function inicializarEditor() {
    const editorPersonalizado = document.getElementById('editor-personalizado');
    const botoesEditor = document.querySelectorAll('.editor-toolbar button');
    
    if (!editorPersonalizado) return;
    
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
    editorPersonalizado.addEventListener('click', () => atualizarEstadoBotoes(botoesEditor));
    editorPersonalizado.addEventListener('keyup', () => atualizarEstadoBotoes(botoesEditor));
}

/**
 * Atualiza o estado visual dos botões baseado na formatação atual
 * @param {NodeList} botoesEditor - Lista de botões da barra de ferramentas
 */
function atualizarEstadoBotoes(botoesEditor) {
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

// Exportar funções
export { inicializarEditor }; 