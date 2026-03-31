<?php

namespace App\DAO;

use App\Core\Database;
use App\Model\Supplier;
use PDO;

class SupplierDAO
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = Database::getInstance()->getConnection();
    }
    
    public function findAll(): array
    {
        $sql = "SELECT * FROM suppliers ORDER BY company_name ASC";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();

        $suppliers = [];
        while ($row = $stmt->fetch()) {
            $supplier = new Supplier();
            $supplier->setId($row['id'])
                     ->setCompanyName($row['company_name'])
                     ->setDocument($row['document'])
                     ->setContact($row['contact'])
                     ->setAddress($row['address'])
                     ->setCreatedAt($row['created_at']);
            $suppliers[] = $supplier;
        }

        return $suppliers;
    }

    public function insert(Supplier $supplier): bool
    {
        $sql = "INSERT INTO suppliers (company_name, document, contact, address) 
                VALUES (:company_name, :document, :contact, :address)";

        $stmt = $this->connection->prepare($sql);
        
        $companyName = $supplier->getCompanyName();
        $document = $supplier->getDocument();
        $contact = $supplier->getContact();
        $address = $supplier->getAddress();

        $stmt->bindParam(':company_name', $companyName);
        $stmt->bindParam(':document', $document);
        $stmt->bindParam(':contact', $contact);
        $stmt->bindParam(':address', $address);

        if ($stmt->execute()) {
            $supplier->setId((int) $this->connection->lastInsertId());
            return true;
        }

        return false;
    }
}