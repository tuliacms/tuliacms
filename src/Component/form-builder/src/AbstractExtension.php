<?php

declare(strict_types=1);

namespace Tulia\Component\FormBuilder;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractExtension implements ExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    abstract public function supports(object $object, string $scope): bool;

    /**
     * {@inheritdoc}
     */
    public function configureForm(FormPrototypeInterface $form): void
    {

    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

    }

    /**
     * {@inheritdoc}
     */
    public function handle(FormInterface $form, object $data): void
    {

    }

    /**
     * {@inheritdoc}
     */
    public function getSections(): array
    {
        return [];
    }
}
