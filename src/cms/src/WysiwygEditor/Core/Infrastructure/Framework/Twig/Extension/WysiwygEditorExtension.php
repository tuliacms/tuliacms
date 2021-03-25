<?php

declare(strict_types=1);

namespace Tulia\Cms\WysiwygEditor\Core\Infrastructure\Framework\Twig\Extension;

use Tulia\Cms\WysiwygEditor\Core\Application\RegistryInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class WysiwygEditorExtension extends AbstractExtension
{
    /**
     * @var RegistryInterface
     */
    protected $registry;

    /**
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
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
