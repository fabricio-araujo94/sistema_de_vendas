<?php

namespace App\DAO;

use App\Core\Database;
use App\Model\Product;
use PDO;

class ProductDAO {
    private PDO $connection;

    public function __construct() {
        $this->connection = Database::getInstance()->getConnection();
    }

    public function findAll(): array {
        $sql = "SELECT * FROM products ORDER BY name ASC";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();

        $products = [];
        while ($row = $stmt->fetch()) {
            $products[] = $this->mapRowToProduct($row);
        }

        return $products;
    }

    public function findById(int $id): ?Product {
        $sql = "SELECT * FROM products WHERE id = :id LIMIT 1";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();

        if ($row) {
            return $this->mapRowToProduct($row);
        }

        return null;
    }

    public function searchByName(string $searchTerm): array {
        $sql = "SELECT * FROM products 
                WHERE name LIKE :term 
                ORDER BY name ASC";
        
        $stmt = $this->connection->prepare($sql);
        $term = "%" . $searchTerm . "%";
        $stmt->bindParam(':term', $term, PDO::PARAM_STR);
        $stmt->execute();

        $products = [];
        while($row = $stmt->fetch()) {
            $products[] = $this->mapRowToProduct($row);
        }

        return $products;
    }

    public function decreaseStock(int $productId, int $quantityToDecrease): bool {
        $sql = "UPDATE products
                SET current_stock = current_stock - :quantity
                WHERE id = :id AND current_stock >= :quantity";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':quantity', $quantityToDecrease, PDO::PARAM_INT);
        $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    private function mapRowToProduct(array $row): Product {
        $product = new Product();
        $product->setId($row['id'])
                ->setName($row['name'])
                ->setDescription($row['description'])
                ->setCostPrice((float) $row['cost_price'])
                ->setSellingPrice((float) $row['selling_price'])
                ->setCurrentStock((int) $row['current_stock'])
                ->setMinimumStock((int) $row['minimum_stock'])
                ->setSupplierId($row['supplier_id'])
                ->setCreatedAt($row['created_at']);

        return $product;
    }
}