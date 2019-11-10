<?php

namespace App\StaticFiles;

use App\Core\JsonResponse;
use Exception;
use Narrowspark\MimeType\MimeTypeFileExtensionGuesser;
use Psr\Http\Message\ServerRequestInterface;
use React\Filesystem\Node\FileInterface;
use React\Http\Response;
use React\Promise\PromiseInterface;

final class Controller
{

    /**
     * @var Webroot
     */
    private $webroot;

    public function __construct(Webroot $webroot)
    {

        $this->webroot = $webroot;
    }

    public function __invoke(ServerRequestInterface $request): PromiseInterface
    {
        return $this->webroot->file($request->getUri()->getPath())
            ->then(
                function (File $file) {
                    return new Response(
                        200,
                        ['Content-type' => $file->mimeType],
                        $file->contents
                    );
                }
            )
            ->otherwise(
                function (FileNotFound $exceptions) {
                    return JsonResponse::notFound();
                }
            )
            ->otherwise(
                function (Exception $exception) {
                    return JsonResponse::internalServerError($exception->getMessage());
                }
            );
    }
}