<?php

namespace App\DAO;

use App\Core\Database;
use App\Model\User;
use PDO;

class UserDAO {
    private PDO $connection;

    public function __construct() {
        $this->connection = Database::getInstance()->getConnection();
    }


    public function findByEmail(string $email): ?User
    {
        $sql = "SELECT id, name, email, password, role, created_at FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        $user = new User();
        
        $user->setId((int) $row['id'])
             ->setName($row['name'])
             ->setEmail($row['email'])
             ->setPassword($row['password'])
             ->setRole($row['role'])
             ->setCreatedAt($row['created_at']);

        return $user;
    }

    public function findAll(): array
    {
        $sql = "SELECT id, name, email, role, created_at FROM users ORDER BY name ASC";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function insert(string $name, string $email, string $rawPassword, string $role): bool
    {
        $sql = "INSERT INTO users (name, email, password, role) 
                VALUES (:name, :email, :password, :role)";

        $stmt = $this->connection->prepare($sql);
        
        $hashedPassword = password_hash($rawPassword, PASSWORD_DEFAULT);

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':role', $role);

        return $stmt->execute();
    }

    private function mapRowToUser(array $row): User {
        $user = new User();
        $user->setId($row['id'])
             ->setName($row['name'])
             ->setEmail($row['email'])
             ->setPassword($row['password'])
             ->setRole($row['role'])
             ->setCreatedAt($row['created_at']);

        return $user;
    }
}