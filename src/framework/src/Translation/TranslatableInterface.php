<?php

declare(strict_types=1);

namespace Tulia\Framework\Translation;

/**
 * @author Adam Banaszkiewicz
 */
interface TranslatableInterface
{
    /**
     * @return string|null
     */
    public function getMessage();

    /**
     * @return array
     */
    public function getParameters(): array;

    /**
     * @return string|null
     */
    public function getDomain();
}
