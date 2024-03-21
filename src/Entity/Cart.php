<?php

namespace MyApp\Entity;

class Cart
{
    private ?int $id;
    private string $creationDate;
    private string $status;
    private int $userId;
    private ?Product $product; // Changer le type de la propriété product pour accepter null
    private float $unitPrice;

    public function __construct(?int $id, string $creationDate, string $status, int $userId, ?Product $product, float $unitPrice)
    {
        $this->id = $id;
        $this->creationDate = $creationDate;
        $this->status = $status;
        $this->userId = $userId;
        $this->product = $product;
        $this->unitPrice = $unitPrice;
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getCreationDate(): string
    {
        return $this->creationDate;
    }

    public function setCreationDate(string $creationDate): void
    {
        $this->creationDate = $creationDate;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function getProduct(): Type 
    {
        return $this->product; 
    }
    public function setProduct(Product $product): void 
    {
        $this->product = $product; 
    }
}
