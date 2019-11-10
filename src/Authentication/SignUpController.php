<?php

namespace App\Authentication;

use App\Core\JsonResponse;
use Exception;
use Psr\Http\Message\ServerRequestInterface;

final class SignUpController
{

    /**
     * @var Storage
     */
    private $storage;

    public function __construct(Storage $storage)
    {

        $this->storage = $storage;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $input = new Input($request);
        $input->validate();

        return $this->storage->create($input->email(), $input->hashedPassword(), $input->name())
            ->then(
                function () {
                    return JsonResponse::created([]);
                }
            )
            ->otherwise(
                function (UserAlreadyExists $exception) {
                    return JsonResponse::badRequest('Email is already taken.');
                }
            )
            ->otherwise(
            function (Exception $exception)
                {
                    return JsonResponse::internalServerError($exception->getMessage());
                }
            );
    }
}