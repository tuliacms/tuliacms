<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Domain\ReadModel\Finder\Enum;

/**
 * @author Adam Banaszkiewicz
 */
class FilemanagerFinderScopeEnum
{
    public const SINGLE = 'single';

    /**
     * All operations in commands in Filemanager.
     */
    public const FILEMANAGER = 'filemanager';

    public const SEARCH = 'search';
}
