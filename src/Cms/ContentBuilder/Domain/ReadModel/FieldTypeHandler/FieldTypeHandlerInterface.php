<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\ReadModel\FieldTypeHandler;

/**
 * @author Adam Banaszkiewicz
 */
interface FieldTypeHandlerInterface
{
    /**
     * Transforms value from Model, to value for Form.
     * Ie. You get the User's Avatar filepath as $value, and return
     * the Symfony\Component\HttpFoundation\File\File instance with this filepath
     * to handle it in the form field.
     */
    public function prepareValueToForm($value);

    /**
     * Handles operation of the value, and returns the handled value.
     * Ie. You get the value from uploaded file (object of Symfony\Component\HttpFoundation\File\UploadedFile),
     * You uploads here the file, and return destination path of the uploaded file.
     */
    public function handle($value);
}
