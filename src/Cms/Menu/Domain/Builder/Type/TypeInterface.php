<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\Builder\Type;

use Tulia\Cms\Menu\UserInterface\Web\Backend\Selector\SelectorInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface TypeInterface
{
    public function getType(): string;

    public function setType(string $type): void;

    public function getLabel(): string;

    public function setLabel(string $label): void;

    public function getTranslationDomain(): string;

    public function setTranslationDomain(string $translationDomain): void;

    public function setSelectorService(SelectorInterface $service): void;

    public function getSelectorService(): ?SelectorInterface;
}
