<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\UserInterface\Web\Backend\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints as Assert;
use Tulia\Cms\ContactForm\Domain\FieldType\FieldsTypeRegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class FieldsType extends AbstractType
{
    private FieldsTypeRegistryInterface $registry;

    public function __construct(FieldsTypeRegistryInterface $registry)
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
                $form->add('alias', Type\TextType::class);

                $parser = $this->registry->getParser($data['alias']);

                foreach ($parser->getDefinition()['options'] ?? [] as $name => $option) {
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
