<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Infrastructure\Persistence\Domain\WriteModel\ReadModel\Finder\Plugin;

use Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\Plugin\AbstractDbalPlugin;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class LocalePlugin extends AbstractDbalPlugin
{
    private CurrentWebsiteInterface $currentWebsite;

    public function __construct(CurrentWebsiteInterface $currentWebsite)
    {
        $this->currentWebsite = $currentWebsite;
    }

    public function filterCriteria(array $criteria): array
    {
        $criteria['website'] = $this->currentWebsite->getId();
        $criteria['locale'] = $this->currentWebsite->getLocale()->getCode();

        return $criteria;
    }
}
