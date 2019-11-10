<?php
/**
 * Created by PhpStorm.
 * User: DEV
 * Date: 29.10.2019
 * Time: 14:27
 */

namespace App\Products\Controller;

use App\Core\JsonResponse;
use App\Products\Controller\Output\Request;
use App\Products\ProductNotFound;
use Exception;
use Psr\Http\Message\ServerRequestInterface;
use App\Products\Storage;

final class DeleteProduct
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
                    $response = [
                      'request' => Request::createProduct(),
                    ];
                    return JsonResponse::ok($response);
                }
            )
            ->otherwise(function (ProductNotFound $error){
                 return JsonResponse::notFound();
            })
            ->otherwise(function (Exception $error){
                return JsonResponse::internalServerError($error->getMessage());
            });
    }
}