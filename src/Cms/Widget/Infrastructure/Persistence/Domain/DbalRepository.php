<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Infrastructure\Persistence\Domain;

use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;
use Tulia\Cms\Widget\Domain\Exception\WidgetNotFoundException;
use Tulia\Cms\Widget\Domain\ValueObject\AggregateId;
use Tulia\Cms\Widget\Domain\Aggregate\Widget;
use Tulia\Cms\Widget\Domain\RepositoryInterface;
use Tulia\Cms\Platform\Infrastructure\DataManipulation\Hydrator\HydratorInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalRepository implements RepositoryInterface
{
    protected ConnectionInterface $connection;
    protected DbalStorage $persister;
    protected HydratorInterface $hydrator;
    private CurrentWebsiteInterface $currentWebsite;

    public function __construct(
        ConnectionInterface $connection,
        DbalStorage $persister,
        HydratorInterface $hydrator,
        CurrentWebsiteInterface $currentWebsite
    ) {
        $this->connection = $connection;
        $this->persister  = $persister;
        $this->hydrator   = $hydrator;
        $this->currentWebsite = $currentWebsite;
    }

    /**
     * {@inheritdoc}
     */
    public function find(AggregateId $id, string $locale): Widget
    {
        $widget = $this->connection->fetchAll('
            SELECT
                tm.*,
                tl.locale,
                IF(ISNULL(tl.title), 0, 1) AS translated,
                COALESCE(tl.title, tm.title) AS title,
                COALESCE(tl.visibility, tm.visibility) AS visibility,
                COALESCE(tl.payload_localized, tm.payload_localized) AS payload_localized,
                tm.widget_id AS widget_id
            FROM #__widget AS tm
            LEFT JOIN #__widget_lang AS tl
                ON tm.id = tl.widget_id AND tl.locale = :locale
            WHERE tm.id = :id
            LIMIT 1', [
            'id'     => $id->getId(),
            'locale' => $locale
        ]);

        if (empty($widget)) {
            throw new WidgetNotFoundException();
        }

        $widget = reset($widget);
        /** @var Widget $aggregate */
        $aggregate = $this->hydrator->hydrate([
            'id'         => new AggregateId($widget['id']),
            'websiteId'  => $widget['website_id'],
            'widgetId'   => $widget['widget_id'],
            'space'      => $widget['space'],
            'name'       => $widget['name'],
            'htmlClass'  => $widget['html_class'],
            'htmlId'     => $widget['html_id'],
            'styles'     => json_decode($widget['styles'], true),
            'payload'    => json_decode($widget['payload'], true),
            'locale'     => $widget['locale'],
            'title'      => $widget['title'],
            'visibility' => $widget['visibility'] === '1',
            'payloadLocalized' => json_decode($widget['payload_localized'], true)
        ], Widget::class);

        return $aggregate;
    }

    /**
     * {@inheritdoc}
     */
    public function save(Widget $widget): void
    {
        $data = $this->extract($widget);

        $this->connection->transactional(function () use ($data) {
            $this->persister->save(
                $data,
                $this->currentWebsite->getDefaultLocale()->getCode()
            );
        });
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Widget $widget): void
    {
        $data = $this->extract($widget);

        $this->connection->transactional(function () use ($data) {
            $this->persister->delete($data['id']);
        });
    }

    private function extract(Widget $widget): array
    {
        $data = $this->hydrator->extract($widget);
        $data['id'] = $widget->getId()->getId();

        if (empty($data['locale'])) {
            $data['locale'] = $this->currentWebsite->getLocale()->getCode();
        }

        if (empty($data['websiteId'])) {
            $data['websiteId'] = $this->currentWebsite->getId();
        }

        return $data;
    }
}
