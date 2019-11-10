<?php

namespace App\Orders\Controller;

use App\Orders\Controller\Output\Request;
use App\Orders\Order;
use App\Orders\OrderNotFound;
use App\Orders\Storage;
use Exception;
use Psr\Http\Message\ServerRequestInterface;
use App\Core\JsonResponse;
use App\Orders\Controller\Output\Order as Output;

final class GetOrderById
{
    /**
     * @var Storage
     */
    private $storage;

    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    public function __invoke(ServerRequestInterface $request, string $id)
    {
        return $this->storage->getById((int)$id)
            ->then(
                function (Order $order) {
                    $response = [
                        'order' => Output::fromEntity(
                            $order, Request::deleteOrder($order->id)
                        ),
                        'request' => Request::listOrders()
                    ];
                    return JsonResponse::ok($response);
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