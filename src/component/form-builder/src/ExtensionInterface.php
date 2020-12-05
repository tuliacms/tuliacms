<?php

declare(strict_types=1);

namespace Tulia\Component\FormBuilder;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Tulia\Component\FormBuilder\Section\SectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface ExtensionInterface
{
    /**
     * Called inside the form. Allows to create new fields, add transformers, events etc.
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void;

    /**
     * Called when the form is in creating. Allows to overwrite or add/remove any part of the form.
     * $form->getData() here, store empty source data, or data from storage (like database).
     *
     * @param FormPrototypeInterface $form
     */
    public function configureForm(FormPrototypeInterface $form): void;

    /**
     * Called when form is already submitted and validated. Allows to process form data, like save
     * metadatas on elements or insert data into Database.
     * $form->getData() here, store data from validated, submited form.
     *
     * @param FormInterface $form
     * @param object $data
     */
    public function handle(FormInterface $form, object $data): void;

    /**
     * Returns sections of the form. Each section has title and content (rendered in Twig).
     *
     * @return array|SectionInterface[]
     */
    public function getSections(): array;

    /**
     * Check if this extension supports this object
     *
     * @param object $object
     * @param string $scope
     *
     * @return bool
     */
    public function supports(object $object, string $scope): bool;
}
