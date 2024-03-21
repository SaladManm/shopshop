<?php

declare(strict_types = 1);

namespace MyApp\Entity;

class Type{

    private ?int $id = null;
    private string $label;

    public function __construct(?int $id, string $label){
        $this->id = $id;
        $this->label = $label;
    }

    public function setId(?int $id):void{
        $this->id = $id;
    }
    public function getId():?int{
        return $this->id;
    }

    public function getLabel():string{
        return $this->label;
    }
    public function setLabel(string $label):void{
        $this->label = $label;
    }
}