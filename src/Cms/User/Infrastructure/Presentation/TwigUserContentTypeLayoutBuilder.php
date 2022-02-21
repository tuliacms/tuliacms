<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Presentation;

use Symfony\Component\Form\FormView;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\LayoutTypeBuilderInterface;
use Tulia\Component\Templating\View;

/**
 * @author Adam Banaszkiewicz
 */
class TwigUserContentTypeLayoutBuilder implements LayoutTypeBuilderInterface
{
    public function editorView(ContentType $contentType, FormView $formView): View
    {
        return new View('@backend/user/content_builder/editor.tpl', [
            'contentType' => $contentType,
            'layout' => $contentType->getLayout(),
            'form' => $formView,
        ]);
    }

    public function builderView(string $contentType, array $data, array $errors, bool $creationMode): View
    {
        return new View('noop.tpl');
    }
}
