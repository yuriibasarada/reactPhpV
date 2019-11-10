<?php



namespace App\Authentication;


use App\Core\JsonResponse;
use Exception;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Response;

final class SignInController
{


    /**
     * @var Authenticator
     */
    private $authenticator;

    public function __construct(Authenticator $authenticator)
    {
        $this->authenticator = $authenticator;
    }

    public function __invoke(ServerRequestInterface $request)
    {

        $input = new Input($request);
        $input->validate();
        return $this->authenticator->authenticate($input->email(), $input->password())
            ->then(
                function ($jwt) {
                    return JsonResponse::ok(['token' => $jwt]);
                }
            )
            ->otherwise(
                function (BadCredentials $exception) {
                    return JsonResponse::unauthorised('Bad Credentials');
                }
            )
            ->otherwise(
                function (UserNotFound $exception) {
                    return JsonResponse::unauthorised('User not found');
                }
            )
            ->otherwise(
                function (Exception $exception) {
                    return JsonResponse::internalServerError($exception->getMessage());
                }
            );
    }
}