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

    public function findByEmail(string $email): ?User {
        $sql = "SELECT id, name, email, password, role, created_at
                FROM users
                WHERE email = :email LIMIT 1";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        $row = $stmt->fetch();

        if ($row) {
            return $this->mapRowToUser($row);
        }

        return null;
    }

    public function insert(User $user): bool {
        $sql = "INSERT INTO users (name, email, password, role)
                VALUES (:name, :email, :password, :role)";

        $stmt = $this->connection->prepare($sql);

        $name = $user->getName();
        $email = $user->getEmail();
        $password = $user->getPassword();
        $role = $user->getRole();

        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password", $password);
        $stmt->bindParam(":role", $role);

        if ($stmt->execute()) {
            $user->setId((int) $this->connection->lastInsertId());
            return true;
        }

        return false;
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