-- Tabela de clientes (estende a tabela de proprietários)
ALTER TABLE proprietarios
ADD COLUMN email VARCHAR(255) UNIQUE,
ADD COLUMN senha VARCHAR(255),
ADD COLUMN token_reset_senha VARCHAR(100),
ADD COLUMN token_expiracao DATETIME,
ADD COLUMN ultimo_acesso DATETIME,
ADD COLUMN ativo BOOLEAN DEFAULT TRUE;

-- Tabela de tokens de acesso
CREATE TABLE tokens_acesso (
    id INT PRIMARY KEY AUTO_INCREMENT,
    proprietario_id INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    user_agent VARCHAR(255),
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    revoked_at TIMESTAMP NULL,
    FOREIGN KEY (proprietario_id) REFERENCES proprietarios(id)
);

-- Índices para otimização
CREATE INDEX idx_tokens_proprietario ON tokens_acesso(proprietario_id);
CREATE INDEX idx_tokens_token ON tokens_acesso(token);
CREATE INDEX idx_tokens_expiracao ON tokens_acesso(expires_at);

-- Tabela de preferências dos clientes
CREATE TABLE preferencias_cliente (
    proprietario_id INT PRIMARY KEY,
    notificacao_email BOOLEAN DEFAULT TRUE,
    notificacao_whatsapp BOOLEAN DEFAULT FALSE,
    tema VARCHAR(20) DEFAULT 'light',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (proprietario_id) REFERENCES proprietarios(id)
);

-- Trigger para criar preferências ao cadastrar cliente
DELIMITER //
CREATE TRIGGER criar_preferencias_cliente
AFTER UPDATE ON proprietarios
FOR EACH ROW
BEGIN
    IF NEW.email IS NOT NULL AND OLD.email IS NULL THEN
        INSERT IGNORE INTO preferencias_cliente (proprietario_id)
        VALUES (NEW.id);
    END IF;
END;
//
DELIMITER ; 