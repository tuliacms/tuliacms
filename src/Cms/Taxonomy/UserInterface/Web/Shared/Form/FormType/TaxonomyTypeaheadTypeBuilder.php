<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\UserInterface\Web\Shared\Form\FormType;

use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\FieldTypeBuilder\FieldTypeBuilderInterface;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\Field;

/**
 * @author Adam Banaszkiewicz
 */
class TaxonomyTypeaheadTypeBuilder implements FieldTypeBuilderInterface
{
    public function build(Field $field, array $options, ContentType $contentType): array
    {
        $options['taxonomy_type'] = $field->getConfig('taxonomy');
        $options['search_route_params'] = [
            'taxonomy_type' => $field->getConfig('taxonomy'),
        ];
        $options['constraints'][] = new Callback(function ($value, ExecutionContextInterface $context) {
            $root = $context->getRoot();

            if (empty($value) === false && $root->has('id') && $value === $root->get('id')->getData()) {
                $context->buildViolation('cannotAssignSelfTermParent')
                    ->setTranslationDomain('taxonomy')
                    ->atPath('parent_id')
                    ->addViolation();
            }
        });

        return $options;
    }
}
