<?php

declare(strict_types=1);

namespace Tulia\Cms\WysiwygEditor\Infrastructure\Framework\Twig\Extension;

use Tulia\Cms\WysiwygEditor\Application\RegistryInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class WysiwygEditorExtension extends AbstractExtension
{
    protected RegistryInterface $registry;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('wysiwyg_editor', function (string $name, ?string $content, array $params = []) {
                return $this->registry->getActiveEditor()->render($name, $content, $params);
            }, [
                'is_safe' => [ 'html' ]
            ]),
        ];
    }
}
