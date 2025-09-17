/**
 * Sistema de AJAX e Histórico de Campos para Dados Técnicos
 * Permite salvar dados sem recarregar a página e manter histórico de valores
 */

class DadosAjaxManager {
    constructor() {
        this.fieldHistory = new Map(); // Armazena histórico de cada campo
        this.currentTable = null;
        this.ordem = null;
        this.saveTimeout = null;
        this.init();
    }

    init() {
        // Obter ordem da URL
        const urlParams = new URLSearchParams(window.location.search);
        this.ordem = urlParams.get('ordem');
        
        // Aguardar DOM carregar
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setupEventListeners());
        } else {
            this.setupEventListeners();
        }
    }

    setupEventListeners() {
        // Configurar eventos para campos de entrada
        this.setupFieldEvents();
        
        // Configurar eventos para botões de histórico
        this.setupHistoryButtons();
        
        // Configurar auto-save
        this.setupAutoSave();
    }

    setupFieldEvents() {
        // Selecionar todos os campos de medição
        const fields = document.querySelectorAll('input[name^="medida["]');
        
        fields.forEach(field => {
            // Armazenar valor inicial no histórico
            this.initFieldHistory(field);
            
            // Eventos de mudança
            field.addEventListener('input', (e) => this.handleFieldChange(e));
            field.addEventListener('blur', (e) => this.handleFieldBlur(e));
            field.addEventListener('focus', (e) => this.handleFieldFocus(e));
        });
    }

    initFieldHistory(field) {
        const fieldName = this.getFieldName(field);
        const currentValue = field.value;
        
        if (!this.fieldHistory.has(fieldName)) {
            this.fieldHistory.set(fieldName, {
                values: [currentValue],
                currentIndex: 0,
                originalValue: currentValue
            });
        }
        
        // Adicionar botão de histórico se não existir
        this.addHistoryButton(field);
    }

    addHistoryButton(field) {
        const fieldName = this.getFieldName(field);
        const container = field.parentElement;
        
        // Verificar se já existe botão
        if (container.querySelector('.history-btn')) return;
        
        // Criar wrapper se necessário
        if (!container.classList.contains('field-wrapper')) {
            const wrapper = document.createElement('div');
            wrapper.className = 'field-wrapper';
            field.parentNode.insertBefore(wrapper, field);
            wrapper.appendChild(field);
        }
        
        // Criar botão de histórico
        const historyBtn = document.createElement('button');
        historyBtn.type = 'button';
        historyBtn.className = 'history-btn';
        historyBtn.innerHTML = '<i class="fas fa-history"></i>';
        historyBtn.title = 'Ver histórico de valores';
        historyBtn.addEventListener('click', (e) => this.showHistoryMenu(e, fieldName));
        
        // Criar indicador de mudança
        const changeIndicator = document.createElement('span');
        changeIndicator.className = 'change-indicator';
        changeIndicator.innerHTML = '<i class="fas fa-circle"></i>';
        changeIndicator.style.display = 'none';
        
        container.appendChild(historyBtn);
        container.appendChild(changeIndicator);
    }

    handleFieldChange(event) {
        const field = event.target;
        const fieldName = this.getFieldName(field);
        const newValue = field.value;
        
        // Atualizar histórico
        this.updateFieldHistory(fieldName, newValue);
        
        // Mostrar indicador de mudança
        this.showChangeIndicator(field);
        
        // Agendar auto-save
        this.scheduleAutoSave();
    }

    handleFieldBlur(event) {
        const field = event.target;
        const fieldName = this.getFieldName(field);
        
        // Salvar valor no histórico se for diferente
        const history = this.fieldHistory.get(fieldName);
        if (history && field.value !== history.values[history.values.length - 1]) {
            this.addToHistory(fieldName, field.value);
        }
    }

    handleFieldFocus(event) {
        const field = event.target;
        this.currentTable = this.getTableFromField(field);
    }

    updateFieldHistory(fieldName, value) {
        const history = this.fieldHistory.get(fieldName);
        if (history) {
            // Não adicionar se for o mesmo valor
            if (history.values[history.values.length - 1] !== value) {
                history.values.push(value);
                history.currentIndex = history.values.length - 1;
                
                // Limitar histórico a 10 valores
                if (history.values.length > 10) {
                    history.values.shift();
                    history.currentIndex = Math.max(0, history.currentIndex - 1);
                }
            }
        }
    }

    addToHistory(fieldName, value) {
        const history = this.fieldHistory.get(fieldName);
        if (history && value !== history.values[history.values.length - 1]) {
            history.values.push(value);
            history.currentIndex = history.values.length - 1;
            
            if (history.values.length > 10) {
                history.values.shift();
                history.currentIndex = Math.max(0, history.currentIndex - 1);
            }
        }
    }

    showHistoryMenu(event, fieldName) {
        event.preventDefault();
        event.stopPropagation();
        
        const history = this.fieldHistory.get(fieldName);
        if (!history || history.values.length <= 1) {
            this.showMessage('Nenhum histórico disponível para este campo', 'info');
            return;
        }
        
        // Remover menu existente
        this.removeHistoryMenu();
        
        // Criar menu de histórico
        const menu = document.createElement('div');
        menu.className = 'history-menu';
        menu.innerHTML = `
            <div class="history-header">
                <span>Histórico de Valores</span>
                <button type="button" class="close-btn">&times;</button>
            </div>
            <div class="history-list">
                ${history.values.map((value, index) => `
                    <div class="history-item ${index === history.currentIndex ? 'current' : ''}" 
                         data-index="${index}">
                        <span class="value">${value || '(vazio)'}</span>
                        <span class="actions">
                            <button type="button" class="restore-btn" title="Restaurar este valor">
                                <i class="fas fa-undo"></i>
                            </button>
                        </span>
                    </div>
                `).join('')}
            </div>
        `;
        
        // Posicionar menu
        const rect = event.target.getBoundingClientRect();
        menu.style.position = 'absolute';
        menu.style.top = (rect.bottom + window.scrollY) + 'px';
        menu.style.left = (rect.left + window.scrollX) + 'px';
        menu.style.zIndex = '1000';
        
        document.body.appendChild(menu);
        
        // Eventos do menu
        menu.querySelector('.close-btn').addEventListener('click', () => this.removeHistoryMenu());
        
        menu.querySelectorAll('.restore-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const index = parseInt(e.target.closest('.history-item').dataset.index);
                this.restoreValue(fieldName, index);
                this.removeHistoryMenu();
            });
        });
        
        // Fechar ao clicar fora
        setTimeout(() => {
            document.addEventListener('click', this.closeHistoryMenuHandler.bind(this), { once: true });
        }, 100);
    }

    closeHistoryMenuHandler(event) {
        if (!event.target.closest('.history-menu')) {
            this.removeHistoryMenu();
        }
    }

    removeHistoryMenu() {
        const menu = document.querySelector('.history-menu');
        if (menu) {
            menu.remove();
        }
    }

    restoreValue(fieldName, index) {
        const history = this.fieldHistory.get(fieldName);
        if (!history || !history.values[index]) return;
        
        const field = document.querySelector(`input[name="medida[${fieldName}]"]`);
        if (field) {
            const oldValue = field.value;
            const newValue = history.values[index];
            
            field.value = newValue;
            history.currentIndex = index;
            
            // Adicionar valor antigo ao histórico se for diferente
            if (oldValue !== newValue && oldValue !== '') {
                this.addToHistory(fieldName, oldValue);
            }
            
            // Mostrar indicador de mudança
            this.showChangeIndicator(field);
            
            // Agendar auto-save
            this.scheduleAutoSave();
            
            this.showMessage(`Valor restaurado: ${newValue}`, 'success');
        }
    }

    showChangeIndicator(field) {
        const indicator = field.parentElement.querySelector('.change-indicator');
        if (indicator) {
            indicator.style.display = 'inline-block';
            indicator.classList.add('pulse');
            
            setTimeout(() => {
                indicator.classList.remove('pulse');
            }, 1000);
        }
    }

    hideChangeIndicator(field) {
        const indicator = field.parentElement.querySelector('.change-indicator');
        if (indicator) {
            indicator.style.display = 'none';
        }
    }

    scheduleAutoSave() {
        // Cancelar save anterior
        if (this.saveTimeout) {
            clearTimeout(this.saveTimeout);
        }
        
        // Agendar novo save em 2 segundos
        this.saveTimeout = setTimeout(() => {
            this.saveData();
        }, 2000);
    }

    async saveData() {
        if (!this.currentTable || !this.ordem) {
            this.showMessage('Erro: Tabela ou ordem não identificada', 'error');
            return;
        }
        
        // Coletar dados dos campos
        const medicoes = {};
        const fields = document.querySelectorAll(`input[name^="medida["]`);
        
        fields.forEach(field => {
            const fieldName = this.getFieldName(field);
            medicoes[fieldName] = field.value;
        });
        
        // Mostrar indicador de salvamento
        this.showSaveIndicator(true);
        
        try {
            const response = await fetch('ajax_handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    table: this.currentTable,
                    ordem: this.ordem,
                    medicoes: medicoes
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.showMessage(result.message, 'success');
                this.hideAllChangeIndicators();
            } else {
                this.showMessage(result.message, 'error');
            }
            
        } catch (error) {
            this.showMessage('Erro de conexão: ' + error.message, 'error');
        } finally {
            this.showSaveIndicator(false);
        }
    }

    showSaveIndicator(show) {
        let indicator = document.querySelector('.save-indicator');
        
        if (show) {
            if (!indicator) {
                indicator = document.createElement('div');
                indicator.className = 'save-indicator';
                indicator.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Salvando...';
                document.body.appendChild(indicator);
            }
            indicator.style.display = 'block';
        } else {
            if (indicator) {
                indicator.style.display = 'none';
            }
        }
    }

    hideAllChangeIndicators() {
        const indicators = document.querySelectorAll('.change-indicator');
        indicators.forEach(indicator => {
            indicator.style.display = 'none';
        });
    }

    showMessage(message, type = 'info') {
        // Remover mensagem anterior
        const existingMsg = document.querySelector('.ajax-message');
        if (existingMsg) {
            existingMsg.remove();
        }
        
        // Criar nova mensagem
        const msgDiv = document.createElement('div');
        msgDiv.className = `ajax-message ajax-message--${type}`;
        msgDiv.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
            ${message}
        `;
        
        // Inserir no topo da página
        const container = document.querySelector('.dados-main-container') || document.body;
        container.insertBefore(msgDiv, container.firstChild);
        
        // Auto-remover após 5 segundos
        setTimeout(() => {
            if (msgDiv.parentNode) {
                msgDiv.remove();
            }
        }, 5000);
    }

    getFieldName(field) {
        const match = field.name.match(/medida\[([^\]]+)\]/);
        return match ? match[1] : field.name;
    }

    getTableFromField(field) {
        // Tentar identificar a tabela pelo contexto
        const tabContent = field.closest('.tab-content');
        if (tabContent) {
            const tabId = tabContent.id;
            return tabId.replace('tab-', '');
        }
        
        // Fallback: tentar pelo formulário
        const form = field.closest('form');
        if (form) {
            const tableInput = form.querySelector('input[name="table"]');
            if (tableInput) {
                return tableInput.value;
            }
        }
        
        return null;
    }

    // Método público para salvar manualmente
    forceSave() {
        if (this.saveTimeout) {
            clearTimeout(this.saveTimeout);
        }
        this.saveData();
    }
}

// Inicializar quando o DOM estiver pronto
let dadosAjaxManager;

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        dadosAjaxManager = new DadosAjaxManager();
    });
} else {
    dadosAjaxManager = new DadosAjaxManager();
}

// Expor globalmente para uso em outros scripts
window.DadosAjaxManager = DadosAjaxManager;
window.dadosAjaxManager = dadosAjaxManager;