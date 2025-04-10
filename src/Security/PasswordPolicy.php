<?php

namespace Subrider\Security;

use Subrider\Database\Database;
use PDO;

class PasswordPolicy {
    const MIN_LENGTH = 8;
    const REQUIRE_UPPERCASE = true;
    const REQUIRE_LOWERCASE = true;
    const REQUIRE_NUMBERS = true;
    const REQUIRE_SPECIAL = true;
    const HISTORY_SIZE = 5;

    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Valida uma senha de acordo com a política
     * 
     * @param string $password Senha a ser validada
     * @return array Array com resultado da validação
     */
    public function validarSenha(string $password): array {
        $erros = [];
        $valido = true;

        if (strlen($password) < self::MIN_LENGTH) {
            $erros[] = "A senha deve ter no mínimo " . self::MIN_LENGTH . " caracteres";
            $valido = false;
        }

        if (self::REQUIRE_UPPERCASE && !preg_match('/[A-Z]/', $password)) {
            $erros[] = "A senha deve conter pelo menos uma letra maiúscula";
            $valido = false;
        }

        if (self::REQUIRE_LOWERCASE && !preg_match('/[a-z]/', $password)) {
            $erros[] = "A senha deve conter pelo menos uma letra minúscula";
            $valido = false;
        }

        if (self::REQUIRE_NUMBERS && !preg_match('/[0-9]/', $password)) {
            $erros[] = "A senha deve conter pelo menos um número";
            $valido = false;
        }

        if (self::REQUIRE_SPECIAL && !preg_match('/[^A-Za-z0-9]/', $password)) {
            $erros[] = "A senha deve conter pelo menos um caractere especial";
            $valido = false;
        }

        return [
            'valido' => $valido,
            'erros' => $erros
        ];
    }

    /**
     * Verifica se a senha já foi usada recentemente
     * 
     * @param string $username Nome de usuário
     * @param string $password Senha a ser verificada
     * @return bool True se a senha já foi usada, false caso contrário
     */
    public function senhaJaUsada(string $username, string $password): bool {
        try {
            $sql = "SELECT password FROM password_history 
                    WHERE username = :username 
                    ORDER BY created_at DESC 
                    LIMIT " . self::HISTORY_SIZE;
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':username' => $username]);
            
            while ($row = $stmt->fetch()) {
                if (password_verify($password, $row['password'])) {
                    return true;
                }
            }
            
            return false;
        } catch (\PDOException $e) {
            error_log("Erro ao verificar histórico de senhas: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Registra uma senha no histórico
     * 
     * @param string $username Nome de usuário
     * @param string $password Senha a ser registrada
     * @return bool True se a senha foi registrada com sucesso, false caso contrário
     */
    public function registrarHistoricoSenha(string $username, string $password): bool {
        try {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            
            $sql = "INSERT INTO password_history (username, password, created_at) 
                    VALUES (:username, :password, NOW())";
            
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([
                ':username' => $username,
                ':password' => $passwordHash
            ]);
        } catch (\PDOException $e) {
            error_log("Erro ao registrar histórico de senha: " . $e->getMessage());
            return false;
        }
    }
} 