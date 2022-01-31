<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service;

use Symfony\Component\Form\FormView;
use Tulia\Cms\ContentBuilder\Domain\WriteModel\Model\ContentType;
use Tulia\Component\Templating\View;

/**
 * @author Adam Banaszkiewicz
 */
interface LayoutTypeBuilderInterface
{
    /**
     * Returns view for building the content type. This builder is wrote in Vue.js in example,
     * and allows to create sections and fields used in the content type.
     */
    public function builderView(string $contentType, array $data, array $errors, bool $creationMode): View;

    /**
     * Returns view for creadint/editing content type. The content type is already configured,
     * and we want to create first content with this type.
     */
    public function editorView(ContentType $contentType, FormView $formView): View;
}
