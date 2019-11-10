<?php

namespace App\Products\Controller;


use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator;

final class Input
{

    private const SUPPORTED_FILE_TYPES = ['image/jpg', 'image/png'];

    private $request;

    public function __construct(ServerRequestInterface $request)
    {

        $this->request = $request;
    }

    /**
     * @throws NestedValidationException
     */
    public function validate(): void
    {
        $this->validateFields();
        $this->validateUploadedFile();
    }

    public function name(): string
    {
        return $this->request->getParsedBody()['name'];
    }

    public function price(): float
    {
        return (float)$this->request->getParsedBody()['price'];
    }

    public function image(): ?UploadedFileInterface
    {
        $files = $this->request->getUploadedFiles();
        return $files['image'] ?? null;
    }

    private function validateFields()
    {
        $nameValidator = Validator::key(
            'name',
            Validator::allOf(
                Validator::notBlank(),
                Validator::stringType()
            )
        )->setName('name');

        $priceValidator = Validator::key(
            'price',
            Validator::allOf(
                Validator::notBlank(),
                Validator::numeric(),
                Validator::positive()
            )
        )->setName('price');

        Validator::allOf($nameValidator, $priceValidator)->assert($this->request->getParsedBody());
    }

    private function validateUploadedFile()
    {
        if ($this->image() === null) {
            return;
        }

        if (!in_array($this->image()->getClientMediaType(), self::SUPPORTED_FILE_TYPES, true)) {
            throw new NestedValidationException(
                'image has invalid extensions. Select Jpg or png file.'
            );
        }
    }
}
