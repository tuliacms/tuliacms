<?php

declare(strict_types=1);

namespace Tulia\Component\FormBuilder\Manager;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface ManagerInterface
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void;

    /**
     * @param string|null $type
     * @param null $data
     * @param array $options
     *
     * @return FormInterface
     */
    public function createForm(string $type = null, $data = null, array $options = []): FormInterface;

    /**
     * @param FormInterface $form
     *
     * @return object
     */
    public function handle(FormInterface $form): object;

    /**
     * @param string|null $group
     *
     * @return array
     */
    public function getSections(string $group = null): array;
}
