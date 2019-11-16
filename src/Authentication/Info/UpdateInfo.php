<?php


namespace App\Authentication\Info;


use App\Authentication\Storage;
use App\Authentication\UserNotFound;
use App\Core\JsonResponse;
use Exception;
use Psr\Http\Message\ServerRequestInterface;
use React\MySQL\QueryResult;

class UpdateInfo
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
        $input = new Input($request);
        $input->validate();
        return $this->storage->updateInfo($uid, $input->name(), $input->bill())
            ->then(function (QueryResult $result) {
                return JsonResponse::ok('Successful');
            })->otherwise(function (UserNotFound $error) {
                return JsonResponse::notFound();
            })
            ->otherwise(function (Exception $exception) {
                return JsonResponse::internalServerError($exception->getMessage());
            });
    }
}