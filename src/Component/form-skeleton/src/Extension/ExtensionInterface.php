<?php

declare(strict_types=1);

namespace Tulia\Component\FormSkeleton\Extension;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use Tulia\Component\FormSkeleton\Section\SectionsBuilderInterface;

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
     */
    public function getSections(SectionsBuilderInterface $builder): void;

    /**
     * Check if this extension supports this object
     *
     * @param FormTypeInterface $formType
     * @param array $options
     * @param mixed $data
     * @return bool
     */
    public function supports(FormTypeInterface $formType, array $options, $data = null): bool;
}
