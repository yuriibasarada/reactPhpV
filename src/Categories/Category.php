<?php


namespace App\Categories;


class Category
{
    /**
     * @var int
     */
    public $id;
    /**
     * @var int
     */
    public $uid;
    /**
     * @var string
     */
    public $name;
    /**
     * @var float
     */
    public $limit;

    public function __construct(int $id, int $uid, string $name, float $limit)
    {
        $this->id = $id;
        $this->uid = $uid;
        $this->name = $name;
        $this->limit = $limit;
    }
}