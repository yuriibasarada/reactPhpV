<?php
/**
 * Created by PhpStorm.
 * User: DEV
 * Date: 29.10.2019
 * Time: 17:13
 */

namespace App\Core;


use React\Http\Response;

final class JsonResponse extends Response
{

    public function __construct(int $statusCode, $data = null)
    {
        $data = $data ? json_encode($data) : null;

        parent::__construct(
            $statusCode,
            ['Content-type' => 'application/json'],
            $data
        );
    }

    public static function ok($data) : self
    {
        return new self(200, $data);
    }

    public static function internalServerError(string $reason): self
    {
        return new self(500, ['message' => $reason]);
    }

public static function notFound(): self
    {
    return new self(404);
}

public static function noContent(): self
{
        return new self(204);
    }

    public static function badRequest(string ...$error): self
    {
        return new self(400, ['errors' => $error]);
    }

    public static function created($data): self
    {
        return new self(201, $data);
    }

    public static function unauthorised(string $error = ''): self
    {
        return new self(401, ['error' => $error]);
    }
}