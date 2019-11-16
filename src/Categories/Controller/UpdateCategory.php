<?php


namespace App\Categories\Controller;


use App\Categories\CategoryNotFound;
use App\Categories\Controller\Output\Request;
use App\Categories\Storage;
use App\Core\JsonResponse;
use Exception;
use Psr\Http\Message\ServerRequestInterface;

class UpdateCategory
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
        $input = new Input($request);
        $input->validate();

        return $this->storage->update($id, $input->name(), $input->limit())
            ->then(function () use ($id){
                $response = [
                    'request' => Request::detailedCategory((int) $id)
                ];
                return JsonResponse::ok($response);
            })
            ->otherwise(function (CategoryNotFound $error) {
                return JsonResponse::notFound();
            })
            ->otherwise(function (Exception $exception) {
                return JsonResponse::internalServerError($exception->getMessage());
            });
    }
}