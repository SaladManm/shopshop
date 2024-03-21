<?php

namespace MyApp\Entity;

    class User
    {
    private ?int $id;
    private string $email;
    private string $lastName;
    private string $firstName;
    private string $password;
    private string $address;
    private string $postalCode;
    private string $city;
    private string $phone;
    private array $roles; 

    public function __construct(
        $id,
        $email,
        $lastName,
        $firstName,
        $password,
        $address,
        $postalCode,
        $city,
        $phone,
        array $roles // Assurez-vous que $roles est de type array
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->lastName = $lastName;
        $this->firstName = $firstName;
        $this->password = $password;
        $this->address = $address;
        $this->postalCode = $postalCode;
        $this->city = $city;
        $this->phone = $phone;
        $this->roles = $roles; // Assurez-vous que $roles est correctement défini dans la classe User
    }
    

    // Getters et setters pour chaque propriété

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): void
    {
        $this->postalCode = $postalCode;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }

}
