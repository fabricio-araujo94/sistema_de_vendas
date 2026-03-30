<?php

namespace App\Model;

class Customer {
    private ?int $id = null;
    private string $name;
    private string $document;
    private ?string $email = null;
    private ?string $phone = null;
    private ?string $address = null;
    private ?string $createdAt = null;

    public function __construct(string $name = '', string $document = '') {
        $this->name = $name;
        $this->document = $document;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): self { $this->id = $id; $this->createdAt = date('Y-m-d H:i:s'); return $this; }

    public function getName(): string { return $this->name; }
    public function setName(string $name): self { $this->name = $name; return $this; }

    public function getDocument(): string { return $this->document; }
    public function setDocument(string $document): self { $this->document = $document; return $this; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(?string $email): self { $this->email = $email; return $this; }

    public function getPhone(): ?string { return $this->phone; }
    public function setPhone(?string $phone): self { $this->phone = $phone; return $this; }

    public function getAddress(): ?string { return $this->address; }
    public function setAddress(?string $address): self { $this->address = $address; return $this; }

    public function getCreatedAt(): ?string { return $this->createdAt; }
    public function setCreatedAt(?string $createdAt): self { $this->createdAt = $createdAt; return $this; }
}