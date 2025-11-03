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
 * Inicializa um editor e sua barra de ferramentas
 * @param {HTMLElement} editorEl - Elemento contenteditable alvo
 * @param {NodeList} botoesToolbar - Botões da toolbar associados
 */
function inicializarEditorComToolbar(editorEl, botoesToolbar) {
    if (!editorEl || !botoesToolbar || botoesToolbar.length === 0) return;

    botoesToolbar.forEach(button => {
        button.addEventListener('click', () => {
            const command = button.getAttribute('data-command');
            document.execCommand(command, false, null);
            editorEl.focus();
        });
    });

    editorEl.addEventListener('click', () => atualizarEstadoBotoes(botoesToolbar));
    editorEl.addEventListener('keyup', () => atualizarEstadoBotoes(botoesToolbar));
}

/**
 * Inicializa os editores personalizados da página
 */
function inicializarEditor() {
    // Editor principal (Detalhes do Serviço)
    const editorPrincipal = document.getElementById('editor-personalizado');
    const toolbarPrincipal = document.querySelectorAll('.editor-toolbar button');
    inicializarEditorComToolbar(editorPrincipal, toolbarPrincipal);

    // Editor de Observações Finais
    const editorObs = document.getElementById('observacoes-finais-editor');
    const toolbarObs = document.querySelectorAll('.editor-toolbar-obs button');
    inicializarEditorComToolbar(editorObs, toolbarObs);
}

/**
 * Atualiza o estado visual dos botões baseado na formatação atual
 * @param {NodeList} botoesEditor - Lista de botões da barra de ferramentas
 */
function atualizarEstadoBotoes(botoesEditor) {
    botoesEditor.forEach(button => {
        const command = button.getAttribute('data-command');
        
        if (['bold', 'underline'].includes(command)) {
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