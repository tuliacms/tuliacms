<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Infrastructure\Framework\Twig\Extension;

use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\LayoutBuilder;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Form\FormDescriptor;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class ContentBuilderExtension extends AbstractExtension
{
    private LayoutBuilder $layoutBuilder;

    public function __construct(LayoutBuilder $layoutBuilder)
    {
        $this->layoutBuilder = $layoutBuilder;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('render_content_builder_form_layout', function (Environment $env, $context, FormDescriptor $formDescriptor) {
                return $this->layoutBuilder->build($formDescriptor);
            }, [
                'is_safe' => [ 'html' ],
                'needs_environment' => true,
                'needs_context' => true,
            ]),
        ];
    }
}
