<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Builder\Type;

/**
 * @author Adam Banaszkiewicz
 */
interface TypeInterface
{
    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @param string $type
     */
    public function setType(string $type): void;

    /**
     * @return string
     */
    public function getLabel(): string;

    /**
     * @param string $label
     */
    public function setLabel(string $label): void;

    /**
     * @return string
     */
    public function getTranslationDomain(): string;

    /**
     * @param string $translationDomain
     */
    public function setTranslationDomain(string $translationDomain): void;

    /**
     * @param null|string $service
     */
    public function setSelectorService(?string $service): void;

    /**
     * @return null|string
     */
    public function getSelectorService(): ?string;
}
