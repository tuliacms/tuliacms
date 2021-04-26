<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\Website\Locale\Storage;

use Tulia\Component\Routing\Website\Exception\LocaleNotExistsException;
use Tulia\Component\Routing\Website\Locale\LocaleInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface StorageInterface
{
    /**
     * @return array
     */
    public function all(): array;

    /**
     * @param string $code

     * @return LocaleInterface
     *
     * @throws LocaleNotExistsException
     */
    public function getByCode(string $code): LocaleInterface;
}
