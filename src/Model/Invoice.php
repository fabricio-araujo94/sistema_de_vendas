<?php

namespace App\Model;

class Invoice {
    private ?int $id = null;
    private int $saleId;
    private string $invoiceNumber;
    private string $accessKey;
    private ?string $issuedAt = null;

    public function __construct(int $saleId, string $invoiceNumber, string $accessKey) {
        $this->saleId = $saleId;
        $this->invoiceNumber = $invoiceNumber;
        $this->accessKey = $accessKey;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): self { $this->id = $id; return $this; }
    
    public function getSaleId(): int { return $this->saleId; }
    public function getInvoiceNumber(): string { return $this->invoiceNumber; }
    public function getAccessKey(): string { return $this->accessKey; }
    
    public function getIssuedAt(): ?string { return $this->issuedAt; }
    public function setIssuedAt(?string $issuedAt): self { $this->issuedAt = $issuedAt; return $this; }
}