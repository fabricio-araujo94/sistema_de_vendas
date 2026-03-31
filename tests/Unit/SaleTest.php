<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Model\Sale;
use App\Model\SaleItem;

class SaleTest extends TestCase
{
    public function test_sale_item_calculates_subtotal_correctly(): void
    {
        $item = new SaleItem(1, 3, 50.00); 
        $this->assertEquals(150.00, $item->getSubtotal());
    }

    public function test_sale_calculates_total_without_discount(): void
    {
        $sale = new Sale(userId: 1);
        $item1 = new SaleItem(productId: 1, quantity: 2, unitPrice: 100.00); 
        $item2 = new SaleItem(productId: 2, quantity: 1, unitPrice: 50.00);  

        $sale->addItem($item1);
        $sale->addItem($item2);

        $this->assertEquals(250.00, $sale->getTotalAmount());
        $this->assertCount(2, $sale->getItems());
    }

    public function test_sale_calculates_total_with_discount(): void
    {
        $sale = new Sale(userId: 1, customerId: null, discount: 30.00);
        $item1 = new SaleItem(productId: 1, quantity: 2, unitPrice: 100.00);

        $sale->addItem($item1);

        $this->assertEquals(170.00, $sale->getTotalAmount());
    }
}