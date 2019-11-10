<?php


namespace App\StaticFiles;


class File
{

    /**
     * @var string
     */
    public $contents;
    /**
     * @var string
     */
    public $mimeType;

    public function __construct(string $contents, string $mimeType)
    {
        $this->contents = $contents;
        $this->mimeType = $mimeType;
    }
}