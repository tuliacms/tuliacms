<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Application\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Tulia\Cms\Website\Application\Model\Website;

/**
 * @author Adam Banaszkiewicz
 */
class WebsiteEvent extends Event
{
    /**
     * @var Website
     */
    protected $website;

    /**
     * @param Website $website
     */
    public function __construct(Website $website)
    {
        $this->website = $website;
    }

    /**
     * @return Website
     */
    public function getWebsite(): Website
    {
        return $this->website;
    }
}
