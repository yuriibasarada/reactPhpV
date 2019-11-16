<?php


namespace App\Records;


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
     * @var int
     */
    public $id;
    /**
     * @var string
     */
    public $date;

    public function __construct(int $id, int $categoryId, int $amount, string $description, int $typeId, string $date
    )
    {
        $this->categoryId = $categoryId;
        $this->amount = $amount;
        $this->description = $description;
        $this->typeId = $typeId;
        $this->id = $id;
        $this->date = $date;
    }


}