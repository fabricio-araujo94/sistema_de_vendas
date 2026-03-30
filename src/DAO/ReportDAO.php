<?php

namespace App\DAO;

use App\Core\Database;
use PDO;

class ReportDAO {
    private PDO $connection;

    public function __construct() {
        $this->connection = Database::getInstance()->getConnection();
    }

    public function getLowStockProducts(): array {
        $sql = "SELECT id, name, current_stock, minimum_stock 
                FROM products 
                WHERE current_stock <= minimum_stock 
                ORDER BY current_stock ASC";
                
        $stmt = $this->connection->query($sql);
        return $stmt->fetchAll();
    }

    public function getTopSellingProducts(int $limit = 5): array {
        $sql = "SELECT p.name, 
                       SUM(si.quantity) as total_sold, 
                       SUM(si.subtotal) as total_revenue
                FROM sale_items si
                JOIN products p ON si.product_id = p.id
                JOIN sales s ON si.sale_id = s.id
                WHERE s.status = 'completed'
                GROUP BY p.id
                ORDER BY total_sold DESC
                LIMIT :limit";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    public function getSalesBySeller(): array {
        $sql = "SELECT u.name as seller_name, 
                       COUNT(s.id) as total_sales, 
                       SUM(s.total_amount) as total_revenue
                FROM sales s
                JOIN users u ON s.user_id = u.id
                WHERE s.status = 'completed'
                GROUP BY u.id
                ORDER BY total_revenue DESC";

        $stmt = $this->connection->query($sql);
        return $stmt->fetchAll();
    }

    public function getRecentRevenue(): float {
        $sql = "SELECT SUM(total_amount) as revenue 
                FROM sales 
                WHERE status = 'completed' 
                AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
                
        $stmt = $this->connection->query($sql);
        $result = $stmt->fetch();
        
        return (float) ($result['revenue'] ?? 0.0);
    }
}