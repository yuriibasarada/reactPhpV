<?php


namespace App\Authentication;


use Firebase\JWT\JWT;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator;

final class Input
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
        $emailValidator = Validator::key('email',
            Validator::allOf(
                Validator::email(),
                Validator::notBlank(),
                Validator::stringType()
            ))->setName('email');

        $passwordValidator = Validator::key('password',
            Validator::allOf(
                Validator::notBlank(),
                Validator::stringType()
            ))->setName('password');


        $validator = Validator::allOf($emailValidator, $passwordValidator);

        $validator->assert($this->request->getParsedBody());
    }

    public function hashedPassword(): string
    {
        return password_hash($this->password(),PASSWORD_BCRYPT);
    }

    public function email(): string
    {
        return  $this->request->getParsedBody()['email'];
    }

    public function password(): string
    {
        return $this->request->getParsedBody()['password'];
    }


    public function name(): ?string
    {
        return $this->request->getParsedBody()['name'];
    }

    public function token()
    {
        return $this->request->getParsedBody()['token'];
    }

}