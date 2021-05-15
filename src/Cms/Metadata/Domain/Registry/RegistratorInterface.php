<?php

declare(strict_types=1);

namespace Tulia\Cms\Metadata\Domain\Registry;

/**
 * @author Adam Banaszkiewicz
 */
interface RegistratorInterface
{
    public function register(ContentFieldsRegistryInterface $registry): void;
}
