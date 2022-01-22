<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service;

use Symfony\Component\Form\FormView;
use Tulia\Cms\ContentBuilder\Domain\ContentType\Model\ContentType;

/**
 * @author Adam Banaszkiewicz
 */
interface LayoutTypeBuilderInterface
{
    public function build(ContentType $contentType, FormView $formView): string;
}
