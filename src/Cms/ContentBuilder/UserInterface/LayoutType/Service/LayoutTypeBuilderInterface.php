<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service;

use Symfony\Component\Form\FormView;
use Tulia\Cms\ContentBuilder\Domain\ContentType\Model\ContentType;
use Tulia\Cms\ContentBuilder\Domain\LayoutType\Model\LayoutType;

/**
 * @author Adam Banaszkiewicz
 */
interface LayoutTypeBuilderInterface
{
    /**
     * Returns name used in admin panel, to select wich builder
     * should be responsible for rendering the node type layout.
     */
    public function getName(): string;

    public function build(ContentType $contentType, LayoutType $layoutType, FormView $formView): string;
}
