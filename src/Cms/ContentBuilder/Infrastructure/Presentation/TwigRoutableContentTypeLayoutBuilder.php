<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Infrastructure\Presentation;

use Symfony\Component\Form\FormView;
use Tulia\Cms\ContentBuilder\Domain\ContentType\Model\ContentType;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\LayoutTypeBuilderInterface;
use Tulia\Component\Templating\EngineInterface;
use Tulia\Component\Templating\View;

/**
 * @author Adam Banaszkiewicz
 */
class TwigRoutableContentTypeLayoutBuilder implements LayoutTypeBuilderInterface
{
    private EngineInterface $engine;

    public function __construct(EngineInterface $engine)
    {
        $this->engine = $engine;
    }

    public function build(ContentType $contentType, FormView $formView): string
    {
        return $this->engine->render(new View('@backend/content_builder/layout/routable_content_type.tpl', [
            'type' => $contentType,
            'layout' => $contentType->getLayout(),
            'form' => $formView,
        ]));
    }
}
