<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service;

use Symfony\Component\Form\FormView;
use Tulia\Cms\ContentBuilder\Domain\NodeType\Model\NodeType;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Model\LayoutType;

/**
 * @author Adam Banaszkiewicz
 */
interface LayoutTypeBuilderInterface
{
    public function getName(): string;

    public function build(NodeType $nodeType, LayoutType $layoutType, FormView $formView): string;
}
