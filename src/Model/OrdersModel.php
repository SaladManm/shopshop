<?php

declare(strict_types=1);

namespace MyApp\Model;

use MyApp\Entity\Order;
use PDO;

class OrdersModel {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getAllOrders(): array {
        $sql = "SELECT * FROM `Order`"; 
        $stmt = $this->db->query($sql);
        $orders = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $orders[] = new Order($row['id_order'], $row['orderdate'], $row['id_user'], $row['status']);
        }

        return $orders;
    }
}
