<?php

namespace App\Model;

class SaleItem
{
    private int $productId;
    private int $quantity;
    private float $unitPrice;
    private float $subtotal;

    public function __construct(int $productId, int $quantity, float $unitPrice)
    {
        $this->productId = $productId;
        $this->quantity = $quantity;
        $this->unitPrice = $unitPrice;
        $this->subtotal = $quantity * $unitPrice;
    }

    public function getProductId(): int { return $this->productId; }
    public function getQuantity(): int { return $this->quantity; }
    public function getUnitPrice(): float { return $this->unitPrice; }
    public function getSubtotal(): float { return $this->subtotal; }
}