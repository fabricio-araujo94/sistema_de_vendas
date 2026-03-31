<?php

namespace App\DAO;

use App\Core\Database;
use PDO;

class InvoiceDAO {
    private PDO $connection;

    public function __construct() {
        $this->connection = Database::getInstance()->getConnection();
    }

    public function getInvoiceDetails(int $saleId): ?array {
        $sql = "SELECT i.invoice_number, i.access_key, i.issued_at,
                       s.id as sale_id, s.total_amount, s.discount,
                       c.name as customer_name, c.document as customer_document
                FROM invoices i
                JOIN sales s ON i.sale_id = s.id
                LEFT JOIN customers c ON s.customer_id = c.id
                WHERE s.id = :sale_id LIMIT 1";
                
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':sale_id', $saleId, PDO::PARAM_INT);
        $stmt->execute();
        
        $invoiceData = $stmt->fetch();

        if (!$invoiceData) return null;

        $sqlItems = "SELECT p.name, si.quantity, si.unit_price, si.subtotal
                     FROM sale_items si
                     JOIN products p ON si.product_id = p.id
                     WHERE si.sale_id = :sale_id";
                     
        $stmtItems = $this->connection->prepare($sqlItems);
        $stmtItems->bindParam(':sale_id', $saleId, PDO::PARAM_INT);
        $stmtItems->execute();
        
        $invoiceData['items'] = $stmtItems->fetchAll();

        return $invoiceData;
    }
}