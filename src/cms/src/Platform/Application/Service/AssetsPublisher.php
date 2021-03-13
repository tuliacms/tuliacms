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

    /**
     * @var array
     */
    private $maps;

    public function __construct(string $publicDir, array $maps = [])
    {
        $this->publicDir = $publicDir;
        $this->maps = $maps;
    }

    public function publishRegisteredAssets(): void
    {
        foreach ($this->maps as $source => $target) {
            $this->publish($source, $target);
        }
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
