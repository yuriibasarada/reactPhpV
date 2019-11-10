<?php

namespace App\Orders\Controller;


use App\Orders\Controller\Output\Request;
use App\Orders\Order as Order;
use App\Orders\Storage;
use Exception;
use Psr\Http\Message\ServerRequestInterface;
use App\Core\JsonResponse;
use App\Orders\Controller\Output\Order as Output;

final class GetAllOrders
{
    private $storage;

    public function __construct(Storage $storage)
    {

        $this->storage = $storage;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        return $this->storage->getAll()
            ->then(
                function($orders) {
                    $response = [
                        'orders' => $this->mapOrders(...$orders),
                        'count' => count($orders)
                    ];
                    return JsonResponse::ok($response);
                },
                function (Exception $exception) {
                    return JsonResponse::internalServerError($exception->getMessage());
                }
            );
    }

    private function mapOrders(Order ...$orders): array
    {
        return array_map(function (Order $order) {
            return Output::fromEntity($order, Request::detailedOrder($order->id));
        }, $orders);
    }
}