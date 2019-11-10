<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 03.11.2019
 * Time: 22:07
 */

namespace App\Core;

use Psr\Http\Message\UploadedFileInterface;
use React\Filesystem\FilesystemInterface;
use React\Promise\PromiseInterface;

class Uploader
{
    private const UPLOADS_DIR = 'uploads';

    private $filesystem;

    private $projectRoot;

    public function __construct(FilesystemInterface $filesystem, string $projectRoot)
    {
        $this->filesystem = $filesystem;
        $this->projectRoot = $projectRoot;
    }

    public function upload(UploadedFileInterface $file): PromiseInterface
    {
        $uploadPath = $this->makeFilePath($file);
        $fullPath = $this->projectRoot . '/' . $uploadPath;

        return $this->filesystem->file($fullPath)
            ->putContents((string) $file->getStream())
            ->then(function () use ($uploadPath){
               return $uploadPath;
            });
    }

    private function makeFilePath(UploadedFileInterface $file): string
    {
        preg_match('/^.*\.(.+)$/', $file->getClientFilename(), $filenameParsed);

        return implode('',[
            self::UPLOADS_DIR,
            '/',
            md5((string)$file->getStream()),
            '.',
            $filenameParsed[1]
        ]);
    }
}
