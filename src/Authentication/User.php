<?php

namespace App\Authentication;

class User
{

    public $id;

    public $email;

    public $password;

    public function __construct(int $id, string $email, string $password)
    {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
    }
}