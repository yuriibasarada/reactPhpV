<?php


namespace App\Records\Controller;


use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator;

class Input
{
    /**
     * @var ServerRequestInterface
     */
    private $request;

    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
    }

    public function validate()
    {
        $categoryIdValidator = Validator::key(
            'category_id',
            Validator::allOf(
                Validator::notBlank(),
                Validator::numeric(),
                Validator::positive()
            )
        )->setName('category_id');
        $amountValidator = Validator::key(
            'amount',
            Validator::allOf(
                Validator::notBlank(),
                Validator::numeric(),
                Validator::positive()
            )
        )->setName('amount');

        $descriptionValidator = Validator::key(
            'description',
            Validator::allOf(
                Validator::notBlank(),
                Validator::stringType()
            )
        )->setName('description');
        $typeIdValidator = Validator::key(
            'type_id',
            Validator::allOf(
                Validator::notBlank(),
                Validator::numeric(),
                Validator::positive()
            )
        )->setName('type_id');
        $dateValidator = Validator::key(
            'date',
            Validator::allOf(
                Validator::notBlank(),
                Validator::stringType()
            )
        )->setName('date');


        Validator::allOf($descriptionValidator, $categoryIdValidator, $amountValidator, $typeIdValidator, $dateValidator)
            ->assert($this->request->getParsedBody());
    }

    public function categoryId(): string
    {
        return  $this->request->getParsedBody()['category_id'];
    }

    public function amount(): int
    {
        return $this->request->getParsedBody()['amount'];
    }

    public function description(): string
    {
        return $this->request->getParsedBody()['description'];
    }

    public function typeId(): int
    {
        return $this->request->getParsedBody()['type_id'];
    }

    public function date(): string
    {
        return  $this->request->getParsedBody()['date'];
    }
}