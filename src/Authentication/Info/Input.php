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

        $localeValidator = Validator::key('locale',
            Validator::allOf(
                Validator::notBlank(),
                Validator::stringType()
            ))->setName('locale');

        $validator = Validator::allOf($nameValidator, $billValidator, $localeValidator);

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

    public function locale(): string
    {
        return  $this->request->getParsedBody()['locale'];
    }

    public function userId(): int
    {
        return $this->request->getParsedBody()['user_id'];
    }
}