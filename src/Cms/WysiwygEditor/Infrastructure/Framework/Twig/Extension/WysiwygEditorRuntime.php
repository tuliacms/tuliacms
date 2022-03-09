<?php

declare(strict_types=1);

namespace Tulia\Cms\WysiwygEditor\Infrastructure\Framework\Twig\Extension;

use Tulia\Cms\WysiwygEditor\Application\RegistryInterface;
use Twig\Extension\RuntimeExtensionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class WysiwygEditorRuntime implements RuntimeExtensionInterface
{
    protected RegistryInterface $registry;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    public function wysiwygEditor(string $name, ?string $content, array $params = []): string
    {
         return $this->registry->getActiveEditor()->render($name, $content, $params);
    }
}
