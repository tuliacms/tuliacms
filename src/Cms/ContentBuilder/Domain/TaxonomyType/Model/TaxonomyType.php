<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\TaxonomyType\Model;

use Tulia\Cms\ContentBuilder\Domain\ContentType\Model\AbstractContentType;
use Tulia\Cms\ContentBuilder\Domain\ContentType\Model\Field;

/**
 * @author Adam Banaszkiewicz
 */
class TaxonomyType extends AbstractContentType
{
    protected string $controller = 'Tulia\Cms\Node\UserInterface\Web\Frontend\Controller\Node::show';

    protected function internalValidate(): void
    {

    }

    protected function internalValidateField(Field $field): void
    {

    }
}
