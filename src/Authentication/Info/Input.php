<?php


namespace App\Authentication\Info;


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

    public function validate(): void
    {
        $nameValidator = Validator::key('name',
            Validator::allOf(
                Validator::notBlank(),
                Validator::stringType()
            ))->setName('name');

        $billValidator = Validator::key('bill',
            Validator::allOf(
                Validator::notBlank(),
                Validator::numeric(),
                Validator::positive()
            ))->setName('bill');


        $validator = Validator::allOf($nameValidator, $billValidator);

        $validator->assert($this->request->getParsedBody());
    }

    public function name(): string
    {
        return $this->request->getParsedBody()['name'];
    }

    public function bill(): int
    {
        return $this->request->getParsedBody()['bill'];
    }

    public function userId(): int
    {
        return $this->request->getParsedBody()['user_id'];
    }
}