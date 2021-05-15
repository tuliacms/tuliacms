<?php

declare(strict_types=1);

namespace Tulia\Cms\Metadata\Domain\Registry;

/**
 * @author Adam Banaszkiewicz
 */
interface ContentFieldsRegistryInterface
{
    public function addContentFields(string $contentType, ContentFieldsInterface $fields): self;

    public function getContentFields(string $contentType): ContentFieldsInterface;

    public function hasContentFields(string $contentType): bool;

    public function getContentTypes(): array;

    public function addRegistrator(RegistratorInterface $registrator): void;
}
