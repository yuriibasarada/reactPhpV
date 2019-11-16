<?php


namespace App\Categories\Controller;


use App\Categories\Category;
use App\Categories\Controller\Output\Request;
use App\Categories\Storage;
use App\Core\JsonResponse;
use Exception;
use Psr\Http\Message\ServerRequestInterface;
use App\Categories\Controller\Output\Category as Output;

class GetAllCategories
{
    /**
     * @var Storage
     */
    private $storage;

    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    public function __invoke(ServerRequestInterface $request, int $uid)
    {

        return $this->storage->getAll($uid)
            ->then(function (array $categories) {
               $response = [
                   'categories' => $this->mapCategories(...$categories),
                   'cont' => count($categories)
               ];
               return JsonResponse::ok($response);
            },
                function (Exception $exception) {
                    return JsonResponse::internalServerError($exception->getMessage());
                }
            );
    }

    private function mapCategories(Category ...$category)
    {
        return array_map(function (Category $category) {
            return Output::fromEntity($category, Request::detailedCategory($category->id));
        }, $category
        );
    }
}