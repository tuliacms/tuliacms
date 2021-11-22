<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\UserInterface\Web\Shared\Form\FormType;

use Tulia\Cms\ContentBuilder\Domain\Field\Model\Field;

/**
 * @author Adam Banaszkiewicz
 */
class TaxonomyTypeaheadTypeBuilder
{
    public function build(Field $field, array $options): array
    {
        $options['taxonomy_type'] = $field->getTaxonomy();
        return $options;
    }
}