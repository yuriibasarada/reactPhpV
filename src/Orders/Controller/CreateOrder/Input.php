<?php

namespace App\Orders\Controller\CreateOrder;

use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator;

class Input
{

    private $request;

    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
    }

    public function validate(): void
    {
        $productIdValidator = Validator::key(
            'productId',
            Validator::allOf(
                Validator::notBlank(),
                Validator::numeric(),
                Validator::positive()
            )
        )->setName('productId');

        $quantityValidator = Validator::key(
            'quantity',
            Validator::allOf(
                Validator::notBlank(),
                Validator::numeric(),
                Validator::positive()
            )
        )->setName('quantity');

        $validator = Validator::allOf($productIdValidator, $quantityValidator);
        $validator->assert($this->request->getParsedBody());
    }

    public function productId(): int
    {
        return (int)$this->request->getParsedBody()['productId'];
    }

    public function quantity(): int
    {
        return (int)$this->request->getParsedBody()['quantity'];
    }
}