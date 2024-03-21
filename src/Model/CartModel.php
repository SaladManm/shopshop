<?php
declare(strict_types=1);

namespace MyApp\Model;

use MyApp\Entity\Cart;
use MyApp\Entity\Product;
use PDO;

class CartModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAllCarts(): array
    {
        $sql = 'SELECT Cart.id_cart, Cart.creationdate, Cart.status, Cart.id_user, 
                COUNT(contenir.id_product) as product_count, 
                SUM(contenir.quantity * contenir.unitprice) as total_price
                FROM Cart
                LEFT JOIN contenir ON Cart.id_cart = contenir.id_cart
                GROUP BY Cart.id_cart, Cart.creationdate, Cart.status, Cart.id_user
                ORDER BY Cart.creationdate DESC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function createCart($userId, $status = 'active')
    {
        $creationDate = date('Y-m-d');
        $sql = 'INSERT INTO Cart (creationdate, status, id_user) VALUES (:creationdate, :status, :id_user)';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':creationdate', $creationDate);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id_user', $userId);
        $stmt->execute();
        return $this->db->lastInsertId();
    }

    public function addItemToCart(Cart $cart, Product $product, int $quantity): bool
    {
        $unitPrice = $product->getPrice();
        $totalPrice = $unitPrice * $quantity;
    
        $sql = "INSERT INTO contenir (id_cart, id_product, quantity, unitprice) VALUES (:cartId, :productId, :quantity, :unitprice)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'cartId' => $cart->getId(),
            'productId' => $product->getId(),
            'quantity' => $quantity,
            'unitprice' => $unitPrice 
        ]);
    
        return true;
    }
    
    public function getCartById(int $cartId): ?Cart
    {
        $sql = "SELECT c.id_cart, c.creationdate, c.status, c.id_user, p.*,
                       cont.quantity, cont.unitprice
                FROM Cart c
                LEFT JOIN contenir cont ON c.id_cart = cont.id_cart
                LEFT JOIN Product p ON cont.id_product = p.id
                WHERE c.id_cart = :cartId";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':cartId', $cartId, PDO::PARAM_INT);
        $stmt->execute();
        
        $cartData = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$cartData) {
            return null;
        }
    
        $product = null;
        if (isset($cartData['id_product'], $cartData['name'], $cartData['description'], $cartData['price'])) {
            $product = new Product(
                $cartData['id_product'],
                $cartData['name'],
                $cartData['description'],
                $cartData['price']
            );
        }

        $cart = new Cart(
            $cartData['id_cart'],
            $cartData['creationdate'],
            $cartData['status'],
            $cartData['id_user'],
            $product,
            floatval($cartData['unitprice'])
        );
    
        return $cart;
    }
    
    
    


    public function deleteCartConfirm(int $cartId): bool
    {
        if (!is_int($cartId)) {
            throw new \InvalidArgumentException('L\'identifiant du panier doit être un entier.');
        }
    
        $sql = "DELETE FROM Cart WHERE id_cart = :cartId";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':cartId', $cartId, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    public function getOrdersByUserId(int $userId)
    {
        $sql = "SELECT o.id_order, o.orderdate, i.totalamount
                FROM `Order` o
                LEFT JOIN Invoice i ON o.id_order = i.id_order
                WHERE o.id_user = :userId";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
    
    public function updateCart(Cart $cart): bool
{
    $sql = "UPDATE Cart SET status = :status, id_user = :userId WHERE id_cart = :cartId";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':status', $cart->getStatus(), PDO::PARAM_STR);
    $stmt->bindValue(':userId', $cart->getUserId(), PDO::PARAM_INT);
    $stmt->bindValue(':cartId', $cart->getId(), PDO::PARAM_INT);

    return $stmt->execute();
}

public function addProductToCart($cartId, $productId, $quantity)
    {


        $db = DatabaseConnection::getInstance();


        $stmt = $db->prepare("INSERT INTO cart_product (cart_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->execute([$cartId, $productId, $quantity]);
    }
    public function getProductsInCart(int $cartId): array
    {
        $sql = "SELECT * FROM Product p INNER JOIN contenir c ON p.id = c.id_product WHERE c.id_cart = :cartId";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':cartId', $cartId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCartsByUserId(int $userId): array
    {

        $sql = "SELECT * FROM Cart WHERE id_user = :userId";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        $carts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $carts;
    }


}
