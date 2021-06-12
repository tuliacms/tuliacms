<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\UserInterface\Web\Backend\Form;

use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints as Assert;
use Tulia\Cms\ContactForms\Application\FieldType\Parser\RegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class FieldsType extends AbstractType
{
    private RegistryInterface $registry;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();
                $data = $event->getData();

                // Every field have this option
                $form->add('type', Type\TextType::class);

                $type = $this->registry->get($data['type']);

                foreach ($type->getDefinition()['options'] ?? [] as $name => $option) {
                    $options = [];

                    if (isset($option['required']) && $option['required'] === true) {
                        $options['required'] = true;
                        $options['constraints'][] = new Assert\NotBlank();
                    }

                    $form->add($name, Type\TextType::class, $options);
                }
            }
        );
    }
}
