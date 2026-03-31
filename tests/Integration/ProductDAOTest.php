<?php

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use App\Core\Database;
use App\DAO\ProductDAO;
use App\Model\Product;

class ProductDAOTest extends TestCase
{
    private ProductDAO $productDAO;

    protected function setUp(): void
    {
        $this->productDAO = new ProductDAO();
        Database::getInstance()->getConnection()->beginTransaction();
    }

    protected function tearDown(): void
    {
        Database::getInstance()->getConnection()->rollBack();
    }

    public function test_it_can_insert_a_new_product(): void
    {
        $product = new Product('Test Product', 15.00, 30.00, 100, 10);
        $product->setDescription('This is a test description');

        $isInserted = $this->productDAO->insert($product);

        $this->assertTrue($isInserted, 'The insert method should return true.');
        $this->assertNotNull($product->getId(), 'The product ID should be populated after insert.');
        $this->assertGreaterThan(0, $product->getId());
    }

    public function test_it_can_find_a_product_by_id(): void
    {
        $product = new Product('Keyboard', 20.00, 50.00, 10, 2);
        $this->productDAO->insert($product);
        $insertedId = $product->getId();

        $foundProduct = $this->productDAO->findById($insertedId);

        $this->assertNotNull($foundProduct);
        $this->assertEquals('Keyboard', $foundProduct->getName());
        $this->assertEquals(50.00, $foundProduct->getSellingPrice());
        $this->assertEquals(10, $foundProduct->getCurrentStock());
    }

    public function test_it_returns_null_when_product_not_found(): void
    {
        $foundProduct = $this->productDAO->findById(999999);
        $this->assertNull($foundProduct);
    }
}