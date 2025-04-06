-- Tabela de notificações
CREATE TABLE notificacoes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    tipo VARCHAR(50) NOT NULL,
    titulo VARCHAR(100) NOT NULL,
    mensagem TEXT NOT NULL,
    link VARCHAR(255),
    lida BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Índices para otimização
CREATE INDEX idx_notificacoes_usuario ON notificacoes(usuario_id);
CREATE INDEX idx_notificacoes_lida ON notificacoes(lida);
CREATE INDEX idx_notificacoes_created_at ON notificacoes(created_at);

-- Tipos de notificações
INSERT INTO enum_tipos_notificacao (tipo, descricao) VALUES
('os_criada', 'Nova ordem de serviço criada'),
('os_atualizada', 'Ordem de serviço atualizada'),
('os_finalizada', 'Ordem de serviço finalizada'),
('os_cancelada', 'Ordem de serviço cancelada'),
('moto_transferida', 'Transferência de propriedade'),
('backup_erro', 'Erro no backup do sistema'),
('sistema_atualizacao', 'Atualização do sistema'); 