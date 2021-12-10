<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Form\NodeType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\FieldTypeMappingRegistry;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Form\Validator\CodenameValidator;

/**
 * @author Adam Banaszkiewicz
 */
class Field extends AbstractType
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
                    new Callback([new CodenameValidator(), 'validateFieldId']),
                ],
            ])
            ->add('type', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Callback([$this, 'validateFieldTypeExists']),
                ],
            ])
            ->add('multilingual', ChoiceType::class, [
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
        $field = $event->getData();
        $form = $event->getForm();

        $form
            ->add('label', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Callback(
                        [$this, 'validateFieldTypeRequiredConfigurationExistence'],
                        null,
                        [
                            'configurations' => $field['configuration'],
                            'field_type' => $field['type'],
                        ],
                    ),
                ],
            ])
            ->add('constraints', CollectionType::class, [
                'entry_type' => Constraint::class,
                'allow_add' => true,
                'allow_delete' => true,
                'entry_options' => [
                    'field_type' => $field['type'],
                ],
            ])
            ->add('configuration', CollectionType::class, [
                'entry_type' => ConfigurationEntry::class,
                'allow_add' => true,
                'allow_delete' => true,
                'entry_options' => [
                    'field_type' => $field['type'],
                ],
            ])
        ;
    }

    public function validateFieldTypeExists(string $type, ExecutionContextInterface $context): void
    {
        if ($this->fieldTypeMappingRegistry->hasType($type) === false) {
            $context->buildViolation('fieldTypeNotExists')
                ->setTranslationDomain('content_builder')
                ->setParameter('%type%', $type)
                ->addViolation();
        }
    }

    public function validateFieldTypeRequiredConfigurationExistence(?string $label, ExecutionContextInterface $context, array $payload): void
    {
        // Validation of the field type is done in self::validateFieldTypeExists()
        if ($this->fieldTypeMappingRegistry->hasType($payload['field_type']) === false) {
            return;
        }

        $type = $this->fieldTypeMappingRegistry->get($payload['field_type']);

        foreach ($type['configuration'] as $configurationId => $requiredConfiguration) {
            if ($requiredConfiguration['required'] === false) {
                continue;
            }

            $found = false;

            foreach ($payload['configurations'] as $filledConfiguration) {
                if ($filledConfiguration['id'] === $configurationId) {
                    $found = true;
                }
            }

            if ($found === false) {
                $context->buildViolation('Configuration named "%name%" for field type "%type%" is required, please fill it.')
                    ->setTranslationDomain('content_builder')
                    ->setParameter('%name%', $configurationId)
                    ->setParameter('%type%', $payload['field_type'])
                    ->addViolation();
            }
        }
    }
}
