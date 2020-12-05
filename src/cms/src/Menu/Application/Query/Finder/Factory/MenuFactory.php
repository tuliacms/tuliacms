<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Application\Query\Finder\Factory;

use Tulia\Cms\Menu\Infrastructure\Cms\Metadata\Item\LoaderInterface;
use Tulia\Cms\Menu\Application\Query\Finder\Model\Item;
use Tulia\Cms\Menu\Application\Query\Finder\Model\Menu;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Tulia\Cms\Platform\Shared\Uuid\UuidGeneratorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class MenuFactory implements MenuFactoryInterface
{
    /**
     * @var UuidGeneratorInterface
     */
    protected $uuidGenerator;

    /**
     * @var LoaderInterface
     */
    protected $loader;

    /**
     * @var CurrentWebsiteInterface
     */
    protected $currentWebsite;

    /**
     * @param UuidGeneratorInterface $uuidGenerator
     * @param LoaderInterface $loader
     * @param CurrentWebsiteInterface $currentWebsite
     */
    public function __construct(
        UuidGeneratorInterface $uuidGenerator,
        LoaderInterface $loader,
        CurrentWebsiteInterface $currentWebsite
    ) {
        $this->uuidGenerator  = $uuidGenerator;
        $this->loader         = $loader;
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
