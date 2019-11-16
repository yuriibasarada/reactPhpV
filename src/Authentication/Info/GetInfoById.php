<?php

namespace App\Authentication\Info;

use App\Authentication\Storage;
use App\Core\JsonResponse;
use Exception;
use Psr\Http\Message\RequestInterface;

class GetInfoById
{
    /**
     * @var Storage
     */
    private $storage;

    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    public function __invoke(RequestInterface $request, int $id)
    {
        return $this->storage->getInfoById($id)
            ->then(
            function ($info) {
                return JsonResponse::ok($info);
            })
            ->otherwise(function(UserNotFound $error){
                return JsonResponse::notFound();
            })
            ->otherwise(function (Exception $error) {
                return JsonResponse::internalServerError($error->getMessage());
            });
    }
}