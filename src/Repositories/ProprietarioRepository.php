<?php

namespace Subrider\Repositories;

use Subrider\Database\Database;
use PDO;

class ProprietarioRepository {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Cria um novo proprietário no banco de dados
     * 
     * @param array $dados Dados do proprietário
     * @return bool True se o proprietário foi criado com sucesso, false caso contrário
     */
    public function criar(array $dados): bool {
        try {
            $sql = "INSERT INTO proprietarios (
                nome, cpf, telefone, email, endereco, 
                cidade, estado, cep, data_cadastro
            ) VALUES (
                :nome, :cpf, :telefone, :email, :endereco,
                :cidade, :estado, :cep, NOW()
            )";

            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([
                ':nome' => $dados['nome'],
                ':cpf' => preg_replace('/[^0-9]/', '', $dados['cpf']),
                ':telefone' => preg_replace('/[^0-9]/', '', $dados['telefone']),
                ':email' => $dados['email'] ?? null,
                ':endereco' => $dados['endereco'],
                ':cidade' => $dados['cidade'],
                ':estado' => $dados['estado'],
                ':cep' => preg_replace('/[^0-9]/', '', $dados['cep'])
            ]);
        } catch (\PDOException $e) {
            error_log("Erro ao criar proprietário: " . $e->getMessage());
            return false;
        }
    }
} 