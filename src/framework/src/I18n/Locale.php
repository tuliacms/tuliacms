<?php

declare(strict_types=1);

namespace Tulia\Framework\I18n;

/**
 * @author Adam Banaszkiewicz
 */
final class Locale implements LocaleInterface
{
    /**
     * @var string
     */
    protected $code;

    /**
     * @param string $code
     */
    public function __construct(string $code)
    {
        $this->code = $code;
    }

    public function __toString(): string
    {
        return $this->code;
    }

    public function getCode(): string
    {
        return $this->code;
    }
}
