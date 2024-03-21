<?php

declare(strict_types=1);

namespace MyApp\Model;

use MyApp\Entity\Product;
use MyApp\Entity\Type;
use PDO;

class ProductModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAllProduct(): array
    {
        $sql = "SELECT Product.id as id, name, description, price, Type.id as typeId, label, stock
                FROM Product INNER JOIN Type ON Product.id_Type = Type.id;";
    
        $stmt = $this->db->query($sql);
        $products = [];
    
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    
            $currentType = new Type($row['typeId'], $row['label']);
            $product = new Product($row['id'], $row['name'], $row['description'], $row['price'], $currentType, $row['stock']);
            $product->setId($row['id']);
            $products[] = $product;
        }
    
        return $products;
    }
    
    

    public function addProduct(Product $product): bool
    {
        $sql = "INSERT INTO Product (name, description, price, id_Type, stock) VALUES (:name, :description, :price, :typeId, :stock)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':name', $product->getName(), PDO::PARAM_STR);
        $stmt->bindValue(':description', $product->getDescription(), PDO::PARAM_STR);
        $stmt->bindValue(':price', $product->getPrice(), PDO::PARAM_STR);
        $stmt->bindValue(':typeId', $product->getType()->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':stock', $product->getStock(), PDO::PARAM_STR);

        return $stmt->execute();
    }


    public function deleteProductsByTypeId(int $typeId): bool
    {
        $sql = "DELETE FROM Product WHERE id_Type = :typeId";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':typeId', $typeId, PDO::PARAM_INT);
        return $stmt->execute();
    }
    

    public function getOneProduct(int $id): ?Product
    {
        $sql = "SELECT * FROM Product WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id", $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }
        $type = new TypeModel($this->db);
        $type = $type->getOneType($row['id_Type']);
        return new Product($row['id'], $row['name'], $row['description'], $row['price'], $type, $row['stock']);
    }

    public function updateProduct(Product $product): bool
    {
        $sql = "UPDATE Product SET name = :name, description = :description, price = :price WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':name', $product->getName(), PDO::PARAM_STR);
        $stmt->bindValue(':description', $product->getDescription(), PDO::PARAM_STR);
        $stmt->bindValue(':price', $product->getPrice(), PDO::PARAM_STR);
        $stmt->bindValue(':id', $product->getId(), PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function getAllProductByType(Type $type): array
    {
        $sql = "SELECT p.id as idProduit, p.name, p.description, p.price, t.id as idType, t.label FROM Product p INNER JOIN Type t ON p.id_Type = t.id WHERE p.id_Type = :typeId ORDER BY p.name";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':typeId', $type->getId(), PDO::PARAM_INT);
        $stmt->execute();
        $products = [];
    
        while ($row = $stmt->fetch()) {
            $type = new Type($row['idType'], $row['label']);
            $products[] = new Product($row['idProduit'], $row['name'], $row['description'], $row['price'], $type, 0.0); // Ajout d'un rang par défaut
        }
    
        return $products;
    }        
    

    public function getProductImages(int $productId): array
    {
        $sql = "SELECT image_path FROM Product_Image WHERE product_id = :productId";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':productId', $productId, PDO::PARAM_INT);
        $stmt->execute();
        $images = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return $images;
    }

    public function getProductsByType($typeId)
    {
        $sql = "SELECT * FROM Product WHERE type_id = :typeId";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':typeId', $typeId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
