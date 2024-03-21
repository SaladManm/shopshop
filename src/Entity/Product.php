<?php

namespace MyApp\Entity;

class Product
{
    private ?int $id;
    private string $name;
    private string $description;
    private float $price;
    private Type $type;
    private float $rank;
    private int $stock;

    public function __construct($id, $name, $description, $price, $type, $stock)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
        $this->type = $type;
        $this->stock = $stock;
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(?float $price): void
    {
        $this->price = $price;
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function setType(Type $type): void
    {
        $this->type = $type;
    }

    public function getstock(): ?int
    {
        return $this->stock;
    }

    public function setstock(?int $id): void
    {
        $this->stock = $stock;
    }

    // public function getImage(): string
    // {
    //     return $this->image;
    // }

    // public function setImage(?string $image): void
    // {
    //     $this->image = $image;
    // }
} 
