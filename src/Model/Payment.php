<?php

namespace App\Model;

class Payment
{
    private string $paymentMethod;
    private float $amount;
    private int $installments;

    public function __construct(string $paymentMethod, float $amount, int $installments = 1)
    {
        $this->paymentMethod = $paymentMethod;
        $this->amount = $amount;
        $this->installments = $installments;
    }

    public function getPaymentMethod(): string { return $this->paymentMethod; }
    public function getAmount(): float { return $this->amount; }
    public function getInstallments(): int { return $this->installments; }
}