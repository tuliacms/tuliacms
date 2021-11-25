<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\Web\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Tulia\Cms\ContentBuilder\Domain\ContentType\Model\AbstractContentType;
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

    public function createForm(AbstractContentType $taxonomyType, array $data): FormInterface
    {
        $builder = $this->createFormBuilder($taxonomyType->getType(), $data);

        $this->buildFieldsWithBuilder($taxonomyType->getFields(), $builder);

        return $builder->getForm();
    }

    protected function createFormBuilder(string $type, array $data): FormBuilderInterface
    {
        return $this->formFactory->createNamedBuilder(
            sprintf('content_builder_form_%s', $type),
            'Symfony\Component\Form\Extension\Core\Type\FormType',
            $data
        );
    }

    protected function buildFieldsWithBuilder(array $fields, FormBuilderInterface $builder): void
    {
        foreach ($fields as $field) {
            try {
                $typeBuilder = $this->mappingRegistry->getTypeBuilder($field->getType());

                $options = array_merge([
                    'label' => $field->getLabel() === ''
                        ? false
                        : $field->getLabel()
                ], $field->getBuilderOptions());

                $options['constraints'] = $this->constraintsBuilder->build($options['constraints'] ?? []);

                if ($typeBuilder) {
                    $options = (new $typeBuilder)->build($field, $options);
                }

                $builder->add(
                    $field->getName(),
                    $this->mappingRegistry->getTypeClassname($field->getType()),
                    $options
                );
            } catch (ConstraintNotExistsException $e) {
                $this->logger->warning(
                    sprintf(
                        'Cms\ContentBuilder: Constraint "%s" not exists. Field "%s" wasn\'t created in form.',
                        $e->getName(),
                        $field->getName()
                    )
                );
            } catch (FieldTypeNotExistsException $e) {
                $this->logger->warning(
                    sprintf(
                        'Cms\ContentBuilder: Mapping for field type "%s" not exists. Field "%s" wasn\'t created in form.',
                        $field->getType(),
                        $field->getName()
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
