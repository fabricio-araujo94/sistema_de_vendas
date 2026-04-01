<?php

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use App\Core\Database;
use App\DAO\SaleDAO;
use App\DAO\ProductDAO;
use App\Model\Sale;
use App\Model\SaleItem;
use App\Model\Payment;
use App\Model\Product;
use Exception;
use PDO;

class SaleDAOTest extends TestCase
{
    private SaleDAO $saleDAO;
    private ProductDAO $productDAO;
    private PDO $connection;
    private int $testUserId;

    protected function setUp(): void
    {
        $this->connection = Database::getInstance()->getConnection();

        // $this->connection->beginTransaction();
        
        $this->saleDAO = new SaleDAO();
        $this->productDAO = new ProductDAO();

        $stmt = $this->connection->prepare("INSERT INTO users (name, email, password, role) VALUES ('Test Seller', 'seller@test.com', '123', 'seller')");
        $stmt->execute();
        $this->testUserId = (int) $this->connection->lastInsertId();
    }

    protected function tearDown(): void
    {
        // $this->connection->rollBack();

        $this->connection->exec("SET FOREIGN_KEY_CHECKS = 0;");
        $this->connection->exec("TRUNCATE TABLE invoices;");
        $this->connection->exec("TRUNCATE TABLE payments;");
        $this->connection->exec("TRUNCATE TABLE sale_items;");
        $this->connection->exec("TRUNCATE TABLE sales;");
        $this->connection->exec("TRUNCATE TABLE products;");
        $this->connection->exec("TRUNCATE TABLE users;");
        $this->connection->exec("SET FOREIGN_KEY_CHECKS = 1;");
    }

    public function test_it_processes_a_complete_sale_successfully(): void
    {
        $product = new Product('Test Monitor', 500.00, 800.00, 10, 2);
        $this->productDAO->insert($product);
        $productId = $product->getId();

        $sale = new Sale(userId: $this->testUserId, customerId: null, discount: 50.00);
        
        $sale->addItem(new SaleItem($productId, 2, 800.00));
        
        $sale->addPayment(new Payment('credit_card', 1550.00, 1));

        $result = $this->saleDAO->create($sale);

        $this->assertTrue($result, "SaleDAO::create should return true on success.");
        $this->assertNotNull($sale->getId(), "Sale ID should be populated after creation.");

        $updatedProduct = $this->productDAO->findById($productId);
        $this->assertEquals(8, $updatedProduct->getCurrentStock(), "Product stock should be reduced by 2.");

        $stmt = $this->connection->prepare("SELECT * FROM invoices WHERE sale_id = ?");
        $stmt->execute([$sale->getId()]);
        $invoice = $stmt->fetch();
        
        $this->assertNotEmpty($invoice, "An invoice should be generated for the sale.");
        $this->assertStringStartsWith('NF-', $invoice['invoice_number'], "Invoice number should start with 'NF-'.");
    }

    public function test_it_rolls_back_and_throws_exception_on_insufficient_stock(): void
    {
        $product = new Product('Test Mouse', 20.00, 50.00, 5, 1);
        $this->productDAO->insert($product);
        $productId = $product->getId();

        $sale = new Sale(userId: $this->testUserId);
        $sale->addItem(new SaleItem($productId, 10, 50.00));
        $sale->addPayment(new Payment('cash', 500.00));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Insufficient stock");

        $this->saleDAO->create($sale);
    }
}