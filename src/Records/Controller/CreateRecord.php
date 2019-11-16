<?php


namespace App\Records\Controller;

use App\Categories\Category;
use App\Categories\CategoryNotFound;
use App\Categories\Storage as Categories;
use App\Core\JsonResponse;
use App\Records\Controller\Output\Request;
use App\Records\Record;
use App\Records\Storage as Records;
use App\Records\TypeNotFound;
use Exception;
use Psr\Http\Message\ServerRequestInterface;
use App\Records\Controller\Output\Record as Output;

class CreateRecord
{
    /**
     * @var Categories
     */
    private $category;
    /**
     * @var Records
     */
    private $record;

    public function __construct(Categories $category, Records $record)
    {
        $this->category = $category;
        $this->record = $record;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $input = new Input($request);
        $input->validate();

        return $this->category->getById($input->categoryId())
            ->then(
                function (Category $category) use($input){
                    return $this->record->create((int)$input->categoryId(), (int)$input->amount(), (string)$input->description(), (int) $input->typeId(), (string) $input->date())
                        ->then(function (Record $record) {
                            $response = [
                                'record' => Output::fromEntity(
                                    $record, Request::detailedRecord($record->id)
                                    )
                            ];
                            return JsonResponse::ok($response);
                        })
                        ->otherwise(function (TypeNotFound $error) {
                            return JsonResponse::notFound();
                        });
                }
            )
            ->otherwise(function (CategoryNotFound $error) {
                return JsonResponse::notFound();
            })
            ->otherwise(function (Exception $exception) {
                return JsonResponse::internalServerError($exception->getMessage());
            });
    }
}