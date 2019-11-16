<?php


namespace App\Records\Controller\Output;

use App\Records\Record as RecordEntity;
class Record
{
    /**
     * @var int
     */
    public $categoryId;
    /**
     * @var int
     */
    public $amount;
    /**
     * @var string
     */
    public $description;
    /**
     * @var int
     */
    public $typeId;
    /**
     * @var Request
     */
    public $request;
    /**
     * @var int
     */
    public $id;
    /**
     * @var string
     */
    public $date;

    private function __construct(int $id, int $categoryId, int $amount, string $description, int $typeId, string $date, Request $request)
    {
        $this->id = $id;
        $this->categoryId = $categoryId;
        $this->amount = $amount;
        $this->description = $description;
        $this->typeId = $typeId;
        $this->request = $request;
        $this->date = $date;
    }


    public static function fromEntity(RecordEntity $entity, Request $request): self
    {
        return new self($entity->id, $entity->categoryId, $entity->amount, $entity->description, $entity->typeId, $entity->date, $request);
    }


}