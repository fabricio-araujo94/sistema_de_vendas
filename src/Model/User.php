<?php

namespace App\Model;

class User {
    private ?int $id = null;
    private string $name;
    private string $email;
    private string $password;
    private string $role;
    private ?string $createdAt = null;

    public function __construct(
        string $name = "", 
        string $email = "", 
        string $password = "", 
        string $role = "seller"
        ) 
    {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;

    }

    public function getId(): ?int {
        return $this->id;
    }

    public function setId(?int $id): self {
        $this->id = $id;
        return $this;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): self {
        $this->name = $name;
        return $this;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function setEmail(string $email): self {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): string {
        return $this->password;
    }

    public function setPassword(string $password): self {
        $this->name = $password;
        return $this;
    }

    public function getRole(): string {
        return $this->role;
    }

    public function setRole(string $role): self {
        $this->role = $role;
        return $this;
    }

    public function getCreatedAt(): ?string {
        return $this->createdAt;
    }

    public function setCreatedAt(?string $createdAt): self {
        $this->createdAt = $createdAt;
        return $this;
    }
    
}