<?php


namespace App\Authentication;


use App\Core\JsonResponse;
use Exception;
use Psr\Http\Message\ServerRequestInterface;

class GetUserById
{
    /**
     * @var Storage
     */
    private $storage;

    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    public function __invoke(ServerRequestInterface $request, int $id)
    {
        return $this->storage->getById($id)
            ->then(
                function (array  $user) {
                    return JsonResponse::ok($user);
                }
            )
            ->otherwise(
                function(UserNotFound $error) {
                    return JsonResponse::notFound();
                }
            )
            ->otherwise(
                function (Exception $exception) {
                    return JsonResponse::internalServerError($exception->getMessage());
                }
            );
    }
}