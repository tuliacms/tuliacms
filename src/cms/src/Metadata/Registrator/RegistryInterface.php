<?php

declare(strict_types=1);

namespace Tulia\Cms\Metadata\Registrator;

/**
 * @author Adam Banaszkiewicz
 */
interface RegistryInterface
{
    public function addContentFields(string $contentType, ContentFieldsInterface $fields): self;
    public function getContentFields(string $contentType): ContentFieldsInterface;
    public function hasContentFields(string $contentType): bool;
    public function getContentTypes(): array;
    public function addRegistrator(RegistratorInterface $registrator): void;
}
