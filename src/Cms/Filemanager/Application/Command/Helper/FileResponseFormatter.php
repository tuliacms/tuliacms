<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Application\Command\Helper;

use Tulia\Cms\Filemanager\Application\Service\ImageUrlResolver;
use Tulia\Cms\Filemanager\Ports\Domain\WriteModel\FileTypeEnum;
use Tulia\Cms\Filemanager\Domain\ReadModel\Model\File;
use Tulia\Cms\Platform\Shared\Unit;

/**
 * @author Adam Banaszkiewicz
 */
class FileResponseFormatter
{
    protected ImageUrlResolver $urlResolver;

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
            'metadata' => []/*array_merge($file->getMetadata()->all(), [
                'type' => $file->getType(),
                'mimetype' => $file->getMimetype(),
                'extension' => $file->getExtension(),
            ])*/,
        ];
    }

    private function getPreview(File $file): string
    {
        if ($file->getType() === FileTypeEnum::IMAGE) {
            return $this->urlResolver->size($file, 'thumbnail');
        }

        return '';
    }
}
