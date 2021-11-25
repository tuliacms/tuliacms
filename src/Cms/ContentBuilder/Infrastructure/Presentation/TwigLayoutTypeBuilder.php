<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Infrastructure\Presentation;

use Symfony\Component\Form\FormView;
use Tulia\Cms\ContentBuilder\Domain\ContentType\Model\AbstractContentType;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Model\LayoutType;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\LayoutTypeBuilderInterface;
use Tulia\Component\Templating\EngineInterface;
use Tulia\Component\Templating\View;

/**
 * @author Adam Banaszkiewicz
 */
class TwigLayoutTypeBuilder implements LayoutTypeBuilderInterface
{
    private EngineInterface $engine;

    public function __construct(EngineInterface $engine)
    {
        $this->engine = $engine;
    }

    public function getName(): string
    {
        return 'default';
    }

    public function build(AbstractContentType $contentType, LayoutType $layoutType, FormView $formView): string
    {
        return $this->engine->render(new View('@backend/contentbuilder/layout/default.tpl', [
            'type' => $contentType,
            'layout' => $layoutType,
            'form' => $formView,
        ]));
    }
}
