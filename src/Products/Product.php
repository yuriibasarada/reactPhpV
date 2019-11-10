<?php

namespace App\Products;

final class Product
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var float
     */
    public $price;
    /**
     * @var string|null
     */
    public $image;

    public function __construct(int $id, string $name, float $price, ?string $image)
    {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->image = $image;
    }
}