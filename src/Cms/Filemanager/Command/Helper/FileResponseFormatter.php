<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Command\Helper;

use Tulia\Cms\Filemanager\Application\ImageUrlResolver;
use Tulia\Cms\Filemanager\Enum\TypeEnum;
use Tulia\Cms\Filemanager\File;
use Tulia\Cms\Platform\Shared\Unit;

/**
 * @author Adam Banaszkiewicz
 */
class FileResponseFormatter
{
    /**
     * @var ImageUrlResolver
     */
    protected $urlResolver;

    /**
     * @param ImageUrlResolver $urlResolver
     */
    public function __construct(ImageUrlResolver $urlResolver)
    {
        $this->urlResolver = $urlResolver;
    }

    public function format(File $file): array
    {
        return [
            'type' => 'file',
            'id'   => $file->getId(),
            'name' => $file->getFilename(),
            'preview' => $this->getPreview($file),
            'size' => $file->getSize(),
            'size_formatted' => Unit::bytesFormat($file->getSize()),
            'metadata' => array_merge($file->getMetadata()->all(), [
                'type' => $file->getType(),
                'mimetype' => $file->getMimetype(),
                'extension' => $file->getExtension(),
            ]),
        ];
    }

    private function getPreview(File $file): string
    {
        if ($file->getType() === TypeEnum::IMAGE) {
            return $this->urlResolver->size($file, 'thumbnail');
        }

        return '';
    }
}
