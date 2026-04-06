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
                INSERT INTO sales (user_id, customer_id, total_amount, discount, status) 
                VALUES (:user_id, :customer_id, :total_amount, :discount, :status)
            ");

            $stmtSale->bindValue(':user_id', $sale->getUserId());
            $stmtSale->bindValue(':customer_id', $sale->getCustomerId()); 
            $stmtSale->bindValue(':total_amount', $sale->getTotalAmount());
            $stmtSale->bindValue(':discount', $sale->getDiscount());
            $stmtSale->bindValue(':status', 'completed'); 

            $stmtSale->execute();

            $saleId = (int) $this->connection->lastInsertId();
            $sale->setId($saleId);

            $stmtItem = $this->connection->prepare("
                INSERT INTO sale_items (sale_id, product_id, quantity, unit_price, subtotal) 
                VALUES (:sale_id, :product_id, :quantity, :unit_price, :subtotal)
            ");

            $stmtStock = $this->connection->prepare("
                UPDATE products 
                SET current_stock = current_stock - :qty_deduct
                WHERE id = :product_id AND current_stock >= :qty_check
            ");

            foreach ($sale->getItems() as $item) {

                $stmtStock->execute([
                    ':qty_deduct' => $item->getQuantity(),
                    ':product_id' => $item->getProductId(),
                    ':qty_check'  => $item->getQuantity()
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