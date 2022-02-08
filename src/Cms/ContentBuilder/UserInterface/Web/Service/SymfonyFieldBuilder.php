<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\Web\Service;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\Field;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\ConstraintsBuilder;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\FieldTypeMappingRegistry;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Shared\Form\FormType\RepeatableGroupType;

/**
 * @author Adam Banaszkiewicz
 */
class SymfonyFieldBuilder
{
    private FieldTypeMappingRegistry $mappingRegistry;
    private ConstraintsBuilder $constraintsBuilder;

    public function __construct(
        FieldTypeMappingRegistry $mappingRegistry,
        ConstraintsBuilder $constraintsBuilder
    ) {
        $this->mappingRegistry = $mappingRegistry;
        $this->constraintsBuilder = $constraintsBuilder;
    }

    public function buildFieldAndAddToBuilder(
        Field $field,
        FormBuilderInterface $builder,
        ContentType $contentType
    ): void {
        if ($field->getType() === 'repeatable') {
            $this->buildRepeatable($field, $builder, $contentType);
            return;
        }

        $options = array_merge([
            'label' => $field->getName() === ''
                ? false
                : $field->getName(),
            'translation_domain' => 'content_builder.field',
        ], $field->getBuilderOptions());

        $options['constraints'] = $this->constraintsBuilder->build($options['constraints'] ?? []);

        $typeBuilder = $this->mappingRegistry->getTypeBuilder($field->getType());

        if ($typeBuilder) {
            $options = (new $typeBuilder)->build($field, $options);
        }

        $builder->add(
            $field->getCode(),
            $this->mappingRegistry->getTypeClassname($field->getType()),
            $options
        );
    }

    private function buildRepeatable(Field $field, FormBuilderInterface $builder, ContentType $contentType): void
    {
        $fields = [];

        foreach ($contentType->getFields() as $pretender) {
            if ($pretender->getParent() === $field->getCode()) {
                $fields[] = $pretender;
            }
        }

        $builder->add(
            $field->getCode(),
            CollectionType::class,
            [
                'label' => 'Repeatable',
                'allow_add' => true,
                'allow_delete' => true,
                'entry_type' => RepeatableGroupType::class,
                'entry_options' => [
                    'fields' => $fields,
                    'repeatable_field' => true,
                    'content_type' => $contentType,
                ],
            ]);
    }
}
