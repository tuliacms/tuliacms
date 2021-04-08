<?php

declare(strict_types=1);

namespace Tulia\Framework\Kernel\Config;

use Symfony\Component\Config\FileLocator as BaseFileLocator;
use Tulia\Framework\Kernel\KernelInterface;

/**
 * @author Adam Banaszkiewicz
 */
class FileLocator extends BaseFileLocator
{
    private KernelInterface $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    public function locate(string $file, string $currentPath = null, bool $first = true)
    {
        if (isset($file[0]) && '@' === $file[0]) {
            $resource = $this->kernel->locateResource($file);

            return $first ? $resource : [$resource];
        }

        return parent::locate($file, $currentPath, $first);
    }
}
