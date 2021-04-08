<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Application\Builder;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tulia\Cms\ContactForms\Application\FieldType\Core\SubmitType;
use Tulia\Cms\ContactForms\Application\FieldType\RegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Form extends AbstractType
{
    /**
     * @var RegistryInterface
     */
    private $typesRegistry;

    /**
     * @param RegistryInterface $typesRegistry
     */
    public function __construct(RegistryInterface $typesRegistry)
    {
        $this->typesRegistry = $typesRegistry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /**
         * Add last field, the Submit button - always.
         */
        $options['fields'][] = [
            'name' => 'submit',
            'type' => SubmitType::class
        ];

        foreach ($options['fields'] as $field) {
            $type = $this->typesRegistry->get($field['type']);
            $options = $this->buildOptions($field['options']  ?? []);

            $builder->add(
                $field['name'],
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
        if (isset($options['constraints_raw'])) {
            foreach ($options['constraints_raw'] as $constraint) {
                $constraint['arguments'] = $constraint['arguments'] ?? [];

                $options['constraints'][] = new $constraint['name'](...$constraint['arguments']);
            }

            unset($options['constraints_raw']);
        }

        return $options;
    }
}
