<?php
require_once __DIR__ . '/../connection/Database.php';

class ProprietarioRepository {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Cria um novo proprietário
     * 
     * @param array $data Dados do proprietário
     * @return int|bool ID do proprietário criado ou false em caso de erro
     */
    public function criar($data) {
        $sql = "INSERT INTO proprietarios (nome, cpf, telefone, email, endereco, cidade, estado, cep, data_cadastro) 
                VALUES (:nome, :cpf, :telefone, :email, :endereco, :cidade, :estado, :cep, NOW())";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':nome' => $data['nome'],
                ':cpf' => preg_replace('/[^0-9]/', '', $data['cpf']),
                ':telefone' => preg_replace('/[^0-9]/', '', $data['telefone']),
                ':email' => $data['email'],
                ':endereco' => $data['endereco'],
                ':cidade' => $data['cidade'],
                ':estado' => $data['estado'],
                ':cep' => preg_replace('/[^0-9]/', '', $data['cep'])
            ]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Erro ao criar proprietário: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Busca um proprietário pelo ID
     * 
     * @param int $id ID do proprietário
     * @return array|false Dados do proprietário ou false se não encontrado
     */
    public function buscarPorId($id) {
        $sql = "SELECT * FROM proprietarios WHERE id = :id";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao buscar proprietário: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lista todos os proprietários com paginação
     * 
     * @param int $pagina Número da página
     * @param int $porPagina Itens por página
     * @return array Array com proprietários e total
     */
    public function listar($pagina = 1, $porPagina = 10) {
        $offset = ($pagina - 1) * $porPagina;
        
        try {
            // Busca total de registros
            $total = $this->db->query("SELECT COUNT(*) FROM proprietarios")->fetchColumn();
            
            // Busca proprietários com paginação
            $sql = "SELECT * FROM proprietarios ORDER BY nome LIMIT :limit OFFSET :offset";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', $porPagina, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return [
                'proprietarios' => $stmt->fetchAll(PDO::FETCH_ASSOC),
                'total' => $total
            ];
        } catch (PDOException $e) {
            error_log("Erro ao listar proprietários: " . $e->getMessage());
            return ['proprietarios' => [], 'total' => 0];
        }
    }

    /**
     * Atualiza um proprietário
     * 
     * @param int $id ID do proprietário
     * @param array $data Dados do proprietário
     * @return bool Sucesso da operação
     */
    public function atualizar($id, $data) {
        $sql = "UPDATE proprietarios SET 
                nome = :nome,
                cpf = :cpf,
                telefone = :telefone,
                email = :email,
                endereco = :endereco,
                cidade = :cidade,
                estado = :estado,
                cep = :cep
                WHERE id = :id";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':nome' => $data['nome'],
                ':cpf' => preg_replace('/[^0-9]/', '', $data['cpf']),
                ':telefone' => preg_replace('/[^0-9]/', '', $data['telefone']),
                ':email' => $data['email'],
                ':endereco' => $data['endereco'],
                ':cidade' => $data['cidade'],
                ':estado' => $data['estado'],
                ':cep' => preg_replace('/[^0-9]/', '', $data['cep'])
            ]);
        } catch (PDOException $e) {
            error_log("Erro ao atualizar proprietário: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Exclui um proprietário
     * 
     * @param int $id ID do proprietário
     * @return bool Sucesso da operação
     */
    public function excluir($id) {
        $sql = "DELETE FROM proprietarios WHERE id = :id";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Erro ao excluir proprietário: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Busca proprietários por termo de pesquisa
     * 
     * @param string $termo Termo de pesquisa
     * @return array Lista de proprietários encontrados
     */
    public function buscar($termo) {
        $sql = "SELECT * FROM proprietarios 
                WHERE nome LIKE :termo 
                OR cpf LIKE :termo 
                OR email LIKE :termo 
                LIMIT 10";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':termo' => "%$termo%"]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao buscar proprietários: " . $e->getMessage());
            return [];
        }
    }
} 