<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\WriteModel;

use Tulia\Cms\Platform\Infrastructure\DataManipulation\Hydrator\HydratorInterface;
use Tulia\Cms\Website\Domain\WriteModel\Aggregate\Locale;
use Tulia\Cms\Website\Domain\WriteModel\Aggregate\LocaleCollection;
use Tulia\Cms\Website\Domain\WriteModel\Aggregate\Website;
use Tulia\Cms\Website\Domain\WriteModel\Exception\WebsiteNotFoundException;
use Tulia\Cms\Website\Domain\WriteModel\ValueObject\AggregateId;
use Tulia\Cms\Website\Ports\Infrastructure\Persistence\Domain\WriteModel\WebsiteStorageInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Repository
{
    protected WebsiteStorageInterface $storage;
    protected HydratorInterface $hydrator;

    public function __construct(WebsiteStorageInterface $storage, HydratorInterface $hydrator) {
        $this->storage = $storage;
        $this->hydrator = $hydrator;
    }

    /**
     * {@inheritdoc}
     */
    public function find(AggregateId $id): Website
    {
        $website = $this->storage->find($id->getId());

        if ($website === null) {
            throw new WebsiteNotFoundException(sprintf('Website %s not exists.', $id->getId()));
        }

        $website['locales'] = array_map([$this, 'hydrateLocale'], $website['locales']);

        return $this->hydrateWebsite($website);
    }

    /**
     * {@inheritdoc}
     */
    public function save(Website $website): void
    {
        $this->storage->insert($this->extract($website));
    }

    /**
     * {@inheritdoc}
     */
    public function update(Website $website): void
    {
        $this->storage->update($this->extract($website));
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Website $website): void
    {
        $this->storage->delete($website->getId()->getId());
    }

    private function extract(Website $website): array
    {
        $data = $this->hydrator->extract($website);
        $data['id'] = $website->getId()->getId();
        $data['locales'] = iterator_to_array($data['locales']->getIterator());
        $data['backend_prefix'] = '/administrator';

        unset($data['backendPrefix']);

        foreach ($data['locales'] as $key => $val) {
            $data['locales'][$key] = $this->hydrator->extract($val);
            $data['locales'][$key]['locale_prefix'] = $data['locales'][$key]['localePrefix'];
            $data['locales'][$key]['path_prefix']   = $data['locales'][$key]['pathPrefix'];
            $data['locales'][$key]['ssl_mode']      = $data['locales'][$key]['sslMode'];
            $data['locales'][$key]['is_default']    = $data['locales'][$key]['isDefault'];

            unset(
                $data['locales'][$key]['localePrefix'],
                $data['locales'][$key]['pathPrefix'],
                $data['locales'][$key]['sslMode'],
                $data['locales'][$key]['isDefault']
            );
        }

        return $data;
    }

    private function hydrateLocale(array $data): Locale
    {
        /** @var Locale $locale */
        $locale = $this->hydrator->hydrate([
            'code'         => $data['code'],
            'domain'       => $data['domain'],
            'localePrefix' => $data['locale_prefix'],
            'pathPrefix'   => $data['path_prefix'],
            'sslMode'      => $data['ssl_mode'],
            'isDefault'    => $data['is_default'] === '1',
        ], Locale::class);

        return $locale;
    }

    private function hydrateWebsite(array $data): Website
    {
        /** @var Website $website */
        $website = $this->hydrator->hydrate([
            'id'      => new AggregateId($data['id']),
            'name'    => $data['name'],
            'backendPrefix' => $data['backend_prefix'],
            'locales' => new LocaleCollection($data['locales']),
        ], Website::class);

        return $website;
    }
}
