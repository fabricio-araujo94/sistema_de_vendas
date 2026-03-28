<?php

namespace App\Model;

class Sale
{
    private ?int $id = null;
    private ?int $customerId;
    private int $userId;
    private float $totalAmount = 0.0;
    private float $discount = 0.0;
    private string $status = 'completed';
    
    private array $items = [];
    
    private array $payments = [];

    public function __construct(int $userId, ?int $customerId = null, float $discount = 0.0)
    {
        $this->userId = $userId;
        $this->customerId = $customerId;
        $this->discount = $discount;
    }

    public function addItem(SaleItem $item): void
    {
        $this->items[] = $item;
        $this->calculateTotal();
    }

    public function addPayment(Payment $payment): void
    {
        $this->payments[] = $payment;
    }

    private function calculateTotal(): void
    {
        $total = 0.0;
        foreach ($this->items as $item) {
            $total += $item->getSubtotal();
        }
        $this->totalAmount = $total - $this->discount;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(int $id): void { $this->id = $id; }
    public function getCustomerId(): ?int { return $this->customerId; }
    public function getUserId(): int { return $this->userId; }
    public function getTotalAmount(): float { return $this->totalAmount; }
    public function getDiscount(): float { return $this->discount; }
    public function getStatus(): string { return $this->status; }
    public function getItems(): array { return $this->items; }
    public function getPayments(): array { return $this->payments; }
}