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

        $typeBuilder = $this->mappingRegistry->getTypeBuilder($field->getType());
        $typeHandler = $this->mappingRegistry->getTypeHandler($field->getType());

        $options = [
            'label' => $field->getName() === ''
                ? false
                : $field->getName(),
            'translation_domain' => 'content_builder.field',
            'constraints' => $this->constraintsBuilder->build($field->getConstraints()),
            'content_builder_field' => $field,
            'content_builder_field_handler' => $typeHandler,
        ];

        if ($typeBuilder) {
            $options = $typeBuilder->build($field, $options, $contentType);
        }

        $builder->add(
            $field->getCode(),
            $this->mappingRegistry->getTypeClassname($field->getType()),
            $options
        );

        if ($typeHandler) {
            $builder->get($field->getCode())->addModelTransformer(new FieldTypeHandlerAwareDataTransformer($typeHandler));
        }
    }

    private function buildRepeatable(Field $field, FormBuilderInterface $builder, ContentType $contentType): void
    {
        $prototypeName = sprintf('__name_%s__', $field->getCode());

        $builder->add(
            $field->getCode(),
            CollectionType::class,
            [
                'label' => $field->getName(),
                'allow_add' => true,
                'allow_delete' => true,
                'attr' => [
                    'class' => 'repeatable-field',
                    'data-prototype-name' => $prototypeName,
                    'data-dynamic-element' => 'repeatable-element',
                ],
                'prototype_name' => $prototypeName,
                'entry_type' => RepeatableGroupType::class,
                'entry_options' => [
                    'label' => false,
                    'fields' => $field->getChildren(),
                    'repeatable_field' => true,
                    'content_type' => $contentType,
                ],
            ]);
    }
}
