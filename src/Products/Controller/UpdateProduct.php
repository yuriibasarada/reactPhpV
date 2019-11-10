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
use App\Products\Storage;
use Exception;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Response;

final class UpdateProduct
{


    private $storage;

    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    public function __invoke(ServerRequestInterface $request, string $id)
    {

        $input = new Input($request);
        $input->validate();


        return $this->storage->update((int)$id, $input->name(), $input->price())
            ->then(
                function() use ($id){
                    $response = [
                        'request' => Request::detailedProduct((int) $id)
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