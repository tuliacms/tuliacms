<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Domain\WriteModel;

/**
 * @author Adam Banaszkiewicz
 */
class FileTypeEnum
{
    public const IMAGE    = 'image';
    public const ARCHIVE  = 'archive';
    public const DOCUMENT = 'document';
    public const PDF      = 'pdf';
    public const FILE     = 'file';
    public const SVG      = 'svg';
}
