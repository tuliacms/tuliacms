<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Domain\Event;

/**
 * @author Adam Banaszkiewicz
 */
class ThemeActivated extends DomainEvent
{
    /**
     * @var string
     */
    private $themeName;

    /**
     * @var string
     */
    private $websiteId;

    public function __construct(string $themeName, string $websiteId)
    {
        $this->themeName = $themeName;
        $this->websiteId = $websiteId;
    }

    public function getThemeName(): string
    {
        return $this->themeName;
    }

    public function getWebsiteId(): string
    {
        return $this->websiteId;
    }
}
