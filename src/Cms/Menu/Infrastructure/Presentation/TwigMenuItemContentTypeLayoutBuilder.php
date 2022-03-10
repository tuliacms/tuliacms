<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Presentation;

use Symfony\Component\Form\FormView;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\LayoutTypeBuilderInterface;
use Tulia\Component\Templating\View;

/**
 * @author Adam Banaszkiewicz
 */
class TwigMenuItemContentTypeLayoutBuilder implements LayoutTypeBuilderInterface
{
    public function editorView(ContentType $contentType, FormView $formView, array $viewContext): View
    {
        return new View('@backend/menu/content_builder/menu-item.editor.tpl', [
            'contentType' => $contentType,
            'layout' => $contentType->getLayout(),
            'form' => $formView,
            'context' => $viewContext,
        ]);
    }

    public function builderView(string $contentType, array $data, array $errors, bool $creationMode): View
    {
        return new View('noop.tpl');
    }
}
