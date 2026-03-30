<?php

namespace App\DAO;

use App\Core\Database;
use PDO;

class CustomerDAO {
    private PDO $connection;

    public function __construct() {
        $this->connection = Database::getInstance()->getConnection();
    }

    public function searchByNameOrDocument(string $searchTerm): array {
        $sql = "SELECT id, name, document FROM customers
                WHERE name LIKE :term OR document LIKE :term
                ORDER BY name ASC LIMIT 10";

        $stmt = $this->connection->prepare($sql);
        $term = "%" . $searchTerm . "%";
        $stmt->bindParam(':term', $term, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}