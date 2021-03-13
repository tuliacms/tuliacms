<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Application\Service;

use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Adam Banaszkiewicz
 */
class AssetsPublisher
{
    /**
     * @var string
     */
    private $publicDir;

    public function __construct(string $publicDir)
    {
        $this->publicDir = $publicDir;
    }

    public function publish(string $source, string $targetname): bool
    {
        $fs = new Filesystem();
        $target = $this->publicDir . '/assets';

        if (file_exists($source) === false) {
            return false;
        }

        $fs->mirror($source, $target . $targetname, null, [
            'override' => true,
            'delete' => true,
        ]);

        return true;
    }
}
