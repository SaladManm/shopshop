<?php


declare(strict_types=1);


namespace MyApp\Entity;
use MyApp\Entity\Order;

class Order
{
    private ?int $id = null;
    private string $orderDate;
    private int $userId;
    private string $status;

    public function __construct(?int $id, string $orderDate, int $userId, string $status)
    {
        $this->id = $id;
        $this->orderDate = $orderDate;
        $this->userId = $userId;
        $this->status = $status;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getOrderDate(): string
    {
        return $this->orderDate;
    }

    public function setOrderDate(string $orderDate): void
    {
        $this->orderDate = $orderDate;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }
}
