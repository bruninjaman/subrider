<?php

namespace Subrider\Repositories;

use Subrider\Database\Database;
use PDO;

class OrdemServicoRepository {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Cria uma nova ordem de serviço
     * 
     * @param array $dados Dados da ordem de serviço
     * @return int ID da ordem de serviço criada
     */
    public function criar(array $dados): int {
        try {
            $sql = "INSERT INTO ordem_servicos (
                motoID, Data, KM, Status, Descricao
            ) VALUES (
                :moto_id, :data_entrada, :km, 'Aberta', :descricao
            )";

            $stmt = $this->db->prepare($sql);
            
            $stmt->execute([
                ':moto_id' => $dados['id_moto'],
                ':data_entrada' => $dados['data_entrada'],
                ':km' => $dados['km'] ?? null,
                ':descricao' => $dados['descricao'] ?? null
            ]);

            return $this->db->getConnection()->lastInsertId();
        } catch (\PDOException $e) {
            error_log("Erro ao criar ordem de serviço: " . $e->getMessage());
            throw $e;
        }
    }
} 