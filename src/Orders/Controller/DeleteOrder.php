<?php
/**
 * Created by PhpStorm.
 * User: DEV
 * Date: 29.10.2019
 * Time: 14:27
 */

namespace App\Orders\Controller;

use App\Core\JsonResponse;
use App\Orders\Controller\Output\Request;
use App\Orders\OrderNotFound;
use App\Orders\Storage;
use Exception;
use Psr\Http\Message\ServerRequestInterface;

final class DeleteOrder
{

    private $storage;

    public function __construct(Storage $storage)
    {

        $this->storage = $storage;
    }

    public function __invoke(ServerRequestInterface $request, string $id)
    {
        return $this->storage->delete((int) $id)
            ->then(
                function() {
                    return JsonResponse::ok(['request' => Request::createOrder()]);
                }
            )
            ->otherwise(
                function (OrderNotFound $error) {
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