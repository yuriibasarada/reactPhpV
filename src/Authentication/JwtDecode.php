<?php

namespace App\Authentication;

use Firebase\JWT\JWT;

class JwtDecode extends JWT
{
    private const ALGORITHM = 'HS256';
    /**
     * @var string
     */
    private $key;

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function getUserId($token)
    {
        return self::decode($token, $this->key, [self::ALGORITHM]);
    }

}