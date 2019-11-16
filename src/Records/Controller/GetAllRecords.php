<?php


namespace App\Records\Controller;


use App\Core\JsonResponse;
use App\Records\Record;
use App\Records\Storage;
use Exception;
use Psr\Http\Message\ServerRequestInterface;
use App\Records\Controller\Output\Record as Output;
use App\Records\Controller\Output\Request;

class GetAllRecords
{

    /**
     * @var Storage
     */
    private $storage;

    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        return $this->storage->getAll()->then(function (array $records) {
             $response = [
                'records' => $this->mapRecords(...$records),
                 'count' => count($records)
            ];
             return JsonResponse::ok($response);
        })
            ->otherwise(function (Exception $exception) {
                return JsonResponse::internalServerError($exception->getMessage());
            });
    }

    private function mapRecords(Record ...$record): array
    {
        return array_map(function (Record $record) {
            return Output::fromEntity($record, Request::detailedRecord($record->id));
        }, $record );
    }
}