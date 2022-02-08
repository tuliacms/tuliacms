<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\Web\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\Field;
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
    private FormFactoryInterface $formFactory;
    private FieldTypeMappingRegistry $mappingRegistry;
    private ConstraintsBuilder $constraintsBuilder;
    private LoggerInterface $logger;
    private SymfonyFieldBuilder $symfonyFieldBuilder;

    public function __construct(
        FormFactoryInterface $formFactory,
        FieldTypeMappingRegistry $mappingRegistry,
        ConstraintsBuilder $constraintsBuilder,
        LoggerInterface $contentBuilderLogger,
        SymfonyFieldBuilder $symfonyFieldBuilder
    ) {
        $this->formFactory = $formFactory;
        $this->mappingRegistry = $mappingRegistry;
        $this->constraintsBuilder = $constraintsBuilder;
        $this->logger = $contentBuilderLogger;
        $this->symfonyFieldBuilder = $symfonyFieldBuilder;
    }

    public function createForm(ContentType $contentType, array $data, bool $expectCqrsToken = true): FormInterface
    {
        $builder = $this->createFormBuilder($contentType->getCode(), $data, $expectCqrsToken);

        $fields = $contentType->getFields();

        $this->buildFieldsWithBuilder($fields, $builder, $contentType);

        return $builder->getForm();
    }

    private function createFormBuilder(string $type, array $data, bool $expectCqrsToken = true): FormBuilderInterface
    {
        return $this->formFactory->createNamedBuilder(
            sprintf('content_builder_form_%s', $type),
            'Symfony\Component\Form\Extension\Core\Type\FormType',
            $data,
            [
                'csrf_protection' => $expectCqrsToken,
                'attr' => [
                    'class' => 'tulia-dynamic-form'
                ],
            ]
        );
    }

    /**
     * @param Field[] $fields
     */
    private function buildFieldsWithBuilder(array $fields, FormBuilderInterface $builder, ContentType $contentType): void
    {
        /** @var Field $field */
        foreach ($fields as $field) {
            // Here we render only main fields, children will be rendered in RepeatableGroupType
            if ($field->getParent()) {
                continue;
            }

            try {
                $this->symfonyFieldBuilder->buildFieldAndAddToBuilder($field, $builder, $contentType);
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
                // @todo Configure back button URL
                'route' => 'backend.widget',
            ])
            ->add('save', SubmitType::class);
    }
}
