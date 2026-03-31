<?php

namespace App\Model;

class Supplier
{
    private ?int $id = null;
    private string $companyName;
    private string $document;
    private ?string $contact = null;
    private ?string $address = null;
    private ?string $createdAt = null;

    public function __construct(string $companyName = '', string $document = '')
    {
        $this->companyName = $companyName;
        $this->document = $document;
    }


    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): self { $this->id = $id; return $this; }

    public function getCompanyName(): string { return $this->companyName; }
    public function setCompanyName(string $companyName): self { $this->companyName = $companyName; return $this; }

    public function getDocument(): string { return $this->document; }
    public function setDocument(string $document): self { $this->document = $document; return $this; }

    public function getContact(): ?string { return $this->contact; }
    public function setContact(?string $contact): self { $this->contact = $contact; return $this; }

    public function getAddress(): ?string { return $this->address; }
    public function setAddress(?string $address): self { $this->address = $address; return $this; }

    public function getCreatedAt(): ?string { return $this->createdAt; }
    public function setCreatedAt(?string $createdAt): self { $this->createdAt = $createdAt; return $this; }
}