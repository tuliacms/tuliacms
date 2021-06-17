<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\UserInterface\Web\Frontend\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tulia\Cms\ContactForm\Domain\ReadModel\Finder\Model\Field;
use Tulia\Cms\ContactForm\Ports\Domain\FieldType\FieldsTypeRegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ContactFormFramework extends AbstractType
{
    private FieldsTypeRegistryInterface $typesRegistry;

    public function __construct(FieldsTypeRegistryInterface $typesRegistry)
    {
        $this->typesRegistry = $typesRegistry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Field $field */
        foreach ($options['fields'] as $field) {
            $type = $this->typesRegistry->get($field->getTypeAlias());
            $options = $this->buildOptions($field->getOptions());

            $builder->add(
                $field->getName(),
                $type->getFormType(),
                $type->buildOptions($options)
            );
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('fields');
        $resolver->setAllowedTypes('fields', 'array');
    }

    protected function buildOptions(array $options): array
    {
        return $this->buildConstraints($options);
    }

    protected function buildConstraints(array $options): array
    {
        if (isset($options['constraints'])) {
            $constraints = [];

            foreach ($options['constraints'] as $constraint) {
                $constraint['arguments'] = $constraint['arguments'] ?? [];
                $constraints[] = new $constraint['name'](...$constraint['arguments']);
            }

            $options['constraints'] = $constraints;
        }

        unset($options['constraints_raw']);

        return $options;
    }
}
