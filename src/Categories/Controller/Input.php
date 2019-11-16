<?php


namespace App\Categories\Controller;


use Psr\Http\Message\RequestInterface;
use Respect\Validation\Validator;

final class Input
{
    /**
     * @var RequestInterface
     */
    private $request;

    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }

    public function validate(): void
    {
        $nameValidator = Validator::key(
            'name',
            Validator::allOf(
                Validator::notBlank(),
                Validator::stringType()
            )
        )->setName('name');

        $limitValidator = Validator::key(
            'limit',
            Validator::allOf(
                Validator::notBlank(),
                Validator::numeric(),
                Validator::positive()
            )
        )->setName('limit');

        $userValidator = Validator::key(
            'user_id',
            Validator::allOf(
                Validator::notBlank(),
                Validator::numeric(),
                Validator::positive()
            )
        )->setName('user_id');
        Validator::allOf($nameValidator, $limitValidator, $userValidator)->assert($this->request->getParsedBody());
    }

    public function name(): string
    {
        return $this->request->getParsedBody()['name'];
    }

    public function limit(): float
    {
        return (float)$this->request->getParsedBody()['limit'];
    }

    public function uid(): int
    {
        return (int)$this->request->getParsedBody()['user_id'];
    }
}