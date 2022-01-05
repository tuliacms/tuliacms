<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Form\NodeType\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\FieldTypeMappingRegistry;

/**
 * @author Adam Banaszkiewicz
 */
class Constraint extends AbstractType
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
        $choices = ['Yes' => true, 'No' => false];

        $builder
            ->add('id', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Callback([$this, 'validateConstraintExists'], null, ['field_type' => $options['field_type']])
                ],
            ])
            ->add('enabled', ChoiceType::class, [
                'choices' => $choices,
                'constraints' => [
                    new Choice(['choices' => $choices]),
                ],
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'addDynamicFields']);
    }

    public function addDynamicFields(FormEvent $event): void
    {
        $constraint = $event->getData();
        $form = $event->getForm();

        $form
            ->add('modificators', CollectionType::class, [
                'entry_type' => ConstraintModificator::class,
                'allow_add' => true,
                'allow_delete' => true,
                'entry_options' => [
                    'field_type' => $form->getConfig()->getOption('field_type'),
                    'constraint_id' => $constraint['id'],
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('field_type');
    }

    public function validateConstraintExists(string $constraint, ExecutionContextInterface $context, array $payload): void
    {
        // Validation of node type existence is done in parent
        if ($this->fieldTypeMappingRegistry->hasType($payload['field_type']) === false) {
            return;
        }

        $type = $this->fieldTypeMappingRegistry->get($payload['field_type']);

        if (isset($type['constraints'][$constraint]) === false) {
            $context->buildViolation('Constraint "%constraint%" not exists in %type% field type.')
                ->setParameter('%constraint%', $constraint)
                ->setParameter('%type%', $payload['field_type'])
                ->addViolation();
        }
    }
}
