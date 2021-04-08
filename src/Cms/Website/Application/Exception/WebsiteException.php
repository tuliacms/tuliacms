<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Application\Exception;

use Tulia\Cms\Website\Application\Model\Website;

/**
 * @author Adam Banaszkiewicz
 */
class WebsiteException extends \Exception
{
    /**
     * @var Website
     */
    protected $website;

    /**
     * @param Website $website
     */
    public function setWebsite(Website $website): void
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
