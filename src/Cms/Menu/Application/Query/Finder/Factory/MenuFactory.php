<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Application\Query\Finder\Factory;

use Tulia\Cms\Menu\Domain\WriteModel\Model\Item;
use Tulia\Cms\Menu\Domain\WriteModel\Model\Menu;
use Tulia\Cms\Menu\Infrastructure\Cms\Metadata\Item\LoaderInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Utils\Uuid\UuidGeneratorInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class MenuFactory
{
    protected UuidGeneratorInterface $uuidGenerator;
    protected LoaderInterface $loader;
    protected CurrentWebsiteInterface $currentWebsite;

    public function __construct(
        UuidGeneratorInterface $uuidGenerator,
        LoaderInterface $loader,
        CurrentWebsiteInterface $currentWebsite
    ) {
        $this->uuidGenerator = $uuidGenerator;
        $this->loader = $loader;
        $this->currentWebsite = $currentWebsite;
    }

    /**
     * {@inheritdoc}
     */
    public function createNewMenu(array $data = []): Menu
    {
        return Menu::buildFromArray(array_merge($data, [
            'id'         => $this->uuidGenerator->generate(),
            'website_id' => $this->currentWebsite->getId(),
        ]));
    }

    /**
     * {@inheritdoc}
     */
    public function createNewItem(array $data = []): Item
    {
        $item = Item::buildFromArray(array_merge($data, [
            'id'         => $this->uuidGenerator->generate(),
            'locale'     => $this->currentWebsite->getLocale()->getCode(),
            'website_id' => $this->currentWebsite->getId(),
        ]));

        $this->loader->load($item);

        return $item;
    }
}
