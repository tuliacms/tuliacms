<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\Web\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Tulia\Cms\ContentBuilder\Domain\NodeType\Model\NodeType;
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

    public function __construct(
        FormFactoryInterface $formFactory,
        FieldTypeMappingRegistry $mappingRegistry,
        ConstraintsBuilder $constraintsBuilder,
        LoggerInterface $logger
    ) {
        $this->formFactory = $formFactory;
        $this->mappingRegistry = $mappingRegistry;
        $this->constraintsBuilder = $constraintsBuilder;
        $this->logger = $logger;
    }

    public function createForm(NodeType $nodeType, array $data): FormInterface
    {
        $builder = $this->formFactory->createNamedBuilder(
            sprintf('content_builder_form_%s', $nodeType->getName()),
            'Symfony\Component\Form\Extension\Core\Type\FormType',
            $data
        );

        foreach ($nodeType->getFields() as $field) {
            if ($this->mappingRegistry->hasType($field->getType()) === false) {
                $this->logger->warning(sprintf('Cms\ContentBuilder: Mapping for field type "%s" not exists. Field wasn\'t created in form.', $field->getType()));
                continue;
            }

            $builder->add(
                $field->getName(),
                $this->mappingRegistry->getTypeClassname($field->getType()),
                $field->getOptions([
                    'label' => $field->getLabel() === ''
                        ? false
                        : $field->getLabel(),
                    'constraints' => $this->constraintsBuilder->build($field->getConstraints())
                ])
            );
        }

        $builder
            ->add('id', HiddenType::class, [
                'required' => true,
            ])
            ->add('cancel', CancelType::class, [
                'route' => 'backend.widget',
            ])
            ->add('save', SubmitType::class);

        return $builder->getForm();
    }
}
