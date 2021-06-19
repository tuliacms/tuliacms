<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Ports\Domain\ReadModel;

/**
 * @author Adam Banaszkiewicz
 */
class FileFinderScopeEnum
{
    public const SINGLE = 'single';

    /**
     * All operations in commands in Filemanager.
     */
    public const FILEMANAGER = 'filemanager';

    public const SEARCH = 'search';
}
