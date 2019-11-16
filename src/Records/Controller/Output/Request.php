<?php


namespace App\Records\Controller\Output;


class Request
{

    private const URI = 'http://localhost:8000/records';

    /**
     * @var string
     */
    public $type;
    /**
     * @var string
     */
    public $url;
    /**
     * @var array
     */
    public $body;


    private function __construct(string $type, string $url, array $body = null)
    {
        $this->type = $type;
        $this->url = $url;
        $this->body = $body;
    }

    public static function detailedRecord(int $id): self
    {
        return new self('GET', self::URI . '/' . $id);
    }

    public static function updateRecord(int $id): self
    {
        return new self('PUT', self::URI . '/' . $id);
    }

    public static function listRecords(): self
    {
        return new self('GET', self::URI);
    }

    public static function createRecord(): self
    {
        return new self('POST', self::URI, ['name' => 'string', 'limit' => 'float']);
    }
}