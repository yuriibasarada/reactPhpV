<?php


namespace App\Categories\Controller;


use App\Authentication\Storage as Users;
use App\Authentication\UserNotFound;
use App\Categories\Category;
use App\Categories\Controller\Output\Request;
use App\Categories\Storage as Categories;
use App\Core\JsonResponse;
use Exception;
use Psr\Http\Message\RequestInterface;
use App\Categories\Controller\Output\Category as Output;

final class CreateCategory
{


    /**
     * @var Categories
     */
    private $categories;
    /**
     * @var Users
     */
    private $users;

    public function __construct(Categories $categories, Users $users)
    {
        $this->categories = $categories;
        $this->users = $users;
    }

    public function __invoke(RequestInterface $request)
    {
        $input = new Input($request);
        $input->validate();

        return $this->users->getById($input->uid())
            ->then(
                function (array $user) use ($input){
                    return $this->categories
                        ->create($input->uid(), $input->name(), $input->limit());
                }
            )
            ->then(
                function (Category $category) {
                    $response = [
                      'category' =>  Output::fromEntity(
                          $category, Request::detailedCategory($category->id)
                      )
                    ];
                    return JsonResponse::ok($response);
                }
            )
            ->otherwise(
                function (UserNotFound $exception) {
                    return JsonResponse::notFound();
                }
            )
            ->otherwise(
                function (Exception $exception) {
                    return JsonResponse::internalServerError($exception->getMessage());
                }
            );
    }

    private function mapCategory()
    {

    }
}