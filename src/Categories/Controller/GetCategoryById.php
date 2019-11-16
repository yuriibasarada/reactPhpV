<?php


namespace App\Categories\Controller;


use App\Categories\Category;
use App\Categories\CategoryNotFound;
use App\Categories\Controller\Output\Request;
use App\Categories\Storage;
use App\Core\JsonResponse;
use Exception;
use Psr\Http\Message\ServerRequestInterface;
use App\Categories\Controller\Output\Category as Output;

final class GetCategoryById
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
        return $this->storage->getById($id)
            ->then(
                function (Category $category) {
                    $response = [
                        'category' =>  Output::fromEntity(
                            $category, Request::updateCategory($category->id)
                        ),
                        'request' => Request::listCategories(),
                    ];
                    return JsonResponse::ok($response);
                }
            )
            ->otherwise(
                function (CategoryNotFound $error) {
                    return JsonResponse::notFound();
                }
            )
            ->otherwise(function (Exception $error){
                return JsonResponse::internalServerError($error->getMessage());
            });
    }
}