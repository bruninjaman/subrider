<?php

namespace Subrider\Database;

use PDO;
use PDOException;

class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        try {
            $host = $_ENV['DB_HOST'] ?? 'localhost';
            $dbname = $_ENV['DB_NAME'] ?? 'subrider';
            $username = $_ENV['DB_USER'] ?? 'root';
            $password = $_ENV['DB_PASS'] ?? '';

            $this->connection = new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            error_log("Erro de conexão com o banco de dados: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Retorna a instância única da classe Database
     * 
     * @return Database
     */
    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Retorna a conexão PDO
     * 
     * @return PDO
     */
    public function getConnection(): PDO {
        return $this->connection;
    }

    /**
     * Prepara uma declaração SQL
     * 
     * @param string $sql SQL a ser preparado
     * @return \PDOStatement
     */
    public function prepare(string $sql): \PDOStatement {
        return $this->connection->prepare($sql);
    }

    /**
     * Executa uma consulta SQL
     * 
     * @param string $sql SQL a ser executado
     * @return \PDOStatement
     */
    public function query(string $sql): \PDOStatement {
        return $this->connection->query($sql);
    }

    public function beginTransaction(): bool {
        return $this->connection->beginTransaction();
    }

    public function commit(): bool {
        return $this->connection->commit();
    }

    public function rollBack(): bool {
        return $this->connection->rollBack();
    }

    private function __clone() {}
    public function __wakeup() {}
} 