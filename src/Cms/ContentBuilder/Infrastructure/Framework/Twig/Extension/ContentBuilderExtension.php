<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Infrastructure\Framework\Twig\Extension;

use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Exception\LayoutNotExists;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\LayoutBuilder;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Form\ContentTypeFormDescriptor;
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
            new TwigFunction('render_content_builder_form_layout', function (ContentTypeFormDescriptor $formDescriptor) {
                try {
                    return $this->layoutBuilder->build($formDescriptor);
                } catch (LayoutNotExists $e) {
                    return sprintf('Layout "%s" defined for "%s" node type not exists. Form cannot be rendered.', $e->getLayoutName(), $e->getNodeType());
                }
            }, [
                'is_safe' => [ 'html' ],
            ]),
        ];
    }
}
