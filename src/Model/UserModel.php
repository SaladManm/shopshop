<?php

declare(strict_types=1);

namespace MyApp\Model;

use MyApp\Entity\User;
use PDO;

class UserModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function createUser(User $user): bool
    {
        $sql = "INSERT INTO User (email, lastName, firstName, password, address, postalCode, city, phone, roles) 
                VALUES (:email, :lastName, :firstName, :password, :address, :postalCode, :city, :phone, :roles)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':email', $user->getEmail());
        $stmt->bindValue(':lastName', $user->getLastName());
        $stmt->bindValue(':firstName', $user->getFirstName());
        $stmt->bindValue(':password', $user->getPassword());
        $stmt->bindValue(':address', $user->getAddress());
        $stmt->bindValue(':postalCode', $user->getPostalCode());
        $stmt->bindValue(':city', $user->getCity());
        $stmt->bindValue(':phone', $user->getPhone());
        $stmt->bindValue(':roles', json_encode($user->getRoles())); 
        return $stmt->execute();
    }
    

    public function getUserByEmail(string $email): ?User
    {
        $sql = "SELECT * FROM User WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$row) {
            return null;
        }
    
        // Vérifiez si la colonne "roles" n'est pas null avant d'appeler json_decode
        $roles = $row['roles'] !== null ? json_decode($row['roles'], true) : [];
    
        return new User(
            $row['id'],
            $row['email'],
            $row['lastName'],
            $row['firstName'],
            $row['password'],
            $row['address'],
            $row['postalCode'],
            $row['city'],
            $row['phone'],
            $roles
        );
    }    

    public function getUserById(int $id): ?User
    {
        $sql = "SELECT * FROM User WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return new User(
            $row['id'],
            $row['email'],
            $row['lastName'],
            $row['firstName'],
            $row['password'],
            $row['address'],
            $row['postalCode'],
            $row['city'],
            $row['phone'],
            json_decode($row['roles'], true) 
        );
    }

    public function updateUser(User $user): bool
    {
        $sql = "UPDATE User SET email = :email, lastName = :lastName, firstName = :firstName, password = :password, address = :address, postalCode = :postalCode, city = :city, phone = :phone, roles = :roles WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':email', $user->getEmail());
        $stmt->bindValue(':lastName', $user->getLastName());
        $stmt->bindValue(':firstName', $user->getFirstName());
        $stmt->bindValue(':password', $user->getPassword());
        $stmt->bindValue(':address', $user->getAddress());
        $stmt->bindValue(':postalCode', $user->getPostalCode());
        $stmt->bindValue(':city', $user->getCity());
        $stmt->bindValue(':phone', $user->getPhone());
        $stmt->bindValue(':roles', json_encode($user->getRoles()));
        $stmt->bindValue(':id', $user->getId());
        return $stmt->execute();
    }

    public function getAllUsers(): array
    {
        $sql = "SELECT * FROM User";
        $stmt = $this->db->query($sql);
        $users = [];
    
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Vérifiez si la colonne "roles" n'est pas null avant d'appeler json_decode
            $roles = $row['roles'] !== null ? json_decode($row['roles'], true) : [];
    
            $users[] = new User(
                $row['id'],
                $row['email'],
                $row['lastName'],
                $row['firstName'],
                $row['password'],
                $row['address'],
                $row['postalCode'],
                $row['city'],
                $row['phone'],
                $roles
            );
        }
    
        return $users;
    }
    
    public function deleteUser(int $id): bool
    {
        // Supprime les enregistrements associés dans la table Order
        $this->deleteOrdersByUserId($id);
    
        // Supprime l'utilisateur
        $sql = "DELETE FROM User WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id);
        return $stmt->execute();
    }
    
    private function deleteOrdersByUserId(int $userId)
    {
        $sql = "DELETE FROM `Order` WHERE id_user = :id_user";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_user', $userId);
        $stmt->execute();
    }
    
    public function getOneUser(int $id): ?User
    {
        $query = "SELECT * FROM User WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$row) {
            return null;
        }
    
        // Vérifiez si la colonne "roles" n'est pas null avant d'appeler json_decode
        $roles = $row['roles'] !== null ? json_decode($row['roles'], true) : [];
    
        return new User(
            $row['id'],
            $row['email'],
            $row['lastName'],
            $row['firstName'],
            $row['password'],
            $row['address'],
            $row['postalCode'],
            $row['city'],
            $row['phone'],
            $roles
        );
    }    
}
