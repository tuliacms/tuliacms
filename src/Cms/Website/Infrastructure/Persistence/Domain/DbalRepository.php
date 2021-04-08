<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Infrastructure\Persistence\Domain;

use Tulia\Cms\Website\Domain\Aggregate\Locale;
use Tulia\Cms\Website\Domain\Aggregate\LocaleCollection;
use Tulia\Cms\Website\Domain\Aggregate\Website;
use Tulia\Cms\Website\Domain\Exception\WebsiteNotFoundException;
use Tulia\Cms\Website\Domain\ValueObject\AggregateId;
use Tulia\Cms\Website\Domain\RepositoryInterface;
use Tulia\Cms\Platform\Infrastructure\DataManipulation\Hydrator\HydratorInterface;
use Tulia\Framework\Database\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalRepository implements RepositoryInterface
{
    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @var DbalPersister
     */
    protected $persister;

    /**
     * @var HydratorInterface
     */
    protected $hydrator;

    /**
     * @param ConnectionInterface $connection
     * @param DbalPersister $persister
     * @param HydratorInterface $hydrator
     */
    public function __construct(
        ConnectionInterface $connection,
        DbalPersister $persister,
        HydratorInterface $hydrator
    ) {
        $this->connection = $connection;
        $this->persister  = $persister;
        $this->hydrator   = $hydrator;
    }

    /**
     * {@inheritdoc}
     */
    public function find(AggregateId $id): Website
    {
        $website = $this->connection->fetchAll('SELECT * FROM #__website AS tm WHERE tm.id = :id LIMIT 1', [
            'id' => $id->getId()
        ]);

        if (empty($website)) {
            throw new WebsiteNotFoundException();
        }

        $locales = $this->connection->fetchAll('SELECT * FROM #__website_locale AS tm WHERE tm.website_id = :id', [
            'id' => $id->getId()
        ]);

        $website = reset($website);
        $website['locales'] = array_map([$this, 'hydrateLocale'], $locales);

        return $this->hydrateWebsite($website);
    }

    /**
     * {@inheritdoc}
     */
    public function save(Website $website): void
    {
        $data = $this->extract($website);

        $this->connection->transactional(function () use ($data) {
            if ($this->recordExists($data['id'])) {
                $this->persister->update($data);
            } else {
                $this->persister->insert($data);
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Website $website): void
    {
        $data = $this->extract($website);

        $this->connection->transactional(function () use ($data) {
            $this->persister->delete($data);
        });
    }

    /**
     * @param string $id
     *
     * @return bool
     */
    private function recordExists(string $id): bool
    {
        $result = $this->connection->fetchAll('SELECT id FROM #__website WHERE id = :id LIMIT 1', ['id' => $id]);

        return isset($result[0]['id']) && $result[0]['id'] === $id;
    }

    private function extract(Website $website): array
    {
        $data = $this->hydrator->extract($website);
        $data['id'] = $website->getId()->getId();
        $data['locales'] = iterator_to_array($data['locales']->getIterator());
        $data['backend_prefix'] = $data['backendPrefix'];

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
