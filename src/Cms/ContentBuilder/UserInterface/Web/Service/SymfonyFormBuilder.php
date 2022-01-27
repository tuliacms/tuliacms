<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\Web\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Tulia\Cms\ContentBuilder\Domain\ContentType\Model\ContentType;
use Tulia\Cms\ContentBuilder\Domain\ContentType\Model\Field;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Exception\ConstraintNotExistsException;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Exception\FieldTypeNotExistsException;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\ConstraintsBuilder;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\FieldTypeMappingRegistry;
use Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType\CancelType;
use Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType\SubmitType;

/**
 * @author Adam Banaszkiewicz
 */
class SymfonyFormBuilder
{
    protected FormFactoryInterface $formFactory;
    protected FieldTypeMappingRegistry $mappingRegistry;
    protected ConstraintsBuilder $constraintsBuilder;
    protected LoggerInterface $logger;

    public function __construct(
        FormFactoryInterface $formFactory,
        FieldTypeMappingRegistry $mappingRegistry,
        ConstraintsBuilder $constraintsBuilder,
        LoggerInterface $contentBuilderLogger
    ) {
        $this->formFactory = $formFactory;
        $this->mappingRegistry = $mappingRegistry;
        $this->constraintsBuilder = $constraintsBuilder;
        $this->logger = $contentBuilderLogger;
    }

    public function createForm(ContentType $taxonomyType, array $data, bool $expectCqrsToken = true): FormInterface
    {
        $builder = $this->createFormBuilder($taxonomyType->getCode(), $data, $expectCqrsToken);

        $this->buildFieldsWithBuilder($taxonomyType->getFields(), $builder);

        return $builder->getForm();
    }

    protected function createFormBuilder(string $type, array $data, bool $expectCqrsToken = true): FormBuilderInterface
    {
        return $this->formFactory->createNamedBuilder(
            sprintf('content_builder_form_%s', $type),
            'Symfony\Component\Form\Extension\Core\Type\FormType',
            $data,
            [
                'csrf_protection' => $expectCqrsToken,
            ]
        );
    }

    protected function buildFieldsWithBuilder(array $fields, FormBuilderInterface $builder): void
    {
        /** @var Field $field */
        foreach ($fields as $field) {
            try {
                $typeBuilder = $this->mappingRegistry->getTypeBuilder($field->getType());

                $options = array_merge([
                    'label' => $field->getName() === ''
                        ? false
                        : $field->getName()
                ], $field->getBuilderOptions());

                $options['constraints'] = $this->constraintsBuilder->build($options['constraints'] ?? []);

                if ($typeBuilder) {
                    $options = (new $typeBuilder)->build($field, $options);
                }

                $builder->add(
                    $field->getCode(),
                    $this->mappingRegistry->getTypeClassname($field->getType()),
                    $options
                );
            } catch (ConstraintNotExistsException $e) {
                $this->logger->warning(
                    sprintf(
                        'Cms\ContentBuilder: Constraint "%s" not exists. Field "%s" wasn\'t created in form.',
                        $e->getName(),
                        $field->getCode()
                    )
                );
            } catch (FieldTypeNotExistsException $e) {
                $this->logger->warning(
                    sprintf(
                        'Cms\ContentBuilder: Mapping for field type "%s" not exists. Field "%s" wasn\'t created in form.',
                        $field->getType(),
                        $field->getCode()
                    )
                );
            }
        }

        $builder
            ->add('id', HiddenType::class, [
                'required' => true,
            ])
            ->add('cancel', CancelType::class, [
                'route' => 'backend.widget',
            ])
            ->add('save', SubmitType::class);
    }
}
