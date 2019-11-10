<?php
/**
 * Created by PhpStorm.
 * User: DEV
 * Date: 29.10.2019
 * Time: 14:25
 */

namespace App\Products\Controller;
use App\Core\JsonResponse;
use App\Products\Controller\Output\Request;
use App\Products\ProductNotFound;
use App\Products\Storage;
use Exception;
use Psr\Http\Message\ServerRequestInterface;
use App\Products\Product;
use App\Products\Controller\Output\Product as Output;

final class GetProductById
{
    private $storage;

    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    public function __invoke(ServerRequestInterface $request, string $id)
    {
        return $this->storage
            ->getById((int)$id)
            ->then(
                function (Product $product) {
                    $response = [
                      'product' => Output::fromEntity(
                          $product, Request::updateProduct($product->id)
                      ),
                        'request' => Request::listProducts(),
                    ];
                    return JsonResponse::ok($response);
                }
            )
            ->otherwise(function(ProductNotFound $error){
                return JsonResponse::notFound();
            })
            ->otherwise(function (Exception $error){
                return JsonResponse::internalServerError($error->getMessage());
            });
    }
}