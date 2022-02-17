<?php

declare(strict_types=1);

namespace Tulia\Cms\Settings\Domain\Group;

use Symfony\Component\Form\FormInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface SettingsGroupInterface
{
    public function getId(): string;

    public function getName(): string;

    public function getIcon(): string;

    public function buildForm(): FormInterface;

    public function buildView(): array;

    public function saveAction(array $data): bool;

    public function getTranslationDomain(): string;
}
