<?php

namespace App\DAO;

use App\Core\Database;
use App\Model\Sale;
use PDO;
use Exception;

class SaleDAO {
    private PDO $connection;

    public function __construct()
    {
        $this->connection = Database::getInstance()->getConnection();
    }

    public function create(Sale $sale): bool {
        try {
            $this->connection->beginTransaction();

            $totalItems = array_sum(array_map(fn($i) => $i->getSubtotal(), $sale->getItems()));
            $totalPayments = array_sum(array_map(fn($p) => $p->getAmount(), $sale->getPayments()));
            $expectedTotal = $totalItems - $sale->getDiscount();

            if (abs($expectedTotal - $totalPayments) > 0.01) {
                throw new Exception("Inconsistent sale totals");
            }

            $stmtSale = $this->connection->prepare("
                INSERT INTO sales (customer_id, user_id, total_amount, discount, status) 
                VALUES (:customer_id, :user_id, :total_amount, :discount, :status)
            ");

            $stmtSale->execute([
                ':customer_id' => $sale->getCustomerId(),
                ':user_id'     => $sale->getUserId(),
                ':total_amount'=> $sale->getTotalAmount(),
                ':discount'    => $sale->getDiscount(),
                ':status'      => $sale->getStatus()
            ]);

            $saleId = (int) $this->connection->lastInsertId();
            $sale->setId($saleId);

            $stmtItem = $this->connection->prepare("
                INSERT INTO sale_items (sale_id, product_id, quantity, unit_price, subtotal) 
                VALUES (:sale_id, :product_id, :quantity, :unit_price, :subtotal)
            ");

            $stmtStock = $this->connection->prepare("
                UPDATE products 
                SET current_stock = current_stock - :quantity 
                WHERE id = :product_id AND current_stock >= :quantity
            ");

            foreach ($sale->getItems() as $item) {

                $stmtStock->execute([
                    ':quantity'   => $item->getQuantity(),
                    ':product_id' => $item->getProductId()
                ]);

                if ($stmtStock->rowCount() === 0) {
                    throw new Exception("Insufficient stock for product ID: " . $item->getProductId());
                }

                $stmtItem->execute([
                    ':sale_id'    => $saleId,
                    ':product_id' => $item->getProductId(),
                    ':quantity'   => $item->getQuantity(),
                    ':unit_price' => $item->getUnitPrice(),
                    ':subtotal'   => $item->getSubtotal()
                ]);
            }

            $stmtPayment = $this->connection->prepare("
                INSERT INTO payments (sale_id, payment_method, amount, installments) 
                VALUES (:sale_id, :payment_method, :amount, :installments)
            ");

            foreach ($sale->getPayments() as $payment) {
                $stmtPayment->execute([
                    ':sale_id'        => $saleId,
                    ':payment_method' => $payment->getPaymentMethod(),
                    ':amount'         => $payment->getAmount(),
                    ':installments'   => $payment->getInstallments()
                ]);
            }

            $invoiceNumber = 'NF-' . str_pad((string)$saleId, 6, '0', STR_PAD_LEFT);
            
            // Generates a 44-digit random access key (Brazilian NFe Standard)
            $accessKey = '';
            for ($i = 0; $i < 44; $i++) {
                $accessKey .= mt_rand(0, 9);
            }

            $sqlInvoice = "INSERT INTO invoices (sale_id, invoice_number, access_key) 
                           VALUES (:sale_id, :invoice_number, :access_key)";
            $stmtInvoice = $this->connection->prepare($sqlInvoice);
            $stmtInvoice->execute([
                ':sale_id'        => $saleId,
                ':invoice_number' => $invoiceNumber,
                ':access_key'     => $accessKey
            ]);

            $this->connection->commit();
            return true;

        } catch (Exception $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }
}