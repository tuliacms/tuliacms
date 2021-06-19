<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Application\Service;

use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Adam Banaszkiewicz
 */
class AssetsPublisher
{
    private string $publicDir;

    private array $assetsPublicPaths;

    public function __construct(string $publicDir, array $assetsPublicPaths = [])
    {
        $this->publicDir = $publicDir;
        $this->assetsPublicPaths = $assetsPublicPaths;
    }

    public function publishRegisteredAssets(): void
    {
        foreach ($this->assetsPublicPaths as $source => $target) {
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
