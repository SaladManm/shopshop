<?php

declare(strict_types = 1);

namespace MyApp\Model;

use MyApp\Entity\Type;
use PDO;

ini_set('display_errors', 1);
error_reporting(E_ALL);


class TypeModel{
    private PDO $db;

    public function __construct(PDO $db){
        $this->db = $db;
    }

    public function getAllTypes():array{
        $sql = "SELECT * FROM Type";
        $stmt = $this->db->query($sql);
        $types=[];

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $types[]= new Type($row['id'], $row['label']);
        }

        return $types;
    }

    public function getOneType(int $id):?Type{
        $sql = "SELECT * from Type where id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id", $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$row){
        return null;
        }
        return new Type($row['id'], $row['label']);
       
    
}

public function updateType(Type $type): bool {
    $sql = "UPDATE Type SET label = :label WHERE id = :id";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':label', $type->getLabel(), PDO::PARAM_STR);
    $stmt->bindValue(':id', $type->getId(), PDO::PARAM_INT);
    return $stmt->execute();



}
public function createType(Type $type): bool {
    $sql = "INSERT INTO Type (label) VALUES (:label)";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':label', $type->getLabel(), PDO::PARAM_STR);
    return $stmt->execute();
    }
    
    public function deleteType(int $id): bool {
        $sql = "DELETE FROM Type WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
        }

        public function getTypeById(int $id): ?Type
    {

        $sql = "SELECT * FROM Type WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return new Type($row['id'], $row['label']);
    } 

    public function getAllTypeNames(): array {
        $sql = "SELECT label FROM Type";
        $stmt = $this->db->query($sql);
        $typeNames = [];
    
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $typeNames[] = $row['label'];
        }
    
        return $typeNames;
    }
    
}