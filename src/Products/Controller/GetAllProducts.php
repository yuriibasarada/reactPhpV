<?php
/**
 * Created by PhpStorm.
 * User: DEV
 * Date: 29.10.2019
 * Time: 14:02
 */

namespace App\Products\Controller;


use App\Core\JsonResponse;
use App\Products\Controller\Output\Product as Output;
use App\Products\Controller\Output\Request;
use App\Products\Product;
use Exception;
use Psr\Http\Message\ServerRequestInterface;
use App\Products\Storage;

final class GetAllProducts
{
    private $storage;

    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        return $this->storage->getAll()
            ->then(function (array $products) {
                $response = [
                    'products' => $this->mapProducts(...$products),
                    'count' => count($products),
                ];
                return JsonResponse::ok($response);
            },
                function (Exception $exception) {
                    return JsonResponse::internalServerError($exception->getMessage());
                }

            );
    }

    private function mapProducts(Product ...$products): array
    {
        return array_map(function (Product $product) {
            return Output::fromEntity($product, Request::detailedProduct($product->id));
        }, $products
        );
    }
}