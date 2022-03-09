<?php

declare(strict_types=1);

namespace Tulia\Cms\WysiwygEditor\Infrastructure\Framework\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class WysiwygEditorExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'wysiwyg_editor',
                [WysiwygEditorRuntime::class, 'wysiwygEditor'],
                ['is_safe' => [ 'html' ]]
            ),
        ];
    }
}
