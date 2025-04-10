<?php

namespace Subrider\Repositories;

use Subrider\Database\Database;
use PDO;

class UserRepository {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Verifica se a senha está correta
     * 
     * @param string $username Nome de usuário
     * @param string $password Senha
     * @return bool True se a senha está correta, false caso contrário
     */
    public function verificarSenha(string $username, string $password): bool {
        try {
            $sql = "SELECT password FROM login WHERE username = :username";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':username' => $username]);
            
            if ($user = $stmt->fetch()) {
                return password_verify($password, $user['password']);
            }
            
            return false;
        } catch (\PDOException $e) {
            error_log("Erro ao verificar senha: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Atualiza a senha do usuário
     * 
     * @param string $username Nome de usuário
     * @param string $newPassword Nova senha
     * @return bool True se a senha foi atualizada com sucesso, false caso contrário
     */
    public function atualizarSenha(string $username, string $newPassword): bool {
        try {
            $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            
            $sql = "UPDATE login SET password = :password WHERE username = :username";
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([
                ':password' => $passwordHash,
                ':username' => $username
            ]);
        } catch (\PDOException $e) {
            error_log("Erro ao atualizar senha: " . $e->getMessage());
            return false;
        }
    }
} 