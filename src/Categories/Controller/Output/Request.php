<?php


namespace App\Categories\Controller\Output;


class Request
{

    private const URI = 'http://localhost:8000/categories';

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

    public static function detailedCategory(int $id): self
    {
        return new self('GET', self::URI . '/' . $id);
    }

    public static function updateCategory(int $id): self
    {
        return new self('PUT', self::URI . '/' . $id);
    }

    public static function listCategories(): self
    {
        return new self('GET', self::URI);
    }

    public static function createCategory(): self
    {
        return new self('POST', self::URI, ['name' => 'string', 'limit' => 'float']);
    }
}