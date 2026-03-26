<?php

namespace App\Model;

class Product {
    private ?int $id = null;
    private string $name;
    private ?string $description = null;
    private float $costPrice;
    private float $sellingPrice;
    private int $currentStock;
    private int $minimumStock;
    private ?int $supplierId = null;
    private ?string $createdAt = null;

    public function __construct(
        string $name = '',
        float $costPrice = 0.0,
        float $sellingPrice = 0.0,
        int $currentStock = 0,
        int $minimumStock = 0
    ) {
        $this->name = $name;
        $this->costPrice = $costPrice;
        $this->sellingPrice = $sellingPrice;
        $this->currentStock = $currentStock;
        $this->minimumStock = $minimumStock;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getCostPrice(): float
    {
        return $this->costPrice;
    }

    public function setCostPrice(float $costPrice): self
    {
        $this->costPrice = $costPrice;
        return $this;
    }

    public function getSellingPrice(): float
    {
        return $this->sellingPrice;
    }

    public function setSellingPrice(float $sellingPrice): self
    {
        $this->sellingPrice = $sellingPrice;
        return $this;
    }

    public function getCurrentStock(): int
    {
        return $this->currentStock;
    }

    public function setCurrentStock(int $currentStock): self
    {
        $this->currentStock = $currentStock;
        return $this;
    }

    public function getMinimumStock(): int
    {
        return $this->minimumStock;
    }

    public function setMinimumStock(int $minimumStock): self
    {
        $this->minimumStock = $minimumStock;
        return $this;
    }

    public function getSupplierId(): ?int
    {
        return $this->supplierId;
    }

    public function setSupplierId(?int $supplierId): self
    {
        $this->supplierId = $supplierId;
        return $this;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?string $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}