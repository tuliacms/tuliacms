<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Form\NodeType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\FieldTypeMappingRegistry;

/**
 * @author Adam Banaszkiewicz
 */
class ConstraintModificator extends AbstractType
{
    private FieldTypeMappingRegistry $fieldTypeMappingRegistry;

    public function __construct(FieldTypeMappingRegistry $fieldTypeMappingRegistry)
    {
        $this->fieldTypeMappingRegistry = $fieldTypeMappingRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('value', TextType::class);

        $builder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'addValueField']);
    }

    public function addValueField(FormEvent $event): void
    {
        $modificator = $event->getData();
        $form = $event->getForm();

        $form
            ->add('id', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Callback(
                        [$this, 'validateConstraintModificatorExistence'],
                        null,
                        [
                            'field_type' => $event->getForm()->getConfig()->getOption('field_type'),
                            'constraint_id' => $event->getForm()->getConfig()->getOption('constraint_id'),
                            'modificator_id' => $modificator['id'],
                        ],
                    )
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('field_type');
        $resolver->setRequired('constraint_id');
    }

    public function validateConstraintModificatorExistence(string $modificatorId, ExecutionContextInterface $context, array $payload): void
    {
        // Validation of node type existence is done in parent
        if ($this->fieldTypeMappingRegistry->hasType($payload['field_type']) === false) {
            return;
        }

        $type = $this->fieldTypeMappingRegistry->get($payload['field_type']);
        $constraintId = $payload['constraint_id'];

        if (isset($type['constraints'][$constraintId]['modificators'][$modificatorId]) === false) {
            $context->buildViolation('Modificator named "%name%" for "%constraint%" of "%type%" field type not exists.')
                ->setParameter('%name%', $modificatorId)
                ->setParameter('%constraint%', $constraintId)
                ->setParameter('%type%', $payload['field_type'])
                ->addViolation();
        }
    }
}
