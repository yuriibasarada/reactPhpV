<?php


namespace App\Records\Controller;


use App\Core\JsonResponse;
use App\Records\Record;
use App\Records\RecordNotFound;
use App\Records\Storage;
use Exception;
use Psr\Http\Message\ServerRequestInterface;
use App\Records\Controller\Output\Record as Output;
use App\Records\Controller\Output\Request;

class GetRecordById
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
       return $this->storage->getById($id)->then(function (Record $record) {
            $response = [
                'record' => Output::fromEntity(
                    $record, Request::updateRecord($record->id)
                )];
            return JsonResponse::ok($response);
        })
       ->otherwise(function (RecordNotFound $exception) {
            return JsonResponse::notFound();
       })
       ->otherwise(function (Exception $exception) {
            return JsonResponse::internalServerError($exception->getMessage());
       });
    }
}