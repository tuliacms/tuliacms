<?php

declare(strict_types=1);

namespace Tulia\Cms\Settings;

use Symfony\Component\Form\FormInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface GroupInterface
{
    /**
     * @return string
     */
    public function getId(): string;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getIcon(): string;

    /**
     * @return FormInterface
     */
    public function buildForm(): FormInterface;

    /**
     * @return array
     */
    public function buildView(): array;

    /**
     * @param array $data
     *
     * @return bool
     */
    public function saveAction(array $data): bool;

    /**
     * @return string
     */
    public function getTranslationDomain(): string;
}
