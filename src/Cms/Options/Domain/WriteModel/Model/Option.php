<?php

declare(strict_types=1);

namespace Tulia\Cms\Options\Domain\WriteModel\Model;

/**
 * @author Adam Banaszkiewicz
 */
class Option
{
    /**
     * @var string
     */
    private $websiteId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var null|string
     */
    private $locale;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @var bool
     */
    private $multilingual;

    /**
     * @var bool
     */
    private $autoload;

    public function __construct(
        string $websiteId,
        string $name,
        $value,
        ?string $locale = null,
        bool $multilingual = false,
        bool $autoload = false
    ) {
        $this->websiteId = $websiteId;
        $this->name = $name;
        $this->value = $value;
        $this->locale = $locale;
        $this->multilingual = $multilingual;
        $this->autoload = $autoload;
    }
}
