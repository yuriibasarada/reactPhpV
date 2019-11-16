<?php

namespace App\Categories\Controller\Output;

use App\Categories\Category as CategoryEntity;

final class Category
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
    /**
     * @var Request
     */
    public $request;

    private function __construct(int $id, int $uid, string $name, float $limit, Request $request)
    {
        $this->id = $id;
        $this->uid = $uid;
        $this->name = $name;
        $this->limit = $limit;
        $this->request = $request;
    }


    public static function fromEntity(CategoryEntity $entity, Request $request): self
    {
        return new self($entity->id, $entity->uid, $entity->name, $entity->limit, $request);
    }
}