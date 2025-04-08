<?php
namespace Subrider;

class Database {
    private $pdo;
    
    public function __construct($host = null, $dbname = null, $user = null, $pass = null) {
        $host = $host ?? ($_ENV['DB_HOST'] ?? 'localhost');
        $dbname = $dbname ?? ($_ENV['DB_DATABASE'] ?? 'subrider_test');
        $user = $user ?? ($_ENV['DB_USERNAME'] ?? 'root');
        $pass = $pass ?? ($_ENV['DB_PASSWORD'] ?? '');
        
        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
        $this->pdo = new \PDO($dsn, $user, $pass);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }
    
    public function query($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        if (stripos($sql, 'SELECT') === 0) {
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        
        return $stmt->rowCount() > 0;
    }
    
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }
} 