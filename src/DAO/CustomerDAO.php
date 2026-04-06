<?php

namespace App\DAO;

use App\Core\Database;
use App\Model\Customer;
use PDO;

class CustomerDAO {
    private PDO $connection;

    public function __construct() {
        $this->connection = Database::getInstance()->getConnection();
    }

    public function searchByNameOrDocument(string $term): array
    {
        $sql = "SELECT id, name, document FROM customers WHERE name LIKE :term1 OR document LIKE :term2 LIMIT 10";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':term1' => '%' . $term . '%', ':term2' => '%' . $term . '%']);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findAll(): array {
        $sql = "SELECT * FROM customers ORDER BY name ASC";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();

        $customers = [];
        while ($row = $stmt->fetch()) {
            $customer = new Customer();
            $customer->setId($row['id'])
                     ->setName($row['name'])
                     ->setDocument($row['document'])
                     ->setEmail($row['email'])
                     ->setPhone($row['phone'])
                     ->setAddress($row['address'])
                     ->setCreatedAt($row['created_at']);
            $customers[] = $customer;
        }

        return $customers;
    }

    public function insert(Customer $customer): bool {
        $sql = "INSERT INTO customers (name, document, email, phone, address) 
                VALUES (:name, :document, :email, :phone, :address)";

        $stmt = $this->connection->prepare($sql);
        
        $name = $customer->getName();
        $document = $customer->getDocument();
        $email = $customer->getEmail();
        $phone = $customer->getPhone();
        $address = $customer->getAddress();

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':document', $document);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':address', $address);

        if ($stmt->execute()) {
            $customer->setId((int) $this->connection->lastInsertId());
            return true;
        }

        return false;
    }
}